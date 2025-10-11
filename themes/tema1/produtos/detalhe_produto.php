<?php

$v->layout("_theme");

$status = array(
    0 => "Rascunho",
    1 => "Em Análise",
    2 => "Aprovado",
    3 => "Reprovado",
    4 => "Bloqueado",
    5 => "Excluido",
    6 => "Inativado",
);

$bg = array(
    0 => "bg-info",
    1 => "bg-info",
    2 => "bg-success",
    3 => "bg-danger",
    4 => "bg-danger",
    5 => "bg-danger",
    6 => "bg-warning",
);

$statusColor = array(
    0 => "#6b7280",
    1 => "#38bdf8",
    2 => "#22c55e",
    3 => "#f87171",
    4 => "#f87171",
    5 => "#f87171",
    6 => "#f87171",
    7 => "#f87171",
    8 => "#22c55e",
    9 => "#9ca3af"
);

$tipo_doc = array(
    1 => "CPF",
    2 => "CNPJ"
);

$simOuNao = array(
    "" => "NÃO",
    0 => "NÃO",
    1 => "SIM",
);

$buscaAfiliacao = $afiliacao->find("afiliado = '{$_SESSION["id"]}' AND produto = '" . @$produto->id . "'")->fetch();
$buscaAfiliacaoApro = $afiliacao->find("status = 2 AND afiliado = '{$_SESSION["id"]}' AND produto = '" . @$produto->id . "'")->fetch();
$BuscaCheckout = $checkout->find("produto = '" . @$produto->id . "'")->fetch(true);
//var_dump($buscaAfiliacaoApro);

include __DIR__ . "/tipo.php";

if ($_SESSION["tipo"] == 6) {
    header("Location: " . url("vendas"));
}

