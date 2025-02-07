<?php

//CLASSE QUE CONTROLA AS ROTAS DOS AFILIADOS

namespace Source\App\Afiliados;

session_start();

use League\Plates\Engine;

use Source\Models\Afiliados\Afiliacao;
use Source\Models\Usuarios\Usuario;
use Source\Models\Usuarios\UsuarioDados;
use Source\Models\Produtos\Produto;
use Source\Models\Vendas\Venda;
use Source\App\Sessao\AppSessao;

use Source\Helpers\Paginador;
use Source\Helpers\Emails;
use Source\Models\Campanhas\Campanha;

class AppAfiliados
{
    private $view;
    private $sessao;
    private $tema;

    public function __construct($router)
    {
        $this->tema = @$_SESSION["tema"] ? @$_SESSION["tema"] : "tema1";
        $this->view = Engine::create(__DIR__ . "/../../../themes/$this->tema", "php");
        $this->view->addData(["router" => $router]);
        $this->sessao = (new AppSessao())->verificaSeLogado();
    }

    public function afiliacao($data)
    {
        $data = filter_var_array($data, FILTER_SANITIZE_STRIPPED);
        $afiliacao = new Afiliacao();
        $buscaAfiliacao = $afiliacao->find("afiliado = '{$data["afiliado"]}' AND produto = '{$data["produto"]}'")->fetch();
        
        $statusAfiliacao = array(
            1 => "Solicitaçao de afiliação em análise! Aguarde aprovação do produtor.",
            2 => "Você já esta afiliado a esse produto",
            3 => "Sua solicitação de afiliação foi REPROVADA!",
            4 => "Você foi impedido de se afiliar a esse produto"
        );
        if($buscaAfiliacao){
            echo $statusAfiliacao[$buscaAfiliacao->status];
            exit;
        }

        $aprovacao = array(
            0 => 1,
            1 => 2
        );

        $codigo = strtoupper(bin2hex(random_bytes(4)));

        $afiliacao->codigo = $codigo;
        $afiliacao->produto = $data["produto"];
        $afiliacao->produtor = $data["produtor"];
        $afiliacao->afiliado = $data["afiliado"];
        $afiliacao->liberar_comprador = $data["liberar_comprador"];
        $afiliacao->tempo_cookie = $data["tempo_cookie"];
        $afiliacao->tipo_comissao = $data["tipo_comissao"];
        $afiliacao->comissao_afiliado = $data["comissao_afiliado"];
        $afiliacao->tipo_atribuicao = $data["tipo_atribuicao"];
        $afiliacao->status = $aprovacao[@$data["afiliados_aprovacao"]];

        if($afiliacao->save()){
            if($afiliacao->status == 2){
                echo "Parabéns! Você acaba de se afiliar à/ao {$data["produto_nome"]}";
            } else {
                echo "Solicitaçao de afiliação em análise! Aguarde aprovação do produtor.";
            }

            $email = new Emails();
            $usuario = (new Usuario())->findById($afiliacao->dataProduto()->usuario);
            $userAF = (new Usuario())->findById($data["afiliado"]);
            $data["nome"] = $usuario->nome;
            $data["email"] = $usuario->email;
            $data["produto"] = $afiliacao->dataProduto()->nome;
            $data["afiliado_nome"] = $userAF->nome;
            $data["afiliado_email"] = $userAF->email;

            $emailAfiliado = $email->afiliacaoProduto($data);
            $email->enviaSendGrid($emailAfiliado);
        }
        else{
            echo "Erro\n";
            echo $afiliacao->fail()->getMessage();
        }
    }

    public function editarAfiliacao($data)
    {
        $data = filter_var_array($data, FILTER_SANITIZE_STRIPPED);
        $afiliacao = (new Afiliacao())->findById($data["afiliacao_id"]);

        $afiliacao->liberar_comprador = $data["liberar_comprador"];
        $afiliacao->tempo_cookie = $data["tempo_cookie"];
        $afiliacao->tipo_comissao = $data["tipo_comissao"];
        $afiliacao->comissao_afiliado = $data["comissao_afiliado"];
        $afiliacao->tipo_atribuicao = $data["tipo_atribuicao"];
        
        $status = $afiliacao->status;
        $afiliacao->status = $data["status"];

        //var_dump($afiliacao);

        if($afiliacao->save()){

            $email = new Emails();
            $usuario = (new Usuario())->findById($afiliacao->afiliado);
            $data["nome"] = $usuario->nome;
            $data["email"] = $usuario->email;
            $data["produto"] = $afiliacao->dataProduto()->nome;

            if($status == $afiliacao->status) {
                $emailAfiliado = $email->afiliacaoProdutoEditar($data, $afiliacao);
            } elseif($afiliacao->status == 2) {
                $emailAfiliado = $email->afiliacaoAprovar($data, $afiliacao);
            } elseif($afiliacao->status == 3) {
                $emailAfiliado = $email->afiliacaoReprovar($data, $afiliacao);
            } elseif($afiliacao->status == 4) {
                $emailAfiliado = $email->afiliacaoBloquear($data, $afiliacao);
            }
            $email->enviaSendGrid($emailAfiliado);

            header('Content-Type: application/json');
            echo json_encode("Atualizado com Sucessso");
        }
        else{
            echo "Erro\n";
            echo $afiliacao->fail()->getMessage();
        }
    }

