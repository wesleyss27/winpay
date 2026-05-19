<?php

namespace Source\App\Sessao;

class AppSessao
{
    public function verificaSeLogado(): void
    {
        if (empty($_SESSION["winturb_id"])) {
            header("Location: " . url("login"));
            exit;
        }
    }

    public static function usuarioId(): ?int
    {
        return isset($_SESSION["winturb_id"]) ? (int)$_SESSION["winturb_id"] : null;
    }

    public static function usuarioNome(): string
    {
        return (string)($_SESSION["winturb_nome"] ?? "");
    }
}
