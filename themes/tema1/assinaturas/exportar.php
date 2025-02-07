<?php

header("Cache-Control: no-cache, must-revalidate");
header('Pragma: no-cache');
header('Content-Type: application/x-msexcel');
header("Content-Disposition: attachment; filename=\"extrato-assinaturas.xls\"");

session_start();
echo $_SESSION["extrato_assinaturas"];