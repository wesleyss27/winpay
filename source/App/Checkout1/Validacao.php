<?php

namespace Source\App\Checkout1;

class Validacao
{
    public function montante($parcelas, $taxa, $capital)
    {
        return (($taxa * ($parcelas - 1)) * $capital) + $capital;
    }

    // juros(parcela, taxa de juros, capital)
    function juros($n, $i, $capital){
        if($n == 1) {
            return $capital;
        } elseif($n == 2) {
            $valor = $capital + ($capital * 0.0519);
            $valor /= $n;
            return $valor;
        } else {
            $t = 1 + $i;
            $exp = pow($t, (-$n));
            $juros = ($i / (1 - $exp)) * $capital;
            return $juros;
        }
    }

    public function limpaStr($valor)
    {
        $valor = trim($valor);
        $valor = str_replace("+", "", $valor);
        $valor = str_replace(".", "", $valor);
        $valor = str_replace(",", "", $valor);
        $valor = str_replace("-", "", $valor);
        $valor = str_replace("/", "", $valor);
        $valor = str_replace("(", "", $valor);
        $valor = str_replace(")", "", $valor);
        $valor = str_replace(" ", "", $valor);
        return $valor;
    }

    public function validaCPF($cpf) {
 
        // Extrai somente os números
        $cpf = preg_replace( '/[^0-9]/is', '', $cpf );
         
        // Verifica se foi informado todos os digitos corretamente
        if (strlen($cpf) != 11) {
            return false;
        }
    
        // Verifica se foi informada uma sequência de digitos repetidos. Ex: 111.111.111-11
        if (preg_match('/(\d)\1{10}/', $cpf)) {
            return false;
        }
    
        // Faz o calculo para validar o CPF
        for ($t = 9; $t < 11; $t++) {
            for ($d = 0, $c = 0; $c < $t; $c++) {
                $d += $cpf[$c] * (($t + 1) - $c);
            }
            $d = ((10 * $d) % 11) % 10;
            if ($cpf[$c] != $d) {
                return false;
            }
        }
        return true;
    }

    public function validaTel($phone)
    {
        $regex = '/^(?:(?:\+|00)?(55)\s?)?(?:\(?([1-9][0-9])\)?\s?)?(?:((?:9\d|[2-9])\d{3})\-?(\d{4}))$/';

        if (preg_match($regex, $phone) == false) {

            // O número não foi validado.
            return false;
        } else {

            // Telefone válido.
            return true;
        }        
    }

    public function validaCep($cep)
    {
        if(!preg_match('/^[0-9]{5,5}([- ]?[0-9]{3,3})?$/', $cep)) {
            return false;
        }
        else {
            return true;
        }
    }

    public function verificaCep($cep)
    {
        if(strlen($cep) < 8){
            for($i = 0; strlen($cep) < 8; $i++){
                $cep .= 0;
            }
        } elseif(strlen($cep) > 8){
            $cep = substr($cep, 0, 8);
        }

        return $cep;
    }

    public function validaEmail($data)
    {
        // $email = filter_var(@$data["email"], FILTER_SANITIZE_EMAIL);

        if(filter_var(@$data["email"], FILTER_VALIDATE_EMAIL)){
            echo "Email válido";
        }
        else{
            echo "Email inválido";
        }
    }

    public function validarEmail($email)
    {
        // $email = filter_var(@$email, FILTER_SANITIZE_EMAIL);

        if(filter_var($email, FILTER_VALIDATE_EMAIL)){
            return true;
        }
        else{
            return false;
        }
    }

    public function formataValidade($mes, $ano)
    {
        /*if($mes < 10) {
            $mes = "0" . $mes;
        }*/
        $ano = substr($ano, -2);
        return $mes.$ano;
    }

    public function validaValidade($mes, $ano)
    {
        $data =  $mes + 1 . $ano;
        $hoje = date("mY");

        if((int) $data <= (int) $hoje){
            return false;
        }
        else {
            return $this->formataValidade($mes, $ano);
        }
    }

    public function enviaEmail($data)
    {
        //$to  = $data["email_cliente"]. ', '; // note the comma
        $to = @$data["email"];

        // subject
        $subject = $data["assunto"];

        // message
        $message = '
        <style type="text/css">
            *{
                font-family: "Segoe UI", Roboto, sans-serif;
                margin: 0;
            }
            body{
                background: #EEEEEE;
            }
            .center{
                text-align: center;
            }
            .topo{
                padding: 1em 0;
                background: #DC3545;
                color: #fff;
            }
            p{
                font-size: 18px;
            }
            main{
                padding: 1em 0;
            }
        </style>
        <html>
            <head>
                <title>'.$data["assunto"].'</title>
            </head>
            <body>
                <header class="topo center">
                    <h1>Octa Life</h1>
                </header>

                <main class="center">
                    <p>'. $data["mensagem"] .'</p>
                </main>
            </body>
        </html>
        ';

        // To send HTML mail, the Content-type header must be set
        $headers  = 'MIME-Version: 1.0' . "\r\n";
        $headers .= 'Content-type: text/html; charset=UtF-8' . "\r\n";

        // Additional headers
        $headers .= 'To: Octa Life <'.$to.'>' . "\r\n";
        $headers .= 'From: Octa Life <sac@octa.life>' . "\r\n";
        $headers .= 'Reply: Octa Life <sac@octa.life>' . "\r\n";

        // Mail it
        mail($to, $subject, $message, $headers);
    }
}