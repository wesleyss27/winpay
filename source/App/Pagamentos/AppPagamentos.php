<?php

namespace Source\App\Pagamentos;

class AppPagamentos
{
    public function solicitarReembolso()
    {
        // Renderiza a página estática de solicitação de reembolso sem alterar o caminho.
        require __DIR__ . "/../../../pagamentos/solicitar-reembolso.php";
        exit;
    }

    public function statusReembolso()
    {
        // Página pública para acompanhamento das solicitações de reembolso.
        require __DIR__ . "/../../../pagamentos/status-reembolso.php";
        exit;
    }
}
