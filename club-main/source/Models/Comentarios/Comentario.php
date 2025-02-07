<?php

namespace Source\Models\Comentarios;

use CoffeeCode\DataLayer\DataLayer;
use Source\Models\Aulas\Aulas;
use Source\Models\Cursos\Cursos;
use Source\Models\Cursos\Modulos;
use Source\Models\Usuarios\Usuario;

class Comentario extends DataLayer
{
    public function __construct()
    {
        parent::__construct("comentarios", []);
    }

    public function dataProdutor()
    {
        if ($this->usuario)
            return (new Usuario())->findById($this->produtor);
    }

    public function dataAluno()
    {
        if ($this->aluno)
            return (new Usuario())->findById($this->aluno);
    }

    public function dataModulo()
    {
        if ($this->modulo)
            return (new Modulos())->findById($this->modulo);
    }

    public function dataCurso()
    {
        if ($this->curso)
            return (new Cursos())->findById($this->curso);
    }

    public function dataAula()
    {
        if ($this->aula)
            return (new Aulas())->findById($this->aula);
    }

    /**
     * PROPRIEDADES DA TABELA PRODUTO
     * 
     *   id` int(11) NOT NULL,
     *   produtor 	int(11)
     *   aluno int(11)
     *   modulo	int(11)
     *   curso int(11)
     *   aula int(11)
     *   comentario text
     *   resposta int(11) 
     *   created_at` timestamp NULL DEFAULT NULL,
     *   updated_at` timestamp NULL DEFAULT NULL,
     */

    /**
     * STATUS DO PRODUTO
     * 1 - PENDENTE
     * 2 - APROVADO
     * 3 - REPROVADO
     * 4 - BLOQUEADO
     */
}
