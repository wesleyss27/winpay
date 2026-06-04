<?php
/**
 * Corrige o moov atom de todos os vídeos existentes para faststart.
 * Rode UMA VEZ: php fix_faststart.php (ou acesse via browser se necessário)
 * Após concluir, delete este arquivo.
 */

$ffmpeg = trim((string) shell_exec('which ffmpeg 2>/dev/null'));
if (!$ffmpeg) {
    die("FFmpeg não encontrado neste servidor. Instale FFmpeg no container para usar este script.\n");
}

$videosDir = __DIR__ . '/uploads/videos/';
$iterator  = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($videosDir));
$total     = 0;
$fixed     = 0;
$skipped   = 0;

foreach ($iterator as $file) {
    if (!$file->isFile()) continue;
    $path = $file->getPathname();
    $ext  = strtolower($file->getExtension());

    // Só processa MP4 originais (ignora _480p já comprimidos)
    if ($ext !== 'mp4' || substr($path, -8) === '_480p.mp4') continue;
    $total++;

    // Verifica se já tem faststart: lê os primeiros 12 bytes e procura por 'moov' cedo
    $fp   = fopen($path, 'rb');
    $head = fread($fp, 512);
    fclose($fp);
    if (strpos($head, 'moov') !== false) {
        echo "  OK (já faststart): $path\n";
        $skipped++;
        continue;
    }

    $tmp = $path . '.fst.mp4';
    exec($ffmpeg . ' -i ' . escapeshellarg($path)
        . ' -c copy -movflags +faststart '
        . escapeshellarg($tmp) . ' 2>/dev/null');

    if (is_file($tmp) && filesize($tmp) > 0) {
        rename($tmp, $path);
        echo "  CORRIGIDO: $path\n";
        $fixed++;
    } else {
        @unlink($tmp);
        echo "  FALHOU: $path\n";
    }
}

echo "\nConcluído: {$total} vídeos encontrados, {$fixed} corrigidos, {$skipped} já estavam ok.\n";
echo "Delete este arquivo após executar.\n";
