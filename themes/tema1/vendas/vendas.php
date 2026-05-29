<?php

use Source\App\Checkout1\PagarMe;
use Source\Models\Afiliados\Afiliacao;

 $v->layout("_theme"); ?>

<?= $v->start("links"); ?>
<link rel="stylesheet" href="<?= url("themes/tema1/dist/css/modal.css") ?>">
<style>
  .sales-page {
    background: #000;
    color: #f5f5f5;
    font-family: 'Inter', sans-serif;
    min-height: 100vh;
    padding-bottom: 4rem;
  }

  .sales-header {
    border: none;
    padding: 2.5rem 1.5rem 1.5rem;
  }

  .sales-header-intro {
    display: flex;
    flex-wrap: wrap;
    justify-content: space-between;
    align-items: flex-end;
    gap: 1.5rem;
  }

  .sales-title {
    margin: 0;
    font-size: 2.2rem;
    font-weight: 700;
    color: #ffffff;
  }

  .sales-subtitle {
    margin: 0.35rem 0 0;
    color: rgba(255, 255, 255, 0.68);
    max-width: 620px;
    line-height: 1.5;
  }

  .sales-filters {
    margin-top: 2rem;
    background: rgba(17, 17, 20, 0.78);
    border: 1px solid rgba(217, 192, 0, 0.18);
    border-radius: 18px;
    padding: 1.75rem;
    display: grid;
    gap: 1.5rem;
  }

  .sales-filters-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
    gap: 1.25rem;
  }

  .filter-field label {
    display: block;
    font-size: 0.75rem;
    text-transform: uppercase;
    letter-spacing: 0.08rem;
    color: rgba(255, 255, 255, 0.55);
    margin-bottom: 0.4rem;
  }

  .filter-field input,
  .filter-field select {
    width: 100%;
    background: rgba(255, 255, 255, 0.06);
    border: 1px solid rgba(255, 255, 255, 0.12);
    color: #f5f5f5;
    border-radius: 12px;
    padding: 0.65rem 0.85rem;
    font-size: 0.95rem;
    appearance: none;
    -webkit-appearance: none;
    -moz-appearance: none;
  }

  .filter-field input::placeholder {
    color: rgba(255, 255, 255, 0.42);
  }

  .filter-field select {
    padding-right: 2.1rem;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='8' viewBox='0 0 12 8'%3E%3Cpath fill='%23d9c000' d='M6 8 0 0h12z'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 0.85rem center;
    background-size: 12px 8px;
    color: #f5f5f5;
  }

  .filter-field select option {
    background: #0f0f12;
    color: #f5f5f5;
  }

  .filter-actions {
    display: flex;
    flex-wrap: wrap;
    gap: 0.75rem;
    align-items: center;
  }

  .filter-button {
    border: none;
    border-radius: 12px;
    padding: 0.75rem 1.4rem;
    text-transform: uppercase;
    font-weight: 600;
    letter-spacing: 0.08rem;
    background: linear-gradient(135deg, #d9c000, #f4d03f);
    color: #111;
    cursor: pointer;
    transition: filter 0.25s ease;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
  }

  .filter-button:hover {
    filter: brightness(1.08);
  }

  .filter-advanced-toggle {
    background: transparent;
    border: 1px solid rgba(217, 192, 0, 0.38);
    color: #f5f5f5;
  }

  .filter-advanced-toggle:hover {
    background: rgba(217, 192, 0, 0.14);
  }

  .sales-advanced {
    display: none;
    margin-top: 1.25rem;
    background: rgba(10, 10, 12, 0.88);
    border: 1px solid rgba(255, 255, 255, 0.08);
    border-radius: 18px;
    padding: 1.5rem;
  }

  .sales-advanced h5 {
    margin: 0 0 1rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.08rem;
    font-size: 0.85rem;
  }

  .sales-stats {
    margin: 2.5rem 0 1.75rem;
    display: grid;
    gap: 1.5rem;
  }

  .sales-stat-row {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1.25rem;
  }

  .sales-stat-card {
    background: linear-gradient(135deg, rgba(17, 17, 20, 0.95), rgba(6, 6, 8, 0.9));
    border: 1px solid rgba(255, 255, 255, 0.06);
    border-radius: 18px;
    padding: 1.35rem;
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
  }

  .sales-stat-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 18px 32px rgba(0, 0, 0, 0.35);
  }

  .sales-stat-label {
    font-size: 0.78rem;
    text-transform: uppercase;
    letter-spacing: 0.08rem;
    color: rgba(255, 255, 255, 0.55);
  }

  .sales-stat-value {
    font-size: 2rem;
    font-weight: 700;
    color: #fdfdfd;
  }

  .sales-stat-value.highlight {
    color: #d9c000;
  }

  .sales-payment-card {
    flex-direction: row;
    align-items: center;
    gap: 1rem;
  }

  .sales-payment-icon {
    width: 52px;
    height: 52px;
    border-radius: 16px;
    display: grid;
    place-items: center;
    background: linear-gradient(135deg, #d9c000, #f4d03f);
    color: #111;
    font-size: 1.35rem;
    flex-shrink: 0;
  }

  .sales-payment-info {
    display: grid;
    gap: 0.35rem;
  }

  .sales-payment-percentage {
    display: inline-block;
    padding: 0.25rem 0.7rem;
    border-radius: 999px;
    background: rgba(217, 192, 0, 0.18);
    color: #f4d03f;
    font-size: 0.7rem;
    font-weight: 600;
    letter-spacing: 0.06rem;
    text-transform: uppercase;
  }

  .sales-payment-count {
    font-size: 1.6rem;
    font-weight: 700;
  }

  .sales-legend {
    display: flex;
    flex-wrap: wrap;
    justify-content: space-between;
    align-items: center;
    gap: 1.5rem;
    background: rgba(17, 17, 20, 0.88);
    border: 1px solid rgba(255, 255, 255, 0.05);
    border-radius: 16px;
    padding: 1rem 1.5rem;
    margin-bottom: 1.5rem;
  }

  .sales-legend-list {
    display: flex;
    flex-wrap: wrap;
    gap: 1rem;
    align-items: center;
  }

  .sales-legend-item {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.78rem;
    color: rgba(255, 255, 255, 0.7);
  }

  .sales-legend-dot {
    width: 14px;
    height: 14px;
    border-radius: 50%;
    display: inline-block;
  }

  .sales-export a {
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    background: rgba(217, 192, 0, 0.18);
    color: #f4d03f;
    padding: 0.45rem 0.85rem;
    border-radius: 10px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.06rem;
    text-decoration: none;
  }

  .sales-info-alert {
    margin-bottom: 1.5rem;
    padding: 1.1rem 1.35rem;
    border-radius: 14px;
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid rgba(217, 192, 0, 0.15);
    display: flex;
    align-items: flex-start;
    gap: 1rem;
  }

  .sales-info-icon {
    color: #f4d03f;
    font-size: 1.2rem;
    margin-top: 0.15rem;
  }

  .sales-info-text {
    font-size: 0.9rem;
    color: rgba(255, 255, 255, 0.72);
  }

  .sales-table-card {
    background: rgba(12, 12, 15, 0.92);
    border-radius: 18px;
    border: 1px solid rgba(255, 255, 255, 0.06);
    overflow: hidden;
  }

  .sales-table-scroll {
    overflow-x: auto;
  }

  .sales-table-scroll::-webkit-scrollbar {
    height: 6px;
  }

  .sales-table-scroll::-webkit-scrollbar-thumb {
    background: rgba(255, 255, 255, 0.12);
    border-radius: 999px;
  }

  .sales-table-card table {
    width: 100%;
    border-collapse: collapse;
    font-size: 0.9rem;
    color: #e5e7eb;
  }

  .sales-table-card thead {
    background: rgba(0, 0, 0, 0.85);
  }

  .sales-table-card th {
    padding: 0.9rem 1rem;
    text-transform: uppercase;
    letter-spacing: 0.08rem;
    font-size: 0.72rem;
    color: rgba(255, 255, 255, 0.65);
    text-align: left;
  }

  .sales-table-card tbody tr {
    border-top: 1px solid rgba(255, 255, 255, 0.05);
    transition: background 0.25s ease;
  }

  .sales-table-card tbody tr:hover {
    background: rgba(255, 255, 255, 0.04);
  }

  .sales-table-card td {
    padding: 0.85rem 1rem;
    vertical-align: middle;
    color: rgba(255, 255, 255, 0.82);
  }

  .badge-modern {
    background: rgba(217, 192, 0, 0.2);
    color: #f4d03f;
    border: 1px solid rgba(217, 192, 0, 0.4);
    padding: 0.25rem 0.6rem;
    border-radius: 999px;
    font-size: 0.68rem;
    font-weight: 600;
    letter-spacing: 0.05rem;
    text-transform: uppercase;
  }

  .sale-id-link {
    color: #f4d03f;
    text-decoration: none;
    font-weight: 600;
  }

  .sale-id-link:hover {
    text-decoration: underline;
  }

  .sales-table-card .whatsapp-link {
    color: #25d366;
  }

  .btn-details-modern {
    background: linear-gradient(135deg, #d9c000, #f4d03f);
    color: #111;
    border: none;
    padding: 0.55rem 1rem;
    border-radius: 12px;
    font-size: 0.8rem;
    font-weight: 600;
    letter-spacing: 0.06rem;
    text-transform: uppercase;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    transition: transform 0.2s ease, filter 0.2s ease;
  }

  .btn-details-modern:hover {
    transform: translateY(-2px);
    filter: brightness(1.05);
  }

  .status-icon {
    font-size: 1rem;
    margin-right: 0.3rem;
    opacity: 0.85;
  }

  .sales-table-card .product-link {
    color: #60a5fa;
  }

  @media (max-width: 1024px) {
    .sales-header {
      padding: 2rem 1.25rem 1.25rem;
    }

    .sales-stat-row {
      grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    }
  }

  @media (max-width: 768px) {
    .sales-header-intro {
      flex-direction: column;
      align-items: flex-start;
    }

    .sales-title {
      font-size: 1.9rem;
    }

    .sales-filters {
      padding: 1.5rem;
    }
  }

  @media (max-width: 540px) {
    .sales-filters-grid {
      grid-template-columns: repeat(auto-fit, minmax(170px, 1fr));
    }

    .filter-actions {
      flex-direction: column;
      align-items: stretch;
    }

    .filter-button {
      width: 100%;
      justify-content: center;
    }

    .sales-legend {
      flex-direction: column;
      align-items: flex-start;
    }
  }
</style>
<?= $v->end(); ?>

<?php
$sim = array(
  1 => "SIM",
  0 => "NÃO",
  "" => "NÃO"
);

$simCor = array(
  1 => "bg-danger",
  0 => "bg-success",
  "" => "bg-success"
);

$status_transacao = array(
  "" => "ERRO NA TRANSAÇÃO",
  1 => "AGUARDANDO PAGAMENTO",
  2 => "EM ANÁLISE",
  3 => "PRÉ AUTORIZADA",
  4 => "PAGA",
  5 => "CANCELADA",
  6 => "DEVOLVIDA",
  7 => "CHARGEBACK",
  8 => "CONCLUÍDA",
  9 => "BLOQUEADA",
  10 => "ANTIFRAUDE",
);

$statusT = array(
  "" => "text-danger",
  0 => "text-warning",
  1 => "text-warning",
  2 => "text-info",
  3 => "text-info",
  4 => "text-success",
  5 => "text-danger",
  6 => "text-danger",
  7 => "text-danger",
  8 => "text-success",
  9 => "txt-block"
);

$status_bg = array(
  1 => "bg-warning",
  2 => "bg-info",
  3 => "bg-info",
  4 => "bg-success",
  5 => "bg-danger",
  6 => "bg-danger",
  7 => "bg-danger",
  8 => "bg-success",
  9 => "bg-danger"
);

$nivel = array(
  "" => "Iniciante",
  1 => "INICIANTE",
  2 => "ESPECIALISTA",
  3 => "ELITE",
  4 => "MESTRE",
  5 => "GRÃO MESTRE",
  6 => "GURU",
  7 => "Lendário"
);

if ($section->getTipo() == 4) {
  $produto = $produtos->find("excluir = 0 AND status != 6 AND status != 0 AND usuario = " . $section->getId())->order("nome ASC")->fetch(true);
  $produtor = $afiliacao->find("excluir = 0 AND status != 6 AND status != 0 AND afiliado = " . $section->getId())->order("nome ASC")->fetch(true);
} elseif ($section->getTipo() == 5) {
  $produto = $afiliacao->find("excluir = 0 AND status != 6 AND status != 0 AND afiliado = " . $section->getId())->order("nome ASC")->fetch(true);
} else {
  $produto = $produtos->find("excluir = 0 AND status != 6 AND status != 0")->order("nome ASC")->fetch(true);
}

function dadosComprador($venda)
{
  echo "
  <b>{$venda->dataComprador()->nome} - CPF/CNPJ: </b> {$venda->dataComprador()->cpf_cnpj}<br>
  <b>Tel: </b> <a href='https://api.whatsapp.com/send?phone=55{$venda->dataComprador()->telefone}' title='Entrar em contato pelo whatsapp' target='_blank'>{$venda->dataComprador()->telefone} <i class='fab fa-whatsapp' style='color: green'></i></a> - 
  <b>email: </b><a href='mailto:{$venda->dataComprador()->email}'>{$venda->dataComprador()->email}</a><br>

  {$venda->dataComprador()->rua_entrega}, nº {$venda->dataComprador()->numero_entrega} - {$venda->dataComprador()->complemento_entrega}<br>
  {$venda->dataComprador()->bairro_entrega}, {$venda->dataComprador()->cidade_entrega}, {$venda->dataComprador()->estado_entrega}<br>
  CEP: {$venda->dataComprador()->cep_entrega}";
}

function dadosEndereco($venda)
{
  echo "
  <b>{$venda->dataEndereco()->nome} - CPF/CNPJ: </b> {$venda->dataEndereco()->cpf_cnpj}<br>
  <b>Tel: </b> <a href='https://api.whatsapp.com/send?phone=55{$venda->dataEndereco()->telefone}' title='Entrar em contato pelo whatsapp' target='_blank'>{$venda->dataEndereco()->telefone} <i class='fab fa-whatsapp' style='color: green'></i></a> - 
  <b>email: </b><a href='mailto:{$venda->dataEndereco()->email}'>{$venda->dataEndereco()->email}</a><br>

  {$venda->dataEndereco()->rua}, nº {$venda->dataEndereco()->numero} - {$venda->dataEndereco()->complemento}<br>
  {$venda->dataEndereco()->bairro}, {$venda->dataEndereco()->cidade}, {$venda->dataEndereco()->estado}<br>
  CEP: {$venda->dataEndereco()->cep}";
}

?>

<div class="ajax_load">
  <div class="ajax_load_box">
    <div class="ajax_load_box_circle"></div>
    <div class="ajax_load_box_title">Aguarde, carregando!</div>
  </div>
</div>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper sales-page">
  <section class="content-header sales-header">

    <?= $section->getTipoSecaoLayout() ?>

    <div class="sales-header-intro">
      <div>
        <h1 class="sales-title"><?= $title ?></h1>
        <p class="sales-subtitle">Acompanhe suas vendas com filtros rapidos, metricas objetivas e uma leitura mais limpa do desempenho da sua operacao.</p>
      </div>
    </div>

    <form action="#" class="sales-filters">
      <div class="sales-filters-grid">
        <div class="filter-field">
          <label for="inicio">Data da venda (inicio)</label>
          <input type="date" name="inicio" id="inicio" value="<?= @$_GET["inicio"]; ?>">
        </div>

        <div class="filter-field">
          <label for="fim">Data da venda (fim)</label>
          <input type="date" name="fim" id="fim" value="<?= @$_GET["fim"]; ?>">
        </div>

        <div class="filter-field">
          <label for="inicio_finalizada">Data finalizada (inicio)</label>
          <input type="date" name="inicio_finalizada" id="inicio_finalizada" value="<?= @$_GET["inicio_finalizada"]; ?>">
        </div>

        <div class="filter-field">
          <label for="fim_finalizada">Data finalizada (fim)</label>
          <input type="date" name="fim_finalizada" id="fim_finalizada" value="<?= @$_GET["fim_finalizada"]; ?>">
        </div>

        <div class="filter-field">
          <label for="pagamento">Forma de pagamento</label>
          <select name="pagamento" id="pagamento">
            <option value="">Pagamento...</option>
            <option value="1" <?php if (@$_GET["pagamento"] == 1) echo "selected"; ?>>Cartao de credito</option>
            <option value="2" <?php if (@$_GET["pagamento"] == 2) echo "selected"; ?>>Boleto</option>
            <option value="3" <?php if (@$_GET["pagamento"] == 3) echo "selected"; ?>>PIX</option>
          </select>
        </div>

        <div class="filter-field">
          <label for="venda">Codigo da venda ou pedido</label>
          <input type="text" name="venda" id="venda" placeholder="ex.: PAY-078VRHCE52BI ou 239" value="<?= @$_GET["venda"]; ?>">
        </div>

        <div class="filter-field">
          <label for="status">Status da transacao</label>
          <select name="status" id="status">
            <option value="">Status...</option>
            <?php foreach ($status_transacao as $cod => $status) : ?>
              <option value="<?= $cod ?>" <?php if (@$_GET["status"] == $cod) echo "selected"; ?>><?= $status ?></option>
            <?php endforeach; ?>
          </select>
        </div>

        <?php if ($section->getTipo() != 6) : ?>
          <div class="filter-field">
            <label for="produto">Produto</label>
            <select name="produto" id="produto">
              <option value="">Produto...</option>
              <?php foreach ($produto as $produto) : ?>
                <option value="<?= $produto->id ?>" <?= (@$_GET["produto"] == $produto->id) ? "selected" : "" ?>><?= $produto->nome ?></option>
              <?php endforeach; ?>
              <?php if (@$produtor) : foreach ($produtor as $produtor) : ?>
                  <option value="<?= $produtor->dataProduto()->id ?>" <?= (@$_GET["produto"] == $produtor->dataProduto()->id) ? "selected" : "" ?>><?= $produtor->dataProduto()->nome ?></option>
              <?php endforeach; endif; ?>
            </select>
          </div>

          <div class="filter-field">
            <label for="comprador">Comprador</label>
            <input type="text" name="comprador" id="comprador" placeholder="Nome, CPF/CNPJ ou e-mail" value="<?= @$_GET["comprador"]; ?>">
          </div>
        <?php endif; ?>
      </div>

      <div class="filter-actions">
        <button type="submit" class="filter-button"><i class="fas fa-search"></i> Filtrar</button>
        <?php if ($section->getTipo() != 6) : ?>
          <button type="button" class="filter-button filter-advanced-toggle" id="btn-filtro"><i class="fas fa-sliders-h"></i> Mais filtros</button>
        <?php endif; ?>
      </div>

      <?php if ($section->getTipo() != 6) : ?>
        <div id="busca-avancada" class="sales-advanced">
          <h5>Busca avancada</h5>
          <div class="sales-filters-grid">
            <div class="filter-field">
              <label for="vencimento_boleto">Data vencimento boleto</label>
              <input type="date" name="vencimento_boleto" id="vencimento_boleto">
            </div>
            <div class="filter-field">
              <label for="planos">Planos</label>
              <select name="planos" id="planos">
                <option value="">Selecionar...</option>
                <?php if ($planos) : ?>
                  <?php foreach ($planos as $plano_produto) : ?>
                    <?php if ($plano_produto->dataProduto()->excluir != 1) : ?>
                      <option value="<?= $plano_produto->id ?>"><?= $plano_produto->nome . " - " . $plano_produto->dataProduto()->nome ?></option>
                    <?php endif ?>
                  <?php endforeach ?>
                <?php endif ?>
              </select>
            </div>
            <div class="filter-field">
              <label for="campanha">Campanha</label>
              <select name="campanha" id="campanha">
                <option value="">Selecionar...</option>
                <?php if ($campanhas) : ?>
                  <?php foreach ($campanhas as $campanha_produto) : ?>
                    <?php if ($campanha_produto->dataProduto()->excluir != 1) : ?>
                      <option value="<?= $campanha_produto->id ?>"><?= $campanha_produto->nome . " - " . $campanha_produto->dataProduto()->nome ?></option>
                    <?php endif ?>
                  <?php endforeach ?>
                <?php endif ?>
              </select>
            </div>
            <div class="filter-field">
              <label for="afiliado">Afiliado</label>
              <select name="afiliado" id="afiliado">
                <option value="">Selecionar...</option>
                <?php if ($afiliados) : ?>
                  <?php foreach ($afiliados as $afiliado_produto) : ?>
                    <option value="<?= $afiliado_produto->afiliado ?>"><?= $afiliado_produto->dataAfiliado()->nome ?></option>
                  <?php endforeach ?>
                <?php endif ?>
              </select>
            </div>
          </div>

          <div class="sales-filters-grid">
            <div class="filter-field">
              <label for="upsell">Upsell</label>
              <select name="upsell" id="upsell">
                <option value="">Selecionar...</option>
                <option value="IS NOT NULL">Sim</option>
                <option value="IS NULL">Nao</option>
              </select>
            </div>
            <div class="filter-field">
              <label for="order_bump">Order bump</label>
              <select name="order_bump" id="order_bump">
                <option value="">Selecionar...</option>
                <option value="IS NOT NULL">Sim</option>
                <option value="IS NULL">Nao</option>
              </select>
            </div>
            <div class="filter-field">
              <label for="cupom">Cupom de desconto</label>
              <input type="text" name="cupom" id="cupom">
            </div>
            <div class="filter-field">
              <label for="frete">Tipo de frete</label>
              <select name="frete" id="frete">
                <option value="">Selecionar...</option>
                <option value="fixo">Frete fixo</option>
                <option value="gratis">Frete gratis</option>
                <option value="sedex">Correios - Sedex</option>
                <option value="pac">Correios - Pac</option>
                <option value="jadlog">Frete Jadlog</option>
                <option value="regiao">Frete por regiao</option>
              </select>
            </div>
          </div>

          <div class="sales-filters-grid">
            <div class="filter-field">
              <label for="assumir_juros">Assumir juros</label>
              <select name="assumir_juros" id="assumir_juros">
                <option value="">Selecionar...</option>
                <option value="IS NOT NULL">Sim</option>
                <option value="IS NULL">Nao</option>
              </select>
            </div>
            <div class="filter-field">
              <label for="parcelas">Parcelas do cartao</label>
              <input type="number" name="parcelas" id="parcelas">
            </div>
            <div class="filter-field">
              <label for="src">SRC, UTMs...</label>
              <input type="text" name="src" id="src">
            </div>
          </div>
        </div>
      <?php endif; ?>
    </form>
  </section>

  <section class="content">

    <!-- Default box -->
    <?php if ($vendas) : ?>
      <?php if ($section->getTipo() != 6) : ?>
        <div class="sales-stats">
          <div class="sales-stat-row">
            <div class="sales-stat-card">
              <span class="sales-stat-label">Total de vendas</span>
              <span class="sales-stat-value"><?= $count; ?></span>
            </div>

            <div class="sales-stat-card">
              <span class="sales-stat-label">Sua categoria</span>
              <span class="sales-stat-value highlight"><?php echo $nivel[@$nivelUsuario]; ?></span>
            </div>

            <div class="sales-stat-card sales-payment-card">
              <div class="sales-payment-icon"><i class="fas fa-credit-card"></i></div>
              <div class="sales-payment-info">
                <span class="sales-payment-percentage"><?= number_format($vendasCartao / $count * 100, 2, ",", "."); ?>%</span>
                <span class="sales-payment-count"><?= $vendasCartao ?></span>
              </div>
            </div>

            <div class="sales-stat-card sales-payment-card">
              <div class="sales-payment-icon"><i class="fas fa-barcode"></i></div>
              <div class="sales-payment-info">
                <span class="sales-payment-percentage"><?= number_format($vendasBoleto / $count * 100, 2, ",", "."); ?>%</span>
                <span class="sales-payment-count"><?= $vendasBoleto ?></span>
              </div>
            </div>

            <div class="sales-stat-card sales-payment-card">
              <div class="sales-payment-icon"><i class="fas fa-qrcode"></i></div>
              <div class="sales-payment-info">
                <span class="sales-payment-percentage"><?= number_format($vendasPix / $count * 100, 2, ",", "."); ?>%</span>
                <span class="sales-payment-count"><?= $vendasPix ?></span>
              </div>
            </div>
          </div>

          <div class="sales-stat-row">
            <div class="sales-stat-card">
              <span class="sales-stat-label">Valor vendas</span>
              <span class="sales-stat-value highlight"><?= @$somaVendas ?></span>
            </div>

            <div class="sales-stat-card">
              <span class="sales-stat-label">Ticket medio</span>
              <span class="sales-stat-value highlight"><?= @$somaVendas ? "R$ " . number_format($somaVendas / $count, 2, ",", ".") : "-"; ?></span>
            </div>

            <div class="sales-stat-card">
              <span class="sales-stat-label">Valor comissao</span>
              <span class="sales-stat-value highlight">R$ <?= @number_format(@$somaProdutor->soma_produtor, 2, ",", ".") ?></span>
            </div>

            <div class="sales-stat-card">
              <span class="sales-stat-label">Ticket medio comissao</span>
              <span class="sales-stat-value highlight"><?= @$somaProdutor->soma_produtor ? "R$ " . number_format($somaProdutor->soma_produtor / $count, 2, ",", ".") : "-"; ?></span>
            </div>
          </div>
        </div>
      <?php endif; ?>

      <div class="sales-legend">
        <div class="sales-legend-list">
          <span class="sales-legend-item"><span class="sales-legend-dot" style="background:#facc15;"></span>Aguardando pagamento</span>
          <span class="sales-legend-item"><span class="sales-legend-dot" style="background:#38bdf8;"></span>Em analise</span>
          <span class="sales-legend-item"><span class="sales-legend-dot" style="background:#4ade80;"></span>Paga / completa</span>
          <span class="sales-legend-item"><span class="sales-legend-dot" style="background:#f87171;"></span>Cancelada / devolvida / estornada</span>
          <span class="sales-legend-item"><span class="sales-legend-dot" style="background:#6b7280;"></span>Bloqueada</span>
        </div>
        <div class="sales-export">
          <span>Exportar</span>
          <a href="<?= url("vendas/exportar") ?>" target="_blank"><i class="fas fa-file-export"></i> XLS</a>
        </div>
      </div>

      <div class="sales-info-alert">
        <span class="sales-info-icon"><i class="fas fa-info-circle"></i></span>
        <div class="sales-info-text">
          <?php if (!$pag_coprodutor) : ?>
            <strong>Atencao:</strong> novas vendas levam alguns minutos para aparecerem neste relatorio apos serem realizadas no checkout. Vendas via PIX podem demorar alguns minutos para aprovacao.
          <?php else : ?>
            <strong>Valores atualizados a cada hora.</strong>
          <?php endif; ?>
        </div>
      </div>

      <div class="sales-table-card">
        <div class="sales-table-scroll">

        <table class="sales-table">
            <thead>
              <tr>
                <th scope="col">Venda</th>
                <th scope="col">Comprador</th>
                <?php if ($section->getTipo() == 1 || $section->getTipo() == 2 || $section->getTipo() == 3) : ?>
                  <th scope="col">Produtor</th>
                <?php endif; ?>
                <th scope="col">Produto</th>
                <th scope="col">Afiliado</th>
                <th scope="col">R$</th>
                <th scope="col">Status</th>
                <th scope="col">Data Transação</th>
                <th scope="col">Data Finalizada</th>
                <th scope="col">Detalhes</th>
              </tr>
            </thead>

            <tbody>
              <?php
              $forma_pagamento = array(
                1 => "CARTÃO DE CRÉDITO",
                2 => "BOLETO",
                3 => "PIX"
              );

              $i = 1;
              $index = 1;
              $analitico = array();
              foreach ($vendas as $venda) :
      
                $status_transacaoP = array(
                  "pending" => 1,
                  "paid" => 4,
                  "canceled" => 5,
                  "failed" => 5,
                );

                if( $venda->forma_pagamento == 3 && ( 1 == $venda->status_transacao && 4 != $venda->status_transacao ) ) {
                  $data_vencimento = new DateTime($venda->created_at);
                  $data_vencimento->add(new DateInterval('PT5H'));
                  $data_atual = new DateTime();
  
          
                  if ( $data_atual > $data_vencimento ) {

                    $venda->status_transacao = $status_transacaoP[ 'canceled' ];
                    $venda->motivo = 'Pagamento expirado';
                    $venda->data_ultimo_evento  = $data_vencimento->format('Y-m-d H:i:s');
                    $venda->save();

                  };

                }
        

                if( ( ( $venda->forma_pagamento == 2  )  && ! empty( $venda->data_vencimento  ) ) && ( 5 != $venda->status_transacao && 4 != $venda->status_transacao) ) {
                  $data_vencimento = new DateTime($venda->data_vencimento);
                  $data_atual = new DateTime();

                  if ( $data_atual > $data_vencimento ) {
           
                    $venda->status_transacao = $status_transacaoP[ 'canceled' ];
                    $venda->motivo = 'Pagamento expirado';
                    $venda->data_ultimo_evento  = $data_vencimento->format('Y-m-d');
                    $venda->save();

                  };

                }
        
                        
                if ( "true" === $_GET['update-status'] ) {

                  $pagar_me = new PagarMe();
                  $order_status = $pagar_me->getOrderStatus( $venda->transacao );
  
                  if ( ! empty( $order_status->status ) ) {
  
                    $venda->status_transacao = $status_transacaoP[ $order_status->status ];
  
                    $venda->data_finalizada = strtotime( $order_status->closed_at );
                    $venda->data_finalizada = date( "Y-m-d H:i:s", $venda->data_finalizada );
   
                    $venda->save();

                  }

                }

                /**
                 * BUSCA INFORMAÇÕES DA AFILIAÇÃO, PARA VERIFICAR SE PODE VER DADOS DO COMPRADOR
                 */
                $afl = new Afiliacao();
                $af1 = $afl->find("produto = $venda->produto AND afiliado = " . $section->getId())->fetch();

                if (@$coprodutor)
                  $coproducao = @$coprodutor->find("co_produtor = " . $section->getId() . " and produto = $venda->produto")->fetch();

                if (@$coproducao) {
                  if (@$coproducao->gerente == 0) {
                    if ($coproducao->tipo_comissao == 0) {
                      $valorComissao = $venda->valor_liquido * ($coproducao->comissao / 100);
                    } else {
                      $valorComissao = $coproducao->comissao;
                    }
                  } else {
                    if ($valor->afiliado) {
                      if ($coproducao->tipo_comissao_afiliados == 0) {
                        $valorComissao = $venda->valor_liquido * ($coproducao->comissao_afiliados / 100);
                      } else {
                        $valorComissao = $coproducao->comissao_afiliados;
                      }
                    } else {
                      if ($coproducao->tipo_comissao == 0) {
                        $valorComissao = $venda->valor_liquido * ($coproducao->comissao / 100);
                      } else {
                        $valorComissao = $coproducao->comissao;
                      }
                    }
                  }
                } else {
                  $valorComissao = @$venda->valor_liquido + @$venda->valor_redrocket + @$venda->valor_frete;
                }

                if ($pag_coprodutor) {
                  $valor = @$comissoes->find("venda = $venda->id AND usuario = " . $section->getId())->fetch();

                  $valorComissao = $valor ? $valor->valor : 0;

                  $com = explode("/", $venda->coprodutores);
                  $comVal = explode("/", $venda->comissao_coprodutores);

                  $i = 0;
                  if ($com) {
                    foreach ($com as $com) {
                      if ($com == $section->getId() && @$comVal[$i] > 0) {
                        $valorComissao = @$comVal[$i];
                      }

                      $i++;
                    }
                  }
                }

              ?>

                <tr>
                  <td>
                  <div style="margin-bottom: 8px;">
                      <?php if ($venda->upsell) : ?>
                        <span class="badge-modern bg-azul-dark">Upsell</span>
                      <?php endif; ?>

                      <?php if (@$venda->planos_order_bump && @$venda->preco_order_bump) : ?>
                        <span class="badge-modern bg-verde-octa2">Order Bump</span>
                      <?php endif; ?>

                      <?php if ($venda->assinatura) : ?>
                        <span class="badge-modern bg-verde-octa3">Assinatura</span>
                      <?php endif; ?>

                      <?php if ($venda->adquirente != "redrocket") : ?>
                        <span class="badge-modern btn-finalizar-tarde">Drop</span>
                      <?php endif; ?>

                      <?php if ($venda->ecommerce == 1) : ?>
                        <span class="badge-modern bg-azul-octa">Ecommerce</span>
                      <?php endif; ?>

                      <?php if ((int)$venda->tipo_venda === 1) : ?>
                        <span class="badge-modern" style="background:rgba(168,85,247,.18);color:#c084fc;border-color:rgba(168,85,247,.4);">Personalizada</span>
                      <?php endif; ?>

                    <a href="#" data-toggle="modal" data-target="#myModal<?= $venda->id; ?>" class="sale-id-link">
                      <div style="font-weight: 600; font-size: 12px;">#<?= $venda->id; ?></div>
                      <div style="font-size: 11px; color:rgb(198, 200, 204);">#<?= $venda->transacao; ?></div>
                    </a>
                  </td>

                  <?php if ($section->getTipo() == 1 || $section->getTipo() == 2 || $section->getTipo() == 3 || $section->getTipo() == 6) : ?>
                    <td>
                      <div style="font-weight: 600; margin-bottom: 4px;"><?= $venda->endereco ? $venda->dataEndereco()->nome : $venda->dataComprador()->nome ?></div>
                      <div style="font-size: 12px; color: rgb(198, 200, 204); margin-bottom: 4px;"><?= $venda->endereco ? $venda->dataEndereco()->email : $venda->dataComprador()->email ?></div>
                      <div style="font-size: 12px;">
                        <?= $venda->endereco ? $venda->dataEndereco()->telefone : $venda->dataComprador()->telefone ?>
                        <a href='https://api.whatsapp.com/send?phone=55<?= $venda->endereco ? $venda->dataEndereco()->telefone : $venda->dataComprador()->telefone ?>' title='Entrar em contato pelo whatsapp' target='_blank' class="whatsapp-link" style="margin-left: 8px;">
                          <i class="fab fa-whatsapp"></i>
                        </a>
                      </div>
                    </td>
                  <?php elseif ($pag_coprodutor == true || $section->getTipo() == 4 || ($section->getTipo() == 5 && @$af1->liberar_comprador == 1)) : ?>
                    <td>
                      <div style="font-weight: 600; margin-bottom: 4px;"><?= $venda->endereco ? $venda->dataEndereco()->nome : $venda->dataComprador()->nome ?></div>
                      <div style="font-size: 12px; color: rgb(198, 200, 204); margin-bottom: 4px;"><?= $venda->endereco ? $venda->dataEndereco()->email : $venda->dataComprador()->email ?></div>
                      <div style="font-size: 12px;">
                        <?= $venda->endereco ? $venda->dataEndereco()->telefone : $venda->dataComprador()->telefone ?>
                        <a href='https://api.whatsapp.com/send?phone=55<?= $venda->endereco ? $venda->dataEndereco()->telefone : $venda->dataComprador()->telefone ?>' title='Entrar em contato pelo whatsapp' target='_blank' class="whatsapp-link" style="margin-left: 8px;">
                          <i class="fab fa-whatsapp"></i>
                        </a>
                      </div>
                    </td>
                  <?php else : ?>
                    <td style="color: rgb(198, 200, 204) font-style: italic;">Dados ocultado pelo produtor</td>
                  <?php endif; ?>

                  <?php if ($section->getTipo() == 1 || $section->getTipo() == 2 || $section->getTipo() == 3) : ?>
                    <td>
                      <div style="font-weight: 600; margin-bottom: 4px;"><?= $venda->dataProdutor()->nome ?></div>
                      <div style="font-size: 12px; color: rgb(198, 200, 204)"><?= $venda->dataProdutor()->email ?></div>
                    </td>
                  <?php endif ?>

                  <?php if ((int)$venda->tipo_venda === 1) : ?>
                    <td>
                      <?php
                        $vpItens = (new Source\Models\Atendente\VendaPersonalizadaItem())->porVenda($venda->id);
                        if ($vpItens) :
                          foreach ($vpItens as $vpI) :
                      ?>
                        <div style="font-size:12px; color:#e0e0e0;">• <?= htmlspecialchars($vpI->nome_produto) ?><?= $vpI->nome_plano ? ' <span style="color:rgba(255,255,255,.4);">(' . htmlspecialchars($vpI->nome_plano) . ')</span>' : '' ?></div>
                      <?php endforeach; else : ?>
                        <div style="font-weight:600;"><?= $venda->dataProduto()->nome ?></div>
                      <?php endif; ?>
                    </td>
                  <?php elseif ($venda->plano) : ?>
                    <td>
                      <a href="<?= url("produtos/detalhe/$venda->produto") ?>" class="product-link">
                        <div style="font-weight: 600; margin-bottom: 4px;"><?= $venda->dataProduto()->nome ?></div>
                        <div style="font-size: 12px; color: rgb(198, 200, 204);"><?= $venda->dataPlano()->nome ?></div>
                      </a>
                    </td>
                  <?php else : ?>
                    <td>
                      <a href="<?= url("produtos/detalhe/$venda->produto") ?>" class="product-link" style="font-weight: 600;">
                        <?= $venda->dataProduto()->nome ?>
                      </a>
                    </td>
                  <?php endif ?>

                  <td style="font-weight: 500;">
                    <?php if ($venda->afiliado) :
                      echo $venda->dataAfiliado()->nome;
                    endif;
                    ?>
                  </td>

                  <?php if (($_SESSION["tipo"] == 1 || $_SESSION["tipo"] == 2 || $_SESSION["tipo"] == 3) && !$pag_coprodutor) : ?>
                    <td style="font-weight: 700; color: #059669;">R$ <?= number_format($venda->valor_bruto, 2, ",", "."); ?></td>
                  <?php else : ?>
                    <td style="font-weight: 700; color: #059669;">R$ <?= number_format($valorComissao, 2, ",", "."); ?></td>
                  <?php endif ?>
                  
                  <td style="text-align: center;">
                    <?php if ($venda->forma_pagamento == 1) : ?>
                      <i class="fa fa-credit-card status-icon <?= @$statusT[$venda->status_transacao] ?>" title="<?= $status_transacao[$venda->status_transacao] ?>"></i>
                    <?php elseif ($venda->forma_pagamento == 2) : ?>
                      <i class="fa fa-barcode status-icon <?= @$statusT[$venda->status_transacao] ?>" title="<?= $status_transacao[$venda->status_transacao] ?>"></i>
                    <?php elseif ($venda->forma_pagamento == 3) : ?>
                      <img src="<?= url("themes/tema1/images/pix-" . @$statusT[$venda->status_transacao] . ".png") ?>" title="<?= $status_transacao[$venda->status_transacao] ?>" width="24px">
                    <?php endif ?>
                  </td>
                  
                  <td style="font-size: 12px; color: rgb(198, 200, 204);">
                    <?= formataDataHora($venda->created_at); ?>
                  </td>
                  
                  <td style="font-size: 12px; color: rgb(198, 200, 204);">
                    <?php
                    if ($venda->data_finalizada != "") {
                      echo formataDataHora($venda->data_finalizada);
                    } elseif ($venda->status_transacao == 5 || $venda->status_transacao == 6 || $venda->status_transacao == 7) {
                      echo formataDataHora($venda->data_ultimo_evento);
                    }
                    ?>
                  </td>

                  <td>
                    <button class="btn-details-modern" data-toggle="modal" data-target="#myModal<?= $venda->id; ?>">
                      <i class="fas fa-eye" style="margin-right: 4px;"></i>Ver
                    </button>
                  </td>
                </tr>

                <!--?= $v->insert("vendas/modal-info", [
                  "venda" => $venda, 
                  "pagina" => $pagina, 
                  "status_transacao" => $status_transacao, 
                  "forma_pagamento" => $forma_pagamento, 
                  "i" => $i, 
                  "afiliado" => $afiliado,
                  //"dadosComprador" => dadosComprador($venda)
              ]); ?-->

                <form action="" method="post" id="form<?= $index ?>">
                  <input type="hidden" name="vencimento" id="vencimento<?= $index ?>">
                  <input type="hidden" name="id" id="idV<?= $index ?>" value="<?= $venda->id ?>">
                  <input type="hidden" name="transacao" id="transacao<?= $index ?>" value="<?= $venda->transacao ?>">
                  <input type="hidden" name="produto" id="produto<?= $index ?>" value="<?= $venda->produto ?>">
                  <input type="hidden" name="nome_produto" id="nome_produto<?= $index ?>" value="<?= $venda->dataProduto()->nome ?>">
                  <input type="hidden" name="produtor" id="produtor<?= $index ?>" value="<?= $venda->produtor ?>">
                  <input type="hidden" name="nome" id="nome<?= $index ?>" value="<?= $venda->dataComprador()->nome ?>">
                  <input type="hidden" name="email" id="email<?= $index ?>" value="<?= $venda->dataComprador()->email ?>">
                  <input type="hidden" name="cpf" id="cpf<?= $index ?>" value="<?= $venda->dataComprador()->cpf_cnpj ?>">

                  <?php
                  $tel = @$venda->dataComprador()->telefone;
                  $ddd = substr($tel, 0, 2);
                  $telefone = substr($tel, 2, 12);

                  $data = mt_rand(strtotime("1970-01-01"), strtotime("2000-01-01"));
                  $date = new DateTime();
                  $date->setTimestamp($data);
                  $nascimento = $date->format('Y-m-d');
                  ?>

                  <input type="hidden" name="nasc" id="nasc<?= $index ?>" value="<?= $nascimento ?>">
                  <input type="hidden" name="ddd" id="ddd<?= $index ?>" value="<?= $ddd ?>">
                  <input type="hidden" name="telefone" id="telefone<?= $index ?>" value="<?= $telefone ?>">
                  <?php
                  $c = $venda->dataComprador();
                  $notazz_rua    = $c->rua_entrega    ?: $c->rua;
                  $notazz_numero = $c->numero_entrega ?: $c->numero;
                  $notazz_comp   = $c->complemento_entrega ?: $c->complemento;
                  $notazz_bairro = $c->bairro_entrega ?: $c->bairro;
                  $notazz_cidade = $c->cidade_entrega ?: $c->cidade;
                  $notazz_estado = $c->estado_entrega ?: $c->estado;
                  $notazz_cep    = $c->cep_entrega    ?: $c->cep;
                  // Fallback: tabela enderecos (preenchida pelo checkout com endereco_obrigatorio)
                  if (!$notazz_rua && $venda->endereco) {
                      $e = $venda->dataEndereco();
                      if ($e) {
                          $notazz_rua    = $e->rua;
                          $notazz_numero = $e->numero;
                          $notazz_comp   = $e->complemento;
                          $notazz_bairro = $e->bairro;
                          $notazz_cidade = $e->cidade;
                          $notazz_estado = $e->estado;
                          $notazz_cep    = $e->cep;
                      }
                  }
                  // Último recurso: genérico
                  $notazz_rua    = $notazz_rua    ?: 'Não Informado';
                  $notazz_numero = $notazz_numero ?: 'S/N';
                  $notazz_bairro = $notazz_bairro ?: 'Não Informado';
                  $notazz_cidade = $notazz_cidade ?: 'Não Informado';
                  $notazz_estado = $notazz_estado ?: 'Não Informado';
                  $notazz_cep    = $notazz_cep    ?: '00000000';
                  ?>
                  <input type="hidden" name="rua" id="rua<?= $index ?>" value="<?= $notazz_rua ?>">
                  <input type="hidden" name="numero" id="numero<?= $index ?>" value="<?= $notazz_numero ?>">
                  <input type="hidden" name="complemento" id="complemento<?= $index ?>" value="<?= $notazz_comp ?>">
                  <input type="hidden" name="bairro" id="bairro<?= $index ?>" value="<?= $notazz_bairro ?>">
                  <input type="hidden" name="cidade" id="cidade<?= $index ?>" value="<?= $notazz_cidade ?>">
                  <input type="hidden" name="estado" id="estado<?= $index ?>" value="<?= $notazz_estado ?>">
                  <input type="hidden" name="pais" id="pais<?= $index ?>" value="<?= $c->pais_entrega ?>">
                  <input type="hidden" name="cep" id="cep<?= $index ?>" value="<?= $notazz_cep ?>">

                  <?php if ($venda->plano) : ?>
                    <input type="hidden" name="nomekit" id="nomekit<?= $index ?>" value="<?= $venda->dataPlano()->nome ?>">
                    <input type="hidden" name="codigo_produto" id="codigo_produto<?= $index ?>" value="<?= $venda->dataPlano()->codigo ?>">
                    <input type="hidden" name="quantidade" id="quantidade<?= $index ?>" value="<?= $venda->dataPlano()->quantidade ?>">
                  <?php else : ?>
                    <input type="hidden" name="nomekit" id="nomekit<?= $index ?>" value="<?= $venda->dataProduto()->nome ?>">
                    <input type="hidden" name="codigo_produto" id="codigo_produto<?= $index ?>" value="<?= $venda->dataProduto()->codigo ?>">
                    <input type="hidden" name="quantidade" id="quantidade<?= $index ?>" value="1">
                  <?php endif ?>

                  <input type="hidden" name="email_suporte" id="email_suporte<?= $index ?>" value="<?= $venda->dataProduto()->email_suporte ?>">
                  <input type="hidden" name="valor_venda" id="valor_venda<?= $index ?>" value="<?= number_format($venda->valor_produto - $venda->valor_desconto, 2, ".", "") ?>">
                  <input type="hidden" name="valor_total" id="valor_total<?= $index ?>" value="<?= $venda->valor_bruto ?>">

                </form>

                <?php include "modal-info.php" ?>

              <?php $i++;
                $index++;
              endforeach; ?>
            </tbody>
          </table>

        </div>
      </div>
    <?php else : ?>
      <h4>Não existem pedidos cadastrados!</h4>
    <?php endif; ?>


    <?php
    //EXIBE A NAVEGAÇÃO DO PAGINADOR
    $uri = explode("?", $_SERVER["REQUEST_URI"], 2);
    if ($pag_coprodutor == true)
      $link = URL_BASE . "/vendas/coprodutor/";
    else
      $link = URL_BASE . "/vendas/";

    //VARIÁVEIS VÊM DO CONTROLADOR AppPedidos
    $paginador->paginas($total, $pagina, $link, @$uri[1]);

    $url = explode("?", $_SERVER["REQUEST_URI"]);
    if (@$url[1] != "") {
      $conc = "&";
    } else {
      $conc = "";
    }
    ?>

    <br>
  </section>
</div>

<?= $v->start("scripts"); ?>
<script src="<?= url("themes/tema1/dist/js/modal.js") ?>"></script>
<script>
  $(document).ready(function() {

    // $("#busca-avancada").hide();

    var cont = 0;
    $("#btn-filtro").click(function() {
      $("#busca-avancada").toggle();

      if (cont == 0) {
        $("#btn-filtro").html("Menos filtros");
        cont = 1;
      } else {
        $("#btn-filtro").html("Mais filtros");
        cont = 0;
      }
    });

    <?php foreach ($vendas as $venda) : ?>
      $('#modalx-slideUp<?= $venda->id ?>').popup({
        pagecontainer: '.container',
        transition: 'all 0.5s',
        background: true,
        color: '#000',
        opacity: '0.5',
      });
    <?php endforeach ?>
  });

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

  function vencimento(i) {
    $("#vencimento" + i).val($("#data_venc" + i).val());
  }

  function editar(i, adquirente = null) {
    load("open", 5000);
    //alert('clicou ' + i);
    var form = $("#form" + i);

    if (adquirente == "redrocket")
      var url = "<?= $router->route("checkout.novoBoleto") ?>";
    else
      var url = "<?= $router->route("chkTransparente.novoBoleto") ?>";

    $.ajax({
      url: url,
      method: "POST",
      data: form.serialize(),
      success: function(callback) {
        console.log(callback);

        function data(callback) {
          try {
            return JSON.parse(callback);
          } catch (error) {
            return false;
          }
        }

        var data = data(callback);
        console.log(data);

        if (data.msg == "Salvo com sucesso") {
          alert(data.msg);
          location.reload();
        } else {
          alert(data.msg);
          location.reload();
        }

      }
    })
  }

  function notaFiscal(i) {
    load("open", 3000);
    var form = $("#form" + i);
    $.ajax({
      url: "<?= $router->route("notazz.gerar") ?>",
      method: "POST",
      data: form.serialize(),
      success: function(callback) {
        console.log(callback);
        var dados = JSON.parse(callback);
        if (dados.statusProcessamento == "sucesso") {
          alert("Nota fiscal gerada com sucesso!\nID da nota fiscal " + dados.id + "\n" + dados.msg);
        } else {
          alert("Erro ao gerar nota fiscal\n" + dados.motivo);
        }
      }
    });
  }

  function motivo(i) {
    load("open", 3000);
    //alert('clicou ' + i);
    var motivo = $("#motivo" + i).val();
    console.log(i);

    $.ajax({
      url: "<?= $router->route("vendas.motivo") ?>",
      method: "POST",
      data: {
        id: i,
        motivo: motivo
      },
      success: function(callback) {
        console.log(callback);

        //location.reload();
      }
    })
  }

  function postback(venda, produtor, produto) {
    load("open", 3000);

    $.ajax({
      url: "<?= $router->route("postback.retorno") ?>",
      method: "POST",
      data: {
        venda: venda,
        produtor: produtor,
        produto: produto
      },
      success: function(callback) {
        console.log(callback);
        alert(callback);

        //location.reload();
      }
    })
  }

  function bling(venda, produtor, produto) {
    load("open", 3000);

    $.ajax({
      url: "<?= $router->route("bling.criarPedido") ?>",
      method: "POST",
      data: {
        venda: venda,
        usuario: produtor,
        produto: produto
      },
      success: function(callback) {
        console.log(callback);
        alert(callback);

        //location.reload();
      }
    })
  }

  for (let i = 1; i <= <?= $count ?>; i++) {
    // console.log("var i", i);
    $("#div_estorno" + i).hide();
    $("#btn_estorno" + i).click((e) => {
      e.preventDefault();
      console.log("clicou", i);
      $("#div_estorno" + i).toggle();
    });
  }

  function inserir(venda) {
    load("open", 3000);

    var rastreio = $("#codigo_rastreio" + venda).val();
    $.ajax({
      url: "<?= $router->route("vendas.rastreio") ?>",
      method: "POST",
      data: {
        venda: venda,
        codigo_rastreio: rastreio
      },
      success: function(callback) {
        console.log(callback);
        alert(callback);

        //location.reload();
      }
    })
  }

  function alertaChargeback(venda) {
    load("open", 3000);

    $.ajax({
      url: "<?= $router->route("chargeback.alerta") ?>",
      method: "POST",
      data: {
        venda: venda
      },
      success: function(callback) {
        console.log(callback);
        alert(callback);

        location.reload();
      }
    })
  }

  function desbloquear(venda) {
    load("open", 3000);

    $.ajax({
      url: "<?= $router->route("chargeback.desbloqueio") ?>",
      method: "POST",
      data: {
        venda: venda
      },
      success: function(callback) {
        console.log(callback);
        alert(callback);

        //location.reload();
      }
    })
  }

  function estornar(id, transacao, forma_pagamento, status, tipo, customer_id = null, adquirente = null, form = null) {
    load("open", 6000);
    var motivo = $("#motivo" + id).val();

    // console.log("id: ", id);
    // console.log("transacao: ", transacao);
    // console.log("forma_pagamento: ", forma_pagamento);
    // console.log("status: ", status);
    // console.log("tipo: ", tipo);
    // console.log("customer_id: ", customer_id);
    // console.log("adquirente", adquirente);

    if (adquirente == "redrocket") {
      if (tipo == "estorno") {
        var url = "<?= $router->route("checkout.estornar") ?>";
      } else {
        var url = "<?= $router->route("checkout.solicitarEstorno") ?>"
      }
    } else {
      var url = "<?= $router->route("chkTransparente.estornar") ?>";
    }

    console.log(url);

    if (form == "sim") {
      var banco = $("#banco" + id).val();
      var agencia = $("#agencia" + id).val();
      var conta = $("#conta" + id).val();
      var tipo = $("#tipo" + id).val();
      var chave_pix = $("#chave_pix" + id).val();
    }

    if (motivo == "" || motivo.length < 50) {
      alert("Preencha o motivo corretamente");
      $("#motivo" + id).focus();
    } else {
      $.ajax({
        url: url,
        method: "POST",
        data: {
          id: id,
          transacao: transacao,
          forma_pagamento: forma_pagamento,
          status: status,
          motivo: motivo,
          customer_id: customer_id,
          adquirente: adquirente,
          banco: banco,
          agencia: agencia,
          conta: conta,
          tipo: tipo,
          chave_pix: chave_pix,
        },
        success: function(callback) {
          console.log(callback);
          alert(callback);
          load();

          location.reload();
        }
      });
    }
  }

  function estornoCompleto(venda) {
    load("open", 6000);

    var url = "<?= $router->route("estorno.completo") ?>";

    $.ajax({
      url: url,
      method: "POST",
      data: {
        venda: venda
      },
      success: function(callback) {
        console.log(callback);
        alert(callback);
        load();

        location.reload();
      }
    });
  }

  function estornarPix(id, transacao, forma_pagamento, tipo, adquirente = null) {
    load("open", 3000);
    var motivo = $("#motivo" + id).val();
    console.log(motivo);

    console.log("id: ", id);
    console.log("transacao: ", transacao);
    console.log("forma_pagamento: ", forma_pagamento);
    console.log("tipo: ", tipo);
    console.log("adquirente", adquirente);

    if (adquirente == "redrocket") {
      if (tipo == "estorno") {
        var url = "<?= $router->route("checkout.cancelamentoPix") ?>";
      } else {
        var url = "<?= $router->route("checkout.solicitarEstorno") ?>";
      }
    } else {
      var url = "<?= $router->route("chkTransparente.estornar") ?>";
    }
    console.log(url);

    if (motivo == "" || motivo.length < 50) {
      alert("Preencha o motivo corretamente");
      $("#motivo" + id).focus();
    } else {
      $.ajax({
        url: url,
        method: "POST",
        data: {
          id: id,
          transacao: transacao,
          forma_pagamento: forma_pagamento,
          status: status,
          motivo: motivo,
          adquirente: adquirente
        },
        success: function(callback) {
          console.log(callback);
          alert(callback);

          // location.reload();
        }
      });
    }
  }
</script>

<div class="modal fade" id="nf-modal-endereco" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Cidade do destinatário — Nota Fiscal</h5>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
        <p class="text-muted" style="font-size:13px">O cliente não possui cidade/estado cadastrado. Informe o endereço a usar na nota (ex: endereço da sua empresa).</p>
        <input type="hidden" id="nf-modal-index">
        <div class="form-group">
          <label>Cidade <span class="text-danger">*</span></label>
          <input type="text" id="nf-modal-cidade" class="form-control" placeholder="Ex: São Paulo">
        </div>
        <div class="row">
          <div class="col-md-4">
            <div class="form-group">
              <label>UF <span class="text-danger">*</span></label>
              <input type="text" id="nf-modal-estado" class="form-control" placeholder="SP" maxlength="2" style="text-transform:uppercase">
            </div>
          </div>
          <div class="col-md-8">
            <div class="form-group">
              <label>CEP <span class="text-danger">*</span></label>
              <input type="text" id="nf-modal-cep" class="form-control" placeholder="00000000">
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-success" id="nf-modal-confirmar">Gerar Nota Fiscal</button>
      </div>
    </div>
  </div>
</div>

<?= $v->end(); ?>