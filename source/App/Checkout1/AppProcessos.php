<?php

namespace Source\App\Checkout1;

use Source\Helpers\Emails;

use Minishlink\WebPush\WebPush;
use Minishlink\WebPush\Subscription;

use Source\Models\CarrinhoAbandonado\CarrinhoAbandonado;
use Source\Models\Vendas\Venda;
use Source\Models\Produtos\Produto;
use Source\Models\Afiliados\Afiliacao;
use Source\Models\Processos\Processo;
use Source\Models\Whatsapp\NotificacoesInteligentes;
use Source\App\Whatsapp\AppNotificacoesInteligentes;
use Source\App\Postback\AppPostback;
use League\Plates\Engine;
use Source\Models\Permissao\Section;
use Source\App\Sessao\AppSessao;


class AppProcessos
{   
    private $view;
    private $section;

    public function __construct($router)
    {
        $tema = $_SESSION["tema"] ?? "tema1";
        $this->view = Engine::create(__DIR__ . "/../../../themes/{$tema}", "php");
        $this->view->addData(["router" => $router]);

        $this->section = new Section();
    }

    public function cadastroProcessos($data)
    {
        $processo = new Processo();

        $processo->venda = $data["venda"];
        if ($processo->save()) {
            echo "Salvo com sucesso";
        } else {
            echo $processo->fail()->getMessage();
        }
    }

    public function processos($data)
    {
        $this->notificacoesInteligentes($data);
        $this->envioEmails($data);
    }

    public function envioEmails($data)
    {
        $status = @$data["status_transacao"];

        $email = new Emails();
        $mensagem = array(
            1 => "",
            2 => "",
            3 => "",
            4 => $email->cartaoAprovado($data),
            5 => $email->pagamentoCancelado($data),
            6 => $email->pagamentoEstornado($data)
        );

        $msg = $mensagem[$status];
        if($status > 3)
        $envia = $email->enviaSendGrid($msg);
        echo @$envia;
    }

    public function notificacoes()
    {
        #
    }

    public function notificacoesInteligentes($data)
    {
        $status = array(
            1 => "billet-printed",
            2 => "order-processing",
            4 => "order-paid",
            5 => "order-canceled",
            6 => "order-refunded",
        );
        $notificacao = (new NotificacoesInteligentes())->find("usuario = " . $data["produtor"])->fetch();

        if($notificacao){
            $api["url"] = "https://api.notificacoesinteligentes.com/integrations/$notificacao->integracao_id/events/" . $status[$data["status_transacao"]];
            $api["token"] = $notificacao->token;

            $mensagem = new AppNotificacoesInteligentes(null);

            try{
                $pedido = $mensagem->pedidos($data);
                $envia = $mensagem->enviaDados($pedido, $api);
                return "Enviando... ";
            } catch(Exception $e) {
                return "Erro... " . $e;
            }
            
            
            //var_dump($pedido);
        }
    }

    public function excluirCarrinho($data)
    {
        #
    } 
    
    public function enviarNotificacaoUsuarioEspecifico($userId, $titulo, $mensagem)
{
    
    $pdo = new \PDO('sqlite:' . dirname(__DIR__, 3) . '/db.sqlite');
    $stmt = $pdo->prepare("SELECT subscription FROM push_subscriptions WHERE user_id = :user_id");
    $stmt->execute([':user_id' => $userId]);
    $row = $stmt->fetch(\PDO::FETCH_ASSOC);
    
    if (!$row) {
        echo "Usuário não encontrado ou sem inscrição";
        return;
    }
 
    $subscriptionData = json_decode($row['subscription'], true);
    $subscription = Subscription::create($subscriptionData);

    $auth = [
        'VAPID' => [
            'subject' => 'mailto:seu@email.com',
            'publicKey' => 'BPVzsIRnzg3Vu6vEQ2RsYEZ_5TF-ow515Sk3kKMOhRcr5F-jL9IRcpIC0Nat_3nv2o8dvJzBIUYytAjP8LzJpeM',
            'privateKey' => 'SJkj3haKFatAyi7BKtMYOwQZwff8F4lq3ZIhMXwpqbE',
        ],
    ];

    $webPush = new WebPush($auth);
    error_log("chamou22222");
    $webPush->sendOneNotification($subscription, json_encode([
        'title' => $titulo,
        'body' => $mensagem,
        'icon' => 'themes/tema1/images/favicon.png', // URL da imagem do ícone
    ]));
}

    public function enviarNotificacaoParaTodos($titulo, $mensagem, $imagem, $url)
    {
        $pdo = new \PDO('sqlite:./db.sqlite');
        $stmt = $pdo->query("SELECT subscription FROM push_subscriptions");
        $auth = [
            'VAPID' => [
                'subject' => 'mailto:seu@email.com',
                'publicKey' => 'BPVzsIRnzg3Vu6vEQ2RsYEZ_5TF-ow515Sk3kKMOhRcr5F-jL9IRcpIC0Nat_3nv2o8dvJzBIUYytAjP8LzJpeM',
                'privateKey' => 'SJkj3haKFatAyi7BKtMYOwQZwff8F4lq3ZIhMXwpqbE',
            ],
        ];

        $webPush = new WebPush($auth);

        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $subscriptionData = json_decode($row['subscription'], true);
            $subscription = Subscription::create($subscriptionData);

            $webPush->queueNotification($subscription, json_encode([
                'title' => $titulo,
                'body' => $mensagem,
                'icon' => $imagem,
                'data' => [
                    'url' => $url
                ]
            ]));
        }

        foreach ($webPush->flush() as $report) {
            echo "Enviado para {$report->getRequest()->getUri()} - " . ($report->isSuccess() ? "Sucesso" : "Falha") . "<br>";
            echo "$url";
        }
    }



    public function formNotificacaoTodos(?array $data): void
    {
        @session_start();
        $this->sessao = (new AppSessao())->verificaSeLogado();
        
        echo $this->view->render("notificacao/form-notificacao-todos", [
            "title" => "Notificar Todos os Usuários"
        ]);
    }

    public function notificarTodosDisparar(array $data): void
    {
        $titulo = filter_var($data["titulo"] ?? '', FILTER_SANITIZE_STRING);
        $mensagem = filter_var($data["mensagem"] ?? '', FILTER_SANITIZE_STRING);
        $imagem = filter_var($data["imagem"] ?? '', FILTER_SANITIZE_URL);
        $url = filter_var($data["url"] ?? '', FILTER_SANITIZE_URL);
    
        if (!$titulo || !$mensagem) {
            echo "<p style='color:red;'>Título e mensagem são obrigatórios.</p>";
            return;
        }
    
        $imagemFinal = $imagem ?: "themes/tema1/images/favicon.png";
        $urlFinal = $url ?: "https://seusite.com"; // fallback se não for enviado
    
        $this->enviarNotificacaoParaTodos($titulo, $mensagem, $imagemFinal, $urlFinal);
    
        echo "<p style='color: #FFD700; font-size: 18px;'>✅ Notificação enviada com sucesso para todos os usuários!</p>";
    }
        
        
        


}