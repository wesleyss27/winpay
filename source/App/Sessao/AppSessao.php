<?php

namespace Source\App\Sessao;

use Source\Models\Permissao\Pagina;
use Source\Models\Permissao\PermissaoModulo;
use Source\Models\Sessao\Sessao;
use Source\Models\Usuarios\Usuario;

class AppSessao
{
    public function verificaSeLogado()
    {
        if (@$_SESSION["sessao"]) {
            $sessao = (new Sessao())->find("sessao = :sessao", "sessao=" . @$_SESSION["sessao"])->fetch();
            $dt_atual = date("Y-m-d H:i:s");
            if (!$sessao || strtotime($dt_atual) >= strtotime(@$sessao->expiration_date)) {

                $data["id"] = $sessao->id;
                if ($data["id"]) {
                    $this->deletaSessao($data);
                    echo "<script>alert('Sua sessão expirou! Faça login novamente'); location.href = '" . URL_BASE . "/login?redirect={$_SERVER["REQUEST_URI"]}'</script>";
                } else {
                    header("Location: " . URL_BASE . "/login?redirect={$_SERVER["REQUEST_URI"]}");
                }
            }
        } else {
            header("Location: " . URL_BASE . "/login?redirect={$_SERVER["REQUEST_URI"]}");
        }
    }

    public function subsessao($data)
    {
        if (@$_SESSION["subsessao"]["id"]) {
            $pagina = (new Pagina)->find("modulo = " . $data["modulo"])->fetch();

            if ($pagina) {
                $permissaoModulo = (new PermissaoModulo())->find("pagina = $pagina->id AND ver = 1")->fetch();
            }
        }
    }

    public function deletaSessao($data)
    {
        $sessao = (new Sessao())->findById($data["id"]);
        if ($sessao) {
            //$sessao->destroy();
            session_destroy();
        }
    }
}
