<?php

date_default_timezone_set("America/Sao_Paulo");

define("WINTURB_URL",  getenv("WINTURB_URL")  ?: "http://localhost:8190");
define("WINTURB_NAME", getenv("WINTURB_NAME") ?: "WinTurb");

define("DATA_LAYER_CONFIG", [
    "driver"   => "mysql",
    "host"     => getenv("DB_HOST")   ?: "mariadb-roxpay",
    "port"     => getenv("DB_PORT")   ?: "3306",
    "dbname"   => getenv("DB_NAME")   ?: "octapay",
    "username" => getenv("DB_USER")   ?: "root",
    "passwd"   => getenv("DB_PASS")   ?: "root",
    "options"  => [
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4",
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
        PDO::ATTR_CASE               => PDO::CASE_NATURAL,
    ],
]);

function url(string $uri = null): string
{
    return $uri ? WINTURB_URL . "/{$uri}" : WINTURB_URL;
}

function formataDataHora(string $data): string
{
    return date("d/m/Y H:i", strtotime($data));
}

/** Iniciais para avatar (até 2 letras). */
function wt_iniciais(string $nome): string
{
    $partes = preg_split('/\s+/', trim($nome), -1, PREG_SPLIT_NO_EMPTY);
    if (!$partes) return "?";
    $ini = mb_substr($partes[0], 0, 1);
    if (count($partes) > 1) $ini .= mb_substr(end($partes), 0, 1);
    return mb_strtoupper($ini);
}

/** Segundos → "m:ss" ou "h:mm:ss". */
function wt_duracao(int $s): string
{
    if ($s <= 0) return "—";
    $h = intdiv($s, 3600);
    $m = intdiv($s % 3600, 60);
    $sec = $s % 60;
    return $h > 0 ? sprintf("%d:%02d:%02d", $h, $m, $sec) : sprintf("%02d:%02d", $m, $sec);
}

/** Variação percentual entre dois períodos. Retorna null quando não há base de comparação. */
function wt_variacao(int $atual, int $anterior): ?float
{
    if ($anterior <= 0) return null;
    return round(($atual - $anterior) / $anterior * 100, 1);
}

/** Sparkline em SVG inline (área + linha). */
function wt_sparkline(array $valores, string $cor = "#5b4df0", int $w = 110, int $h = 40): string
{
    $valores = array_values(array_map('floatval', $valores));
    $n = count($valores);
    if ($n < 2) return "";
    $max = max($valores);
    $min = min($valores);
    $range = ($max - $min) ?: 1;
    $pad = 3;
    $pts = [];
    foreach ($valores as $i => $val) {
        $x = round($i / ($n - 1) * $w, 1);
        $y = round($h - $pad - (($val - $min) / $range) * ($h - $pad * 2), 1);
        $pts[] = "$x,$y";
    }
    $linha = "M" . implode(" L", $pts);
    $area  = $linha . " L{$w},{$h} L0,{$h} Z";
    static $seq = 0;
    $id = "sp" . (++$seq);
    return '<svg class="spark" viewBox="0 0 ' . $w . ' ' . $h . '" width="' . $w . '" height="' . $h . '" aria-hidden="true">'
        . '<defs><linearGradient id="' . $id . '" x1="0" y1="0" x2="0" y2="1">'
        . '<stop offset="0" stop-color="' . $cor . '" stop-opacity=".22"/><stop offset="1" stop-color="' . $cor . '" stop-opacity="0"/></linearGradient></defs>'
        . '<path d="' . $area . '" fill="url(#' . $id . ')"/>'
        . '<path d="' . $linha . '" fill="none" stroke="' . $cor . '" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>';
}
