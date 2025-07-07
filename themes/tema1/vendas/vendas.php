<?php

use Source\App\Checkout1\PagarMe;
use Source\Models\Afiliados\Afiliacao;

 $v->layout("_theme"); ?>

<?= $v->start("links"); ?>
<link rel="stylesheet" href="<?= url("themes/tema1/dist/css/modal.css") ?>">
<style>

.modern-stats-container {
      margin: 1.5rem 0;
      font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
    }

    .stats-row {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
      gap: 1rem;
      margin-bottom: 1rem;
    }

    .stat-card {
      background: linear-gradient(135deg, rgb(5, 5, 5) 0%, rgb(0, 0, 0) 100%);
      border-radius: 12px;
      padding: 1.5rem;
      position: relative;
      overflow: hidden;
      transition: all 0.3s ease;
      min-height: 120px;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
    }

    .stat-card::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      height: 3px;
      background: linear-gradient(90deg, #f4d03f 0%, #f4d03f 100%);
      border-radius: 12px 12px 0 0;
    }

    .stat-card:hover {
      transform: translateY(-4px);
      box-shadow: 0 5px 9px rgba(238, 255, 0, 0.2);
      border-color: #f4d03f;
    }

    .stat-header {
      margin-bottom: 0.5rem;
    }

    .stat-label {
      color: #888;
      font-size: 0.875rem;
      font-weight: 500;
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }

    .stat-value h2 {
      color: #fff;
      font-size: 2rem;
      font-weight: 700;
      margin: 0;
      line-height: 1.2;
      word-break: break-word;
    }

    .category-text {
      color: #00ff88 !important;
      font-size: 1.5rem !important;
    }

    /* Payment cards styling */
    .payment-card {
      min-height: 100px;
    }

    .payment-header {
      display: flex;
      align-items: center;
      gap: 1rem;
      height: 100%;
    }

    .payment-icon {
      width: 50px;
      height: 50px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      flex-shrink: 0;
    }

    .payment-icon.credit-card {
      background: linear-gradient(135deg, #00ff88 0%, #00cc6a 100%);
    }

    .payment-icon.barcode {
      background: linear-gradient(135deg, #00ff88 0%, #00cc6a 100%);
    }

    .payment-icon.pix {
      background: linear-gradient(135deg, #00ff88 0%, #00cc6a 100%);
    }

    .payment-icon i {
      color: #000;
      font-size: 24px;
    }

    .pix-icon {
      width: 24px;
      height: 24px;
      filter: brightness(0);
    }

    .payment-info {
      flex: 1;
      min-width: 0;
    }

    .payment-percentage {
      background: linear-gradient(135deg, rgb(217, 192, 0) 0%, #f4d03f 100%);
      color: #000;
      padding: 0.25rem 0.75rem;
      border-radius: 20px;
      font-size: 0.75rem;
      font-weight: 600;
      display: inline-block;
      margin-bottom: 0.5rem;
      white-space: nowrap;
    }

    .payment-count {
      color: #fff;
      font-size: 1.75rem;
      font-weight: 700;
      margin: 0;
      word-break: break-word;
    }

    /* Financial cards styling */
    .financial-card .stat-value h2 {
      color: #f4d03f;
    }

    .financial-value {
      font-size: 1.5rem !important;
      word-break: break-word;
    }

    /* Responsive design */
    @media (max-width: 1024px) {
      .stats-row {
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
      }
    }

    @media (max-width: 768px) {
      .modern-stats-container {
        margin: 1rem 0;
      }
      
      .stats-row {
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 0.75rem;
      }
      
      .stat-card {
        padding: 1.25rem;
        min-height: 110px;
        text-align: center;
      }
      
      /* Remove top stripe on mobile */
      .stat-card::before {
        display: none;
      }
      
      .stat-header {
        text-align: left;
      }
      
      .stat-value h2 {
        font-size: 1.75rem;
        text-align: center;
      }
      
      .payment-count {
        font-size: 1.5rem;
        text-align: center;
      }
      
      .payment-percentage {
        text-align: center;
        display: block;
        margin: 0 auto 0.5rem auto;
        width: fit-content;
      }
      
      .payment-icon {
        width: 45px;
        height: 45px;
      }
      
      .payment-icon i {
        font-size: 22px;
      }
      
      .pix-icon {
        width: 22px;
        height: 22px;
      }

      .payment-header {
        gap: 0.75rem;
        flex-direction: column;
      }

      .payment-info {
        text-align: center;
      }

      .financial-value {
        font-size: 1.4rem !important;
        text-align: center;
      }
    }

    @media (max-width: 480px) {
      .stats-row {
        grid-template-columns: 1fr;
        gap: 0.5rem;
      }
      
      .stat-card {
        padding: 1rem;
        min-height: 100px;
      }
      
      .stat-value h2 {
        font-size: 1.5rem;
      }
      
      .payment-header {
        flex-direction: column;
        gap: 0.75rem;
        align-items: center;
      }
      
      .payment-icon {
        width: 40px;
        height: 40px;
      }
      
      .payment-icon i {
        font-size: 18px;
      }
      
      .pix-icon {
        width: 18px;
        height: 18px;
      }
      
      .payment-count {
        font-size: 1.25rem;
      }
      
      .financial-value {
        font-size: 1.25rem !important;
      }

      .stat-label {
        font-size: 0.8rem;
      }

      .payment-percentage {
        font-size: 0.7rem;
        padding: 0.2rem 0.6rem;
      }
    }

    @media (max-width: 360px) {
      .stat-card {
        padding: 0.75rem;
      }
      
      .stat-value h2 {
        font-size: 1.25rem;
      }
      
      .payment-count {
        font-size: 1.1rem;
      }
      
      .financial-value {
        font-size: 1.1rem !important;
      }
    }






    
 /* Container da Tabela */
.modern-table-container {
  background: #1e1e1e;
  border-radius: 16px;
  box-shadow: 0 8px 24px rgba(0, 0, 0, 0.6);
  overflow: hidden;
  border: 1px solid #2c2c2c;
}

/* Tabela */
.modern-table {
  margin: 0;
  font-size: 14px;
  width: 100%;
  border-collapse: collapse;
  color: #e5e7eb;
}

.modern-table thead {
  background: linear-gradient(135deg,rgb(0, 0, 0),rgb(41, 42, 43));
  color: #f9fafb;
}

.modern-table thead th {
  padding: 16px;
  font-weight: 600;
  font-size: 12px;
  text-transform: uppercase;
  letter-spacing: 0.6px;
  border: none;
  text-align: left;
  font-family: 'Inter', sans-serif;
}

/* Corpo da Tabela */
.modern-table tbody tr {
  transition: background-color 0.2s ease, transform 0.2s ease;
  border-bottom: 1px solid #2c2c2c;
}

.modern-table tbody tr:hover {
  background-color: #272727;
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.6);
}

.modern-table tbody td {
  padding: 14px 16px;
  vertical-align: middle;
  border: none;
  font-family: 'Inter', sans-serif;
  color: #d1d5db;
}

.modern-table tbody tr:last-child {
  border-bottom: none;
}

/* Scroll responsivo */
.table-responsive-modern {
  overflow-x: auto;
  -webkit-overflow-scrolling: touch;
}

.table-responsive-modern::-webkit-scrollbar {
  height: 8px;
}

.table-responsive-modern::-webkit-scrollbar-track {
  background: #111827;
  border-radius: 4px;
}

.table-responsive-modern::-webkit-scrollbar-thumb {
  background: #374151;
  border-radius: 4px;
}

.table-responsive-modern::-webkit-scrollbar-thumb:hover {
  background: #4b5563;
}

/* Badge (status, tags, etc.) */
.badge-modern {
  padding: 4px 10px;
  border-radius: 9999px;
  font-size: 11px;
  font-weight: 600;
  background-color: #374151;
  color: #f9fafb;
  display: inline-block;
  line-height: 1;
}

/* Botão de detalhes */
.btn-details-modern {
  background: linear-gradient(135deg, rgb(217, 192, 0), rgb(217, 192, 0));
  color: #fff;
  border: none;
  padding: 8px 14px;
  border-radius: 10px;
  font-size: 13px;
  font-weight: 600;
  transition: all 0.2s ease;
  box-shadow: 0 2px 6px rgba(245, 158, 11, 0.3);
  cursor: pointer;
}

.btn-details-modern:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 16px rgba(245, 158, 11, 0.4);
}

/* Ícones */
.status-icon {
  font-size: 20px;
  margin: 0;
  opacity: 0.85;
  color: #facc15;
}

/* Links */
.whatsapp-link {
  color: #25d366;
  transition: all 0.2s ease;
}

.whatsapp-link:hover {
  color: #128c7e;
  transform: scale(1.1);
}

.product-link {
  color: #60a5fa;
  text-decoration: none;
  transition: all 0.2s ease;
}

.product-link:hover {
  color: #3b82f6;
  text-decoration: underline;
}

.sale-id-link {
  color: #9ca3af;
  text-decoration: none;
  transition: all 0.2s ease;
}

.sale-id-link:hover {
  color: #e5e7eb;
  text-decoration: underline;
}

modern-info-alert {
  background: rgb(0, 0, 0);
  border: none;
  border-radius: 12px;
  padding: 0;
  margin: 1.5rem 0;
  box-shadow: 0 4px 12px rgba(33, 150, 243, 0.1);
  transition: all 0.3s ease;
  overflow: hidden;
}

.modern-info-alert:hover {
  transform: translateY(-2px);

}

.alert-content {
  display: flex;
  align-items: flex-start;
  padding: 1.25rem;
  gap: 1rem;
}

.alert-icon {
  flex-shrink: 0;
  width: 40px;
  height: 40px;
  background: rgba(33, 150, 243, 0.1);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-top: 2px;
}

.alert-icon i {
  color: rgb(217, 192, 0);
  font-size: 18px;
}

.alert-text {
  flex: 1;
  color: rgb(217, 192, 0);
  font-size: 14px;
  line-height: 1.6;
  margin: 0;
}

.alert-text strong {
  color: rgb(217, 192, 0)
  font-weight: 600;
}



/* Responsive */
@media (max-width: 768px) {
  .modern-info-alert {
    margin: 1rem 0;
  }
  
  .alert-content {
    padding: 1rem;
    gap: 0.75rem;
  }
  
  .alert-icon {
    width: 36px;
    height: 36px;
  }
  
  .alert-icon i {
    font-size: 16px;
  }
  
  .alert-text {
    font-size: 13px;
  }
}

/* Dark theme support */
@media (prefers-color-scheme: dark) {
  .modern-info-alert {
    background: rgb(0, 0, 0);;
  }
  
  .alert-icon {
    background: rgba(59, 130, 246, 0.2);
  }
  
  .alert-icon i {
    color: rgb(217, 192, 0);
  }
  
  .alert-text {
    color: #dbeafe;
  }
  
  .alert-text strong {
    color: rgb(217, 192, 0);
  }
}

/* Estilo unificado para inputs, selects e filtros do formulário de busca avançada */

form input[type="text"],
form input[type="number"],
form input[type="date"],
form select {
  background: #2a2a2a !important;
  border: 2px solid #444 !important;
  border-radius: 8px !important;
  padding: 12px 16px !important;
  color: #ffffff !important;
  font-size: 14px !important;
  font-family: 'Inter', sans-serif !important;
  width: 100% !important;
  height: auto !important;
  min-height: 45px !important;
  box-sizing: border-box !important;
  transition: all 0.3s ease !important;
}

form input[type="text"]:focus,
form input[type="number"]:focus,
form input[type="date"]:focus,
form select:focus {
  background: #333333 !important;
  border-color: #FFDD00 !important;
  box-shadow: 0 0 0 3px rgba(255, 221, 0, 0.2) !important;
  outline: none !important;
  color: #ffffff !important;
}

form select {
  background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%23FFDD00' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='m6 8 4 4 4-4'/%3e%3c/svg%3e") !important;
  background-position: right 12px center !important;
  background-repeat: no-repeat !important;
  background-size: 16px 12px !important;
  padding-right: 40px !important;
  cursor: pointer !important;
  appearance: none !important;
  -webkit-appearance: none !important;
  -moz-appearance: none !important;
}

form select option {
  background: #2a2a2a !important;
  color: #ffffff !important;
}

form h6 {
  color: #FFDD00 !important;
  font-size: 14px !important;
  font-weight: 500 !important;
  font-family: 'Inter', sans-serif !important;
  margin-bottom: 8px !important;
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
<div class="content-wrapper">
  <!-- FORMULARIO -->
  <section class="content-header">

    <?= $section->getTipoSecaoLayout() ?>

    <div class="col-sm-6">
      <h4 style="font-family:inter; color: white"><?= $title ?></h4>
    </div>

    <form action="#" style="font-family:inter; font-size:10px">
      <div class="callout callout-primary">
        <div class="row">
          <div class="col-12 col-md-2">
            <h6 style="font-family:inter">Data da venda:</h6>
            <input type="date" name="inicio" id="inicio" class="form-control" style="cursor:pointer;" value="<?= @$_GET["inicio"]; ?>">
          </div>

          <div class="col-12 col-md-2">
            <h6 style="font-family:inter; color:#fff">|</h6>
            <input type="date" name="fim" id="fim" class="form-control" style="cursor:pointer;" value="<?= @$_GET["fim"]; ?>">
          </div>

          <div class="col-12 col-md-2">
            <h6 style="font-family:inter">Data finalizada:</h6>
            <input type="date" name="inicio_finalizada" id="inicio_finalizada" class="form-control" style="cursor:pointer;" value="<?= @$_GET["inicio_finalizada"]; ?>">
          </div>

          <div class="col-12 col-md-2">
            <h6 style="font-family:inter; color:#fff">|</h6>
            <input type="date" name="fim_finalizada" id="fim_finalizada" class="form-control" style="cursor:pointer;" value="<?= @$_GET["fim_finalizada"]; ?>">
          </div>

          <div class="col-12 col-md-4">
            <h6 style="font-family:inter">Forma de Pagamento:</h6>

            <select name="pagamento" id="pagamento" class="form-control">
              <option value="">Pagamento...</option>
              <option value="1" <?php if (@$_GET["pagamento"] == 1) echo "selected";
                                echo "" ?>>Cartão de crédito</option>
              <option value="2" <?php if (@$_GET["pagamento"] == 2) echo "selected";
                                echo "" ?>>Boleto</option>
              <option value="3" <?php if (@$_GET["pagamento"] == 3) echo "selected";
                                echo "" ?>>PIX</option>
            </select>
          </div>
        </div>
      </div>

      <div class="callout callout-primary">
        <div class="row">
          <div class="col-12 col-md-3">
            <h6 style="font-family:inter">Código da venda ou pedido:</h6>
            <input type="text" name="venda" class="form-control" placeholder="ex.: PAY-078VRHCE52BI ou 239" value="<?= @$_GET["venda"]; ?>">
          </div>

          <div class="col-12 col-md-2">
            <h6 style="font-family:inter">Status da transação:</h6>
            <select name="status" id="status" class="form-control">
              <option value="">Status...</option>
              <?php foreach ($status_transacao as $cod => $status) : ?>
                <option value="<?= $cod ?>" <?php if (@$_GET["status"] == $cod) echo "selected";
                                            echo "" ?>><?= $status ?></option>
              <?php endforeach; ?>
            </select>
          </div>

          <?php if ($section->getTipo() != 6) : ?>
            <div class="col-12 col-md-2">
              <h6 style="font-family:inter">Produto:</h6>

              <select name="produto" id="produto" class="form-control">
                <option value="">Produto...</option>
                <?php foreach ($produto as $produto) : ?>
                  <option value="<?= $produto->id ?>" <?= (@$_GET["produto"] == $produto->id) ? "selected" : "" ?>><?= $produto->nome ?></option>
                <?php endforeach; ?>
                <?php if (@$produtor) : foreach ($produtor as $produtor) : ?>
                    <option value="<?= $produtor->dataProduto()->id ?>" <?= (@$_GET["produto"] == $produtor->dataProduto()->id) ? "selected" : "" ?>><?= $produtor->dataProduto()->nome ?></option>
                <?php endforeach;
                endif; ?>
              </select>
            </div>


            <div class="col-12 col-md-3">
              <h6 style="font-family:inter">Comprador:</h6>
              <input type="text" name="comprador" id="cpf" class="form-control" placeholder="(nome ou CPF/CNPJ ou email)" value="<?= @$_GET["comprador"]; ?>">
            </div>
          <?php endif; ?>


          <div class="col-12 col-md-2">
            <h6 style="font-family:inter; color:#fff">|</h6>
            <button type="submit" name="" class="form-control form-control btn-primary"> <i class="fa fa-check"></i> Filtrar</button>
          </div>
        </div>
      </div>

      <?php if ($section->getTipo() != 6) : ?>
        <button type="button" class="btn btn-outline-primary btn-sm" id="btn-filtro">Mais Filtros</button>
      <?php endif ?>

      <div id="busca-avancada">
        <br>
        <h5>Busca Avançada</h5>

        <div class="callout callout-primary">
          <div class="row">

            <div class="col-md-3">
              <h6>Data vencimento boleto:</h6>
              <input type="date" name="vencimento_boleto" id="vencimento_boleto" class="form-control">
            </div>

            <div class="col-md-3">
              <h6>Planos:</h6>
              <select name="planos" id="planos" class="form-control">
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

            <div class="col-md-3">
              <h6>Campanha:</h6>
              <select name="campanha" id="campanha" class="form-control">
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

            <div class="col-md-3">
              <h6>Afiliado:</h6>
              <select name="afiliado" id="afiliado" class="form-control">
                <option value="">Selecionar...</option>
                <?php if ($afiliados) : ?>
                  <?php foreach ($afiliados as $afiliado_produto) : ?>
                    <option value="<?= $afiliado_produto->afiliado ?>"><?= $afiliado_produto->dataAfiliado()->nome ?></option>
                  <?php endforeach ?>
                <?php endif ?>
              </select>
            </div>

          </div>
        </div>

        <div class="callout callout-primary">
          <div class="row">

            <div class="col-md-3">
              <h6>Upsell:</h6>
              <select name="upsell" id="upsell" class="form-control">
                <option value="">Selecionar...</option>
                <option value="IS NOT NULL">Sim</option>
                <option value="IS NULL">Não</option>
              </select>
            </div>

            <div class="col-md-3">
              <h6>Order bump:</h6>
              <select name="order_bump" id="order_bump" class="form-control">
                <option value="">Selecionar...</option>
                <option value="IS NOT NULL">Sim</option>
                <option value="IS NULL">Não</option>
              </select>
            </div>

            <div class="col-md-3">
              <h6>Cupom de desconto:</h6>
              <input type="text" name="cupom" id="cupom" class="form-control">
            </div>

            <div class="col-md-3">
              <h6>Tipo de frete:</h6>
              <select name="frete" id="frete" class="form-control">
                <option value="">Selecionar...</option>
                <option value="fixo">Frete Fixo</option>
                <option value="gratis">Frete Grátis</option>
                <option value="sedex">Correios - Sedex</option>
                <option value="pac">Correios - Pac</option>
                <option value="jadlog">Frete Jadlog</option>
                <option value="regiao">Frete Por Região</option>
              </select>
            </div>

          </div>
        </div>

        <div class="callout callout-primary">
          <div class="row">

            <div class="col-md-3">
              <h6>Assumir juros:</h6>
              <select name="assumir_juros" id="assumir_juros" class="form-control">
                <option value="">Selecionar...</option>
                <option value="IS NOT NULL">Sim</option>
                <option value="IS NULL">Não</option>
              </select>
            </div>

            <div class="col-md-3">
              <h6>Parcelas do cartão:</h6>
              <input type="number" name="parcelas" id="parcelas" class="form-control">
            </div>

            <div class="col-md-3">
              <h6>SRC, UTMs...:</h6>
              <input type="text" name="src" id="src" class="form-control">
            </div>

            <!-- <div class="col-md-3">
              <h6>Televendas:</h6>
              <select name="televendas" id="televendas" class="form-control">
                <option value="">Televendas...</option>
                <option value="IS NOT NULL">Sim</option>
                <option value="IS NULL">Não</option>
              </select>
            </div> -->

          </div>
        </div>

      </div>
    </form>
  </section>
  <!-- FIM FORMULARIO -->

  <!-- Main content -->
  <section class="content">

    <!-- Default box -->
    <?php if ($vendas) : ?>
      <?php if ($section->getTipo() != 6) : ?>
        <div class="modern-stats-container">
          <!-- Primera fila de cards -->
          <div class="stats-row">
            <div class="stat-card">
              <div class="stat-header">
                <span class="stat-label">Total de vendas:</span>
              </div>
              <div class="stat-value">
                <h2><?= $count; ?></h2>
              </div>
            </div>

            <div class="stat-card">
              <div class="stat-header">
                <span class="stat-label">Sua categoria:</span>
              </div>
              <div class="stat-value">
                <h2 class="category-text"><?php echo $nivel[@$nivelUsuario]; ?></h2>
              </div>
            </div>

            <div class="stat-card payment-card">
              <div class="payment-header">
                <div class="payment-icon credit-card">
                  <i class="fas fa-credit-card"></i>
                </div>
                <div class="payment-info">
                  <span class="payment-percentage"><?= number_format($vendasCartao / $count * 100, 2, ",", "."); ?>%</span>
                  <h3 class="payment-count"><?= $vendasCartao ?></h3>
                </div>
              </div>
            </div>

            <div class="stat-card payment-card">
              <div class="payment-header">
                <div class="payment-icon barcode">
                  <i class="fas fa-barcode"></i>
                </div>
                <div class="payment-info">
                  <span class="payment-percentage"><?= number_format($vendasBoleto / $count * 100, 2, ",", "."); ?>%</span>
                  <h3 class="payment-count"><?= $vendasBoleto ?></h3>
                </div>
              </div>
            </div>

            <div class="stat-card payment-card">
              <div class="payment-header">
                <div class="payment-icon pix">
                  <img src="<?= url("themes/tema1/images/pix-text-success.png") ?>" alt="PIX" class="pix-icon">
                </div>
                <div class="payment-info">
                  <span class="payment-percentage"><?= number_format($vendasPix / $count * 100, 2, ",", "."); ?>%</span>
                  <h3 class="payment-count"><?= $vendasPix ?></h3>
                </div>
              </div>
            </div>
          </div>

          <!-- Segunda fila de cards -->
          <div class="stats-row">
            <div class="stat-card financial-card">
              <div class="stat-header">
                <span class="stat-label">Valor Vendas:</span>
              </div>
              <div class="stat-value">
                <h2 class="financial-value"><?= @$somaVendas ?></h2>
              </div>
            </div>

            <div class="stat-card financial-card">
              <div class="stat-header">
                <span class="stat-label">Tiket Médio:</span>
              </div>
              <div class="stat-value">
                <h2 class="financial-value"><?= @$somaVendas ? "R$ " . number_format($somaVendas / $count, 2, ",", ".") : "-"; ?></h2>
              </div>
            </div>

            <div class="stat-card financial-card">
              <div class="stat-header">
                <span class="stat-label">Valor Comissão:</span>
              </div>
              <div class="stat-value">
                <h2 class="financial-value">R$ <?= @number_format(@$somaProdutor->soma_produtor, 2, ",", ".") ?></h2>
              </div>
            </div>

            <div class="stat-card financial-card">
              <div class="stat-header">
                <span class="stat-label">Tiket Médio Comissão:</span>
              </div>
              <div class="stat-value">
                <h2 class="financial-value"><?= @$somaProdutor->soma_produtor ? "R$ " . number_format($somaProdutor->soma_produtor / $count, 2, ",", ".") : "-"; ?></h2>
              </div>
            </div>
          </div>
        </div>
      <?php endif; ?>

      <style>
        .legenda {
          width: 25px;
          height: 25px;
          margin: 0 10px;
          border-radius: 50%;
          display: inline-block;
        }
      </style>
      <div class="card card-body callout">
        <div class="row">

          <div class="col-12 d-flex">
            <div class="d-flex align-items-center">
              <div class="d-flex align-items-center">
                <span class="legenda bg-warning"></span> <small>AGUARDANDO PAGAMENTO </small>
              </div>
              <div class="d-flex align-items-center">
                <span class="legenda bg-info"></span> <small>EM ANÁLISE </small>
              </div>
              <div class="d-flex align-items-center">
                <span class="legenda bg-success"></span> <small>PAGA / COMPLETA </small>
              </div>
              <div class="d-flex align-items-center">
                <span class="legenda bg-danger"></span> <small>CANCELADA / DEVOLVIDA / ESTORNADA </small>
              </div>
              <div class="d-flex align-items-center">
                <span class="legenda bg-block"></span> <small>BLOQUEADA </small>
              </div>
            </div>

            <div class="exportar">
              Exportar:
              <a href="<?= url("vendas/exportar") ?>" class="badge badge-success text-white" target="_blank">xls</a>
            </div>
          </div>

          <div class="col-6">
            <a href="?update-status=true" class="btn btn-success float-right" role="button" style="text-decoration: none; color: white; display: none">Atualizar o status da listagem</a>
          </div>

        </div>

      </div>

      <div class="modern-info-alert">
        <div class="alert-content">
          <div class="alert-icon">
            <i class="fas fa-info-circle"></i>
          </div>
          <div class="alert-text">
            <?php if (!$pag_coprodutor) : ?>
              <strong>Atenção:</strong> Novas vendas levam alguns minutos para aparecerem nesse relatório, após serem realizadas no checkout.<br>
              Vendas do PIX levam alguns minutos para aparecerem a aprovação.
            <?php else : ?>
              <strong>Valores atualizados a cada hora</strong>
            <?php endif; ?>
          </div>
        </div>
      </div>

      <div class="modern-table-container">

      <div class="table-responsive-modern">
        <table class="modern-table">
            <thead>
              <tr>
                <th scope="col">Venda</th>
                <th scope="col">Comprador</th>
                <?php if ($section->getTipo() == 6) : ?>
                  <th scope="col">Vendedor</th>
                <?php elseif ($section->getTipo() == 1 || $section->getTipo() == 2 || $section->getTipo() == 3) : ?>
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

                  <?php if ($section->getTipo() == 1 || $section->getTipo() == 2 || $section->getTipo() == 3 || $section->getTipo() == 6) : ?>
                    <td>
                      <div style="font-weight: 600; margin-bottom: 4px;"><?= $venda->dataProdutor()->nome ?></div>
                      <div style="font-size: 12px; color: rgb(198, 200, 204)"><?= $venda->dataProdutor()->email ?></div>
                    </td>
                  <?php endif ?>

                  <?php if ($venda->plano) : ?>
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
                  <input type="hidden" name="rua" id="rua<?= $index ?>" value="<?= $venda->dataComprador()->rua_entrega ?>">
                  <input type="hidden" name="numero" id="numero<?= $index ?>" value="<?= $venda->dataComprador()->numero_entrega ?>">
                  <input type="hidden" name="complemento" id="complemento<?= $index ?>" value="<?= $venda->dataComprador()->complemento_entrega ?>">
                  <input type="hidden" name="bairro" id="bairro<?= $index ?>" value="<?= $venda->dataComprador()->bairro_entrega ?>">
                  <input type="hidden" name="cidade" id="cidade<?= $index ?>" value="<?= $venda->dataComprador()->cidade_entrega ?>">
                  <input type="hidden" name="estado" id="estado<?= $index ?>" value="<?= $venda->dataComprador()->estado_entrega ?>">
                  <input type="hidden" name="pais" id="pais<?= $index ?>" value="<?= $venda->dataComprador()->pais_entrega ?>">
                  <input type="hidden" name="cep" id="cep<?= $index ?>" value="<?= $venda->dataComprador()->cep_entrega ?>">

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

<style>
  #busca-avancada {
    display: none;
  }
</style>

<?= $v->start("scripts"); ?>
<script src="<?= url("themes/tema1/dist/js/modal.js") ?>"></script>
<script>
  $(document).ready(function() {

    // $("#busca-avancada").hide();

    var cont = 0;
    $("#btn-filtro").click(function() {
      $("#busca-avancada").toggle();

      if (cont == 0) {
        $("#btn-filtro").html("Menos Filtros");
        cont = 1;
      } else {
        $("#btn-filtro").html("Mais Filtros");
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
    //alert('clicou ' + i);
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
        //location.reload();
      }
    })
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
<?= $v->end(); ?>