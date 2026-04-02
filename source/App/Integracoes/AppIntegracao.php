<?php

/** 
 * CADA MÉTODO É UMA ROTA DO SISTEMA(HOME, CONTATO, BLOG, ETC...)
 *  
 **/

namespace Source\App\Integracoes;

use League\Plates\Engine;
use Source\App\Sessao\AppSessao;
use Source\Helpers\Paginador;
use Source\Models\ActiveCampaign\ActiveCampaign;
use Source\Models\RdStation\RdStation;
use Source\Models\Produtos\Produto;
use Source\Models\Usuarios\UsuarioPremium;
use Source\Models\Usuarios\Usuario;
use Source\Models\Comissao\Comissao;
use Source\Models\Comissao\Saldo;
use Source\Models\Vendas\Venda;
use Source\Models\Postback\Postback;
use Source\Models\Bling\Bling;
use Source\Models\ChatGuru\ChatGuru;
use Source\Models\Hotjar\Hotjar;
use Source\Models\Leadlovers\Leadlovers;
use Source\Models\Notazz\IntegracaoNotazz;
use Source\Models\Shopify\Shopify;
use Source\Models\Whatsapp\NotificacoesInteligentes;
use Source\Models\Mex\Mex;
use Source\Models\Utmify\Utmify;
use Source\Models\Permissao\PermissaoModulo;
use Source\Models\Permissao\Section;

session_start();

class AppIntegracao
{
    private $view;
    private $sessao;
    private $tema;
    private $section;

    public function __construct($router)
    {
        $this->tema = @$_SESSION["tema"] ? @$_SESSION["tema"] : "tema1";
        $this->view = Engine::create(__DIR__ . "/../../../themes/$this->tema", "php");
        $this->view->addData(["router" => $router]);

        $this->sessao = (new AppSessao())->verificaSeLogado();
        $this->section = new Section();
    }

    public function integracoes($data)
    {
        $section = new Section();
        (new Section())->permissaoModulo("integracoes");

        if (@$_SESSION["subusuario"])
            $permissao = (new PermissaoModulo())->find("usuario_permitido = " . $_SESSION["id"] . " AND modulo = 'integracoes'")->fetch();

        $usuario = (new Usuario())->findById($this->section->getId());
        $produtos = (new Produto())->find("usuario = " . $this->section->getId())->fetch(true);
        $shopify = (new Shopify())->find("usuario = " . $this->section->getId())->fetch();
        $utmify = (new Utmify())->find("usuario = " . $this->section->getId())->fetch();

        echo $this->view->render("integracoes/disponiveis", [
            "title" => "Integrações Disponíveis",
            "section" => @$section,
            "permissao" => @$permissao,
            "usuario" => $usuario,
            "produtos" => $produtos,
            "shopify" => $shopify,
            "utmify" => $utmify,
        ]);
    }

