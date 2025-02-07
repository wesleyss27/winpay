<?php

include "vendor/autoload.php";
use Source\App\Checkout1\AppCheckout2;

$consulta = (new AppCheckout2(null))->consultarPix(["nit" => "a5458b1da078748f024c690988ed040b38633de8e4bd140012f78e8b3ab0b1c8", "venda" => "452"]);

var_dump($consulta);
