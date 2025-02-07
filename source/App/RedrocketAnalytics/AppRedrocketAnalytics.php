<?php

namespace Source\App\RedrocketAnalytics;

use League\Plates\Engine;
use Source\Models\RedrocketAnalytics\RedrocketAnalytics;

class AppRedrocketAnalytics
{
    private $view;
    private $tema;

    public function __construct($router)
    {
        $this->tema = @$_SESSION["tema"] ? @$_SESSION["tema"] : "tema1";
        $this->view = Engine::create(__DIR__ . "/../../../themes/$this->tema", "php");
        $this->view->addData(["router" => $router]);
    }

    public function cadastrarAnalytics($data)
    {
        if(@$data["url_anterior"])
        $analytic = (new RedrocketAnalytics())->find("url = '" . @$data["url"] . "' AND navegador = '" . @$data["navegador"] . "' AND url_anterior = '" . @$data["url_anterior"] . "'")->fetch();
        else
        $analytic = (new RedrocketAnalytics())->find("url = '" . @$data["url"] . "' AND navegador = '" . @$data["navegador"] . "'")->fetch();

        if (!$analytic)
            $analytic = new RedrocketAnalytics();

        $analytic->url = @$data["url"];
        $analytic->url_anterior = @$data["url_anterior"];
        $analytic->navegador = @$data["navegador"];
        $analytic->proxima_url = @$data["proxima_url"];
        $analytic->produto = @$data["produto"];
        $analytic->plano = @$data["plano"];
        $analytic->checkout = @$data["checkout"];
        $analytic->afiliado = @$data["afiliado"];
        $analytic->campanha = @$data["campanha"];
        $analytic->venda = @$data["venda"];
        $analytic->produtor = @$data["produtor"];
        $analytic->tempo = @$data["tempo"];

        if ($analytic->save()) {
            return "Salvo";
        } else {
            return "Erro";
        }
    }
}
