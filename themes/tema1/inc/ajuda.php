<?php
$assunto = "Preciso de Ajuda";
$para = "suporte.oficial@redrocketpay.com";
$mensagem = "Email: {$_SESSION["nome"]} %0D" .
  "ID: {$_SESSION["id"]} %0D" .
  "Página: {$_SERVER["REQUEST_URI"]} %0D%0D" .
  "Olá, sou " . $_SESSION["nome"] . " e gostaria de ajuda. Estou com o seguinte problema: ";

$mensagem_whats = "Email: {$_SESSION["nome"]} %0D" .
  "ID: {$_SESSION["id"]} %0D" .
  "Página: {$_SERVER["REQUEST_URI"]} %0D%0D" .
  "Olá, sou " . $_SESSION["nome"] . " e gostaria de ajuda.";
