<?php 

include "vendor/autoload.php";

use Source\Models\Usuarios\Usuario;

$usuarios = (new Usuario())->find("tipo_usuario = 4 OR tipo_usuario = 5")->fetch(true);

foreach ($usuarios as $usuario) {
    echo $usuario->email . ",";
}