    public function afiliacaoExcluir($data)
    {
        $data = filter_var_array($data, FILTER_SANITIZE_STRIPPED);

        $afiliacao = (new Afiliacao())->findById($data["id"]);

        $campanhas = (new Campanha())->find("usuario = $afiliacao->afiliado AND produto = $afiliacao->produto")->fetch(true);
        if($campanhas){
            foreach ($campanhas as $campanha) {
                $campanha->destroy();
            }
        }
        if($afiliacao->destroy()){
            echo "Excluido com sucesso";

            $email = new Emails();
            $usuario = (new Usuario())->findById($afiliacao->afiliado);
            $data["nome"] = $usuario->nome;
            $data["email"] = $usuario->email;
            $data["produto"] = $afiliacao->dataProduto()->nome;
            
            $emailAfiliado = $email->afiliacaoExcluir($data);
            $email->enviaSendGrid($emailAfiliado);
        }
        else {
            echo "Erro ao excluir <br>";
            echo $afiliacao->fail()->getMessage();
        }
    }

    public function afiliacaoExcluirTodos($data)
    {
        $data = filter_var_array($data, FILTER_SANITIZE_STRIPPED);

        $afiliacao = (new Afiliacao())->find("produto = " . $data["id"])->fetch(true);
        
        if($afiliacao){
            foreach($afiliacao as $afiliacao){
                $afiliacao->destroy();
            }
        }
    }

    public function afiliados($data)
    {
        $paginador = new Paginador();
        $pagina = @$data["page"];
        $offset = 10;
        $afiliadoCount = (new Usuario())->find("tipo_usuario = 5")->count();
        $paginas = ceil($afiliadoCount / $offset);
        $afiliado = (new Usuario())->find("tipo_usuario = 5")->limit($offset)->offset($paginador->paginador($pagina, $offset))->order("grupo DESC")->fetch(true);
        $afiliadoDados = new UsuarioDados();
        $vendas = (new Venda());

        $afiliacao = (new Afiliacao())->find()->fetch(true);
        foreach ($afiliacao as $afiliacao) {
            $produto = (new Produto())->findById($afiliacao->produto);
            $afiliacao->produtor = $produto->usuario;
            $afiliacao->save();
        }
        exit;
        echo $this->view->render("afiliados", [
            "title" => "Afiliados",
            "afiliado" => $afiliado,
            "count" => $afiliadoCount,
            "afiliadoDados" => $afiliadoDados,
            "vendas" => $vendas,
            "paginador" => $paginador,
            "total" => $paginas,
            "pagina" => @$data["page"] ? $data["page"] : 1,
            "msg" => @$data["msg"],
            "classCss" => @$data["classCss"]
        ]);
    }

    public function editarAfiliado($data)
    {
        $data = filter_var_array($data, FILTER_SANITIZE_STRIPPED);

        $afiliado = (new Usuario())->findById($data["id"]);
        $afiliado->tempo_saque = $data["tempo_saque"];
        $afiliado->taxa_saque = $data["taxa_saque"];
        $afiliado->observacao = @$data["observacao"];
        $afiliado->ativo = $data["ativo"];
        $afiliado->grupo = $data["grupo"];
        
        if($afiliado->save()){
            echo "Atualizado Com Sucesso!";
        } 
        else {
            echo $afiliado->fail()->getMessage();
        }
    }

    public function inativarAfiliado($data)
    {
        $data = filter_var_array($data, FILTER_SANITIZE_STRIPPED);

        $afiliado = (new Usuario())->findById($data["id"]);
        $afiliado->ativo = $data["ativo"];
        if($afiliado->save()){
            echo "Atualizado com sucesso";
        } else {
            echo $afiliado->fail()->getMessage();
        }
    }
}