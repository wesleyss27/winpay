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
}
