<?php
/**
 * CLI: php source/Scripts/GerarHls.php <video_id>
 *
 * Gera HLS multi-bitrate (ABR) para o vídeo informado.
 * Renditions gerados apenas quando <= resolução original:
 *   360p  ~600 kbps
 *   480p  ~1200 kbps
 *   720p  ~2500 kbps
 *   1080p ~5000 kbps
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
if (!is_dir($outDir)) mkdir($outDir, 0775, true);

$video->hls_status = 'processing';
$video->save();

// ── Detecta resolução original via ffprobe ────────────────────────────────
$probe = shell_exec(sprintf(
    'ffprobe -v error -select_streams v:0'
    . ' -show_entries stream=width,height'
    . ' -of csv=s=x:p=0 %s 2>/dev/null',
    escapeshellarg($input)
));
$probe = trim($probe ?? '');
[$srcW, $srcH] = array_map('intval', explode('x', $probe) + [0, 0]);
if (!$srcH) $srcH = 1080; // fallback

echo "Resolução original: {$srcW}x{$srcH}\n";

// ── Renditions disponíveis ────────────────────────────────────────────────
$renditions = [
    ['height' => 360,  'bitrate' => '600k',  'audio' => '96k',  'buf' => '1200k'],
    ['height' => 480,  'bitrate' => '1200k', 'audio' => '128k', 'buf' => '2400k'],
    ['height' => 720,  'bitrate' => '2500k', 'audio' => '128k', 'buf' => '5000k'],
    ['height' => 1080, 'bitrate' => '5000k', 'audio' => '192k', 'buf' => '10000k'],
];

// Só gera renditions que não excedem a resolução original
$renditions = array_filter($renditions, fn($r) => $r['height'] <= $srcH);
if (empty($renditions)) {
    // Vídeo muito pequeno — usa só a resolução original
    $renditions = [['height' => $srcH, 'bitrate' => '800k', 'audio' => '128k', 'buf' => '1600k']];
}

$masterLines = ["#EXTM3U", "#EXT-X-VERSION:3", ""];
$failed = false;

foreach ($renditions as $r) {
    $h       = $r['height'];
    $dir     = $outDir . $h . 'p/';
    $segPat  = $dir . 'seg_%04d.ts';
    $playlist = $dir . 'playlist.m3u8';

    if (!is_dir($dir)) mkdir($dir, 0775, true);

    // Calcula largura mantendo aspect ratio (múltiplo de 2)
    $w = ($srcH > 0) ? (int)(round($srcW * $h / $srcH / 2) * 2) : -2;
    $scale = ($w > 0) ? "{$w}:{$h}" : "-2:{$h}";

    $bwBps = (int)(str_replace('k', '', $r['bitrate'])) * 1000;

    $cmd = sprintf(
        'ffmpeg -i %s'
        . ' -vf scale=%s'
        . ' -c:v libx264 -preset veryfast -crf 23 -b:v %s -maxrate %s -bufsize %s'
        . ' -g 48 -sc_threshold 0 -keyint_min 48'
        . ' -c:a aac -b:a %s -ar 44100'
        . ' -hls_time 4'
        . ' -hls_playlist_type vod'
        . ' -hls_flags independent_segments'
        . ' -hls_segment_filename %s'
        . ' -y %s 2>&1',
        escapeshellarg($input),
        $scale,
        $r['bitrate'],
        $r['bitrate'],
        $r['buf'],
        $r['audio'],
        escapeshellarg($segPat),
        escapeshellarg($playlist)
    );

    echo "Gerando {$h}p…\n";
    exec($cmd, $cmdOut, $code);

    if ($code !== 0 || !is_file($playlist)) {
        fwrite(STDERR, "Falha em {$h}p (código $code):\n" . implode("\n", $cmdOut) . "\n");
        $failed = true;
        continue;
    }

    echo "  ✓ {$h}p pronto\n";

    $masterLines[] = "#EXT-X-STREAM-INF:BANDWIDTH={$bwBps},RESOLUTION={$scale},NAME=\"{$h}p\"";
    $masterLines[] = "{$h}p/playlist.m3u8";
}

if ($failed && count($masterLines) <= 3) {
    $video->hls_status = 'failed';
    $video->save();
    exit(1);
}

// ── Grava master.m3u8 ─────────────────────────────────────────────────────
$masterPath = $outDir . 'master.m3u8';
file_put_contents($masterPath, implode("\n", $masterLines) . "\n");

echo "Master playlist: $masterPath\n";

$video->hls_status = 'ready';
$video->save();
exit(0);
