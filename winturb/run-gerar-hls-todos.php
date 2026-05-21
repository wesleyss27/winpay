<?php
/**
 * Gera HLS para todos os vídeos que ainda não foram processados.
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
echo "Iniciando geração de HLS para $total vídeo(s)...\n\n";

foreach ($ids as $i => $videoId) {
    $num   = $i + 1;
    $video = (new Video())->findById($videoId);

    if (!$video) {
        echo "[$num/$total] #{$videoId} — não encontrado, pulando.\n";
        continue;
    }

    echo "[$num/$total] #{$video->id} — {$video->titulo} ... ";
    flush();

    $input = __DIR__ . '/uploads/videos/' . $video->arquivo;
    if (!is_file($input)) {
        echo "ARQUIVO NÃO ENCONTRADO\n";
        $video->hls_status = 'failed';
        $video->save();
        continue;
    }

    $outDir = __DIR__ . '/uploads/hls/' . $video->embed_token . '/';
    if (!is_dir($outDir)) {
        mkdir($outDir, 0775, true);
    }

    $video->hls_status = 'processing';
    $video->save();

    $playlist = $outDir . 'master.m3u8';
    $segPat   = $outDir . 'seg_%04d.ts';

    $cmd = sprintf(
        'ffmpeg -i %s'
        . ' -c:v libx264 -preset veryfast -crf 23 -g 48 -sc_threshold 0'
        . ' -c:a aac -b:a 128k'
        . ' -hls_time 4'
        . ' -hls_playlist_type vod'
        . ' -hls_flags independent_segments'
        . ' -hls_segment_filename %s'
        . ' -y %s 2>&1',
        escapeshellarg($input),
        escapeshellarg($segPat),
        escapeshellarg($playlist)
    );

    $output = [];
    $code   = 0;
    exec($cmd, $output, $code);

    if ($code === 0 && is_file($playlist)) {
        $video->hls_status = 'ready';
        echo "OK\n";
    } else {
        $video->hls_status = 'failed';
        echo "FALHOU (código $code)\n";
        $tail = array_slice($output, -5);
        foreach ($tail as $line) {
            echo "  $line\n";
        }
    }
    $video->save();
}

echo "\nConcluído.\n";
