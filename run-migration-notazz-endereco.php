<?php
// APAGUE ESTE ARQUIVO DEPOIS DE RODAR
// Acesse via: /run-migration-notazz-endereco.php

require __DIR__ . "/vendor/autoload.php";

$cfg = DATA_LAYER_CONFIG;
try {
    $pdo = new PDO(
        "{$cfg['driver']}:host={$cfg['host']};port={$cfg['port']};dbname={$cfg['dbname']};charset=utf8",
        $cfg['username'],
        $cfg['passwd']
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $sql   = file_get_contents(__DIR__ . "/database/migrations/20260507_notazz_endereco_padrao.sql");
    $stmts = array_filter(array_map('trim', explode(';', $sql)));
    $ok    = 0;
    foreach ($stmts as $s) {
        if ($s) { $pdo->exec($s); $ok++; }
    }
    echo "<pre style='color:green'>Migration OK — {$ok} statements executados.\n\nAPAGUE ESTE ARQUIVO AGORA: /run-migration-notazz-endereco.php</pre>";
} catch (Exception $e) {
    echo "<pre style='color:red'>ERRO: " . $e->getMessage() . "</pre>";
}
