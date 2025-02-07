<?php

//CLASSE QUE CONTROLA AS ROTAS DOS Produtos

//namespace Source\App\Produtos;
require __DIR__ . "/vendor/autoload.php";
use Source\Models\Produtos\Produto;
use Source\Models\Sessao\Sessao;

        $produto = (new Produto())->findById(1);
        //$produto->nome = "Produto Teste Atualização2";
        $produto->codigo = "12345";
        $produto->usuario = "1";
        $produto->descricao = "1";
        $produto->categoria = "1";
        $produto->subcategoria = "2";
        $produto->tipo_produto = "2";


        if($produto->save()){
            echo "Atualizado<br>";
            echo $produto->fail()->getMessage();
        }
        else
            echo $produto->fail()->getMessage();

        $sessao = new Sessao();
        /*$sessao->usuario = 3;
        $sessao->sessao = "dklslfjlajdfçla";
        $sessao->expiration_date = "2020-08-03 12:08:00";
        $sessao->save();*/

        $id = $sessao->findById(18);
        $id->sessao = "minha sessao";
        if($id->save()){
            echo "<br>Salvo<br>";
            echo $id->fail()->getMessage();
        }
        else
            echo $id->fail()->getMessage();