<?php

namespace Source\App\Atendente;

use League\Plates\Engine;
use Source\Models\Atendente\VendaPersonalizadaItem;
use Source\Models\Vendas\Venda;

class AppPagamentoPublico
{
    private $view;

    public function __construct($router)
    {
        $this->view = Engine::create(__DIR__ . "/../../../pagamentos", "php");
        $this->view->addData(["router" => $router]);
    }

    public function pagar($data)
    {
        $uuid = trim($data["uuid"] ?? "");

        if (!$uuid) {
            $this->erro("Link inválido.");
            return;
        }

        $venda = (new Venda())->find(
            "link_publico = :lp AND tipo_venda = 1",
            "lp={$uuid}"
        )->fetch();

        if (!$venda) {
            $this->erro("Link de pagamento não encontrado ou expirado.");
            return;
        }

        $itens = (new VendaPersonalizadaItem())->porVenda($venda->id);

        // Fallback: se não há itens detalhados, usa o produto da venda principal
        if (empty($itens) && $venda->produto) {
            $produto = $venda->dataProduto();
            if ($produto) {
                $fallback        = new \stdClass();
                $fallback->nome_produto = $produto->nome;
                $fallback->nome_plano   = $venda->plano ? ($venda->dataPlano()->nome ?? "") : "";
                $itens = [$fallback];
            }
        }

        $comprador   = $venda->dataComprador();
        $nomeCliente = $comprador ? $comprador->nome : "";

        echo $this->view->render("pagar-personalizado", [
            "title"        => "Pagamento PIX | " . SITE,
            "venda"        => $venda,
            "itens"        => $itens,
            "qr_code"      => $venda->url_checkout ?? "",
            "link_publico" => $uuid,
            "nome_cliente" => $nomeCliente,
        ]);
    }

    private function erro(string $msg): void
    {
        echo $this->view->render("error", [
            "title"   => "Erro | " . SITE,
            "mensagem" => $msg,
        ]);
    }
}
