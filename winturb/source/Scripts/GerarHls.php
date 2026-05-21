<?php
/**
 * CLI: php source/Scripts/GerarHls.php <video_id>
 * Gera segmentos HLS (.ts + .m3u8) para o vídeo informado.
 * Deve ser executado em background logo após o upload.
 */

define('WINTURB_ROOT', realpath(__DIR__ . '/../../'));

require WINTURB_ROOT . '/vendor/autoload.php';

use Source\Models\Videos\Video;

$videoId = (int)($argv[1] ?? 0);
if (!$videoId) {
    fwrite(STDERR, "Uso: php GerarHls.php <video_id>\n");
    exit(1);
}

$video = (new Video())->findById($videoId);
if (!$video) {
    fwrite(STDERR, "Vídeo #$videoId não encontrado.\n");
    exit(1);
}

$input = WINTURB_ROOT . '/uploads/videos/' . $video->arquivo;
if (!is_file($input)) {
    fwrite(STDERR, "Arquivo não encontrado: $input\n");
    $video->hls_status = 'failed';
    $video->save();
    exit(1);
}

$outDir = WINTURB_ROOT . '/uploads/hls/' . $video->embed_token . '/';
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

exec($cmd, $output, $code);

if ($code === 0 && is_file($playlist)) {
    $video->hls_status = 'ready';
    echo "HLS pronto: $playlist\n";
} else {
    $video->hls_status = 'failed';
    fwrite(STDERR, "FFmpeg falhou (código $code):\n" . implode("\n", $output) . "\n");
}

$video->save();
exit($code === 0 ? 0 : 1);
