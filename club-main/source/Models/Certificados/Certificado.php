<?php

namespace Source\Models\Certificados;

use CoffeeCode\DataLayer\DataLayer;
use Source\Models\Cursos\Cursos;
use Source\Models\Usuarios\Usuario;

class Certificado extends DataLayer
{
    public function __construct()
    {
        parent::__construct("certificados", []);
    }

    public function dataUsuario()
    {
        if ($this->usuario)
            return (new Usuario())->findById($this->produtor);
    }

    public function dataCurso()
    {
        if ($this->curso)
            return (new Cursos())->findById($this->curso);
    }
}
