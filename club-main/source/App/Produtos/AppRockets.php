<?php

//CLASSE QUE CONTROLA AS ROTAS DOS Produtos

namespace Source\App\Produtos;

use League\Plates\Engine;
use Source\Models\Produtos\Produto;
use Source\Models\Produtos\Plano;
use Source\Models\Vendas\Venda;
use Source\Models\Usuarios\UsuarioDados;
use Source\Models\Usuarios\Usuario;

class AppRockets
{
    private $view;

    public function __construct($router)
    {
        $this->view = Engine::create(__DIR__ . "/../../../themes/tema1", "php");
        $this->view->addData(["router" => $router]);
    }

    public function produtos($data)
    {
        $hoje = date("Y-m-d 23:59:59");
        $semana = date("Y-m-d 23:59:59", strtotime("-7 days"));
        $vendasAtual = (new Venda())->find("created_at BETWEEN '$semana' AND '$hoje' GROUP BY produto", null, "COUNT(*) as count, produto")->fetch(true);
        $vendasAntiga = (new Venda())->find("created_at < '$semana' GROUP BY produto", null, "COUNT(*) as count, produto")->fetch(true);
        //var_dump($vendas);

        foreach(@$vendasAntiga as $venda){
            echo "<b>Produto:</b> {$venda->produto} <b>QTD:</b> {$venda->data()->count}<br>";

            $produto = (new Produto())->findById($venda->produto);
            echo "Rockets antigo $produto->rockets <br>";
            $rockets = $produto->rockets < 1 ? 0 : $produto->rockets - 1;
            $produto->rockets = $rockets;
            $produto->save();
        }

        foreach(@$vendasAtual as $venda){
            echo "<b>Produto:</b> {$venda->produto} <b>QTD:</b> {$venda->data()->count}<br>";
            $rockets = $this->rockets($venda->data()->count);
            echo "Rockets $rockets <br>";

            $produto = (new Produto())->findById($venda->produto);
            $produto->rockets = $rockets;
            $produto->save();
        }
    }

    public function rockets($count)
    {
        if($count > 0 && $count <= 30){
            $rockets = $count / 30;
        } elseif($count > 30 && $count <= 80){
            $rockets = 1 + ($count / 80);
        } elseif($count > 80 && $count <= 120){
            $rockets = 2 + ($count / 120);
        } elseif($count > 120 && $count <= 210){
            $rockets = 3 + ($count / 210);
        } elseif($count > 210 && $count <= 300){
            $rockets = 4 + ($count / 300);
        } else {
            $rockets = 5;
        }

        return number_format($rockets, 1);
    }
}