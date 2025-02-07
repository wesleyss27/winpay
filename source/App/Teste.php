<?php

namespace Source\App;

use Source\Models\Pagamento\Cartao;

class Teste
{
    public function teste()
    {
        $dados = $_POST;
        echo "
        <form action='teste' method='post'>
        <input type='text' value='pedro' name='nome'>
        <input type='email' value='pedro@octa.life' name='email'>
        <input type='text' value='cd0658b132aa192f1a718612c494c1da' name='chave_unica'>
        <input type='submit' value='Enviar'>
        </form>
        ";
    }
    public function teste2()
    {
        $dados = $_POST;
 
        $numero             = $dados['numero'];
        $titular            = $dados['titular'];
        $validade           = $dados['validade'];
        $cod_seguranca      = $dados['cod_seguranca'];
        $bandeira           = $dados['bandeira'];

        /*var_dump($dados);
        
        $json = json_encode($dados); 
        var_dump($json);

        $json = json_decode($json); 
        var_dump($json);*/

        $cartao = new Cartao();
        $cartao->numero = $numero;
        $cartao->titular = $titular;
        $cartao->validade = $validade;
        $cartao->cod_seguranca = $cod_seguranca;
        $cartao->bandeira = $bandeira;
        $cartao->save();

    }
}