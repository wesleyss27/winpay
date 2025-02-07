<?php

namespace Source\App\Sessao;

//session_start();

use Source\Models\Sessao\Sessao;
use Source\Models\Usuarios\Usuario;

class AppSessao
{
    public function verificaSeLogado()
    {
        if(@$_SESSION["sessao"]){
            $sessao = (new Sessao())->find("sessao = :sessao", "sessao=".@$_SESSION["sessao"])->fetch();
            $dt_atual = date("Y-m-d H:i:s");
            if(!$sessao || strtotime($dt_atual) >= strtotime(@$sessao->expiration_date)){

                $data["id"] = $sessao->id;
                if($data["id"]){
                    $this->deletaSessao($data);
                    echo "<script>alert('Sua sessão expirou! Faça login novamente'); location.href = '". URL_BASE ."/login?redirect={$_SERVER["REQUEST_URI"]}'</script>";
                }
                else {
                    header("Location: " . URL_BASE . "/login");
                }
            }
        } else {
            header("Location: " . URL_BASE . "/login");
        }
    }

    public function criaSessao($data)
    {
        
    }

    public function deletaSessao($data)
    {
        $sessao = (new Sessao())->findById($data["id"]);
        if($sessao) {
            //$sessao->destroy();
            session_destroy();
        }
    }

}