    public function ativas($data)
    {
        $section = new Section();
        (new Section())->permissaoModulo("integracoes");

        if (@$_SESSION["subusuario"])
            $permissao = (new PermissaoModulo())->find("usuario_permitido = " . $_SESSION["id"] . " AND modulo = 'integracoes'")->fetch();

        $usuario = (new Usuario())->findById($this->section->getId());
        $produtos = (new Produto())->find("usuario = " . $this->section->getId())->fetch(true);

        $blingCount = (new Bling())->find("usuario = " . $this->section->getId())->count();
        $bling = (new Bling())->find("usuario = " . $this->section->getId())->fetch(true);

        $hotzappCount = (new Postback())->find("descricao ='hotzapp' AND usuario = " . $this->section->getId())->count();
        $hotzapp = (new Postback())->find("descricao ='hotzapp' AND usuario = " . $this->section->getId())->fetch(true);

        $hotjarCount = (new Hotjar())->find("usuario = " . $this->section->getId())->count();
        $hotjar = (new Hotjar())->find("usuario = " . $this->section->getId())->fetch(true);

        $iseiCount = (new Postback())->find("descricao ='isei' AND usuario = " . $this->section->getId())->count();
        $isei = (new Postback())->find("descricao ='isei' AND usuario = " . $this->section->getId())->fetch(true);

        $notasCount = (new IntegracaoNotazz())->find("usuario = " . $this->section->getId())->count();
        $notas = (new IntegracaoNotazz())->find("usuario = " . $this->section->getId())->fetch(true);

        $notificacoesCount = (new NotificacoesInteligentes())->find("usuario = " . $this->section->getId())->count();
        $notificacoes = (new NotificacoesInteligentes())->find("usuario = " . $this->section->getId())->fetch(true);

        $leadlovers = (new Leadlovers())->find("usuario = " . $this->section->getId())->count();
        $lead = (new Leadlovers())->find("usuario = " . $this->section->getId())->fetch(true);

        $hotbilletCount = (new Postback())->find("descricao ='hotbillet' AND usuario = " . $this->section->getId())->count();
        $hotbillet = (new Postback())->find("descricao ='hotbillet' AND usuario = " . $this->section->getId())->fetch(true);

        $mexCount = (new Mex())->find("usuario = " . $this->section->getId())->count();
        $mex = (new Mex())->find("usuario = " . $this->section->getId())->fetch(true);

        $chatguru = (new ChatGuru())->find("usuario = " . $this->section->getId())->count();
        $chat = (new ChatGuru())->find("usuario = " . $this->section->getId())->fetch(true);

        $activeCampaignCount = (new ActiveCampaign())->find("usuario = " . $this->section->getId())->count();
        $activecampaign = (new ActiveCampaign())->find("usuario = " . $this->section->getId())->fetch(true);

        $rdstationCount = (new RdStation())->find("usuario = " . $this->section->getId())->count();
        $rdstation = (new RdStation())->find("usuario = " . $this->section->getId())->fetch(true);

        $postbacksCount = (new Postback())->find("descricao ='postback' AND usuario = " . $this->section->getId())->count();
        $postbacks = (new Postback())->find("descricao ='postback' AND usuario = " . $this->section->getId())->fetch(true);

        $shopifyCount = (new Shopify())->find("usuario = " . $this->section->getId())->count();
        $shopify = (new Shopify())->find("usuario = " . $this->section->getId())->fetch();

        $utmifyCount = (new Utmify())->find("usuario = " . $this->section->getId())->count();
        $utmify = (new Utmify())->find("usuario = " . $this->section->getId())->fetch();

        echo $this->view->render("integracoes/ativas", [
            "title" => "Integrações Ativas",
            "section" => @$section,
            "permissao" => @$permissao,
            "usuario" => $usuario,
            "produtos" => $produtos,
            "bling" => $bling,
            "blingCount" => $blingCount,
            "hotzappCount" => $hotzappCount,
            "hotzapp" => $hotzapp,
            "hotjarCount" => $hotjarCount,
            "hotjar" => $hotjar,
            "iseiCount" => $iseiCount,
            "isei" => $isei,
            "notasCount" => $notasCount,
            "notas" => $notas,
            "notificacoesCount" => $notificacoesCount,
            "notificacoes" => $notificacoes,
            "leadlovers" => $leadlovers,
            "lead" => $lead,
            "hotbilletCount" => $hotbilletCount,
            "hotbillet" => $hotbillet,
            "mexCount" => $mexCount,
            "mex" => $mex,
            "chatguru" => $chatguru,
            "chat" => $chat,
            "activeCampaignCount" => $activeCampaignCount,
            "activecampaign" => $activecampaign,
            "rdstationCount" => $rdstationCount,
            "rdstation" => $rdstation,
            "postbacksCount" => $postbacksCount,
            "postbacks" => $postbacks,
            "shopifyCount" => $shopifyCount,
            "shopify" => $shopify,
            "utmifyCount" => $utmifyCount,
            "utmify" => $utmify,
        ]);
    }

    public function salvarUtmify($data)
    {
        (new Section())->permissaoModulo("integracoes");

        $apiKey = trim(@$_POST["api_key"]);
        if (!$apiKey) {
            echo json_encode(["msg" => "Informe a API Key da Utmify"]);
            return;
        }

        $utmify = (new Utmify())->find("usuario = " . $this->section->getId())->fetch();
        if (!$utmify) {
            $utmify = new Utmify();
            $utmify->usuario = $this->section->getId();
        }
        $utmify->api_key = $apiKey;
        $utmify->save();

        if ($utmify->fail()) {
            echo json_encode(["msg" => "Erro ao salvar: " . $utmify->fail()->getMessage()]);
            return;
        }

        echo json_encode(["msg" => "Integração Utmify salva com sucesso"]);
    }

    public function excluirUtmify($data)
    {
        (new Section())->permissaoModulo("integracoes");

        $utmify = (new Utmify())->find("usuario = " . $this->section->getId())->fetch();
        if ($utmify) {
            $utmify->destroy();
        }

        echo json_encode(["msg" => "Integração Utmify removida"]);
    }
}