$produtorData = $produto ? $produto->dataProdutor() : null;
$produtorNome = $produtorData ? $produtorData->nome : '';
$produtorDocTipo = $produtorData ? ($tipo_doc[$produtorData->tipo_doc] ?? 'CPF') : 'CPF';
$produtorDocValor = $produtorData ? $produtorData->cpf_cnpj : '';
$produtorEmail = $produtorData ? $produtorData->email : '';
$produtorTelefone = $produtorData ? $produtorData->telefone : '';
$produtorEnderecoLinha1 = $produtorData ? "{$produtorData->rua}, {$produtorData->numero}, {$produtorData->bairro}" : '';
$produtorEnderecoLinha2 = $produtorData ? "{$produtorData->cidade} - {$produtorData->estado}" : '';
$produtorClienteSt4rt = $produtorData ? ($produtorData->cliente_st4rt ?? 0) : 0;
?>
<?= $v->start("links"); ?>
<link rel="stylesheet" href="<?= url("themes/tema1/dist/css/modal.css") ?>">
<style>
  .product-detail-page {
    background: #000;
    color: #f5f5f5;
    font-family: 'Inter', sans-serif;
    min-height: 100vh;
    padding-bottom: 4rem;
  }

  .product-header {
    border: none;
    padding: 2.5rem 1.5rem 1.5rem;
  }

  .product-hero {
    display: grid;
    gap: 1.25rem;
  }

  .product-hero-kicker {
    text-transform: uppercase;
    letter-spacing: 0.35rem;
    font-size: 0.75rem;
    color: rgba(255, 255, 255, 0.6);
  }

  .product-title {
    font-size: 2.4rem;
    font-weight: 700;
    margin: 0;
    color: #ffffff;
  }

  .product-subtitle {
    margin: 0.35rem 0 0;
    max-width: 680px;
    color: rgba(255, 255, 255, 0.72);
    line-height: 1.5;
  }

  .product-meta-line {
    display: flex;
    flex-wrap: wrap;
    gap: 0.6rem;
    align-items: center;
    margin-top: 0.75rem;
  }

  .product-meta-chip {
    background: rgba(255, 255, 255, 0.08);
    border: 1px solid rgba(255, 255, 255, 0.12);
    color: rgba(255, 255, 255, 0.78);
    padding: 0.35rem 0.75rem;
    border-radius: 999px;
    font-size: 0.75rem;
    letter-spacing: 0.05rem;
    text-transform: uppercase;
  }

  .product-status-pill {
    border-radius: 999px;
    padding: 0.35rem 0.8rem;
    font-size: 0.72rem;
    font-weight: 600;
    letter-spacing: 0.06rem;
    color: #111;
    text-transform: uppercase;
  }

  .product-rockets img {
    width: 22px;
    height: 22px;
  }

  .product-subnav {
    margin-top: 2rem;
    display: flex;
    flex-wrap: wrap;
    gap: 0.75rem;
  }

  .product-subnav a {
    background: rgba(255, 255, 255, 0.04);
    border: 1px solid rgba(255, 255, 255, 0.1);
    color: #f5f5f5;
    padding: 0.55rem 1rem;
    border-radius: 12px;
    text-transform: uppercase;
    letter-spacing: 0.08rem;
    font-size: 0.75rem;
    transition: background 0.25s ease, border 0.25s ease;
  }

  .product-subnav a:hover {
    background: rgba(217, 192, 0, 0.18);
    border-color: rgba(217, 192, 0, 0.5);
    color: #f4d03f;
    text-decoration: none;
  }

  .product-alert {
    margin-top: 1.5rem;
    background: rgba(217, 192, 0, 0.12);
    border: 1px solid rgba(217, 192, 0, 0.25);
    border-radius: 16px;
    padding: 1rem 1.25rem;
    color: #f4d03f;
    font-size: 0.9rem;
  }

  .product-main-card {
    margin-top: 2.5rem;
    background: rgba(17, 17, 20, 0.9);
    border: 1px solid rgba(255, 255, 255, 0.06);
    border-radius: 20px;
    padding: 2rem;
  }

  .product-main-grid {
    display: grid;
    grid-template-columns: minmax(240px, 300px) 1fr;
    gap: 2.2rem;
    align-items: start;
  }

  .product-media {
    display: grid;
    gap: 1.25rem;
  }

  .product-status-group {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
    align-items: center;
  }

  .product-control-button {
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid rgba(255, 255, 255, 0.12);
    color: #f5f5f5;
    border-radius: 10px;
    padding: 0.45rem 0.85rem;
    text-transform: uppercase;
    letter-spacing: 0.08rem;
    font-size: 0.7rem;
    cursor: pointer;
    transition: background 0.2s ease, border 0.2s ease;
  }

  .product-control-button:hover {
    background: rgba(217, 192, 0, 0.2);
    border-color: rgba(217, 192, 0, 0.5);
    color: #f4d03f;
  }

  .product-control-button.danger:hover {
    background: rgba(248, 113, 113, 0.18);
    border-color: rgba(248, 113, 113, 0.45);
    color: #fca5a5;
  }

  .product-control-button.warning {
    border-color: rgba(253, 224, 71, 0.45);
    color: #fde047;
  }

  .product-control-button.success {
    border-color: rgba(34, 197, 94, 0.45);
    color: #4ade80;
  }

  .product-media-image {
    border-radius: 18px;
    overflow: hidden;
    background: rgba(255, 255, 255, 0.04);
    border: 1px solid rgba(255, 255, 255, 0.08);
  }

  .product-media-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
  }

  .product-promote-actions {
    display: grid;
    gap: 0.75rem;
  }

  .product-promote-actions button {
    border: none;
    border-radius: 12px;
    padding: 0.75rem 1.1rem;
    text-transform: uppercase;
    letter-spacing: 0.08rem;
    font-weight: 600;
    cursor: pointer;
    transition: filter 0.25s ease;
    display: inline-flex;
    justify-content: center;
    align-items: center;
    gap: 0.4rem;
  }

  .product-primary-action {
    background: linear-gradient(135deg, #d9c000, #f4d03f);
    color: #111;
  }

  .product-primary-action:hover {
    filter: brightness(1.08);
  }

  .product-primary-action.is-disabled {
    cursor: default;
    opacity: 0.65;
  }

  .product-secondary-action {
    background: rgba(255, 255, 255, 0.05);
    color: #f5f5f5;
    border: 1px solid rgba(255, 255, 255, 0.12);
  }

  .product-secondary-action:hover {
    border-color: rgba(217, 192, 0, 0.45);
    color: #f4d03f;
  }

  .product-info {
    display: grid;
    gap: 1.5rem;
  }

  .product-price-card {
    background: rgba(217, 192, 0, 0.12);
    border: 1px solid rgba(217, 192, 0, 0.4);
    border-radius: 16px;
    padding: 1.1rem 1.35rem;
    display: grid;
    gap: 0.35rem;
  }

  .product-price-card span {
    text-transform: uppercase;
    font-size: 0.75rem;
    letter-spacing: 0.08rem;
    color: rgba(17, 17, 17, 0.85);
    font-weight: 600;
  }

  .product-price-card h2 {
    margin: 0;
    color: #111;
    font-size: 1.8rem;
    font-weight: 700;
  }

  .product-description {
    color: rgba(255, 255, 255, 0.78);
    line-height: 1.6;
    font-size: 0.95rem;
  }

  .product-info-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 1.2rem;
  }

  .product-info-item {
    background: rgba(255, 255, 255, 0.03);
    border: 1px solid rgba(255, 255, 255, 0.08);
    border-radius: 14px;
    padding: 1rem 1.15rem;
    display: grid;
    gap: 0.35rem;
  }

  .product-info-label {
    font-size: 0.75rem;
    text-transform: uppercase;
    letter-spacing: 0.08rem;
    color: rgba(255, 255, 255, 0.55);
  }

  .product-info-value {
    font-size: 1.05rem;
    font-weight: 600;
    color: #fdfdfd;
    word-break: break-word;
  }

  .product-info-links {
    display: grid;
    gap: 0.35rem;
  }

  .product-info-links a {
    color: #60a5fa;
    text-decoration: none;
    font-size: 0.9rem;
    word-break: break-all;
  }

  .product-info-links a:hover {
    color: #93c5fd;
  }

  .product-secondary-grid {
    margin-top: 2rem;
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
    gap: 1.5rem;
  }

  .product-panel {
    background: rgba(255, 255, 255, 0.04);
    border: 1px solid rgba(255, 255, 255, 0.08);
    border-radius: 16px;
    padding: 1.25rem;
    display: grid;
    gap: 0.75rem;
  }

  .product-panel h3 {
    margin: 0;
    font-size: 1rem;
    text-transform: uppercase;
    letter-spacing: 0.08rem;
    color: rgba(255, 255, 255, 0.8);
  }

  .product-textarea {
    width: 100%;
    min-height: 180px;
    background: rgba(0, 0, 0, 0.45);
    border: 1px solid rgba(255, 255, 255, 0.1);
    color: #f5f5f5;
    border-radius: 12px;
    padding: 0.75rem;
    resize: vertical;
    font-size: 0.9rem;
  }

  .product-textarea:focus {
    outline: none;
    border-color: rgba(217, 192, 0, 0.45);
  }

  .approval-panel {
    position: relative;
    padding: 1.5rem;
    background: linear-gradient(145deg, rgba(255, 255, 255, 0.04), rgba(255, 255, 255, 0.01));
    border: 1px solid rgba(255, 255, 255, 0.1);
    box-shadow: 0 12px 26px rgba(0, 0, 0, 0.35);
  }

  .product-tabs {
    margin-top: 2.5rem;
  }

  .product-tabs .nav-tabs {
    border: none;
    gap: 0.6rem;
    flex-wrap: wrap;
  }

  .product-tabs .nav-link {
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid rgba(255, 255, 255, 0.08);
    border-radius: 12px;
    color: rgba(255, 255, 255, 0.7);
    text-transform: uppercase;
    letter-spacing: 0.08rem;
    font-size: 0.75rem;
    padding: 0.6rem 1.2rem;
    transition: background 0.25s ease, color 0.25s ease, border 0.25s ease;
  }

  .product-tabs .nav-link.active {
    background: linear-gradient(135deg, #d9c000, #f4d03f);
    color: #111;
    border-color: transparent;
  }

  .product-tab-content {
    background: rgba(17, 17, 20, 0.82);
    border: 1px solid rgba(255, 255, 255, 0.06);
    border-radius: 16px;
    margin-top: 1.2rem;
    padding: 1.5rem;
  }

  .product-tab-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
    gap: 1.5rem;
  }

  .product-info-card {
    background: rgba(255, 255, 255, 0.02);
    border: 1px solid rgba(255, 255, 255, 0.08);
    border-radius: 14px;
    padding: 1.2rem;
    display: grid;
    gap: 0.75rem;
  }

  .product-info-title {
    margin: 0;
    font-size: 1rem;
    text-transform: uppercase;
    letter-spacing: 0.08rem;
    color: rgba(255, 255, 255, 0.75);
  }

  .product-info-list {
    display: grid;
    gap: 0.85rem;
    font-size: 0.9rem;
  }

  .product-info-value-block {
    display: block;
    margin-top: 0.15rem;
    color: #fdfdfd;
    word-break: break-word;
  }

  .product-info-note {
    background: rgba(255, 255, 255, 0.05);
    border-radius: 12px;
    padding: 0.75rem;
    font-size: 0.85rem;
    color: rgba(255, 255, 255, 0.78);
  }

  .product-info-card .form-control,
  .product-info-card .form-select,
  .product-info-card textarea {
    background: rgba(0, 0, 0, 0.45);
    border: 1px solid rgba(255, 255, 255, 0.12);
    color: #f5f5f5;
  }

  .product-info-card .form-control:focus,
  .product-info-card .form-select:focus,
  .product-info-card textarea:focus {
    border-color: rgba(217, 192, 0, 0.45);
    box-shadow: none;
  }

  .product-info-card .btn {
    border-radius: 10px;
    text-transform: uppercase;
    letter-spacing: 0.08rem;
  }

  .product-tabs .input-group {
    background: rgba(0, 0, 0, 0.45);
    border: 1px solid rgba(255, 255, 255, 0.12);
    border-radius: 12px;
    overflow: hidden;
  }

  .product-tabs .input-group input {
    background: transparent;
    border: none;
    color: #f5f5f5;
  }

  .product-tabs .input-group .btn {
    border: none;
    background: rgba(217, 192, 0, 0.18);
    color: #f4d03f;
  }

  .modal-content {
    background: rgba(12, 12, 15, 0.95);
    border: 1px solid rgba(255, 255, 255, 0.08);
    color: #f5f5f5;
    border-radius: 16px;
  }

  .modal-header,
  .modal-footer {
    border-color: rgba(255, 255, 255, 0.08);
    background: rgba(255, 255, 255, 0.02);
    color: inherit;
  }

  .modal-body {
    background: transparent;
    color: inherit;
  }

  .modal-content .form-control,
  .modal-content select,
  .modal-content textarea {
    background: rgba(0, 0, 0, 0.45);
    border: 1px solid rgba(255, 255, 255, 0.12);
    color: #f5f5f5;
    border-radius: 10px;
  }

  .modal-content .form-control:focus,
  .modal-content select:focus,
  .modal-content textarea:focus {
    border-color: rgba(217, 192, 0, 0.45);
    box-shadow: none;
  }

  .modal-content .btn-primary {
    background: linear-gradient(135deg, #d9c000, #f4d03f);
    border: none;
    color: #111;
  }

  .modal-content .btn-danger {
    background: linear-gradient(135deg, #ef4444, #f87171);
    border: none;
    color: #111;
  }

  .modal-content .btn-success {
    background: linear-gradient(135deg, #22c55e, #4ade80);
    border: none;
    color: #111;
  }

  .modal-content .btn-warning {
    background: linear-gradient(135deg, #fde047, #facc15);
    border: none;
    color: #111;
  }

  .product-tabs small,
  .product-tabs i {
    color: rgba(255, 255, 255, 0.6);
  }

  @media (max-width: 1024px) {
    .product-main-grid {
      grid-template-columns: 1fr;
    }

    .product-media-image {
      max-width: 420px;
    }
  }

  @media (max-width: 768px) {
    .product-header {
      padding: 2rem 1.25rem 1.25rem;
    }

    .product-title {
      font-size: 2rem;
    }

    .product-subnav {
      flex-direction: column;
      align-items: flex-start;
    }

    .product-main-card {
      padding: 1.5rem;
    }
  }

  @media (max-width: 540px) {
    .product-info-grid {
      grid-template-columns: 1fr;
    }

    .product-secondary-grid {
      grid-template-columns: 1fr;
    }
  }
</style>
<?= $v->end(); ?>

<div class="ajax_load">
    <div class="ajax_load_box">
        <div class="ajax_load_box_circle"></div>
        <div class="ajax_load_box_title">Aguarde, carregando!</div>
    </div>
</div>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper product-detail-page">
  <?php if ($produto) : ?>
    <section class="content-header product-header">
      <?= $section->getTipoSecaoLayout() ?>
      <div class="product-hero">
        <div>
          <span class="product-hero-kicker">Detalhes do produto</span>
          <h1 class="product-title"><?= $produto->nome; ?></h1>
          <div class="product-meta-line">
            <span class="product-meta-chip">Código: <?= $produto->codigo; ?></span>
            <?php if ($produtorData) : ?>
              <span class="product-meta-chip">Produtor: <?= $produtorData->nome; ?></span>
            <?php endif; ?>
            <span class="product-status-pill" style="background: <?= $statusColor[$produto->status] ?? '#4b5563'; ?>;"><?= $status[$produto->status]; ?></span>
          </div>
          <div class="product-meta-line product-rockets">
            <?php $rocketCount = round($produto->rockets); ?>
            <?php if ($rocketCount <= 0) : ?>
              <img src="<?= url('themes/tema1/images/foguete2.png'); ?>" alt="rocket" title="Rockets <?= $produto->rockets; ?>">
            <?php else : ?>
              <?php for ($i = 1; $i <= $rocketCount; $i++) : ?>
                <img src="<?= url('themes/tema1/images/foguete1.png'); ?>" alt="rocket" title="Rockets <?= $produto->rockets; ?>">
              <?php endfor; ?>
            <?php endif; ?>
          </div>
          <p class="product-subtitle">Visualize dados completos, materiais de divulgação e links prontos para promover.</p>
        </div>
      </div>

      <?php if (($buscaAfiliacaoApro || $produto->usuario == $section->getId()) && $produto->excluir == 0) : ?>
        <nav class="product-subnav">
          <a href="<?= url('produtos/detalhe/') . $produto->id; ?>">Detalhes</a>
          <?php if ($produto->usuario == $section->getId()) : ?>
            <?= $v->insert('produtos/menu-produto', ['produto' => $produto]); ?>
          <?php endif; ?>
          <?php if (!@$_SESSION['subusuario'] || $section->permissaoModuloBool('campanha_produto')) : ?>
            <a href="<?= url('produtos/campanhas/') . $produto->id; ?>">Campanhas</a>
          <?php endif; ?>
        </nav>
      <?php endif; ?>

      <?php if (@$_GET['convite']) : ?>
        <div class="product-alert">
          <strong>Parabéns!</strong> Você recebeu um convite de afiliação. Clique em <strong>Promover este produto</strong> e comece a faturar.
        </div>
      <?php endif; ?>
    </section>

    <section class="content product-content">
      <?= $v->insert('fragmentos/msg-cadastrar-editar', []); ?>

      <div class="product-main-card">
        <div class="product-main-grid">
          <div class="product-media">
            <?php if ($produto->usuario == $section->getId() || ($_SESSION['tipo'] != 4 && $_SESSION['tipo'] != 5)) : ?>
              <div class="product-status-group">
                <span class="product-status-pill" style="background: <?= $statusColor[$produto->status] ?? '#4b5563'; ?>;"><?= $status[$produto->status]; ?></span>
                <?php if ($produto->status != 6 && $produto->status == 2) : ?>
                  <button type="button" class="product-control-button warning" data-target="#modal-inativar" data-toggle="modal">Inativar</button>
                <?php elseif ($produto->status == 6) : ?>
                  <button type="button" class="product-control-button success" id="btn-ativar">Ativar</button>
                <?php endif ?>
                <?php if ($produto->excluir != 1 && (!@$_SESSION['subusuario'] || $permissao->excluir == 1)) : ?>
                  <button type="button" class="product-control-button danger" data-target="#modal-excluir" data-toggle="modal">Excluir</button>
                <?php endif ?>
              </div>
            <?php endif; ?>

            <div class="product-media-image">
              <?php if ($produto->foto != '' && $produto->shopify == 0) : ?>
                <img src="<?= url('uploads/produtos/' . $produto->foto); ?>" alt="<?= $produto->id; ?>">
              <?php elseif ($produto->foto != '' && $produto->shopify == 1) : ?>
                <img src="<?= $produto->foto; ?>" alt="<?= $produto->id; ?>">
              <?php else : ?>
                <img src="<?= url('uploads/produtos/default.jpg'); ?>" alt="<?= $produto->id; ?>">
              <?php endif; ?>
            </div>

            <?php if ($produto->usuario != $section->getId() && $produto->excluir == 0 && $produto->status == 2) : ?>
              <div class="product-promote-actions">
                <?php if ($produto->abrir_afiliacao == 1) : ?>
                  <?php if ($buscaAfiliacao == null) : ?>
                    <button type="button" class="product-primary-action" id="promover">Promover este produto</button>
                  <?php elseif ($buscaAfiliacaoApro) : ?>
                    <button type="button" class="product-primary-action is-disabled" disabled>Afiliação aprovada</button>
                  <?php else : ?>
                    <button type="button" class="product-primary-action is-disabled" disabled>Afiliação <?= $status[$buscaAfiliacao->status] ?></button>
                  <?php endif; ?>
                  <button type="button" class="product-secondary-action" id="denunciar" data-target="#modal-lg" data-toggle="modal">Denunciar produto</button>
                <?php endif; ?>
              </div>
            <?php endif; ?>
          </div>

          <div class="product-info">
            <?php if (@!$planos) : ?>
              <div class="product-price-card">
                <span>Preço</span>
                <h2>R$ <?= number_format($produto->preco, 2, ",", "."); ?></h2>
              </div>
            <?php endif; ?>

            <p class="product-description"><?= nl2br(mb_strimwidth($produto->descricao, 0, 500, "...")); ?></p>

            <?php
            $paginaVendas = [];
            if ($urls) {
                foreach ($urls as $url) {
                    if ($url->pv == 1) {
                        $paginaVendas[] = $url->url;
                    }
                }
            }
            ?>

            <div class="product-info-grid">
              <div class="product-info-item">
                <span class="product-info-label">Sua comissão</span>
                <span class="product-info-value"><?php
                    if ($buscaAfiliacao) {
                        if ($buscaAfiliacao->tipo_comissao == 0) {
                            echo number_format($buscaAfiliacao->comissao_afiliado, 2, ",", ".") . "%";
                        } else {
                            echo "R$ " . number_format($buscaAfiliacao->comissao_afiliado, 2, ",", ".");
                        }
                    } else {
                        if ($produto->tipo_comissao == 0) {
                            echo number_format($produto->comissao_afiliado, 2, ",", ".") . "%";
                        } else {
                            echo "R$ " . number_format($produto->comissao_afiliado, 2, ",", ".");
                        }
                    }
                ?></span>
              </div>

              <div class="product-info-item">
                <span class="product-info-label">Tempo de cookie</span>
                <span class="product-info-value"><?= $produto->tempo_cookie; ?> dias</span>
              </div>

              <?php if ($paginaVendas) : ?>
                <div class="product-info-item">
                  <span class="product-info-label">Página de vendas</span>
                  <div class="product-info-links">
                    <?php foreach ($paginaVendas as $link) : ?>
                      <a href="<?= $link ?>" target="_blank"><?= $link ?></a>
                    <?php endforeach; ?>
                  </div>
                </div>
              <?php endif; ?>

              <div class="product-info-item">
                <span class="product-info-label">Suporte</span>
                <div class="product-info-links">
                  <a href="mailto:<?= $produto->email_suporte; ?>"><?= $produto->email_suporte; ?></a>
                </div>
              </div>

              <div class="product-info-item">
                <span class="product-info-label">Tipo de produto</span>
                <span class="product-info-value"><?= $tipoProduto[$produto->tipo_produto]; ?></span>
              </div>

              <div class="product-info-item">
                <span class="product-info-label">Categoria</span>
                <span class="product-info-value"><?= $categoria[$produto->categoria]; ?></span>
              </div>

              <?php if ($produto->tipo_produto == 11) : ?>
                <div class="product-info-item">
                  <span class="product-info-label">Link de acesso membros</span>
                  <div class="product-info-links">
                    <a href="<?= @$produto->url_membros ?>" target="_blank"><?= @$produto->url_membros ?></a>
                  </div>
                </div>
              <?php endif; ?>
            </div>
          </div>
        </div>
      </div>

      <?php if ($produto->descricao_afiliados || $produto->termos) : ?>
        <div class="product-secondary-grid">
          <?php if ($produto->descricao_afiliados) : ?>
            <div class="product-panel">
              <h3>Descrição de afiliação</h3>
              <textarea class="product-textarea" readonly><?= $produto->descricao_afiliados; ?></textarea>
            </div>
          <?php endif; ?>
          <?php if ($produto->termos) : ?>
            <div class="product-panel">
              <h3>Termos de afiliação</h3>
              <textarea class="product-textarea" readonly><?= $produto->termos; ?></textarea>
            </div>
          <?php endif; ?>
        </div>
      <?php endif; ?>

      <?php if ($produto->excluir == 0 && (@$buscaAfiliacao->status == 2 || $_SESSION['id'] == $produto->usuario || $_SESSION['tipo'] == 1 || $_SESSION['tipo'] == 2 || $_SESSION['tipo'] == 3)) : ?>
        <?php $isMaster = ($_SESSION['tipo'] == 1 || $_SESSION['tipo'] == 2 || $_SESSION['tipo'] == 3); ?>
        <div class="product-tabs">
          <nav>
            <div class="nav nav-tabs" id="product-tab" role="tablist">
              <?php if ($isMaster) : ?>
                <a class="nav-item nav-link active" id="product-desc-tab" data-toggle="tab" href="#product-desc" role="tab" aria-controls="product-desc" aria-selected="true">Produtor</a>
                <a class="nav-item nav-link" id="product-comments-tab" data-toggle="tab" href="#product-comments" role="tab" aria-controls="product-comments" aria-selected="false">URLs</a>
              <?php else : ?>
                <a class="nav-item nav-link active" id="product-comments-tab" data-toggle="tab" href="#product-comments" role="tab" aria-controls="product-comments" aria-selected="true">URLs</a>
              <?php endif ?>
              <a class="nav-item nav-link" id="product-rating-tab" data-toggle="tab" href="#product-rating" role="tab" aria-controls="product-rating" aria-selected="false">Material de divulgação</a>
            </div>
          </nav>

          <div class="tab-content product-tab-content" id="nav-tabContent">
            <?php if ($isMaster) : ?>
              <div class="tab-pane fade show active" id="product-desc" role="tabpanel" aria-labelledby="product-desc-tab">
                <div class="product-tab-grid">
                  <div class="product-info-card">
                    <h5 class="product-info-title">Dados pessoais</h5>
                    <div class="product-info-list">
                      <div>
                        <span class="product-info-label">Nome</span>
                        <span class="product-info-value-block"><?= $produtorNome ?: '-' ?></span>
                      </div>
                      <div>
                        <span class="product-info-label"><?= $produtorDocTipo; ?></span>
                        <span class="product-info-value-block"><?= $produtorDocValor ?: '-' ?></span>
                      </div>
                      <div>
                        <span class="product-info-label">Email</span>
                        <span class="product-info-value-block"><?= $produtorEmail ?: '-' ?></span>
                      </div>
                      <div>
                        <span class="product-info-label">Telefone</span>
                        <?php if ($produtorTelefone) : ?>
                          <a href="https://api.whatsapp.com/send?phone=55<?= $produtorTelefone; ?>" class="product-info-value-block" target="_blank"><i class="fab fa-whatsapp"></i> <?= $produtorTelefone ?></a>
                        <?php else : ?>
                          <span class="product-info-value-block">-</span>
                        <?php endif; ?>
                      </div>
                      <div>
                        <span class="product-info-label">Endereço</span>
                        <span class="product-info-value-block"><?= $produtorEnderecoLinha1 ?: '-' ?></span>
                        <span class="product-info-value-block"><?= $produtorEnderecoLinha2 ?></span>
                      </div>
                    </div>
                    <?php if ($produto->observacao != "") : ?>
                      <div class="product-info-note"><?= $produto->observacao ?></div>
                    <?php endif; ?>
                  </div>

                  <?php if ($produto->excluir != 1) : ?>
                    <div class="product-info-card approval-panel">
                      <h5 class="product-info-title">Atualizar status</h5>
                      <form action="<?= $router->route('produtos.editarStatus') ?>" method="post">
                        <input type="hidden" name="id" value="<?= $produto->id; ?>">
                        <div class="mb-3">
                          <label for="observacao" class="product-info-label">Adicionar observação (opcional)</label>
                          <textarea name="observacao" class="form-control" rows="4"></textarea>
                        </div>
                        <div class="row">
                          <div class="col-md-6 mb-3">
                            <label for="status" class="product-info-label">Status</label>
                            <select name="status" class="form-control">
                              <option value="">Selecione</option>
                              <option value="2" <?= $produto->status == 2 ? 'selected' : '' ?>>Aprovado</option>
                              <option value="3" <?= $produto->status == 3 ? 'selected' : '' ?>>Reprovado</option>
                              <option value="4" <?= $produto->status == 4 ? 'selected' : '' ?>>Bloqueado</option>
                            </select>
                          </div>
                          <div class="col-md-6 mb-3">
                            <label for="st4rt" class="product-info-label">Integrar com Logística St4rt?</label>
                            <select name="st4rt" class="form-control">
                              <option value="0" <?= $produto->st4rt == 0 ? 'selected' : '' ?>>Não</option>
                              <option value="1" <?= $produto->st4rt == 1 ? 'selected' : '' ?>>Sim</option>
                            </select>
                          </div>
                        </div>
                        <div class="d-grid">
                          <button class="btn btn-danger">Atualizar</button>
                        </div>
                      </form>
                    </div>
                  <?php endif; ?>

                  <div class="product-info-card">
                    <h5 class="product-info-title">Informações do produto</h5>
                    <div class="product-info-list">
                      <div>
                        <span class="product-info-label">Cliente St4rt</span>
                        <span class="product-info-value-block"><?= $simOuNao[$produtorClienteSt4rt]; ?></span>
                      </div>
                      <div>
                        <span class="product-info-label">Tipo de produto</span>
                        <span class="product-info-value-block"><?= $tipoProduto[$produto->tipo_produto]; ?></span>
                      </div>
                      <?php if ($produto->tipo_produto == 8) : ?>
                        <div>
                          <span class="product-info-label">Link de rastreio</span>
                          <a href="<?= @$produto->link_rastreio ?>" target="_blank" class="product-info-value-block"><?= @$produto->link_rastreio ?></a>
                        </div>
                      <?php endif; ?>
                      <?php if ($produto->tipo_produto == 11) : ?>
                        <div>
                          <span class="product-info-label">Link de acesso membros</span>
                          <a href="<?= @$produto->url_membros ?>" target="_blank" class="product-info-value-block"><?= @$produto->url_membros ?></a>
                        </div>
                      <?php endif; ?>
                      <?php if ($produto->documentos) : ?>
                        <div>
                          <span class="product-info-label">Documento técnico</span>
                          <a href="<?= url('uploads/documentos/') . $produto->documentos ?>" target="_blank" class="product-info-value-block"><?= $produto->documentos ?></a>
                        </div>
                      <?php endif; ?>
                      <div>
                        <span class="product-info-label">Visualização no marketplace</span>
                        <span class="product-info-value-block"><?= $simOuNao[$produto->visualizacao]; ?></span>
                      </div>
                      <?php if ($produto->obrigado_boleto) : ?>
                        <div>
                          <span class="product-info-label">Página de obrigado boleto</span>
                          <a href="<?= @$produto->obrigado_boleto ?>" target="_blank" class="product-info-value-block"><?= @$produto->obrigado_boleto ?></a>
                        </div>
                      <?php endif; ?>
                      <?php if ($produto->obrigado_cartao) : ?>
                        <div>
                          <span class="product-info-label">Página de obrigado cartão</span>
                          <a href="<?= @$produto->obrigado_cartao ?>" target="_blank" class="product-info-value-block"><?= @$produto->obrigado_cartao ?></a>
                        </div>
                      <?php endif; ?>
                      <?php if ($produto->obrigado_analise) : ?>
                        <div>
                          <span class="product-info-label">Página de obrigado análise</span>
                          <a href="<?= @$produto->obrigado_analise ?>" target="_blank" class="product-info-value-block"><?= @$produto->obrigado_analise ?></a>
                        </div>
                      <?php endif; ?>
                    </div>
                  </div>
                </div>
              </div>
            <?php endif; ?>

            <div class="tab-pane fade<?= $isMaster ? '' : ' show active' ?>" id="product-comments" role="tabpanel" aria-labelledby="product-comments-tab">
              <div class="product-tab-grid">
                <div class="product-info-card">
                  <h5 class="product-info-title">Links cadastrados</h5>
                  <div class="product-info-list">
                    <?php if ($urls) : ?>
                      <?php foreach ($urls as $url) : ?>
                        <?php if ($url->privada == 0) : ?>
                          <div>
                            <span class="product-info-label"><?= $url->descricao ?></span>
                            <div class="input-group">
                              <?php if ($buscaAfiliacao) : ?>
                                <input type="text" class="form-control" value="<?= url('a/' . $produto->id . '/' . $buscaAfiliacao->codigo . '/?pg=' . $url->id . '&tp=5') ?>">
                                <div class="input-group-append">
                                  <a href="<?= url('a/' . $produto->id . '/' . $buscaAfiliacao->codigo . '/?pg=' . $url->id . '&tp=5') ?>" target="_blank" class="btn btn-sm btn-primary"><i class="fas fa-external-link-alt"></i></a>
                                </div>
                              <?php else : ?>
                                <input type="text" class="form-control" value="<?= $url->url ?>">
                                <div class="input-group-append">
                                  <a href="<?= $url->url ?>" target="_blank" class="btn btn-sm btn-primary"><i class="fas fa-external-link-alt"></i></a>
                                </div>
                              <?php endif; ?>
                            </div>
                          </div>
                        <?php endif; ?>
                      <?php endforeach; ?>
                    <?php else : ?>
                      <span class="product-info-value-block">Nenhum link cadastrado.</span>
                    <?php endif; ?>
                  </div>
                </div>

                <div class="product-info-card">
                  <h5 class="product-info-title">URLs de checkout</h5>
                  <div class="product-info-list">
                    <?php if ($BuscaCheckout) : ?>
                      <?php foreach ($BuscaCheckout as $checkout) : ?>
                        <?php if ($checkout->privado == 0 && $checkout->adquirente == 'redrocket' && $checkout->adquirente_pix == 'redrocket' && $checkout->adquirente_boleto == 'redrocket') : ?>
                          <div>
                            <span class="product-info-label"><?= $checkout->descricao ?></span>
                            <div class="input-group">
                              <?php if ($buscaAfiliacao) : ?>
                                <input type="text" class="form-control" value="<?= url('a/' . $produto->id . '/' . $buscaAfiliacao->codigo . '/?ch=' . $checkout->codigo . '&tp=5') ?>">
                                <div class="input-group-append">
                                  <a href="<?= url('a/' . $produto->id . '/' . $buscaAfiliacao->codigo . '/?ch=' . $checkout->codigo . '&tp=5') ?>" target="_blank" class="btn btn-sm btn-primary"><i class="fas fa-external-link-alt"></i></a>
                                </div>
                              <?php else : ?>
                                <input type="text" class="form-control" value="<?= url('checkout/' . $checkout->codigo) ?>">
                                <div class="input-group-append">
                                  <a href="<?= url('checkout/' . $checkout->codigo) ?>" target="_blank" class="btn btn-sm btn-primary"><i class="fas fa-external-link-alt"></i></a>
                                </div>
                              <?php endif; ?>
                            </div>
                          </div>
                        <?php endif; ?>
                      <?php endforeach; ?>
                    <?php else : ?>
                      <span class="product-info-value-block">Nenhum checkout cadastrado.</span>
                    <?php endif; ?>
                  </div>
                </div>
              </div>
            </div>

            <div class="tab-pane fade" id="product-rating" role="tabpanel" aria-labelledby="product-rating-tab">
              <div class="product-info-card">
                <h5 class="product-info-title">Material de divulgação</h5>
                <?php if ($produto->url_midias) : ?>
                  <p>Para acessar as mídias deste produto utilize o link abaixo:</p>
                  <a href="<?= $produto->url_midias ?>" target="_blank" class="product-info-value-block"><?= $produto->url_midias ?></a>
                <?php else : ?>
                  <p>Nenhum material de divulgação cadastrado.</p>
                <?php endif; ?>
                <?php if ($produto->usuario == $_SESSION['id']) : ?>
                  <small><i class="fas fa-question-circle"></i> Para editar o link de mídias, vá à seção editar no menu superior.</small>
                <?php endif; ?>
              </div>
            </div>
          </div>
        </div>
      <?php endif; ?>
    </section>
  <?php else : ?>
    <h4>Produto Não encontrado</h4>
  <?php endif; ?>
</div>
<!-- FIM Content Wrapper -->


<!-- DENUNCIA PRODUTO -->
<div class="modal fade" id="modal-lg">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title"><i class="fa fa-info-circle"></i> Denunciar Produto</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="" method="post" class="product-modal-form">
                    <input type="hidden" name="usuario" id="usuario" value="<?= $_SESSION["id"] ?>">
                    <input type="hidden" name="produto" id="produto" value="<?= $produto->id ?>">

                    <label for="assunto" class="product-info-label">Assunto</label>
                    <select name="assunto" id="assunto" class="form-control" required>
                        <option value="">Selecionar...</option>
                        <option value="1">Produto enganoso</option>
                        <option value="2">Outro</option>
                    </select>

                    <label for="denuncia" class="product-info-label mt-3">Denúncia</label>
                    <textarea name="denuncia" id="denuncia" class="form-control" rows="4" placeholder="Descreva o ocorrido"></textarea>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-dismiss="modal" id="cancelar1">Cancelar</button>
                        <button class="btn btn-primary" id="salvar1">Salvar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- FIM DENUNCIA PRODUTO -->

<!-- EXCLUIR PRODUTO -->
<div class="modal fade" id="modal-excluir">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title"><i class="fa fa-info-circle"></i> Tem certeza que deseja excluir esse produto?</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                Todos afiliados serão notificados e esta operação não poderá ser revertida.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success" data-dismiss="modal">Cancelar</button>
                <button class="btn btn-danger" id="btn-excluir">Excluir</button>
            </div>
        </div>
    </div>
</div>
<!-- FIM EXCLUIR PRODUTO -->

<!-- INATIVAR PRODUTO -->
<div class="modal fade" id="modal-inativar">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title"><i class="fa fa-info-circle"></i> Tem certeza que deseja inativar esse produto?</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                Todos afiliados serão notificados e o checkout ficará indisponível para visualização.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success" data-dismiss="modal">Cancelar</button>
                <button class="btn btn-warning" id="btn-inativar">Inativar</button>
            </div>
        </div>
    </div>
</div>
<!-- FIM INATIVAR PRODUTO -->

<?= $v->start("scripts") ?>
<script>
    function load(action, tempo = 1000) {
        var load_div = $(".ajax_load");
        if (action === "open") {
            load_div.fadeIn().css("display", "flex");
            setTimeout(() => {
                load_div.fadeOut();
            }, tempo);
        } else {
            load_div.fadeOut();
        }
    }

    $("#promover").click((e) => {

        var produto = "<?= @$produto->id ?>";
        var produtor = "<?= @$produto->usuario ?>";
        var afiliado = "<?= @$_SESSION["id"] ?>";
        var liberar_comprador = "<?= @$produto->libera_comprador ?>";
        var tempo_cookie = "<?= @$produto->tempo_cookie ?>";
        var tipo_comissao = "<?= @$produto->tipo_comissao ?>";
        var comissao_afiliado = "<?= @$produto->comissao_afiliado ?>";
        var tipo_atribuicao = "<?= @$produto->tipo_atribuicao ?>";
        var afiliados_aprovacao = "<?= @$produto->afiliados_aprovacao ?>";
        var produto_nome = "<?= @$produto->nome ?>";

        $.ajax({
            url: "<?= $router->route("appafiliados.afiliacao") ?>",
            data: {
                produto: produto,
                produtor: produtor,
                afiliado: afiliado,
                liberar_comprador: liberar_comprador,
                tempo_cookie: tempo_cookie,
                tipo_comissao: tipo_comissao,
                comissao_afiliado: comissao_afiliado,
                tipo_atribuicao: tipo_atribuicao,
                afiliados_aprovacao: afiliados_aprovacao,
                produto_nome: produto_nome
            },

            type: "POST",
            beforeSend: function() {
                console.log("atualizando");
                load("open", 3000);
            },
            error: function() {
                console.log("O servidor não conseguiu processar o pedido");
            },
            success: function(callback) {
                console.log(callback)
                load("close");
                alert(callback);
                location.reload();
            }
        })
    });

    $("#salvar1").click((e) => {
        e.preventDefault();

        if ($("#assunto").val() == "" || $("#denuncia").val() == "") {
            alert("Preencha todos os campos");
        } else {
            var form = $("form");
            $.ajax({
                url: "<?= $router->route("denuncias.cadastrar") ?>",
                data: form.serialize(),
                type: "POST",
                beforeSend: function() {
                    console.log("atualizando")
                },
                error: function() {
                    console.log("O servidor não conseguiu processar o pedido");
                },
                success: function(callback) {
                    console.log(callback)
                    alert(callback);
                    location.reload();
                }
            })
        }
    })

    var click = 1;
    $("#observacao").click(() => {
        if (click == 1)
            $("#observacao").html("Tentativas: \nOperador: \nMotivo: \nÚltimo motivo: ");

        click++;
    })

    $("#btn-ativar").click((e) => {
        e.preventDefault();
        load("open");
        var dados = {
            "produto": "<?= $produto->id ?>"
        };
        enviaDados("<?= $router->route("produtos.ativar") ?>", dados, 1);
    });

    $("#btn-inativar").click((e) => {
        e.preventDefault();
        load("open");
        var dados = {
            "produto": "<?= $produto->id ?>"
        };
        enviaDados("<?= $router->route("produtos.inativar") ?>", dados, 1);
    });

    $("#btn-excluir").click((e) => {
        e.preventDefault();
        load("open", 5000);
        var dados = {
            "produto": "<?= $produto->id ?>"
        };
        enviaDados("<?= $router->route("produtos.excluir") ?>", dados);
    });

    function enviaDados(url, dados, redirecionar = 0) {
        $.ajax({
            url: url, //"",
            data: dados,
            type: "POST",
            beforeSend: function() {
                console.log("atualizando")
            },
            error: function() {
                console.log("O servidor não conseguiu processar o pedido");
            },
            success: function(callback) {
                console.log(callback)
                alert(callback);
                if (redirecionar == 0)
                    location.href = "<?= url("produtos") ?>";
                else
                    location.reload();
            }
        })
    }
</script>
<?= $v->end(); ?>
