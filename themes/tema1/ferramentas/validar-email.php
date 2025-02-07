<?php

function validarEmail($email)
{
    // $email = filter_var(@$email, FILTER_SANITIZE_EMAIL);

    if(filter_var($email, FILTER_VALIDATE_EMAIL)){
        return true;
    }
    else{
        return false;
    }
}

echo $_POST["email"] . "\n";
$validar = validarEmail($_POST["email"]);
if ($validar == true) {
    echo "Email válido";
} else {
    echo "Email inválido. Favor corrigir";
}