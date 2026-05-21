<?php
/**
 * Gera HLS para todos os vídeos que ainda não foram processados.
 * Rode: php run-gerar-hls-todos.php
 * Processa um por um (evita sobrecarregar o servidor com vários FFmpeg em paralelo).
 */

require __DIR__ . '/vendor/autoload.php';

$pdo = \CoffeeCode\DataLayer\Connect::getInstance();

$stmt = $pdo->query(
    "SELECT id, titulo FROM winturb_videos
     WHERE status = 1
       AND (hls_status IS NULL OR hls_status IN ('none','failed'))
     ORDER BY id ASC"
);
$videos = $stmt->fetchAll(\PDO::FETCH_OBJ);

if (!$videos) {
    echo "Nenhum vídeo pendente de HLS.\n";
    exit(0);
}

$script = __DIR__ . '/source/Scripts/GerarHls.php';
$total  = count($videos);

echo "Iniciando geração de HLS para $total vídeo(s)...\n\n";

foreach ($videos as $i => $v) {
    $num = $i + 1;
    echo "[$num/$total] #{$v->id} — {$v->titulo} ... ";
    flush();

    $cmd    = sprintf('php %s %d 2>&1', escapeshellarg($script), (int)$v->id);
    $output = [];
    $code   = 0;
    exec($cmd, $output, $code);

    echo ($code === 0 ? "OK" : "FALHOU (código $code)") . "\n";
    if ($code !== 0) {
        echo "  " . implode("\n  ", array_slice($output, -5)) . "\n";
    }
}

echo "\nConcluído.\n";
