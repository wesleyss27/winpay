<?php

use Source\Models\Produtos\Produto;

$produto = $produto ?? null;

if (!$produto instanceof Produto) {
    return;
}

$subtitle = $subtitle ?? "Gerencie todos os recursos deste produto em um só lugar.";

$status = [
    0 => "Rascunho",
    1 => "Em Análise",
    2 => "Aprovado",
    3 => "Reprovado",
    4 => "Bloqueado",
    5 => "Excluído",
    6 => "Inativado",
];

$statusColor = [
    0 => "#6b7280",
    1 => "#38bdf8",
    2 => "#22c55e",
    3 => "#f87171",
    4 => "#f87171",
    5 => "#f87171",
    6 => "#fbbf24",
];

$chips = [];
$chips[] = "Código: " . ($produto->codigo ?? "—");

if (!empty($produto->tipo_cobranca)) {
    $tipo = [
        1 => "Venda única",
        2 => "Assinatura",
        3 => "Recorrência",
        4 => "Físico",
    ];
    if (!empty($tipo[$produto->tipo_cobranca])) {
        $chips[] = $tipo[$produto->tipo_cobranca];
    }
}

$produtor = $produto->dataProdutor();
if ($produtor && !empty($produtor->nome)) {
    $chips[] = "Produtor: {$produtor->nome}";
}

$statusLabel = $status[$produto->status] ?? "—";
$statusBg = $statusColor[$produto->status] ?? "#4b5563";
?>

<div class="product-hero-slim">
    <div class="product-hero-slim__kicker">Produto</div>
    <div class="product-hero-slim__heading">
        <h1><?= $produto->nome; ?></h1>
        <?php if (!empty($subtitle)) : ?>
            <p><?= $subtitle; ?></p>
        <?php endif; ?>
    </div>
    <div class="product-hero-slim__meta">
        <?php foreach ($chips as $chip) : ?>
            <span class="product-hero-chip"><?= $chip; ?></span>
        <?php endforeach; ?>
        <span class="product-hero-status" style="background: <?= $statusBg; ?>;">
            <?= $statusLabel; ?>
        </span>
    </div>
</div>

<?= $v->insert("produtos/menu-produto", ["produto" => $produto]); ?>
