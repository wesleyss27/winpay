<?php
/**
 * Gera HLS multi-bitrate para todos os vídeos pendentes.
 * Rode: php run-gerar-hls-todos.php
 */

require __DIR__ . '/vendor/autoload.php';

use Source\Models\Videos\Video;

$pdo = \CoffeeCode\DataLayer\Connect::getInstance();

$stmt = $pdo->query(
    "SELECT id FROM winturb_videos
     WHERE status = 1
       AND (hls_status IS NULL OR hls_status IN ('none','failed'))
     ORDER BY id ASC"
);
$ids = $stmt->fetchAll(\PDO::FETCH_COLUMN);

if (!$ids) {
    echo "Nenhum vídeo pendente de HLS.\n";
    exit(0);
}

$total = count($ids);
echo "Iniciando geração de HLS multi-bitrate para $total vídeo(s)...\n\n";

$script = __DIR__ . '/source/Scripts/GerarHls.php';

foreach ($ids as $i => $videoId) {
    $num = $i + 1;
    echo "[$num/$total] Vídeo #{$videoId}...\n";
    flush();

    $cmd = sprintf('php %s %d', escapeshellarg($script), (int)$videoId);
    passthru($cmd, $code);

    echo $code === 0 ? "  → Concluído\n\n" : "  → FALHOU (código $code)\n\n";
}

echo "Concluído.\n";
