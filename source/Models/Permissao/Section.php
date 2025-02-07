<?php

namespace Source\Models\Permissao;

class Section
{
    private $tipo_secao;
    private $id;
    private $tipo;
    private $nome;
    private $nome_abreviado;
    private $email;
    private $idU;
    private $tema;
    private $foto;
    private $verificado;
    private $testador;

    public function __construct()
    {
        $this->id = $this->getId();
    }

    public function permissaoModulo($modulo)
    {
        if (@$_SESSION["subusuario"]) {
            $pagina = (new Pagina())->find("modulo = '$modulo'")->fetch();
            $permissao = (new PermissaoModulo())->find("usuario_permitido = " . $_SESSION["id"] . " AND pagina = " . @$pagina->id)->fetch();

            if (!$permissao || @$permissao->ver == 0) {
                header("Location: " . url());
            }
        }
    }

    public function permissaoModuloBool($modulo)
    {
        if (@$_SESSION["subusuario"]) {
            $permissao = (new PermissaoModulo())->find("usuario_permitido = " . $_SESSION["id"] . " AND modulo = '$modulo'")->fetch();

            if (!$permissao || @$permissao->ver == 0) {
                return false;
            } else {
                return true;
            }
        }
    }

    public function getTipoSecao()
    {
        return @$_SESSION["subusuario"] ? "subusuario" : "usuario";
    }

    public function getTipoSecaoLayout()
    {
        if ($this->getTipoSecao() == "subusuario") {
            echo "<div class='alert alert-warning'>Você está logado como {$this->getNome()}</div>";
        }
    }

    public function getId()
    {
        return @$_SESSION["subusuario"] ? @$_SESSION["subusuario"]["id"] : @$_SESSION["id"];
    }

    public function getTipo()
    {
        return @$_SESSION["subusuario"] ? $_SESSION["subusuario"]["tipo"] : $_SESSION["tipo"];
    }

    public function getNome()
    {
        return @$_SESSION["subusuario"] ? $_SESSION["subusuario"]["nome"] : $_SESSION["nome"];
    }

    public function getNomeAbreviado()
    {
        return @$_SESSION["subusuario"] ? $_SESSION["subusuario"]["nome_abreviado"] : $_SESSION["nome_abreviado"];
    }

    public function getEmail()
    {
        return @$_SESSION["subusuario"] ? $_SESSION["subusuario"]["email"] : $_SESSION["email"];
    }

    public function getIdU()
    {
        return @$_SESSION["subusuario"] ? $_SESSION["subusuario"]["idU"] : $_SESSION["idU"];
    }

    public function getTema()
    {
        return @$_SESSION["subusuario"] ? $_SESSION["subusuario"]["tema"] : $_SESSION["tema"];
    }

    public function getFoto()
    {
        return @$_SESSION["subusuario"] ? $_SESSION["subusuario"]["foto"] : $_SESSION["foto"];
    }

    public function getVerificado()
    {
        return @$_SESSION["subusuario"] ? $_SESSION["subusuario"]["verificado"] : $_SESSION["verificado"];
    }

    public function getTestador()
    {
        return @$_SESSION["subusuario"] ? $_SESSION["subusuario"]["testador"] : $_SESSION["testador"];
    }
}
