<?php

use Source\App\Checkout1\PagarMe;
use Source\Models\Afiliados\Afiliacao;

 $v->layout("_theme"); ?>

<?= $v->start("links"); ?>
<link rel="stylesheet" href="<?= url("themes/tema1/dist/css/modal.css") ?>">
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
        <div class="row" style="margin: 0 0 20px 0">
          <div class="col-md-3 center">
            <div class="card card-body" style="border-bottom: 3px solid #3aaa3a">
              Total de vendas: <br>
              <h4><?= $count; ?></h4>
            </div>
          </div>

          <div class="col-md-3 center">
            <div class="card card-body" style="border-bottom: 3px solid #3aaa3a">
              Sua categoria: <br>
              <h4><?php echo $nivel[@$nivelUsuario]; ?></h4>
            </div>
          </div>

          <div class="col-md-2 center">
            <div class="card card-body" style="border-bottom: 3px solid #3aaa3a">

              <div class="row">
                <div class="col-md-3 col-3" style="margin: 15px 0 0 0;">
                  <i class="fas fa-credit-card text-success" style="font-size: 30px;"></i>
                </div>

                <div class="col-md-9 col-9">
                  <span class="badge bg-warning"><?= number_format($vendasCartao / $count * 100, 2, ",", "."); ?>%</span><br>
                  <h4><?= $vendasCartao ?></h4>
                </div>
              </div>

            </div>
          </div>

          <div class="col-md-2 center">
            <div class="card card-body" style="border-bottom: 3px solid #3aaa3a">

              <div class="row">
                <div class="col-md-3 col-3" style="margin: 15px 0 0 0;">
                  <i class="fas fa-barcode text-success" style="font-size: 30px;"></i>
                </div>

                <div class="col-md-9 col-9">
                  <span class="badge bg-warning"><?= number_format($vendasBoleto / $count * 100, 2, ",", "."); ?>%</span><br>
                  <h4><?= $vendasBoleto ?></h4>
                </div>
              </div>

            </div>
          </div>

          <div class="col-md-2 center">
            <div class="card card-body" style="border-bottom: 3px solid #3aaa3a">

              <div class="row">
                <div class="col-md-3 col-3" style="margin: 15px 0 0 0;">
                  <img src="<?= url("themes/tema1/images/pix-text-success.png") ?>" title="Pix" width="30px"><br>
                </div>

                <div class="col-md-9 col-9">
                  <span class="badge bg-warning"><?= number_format($vendasPix / $count * 100, 2, ",", "."); ?>%</span><br>
                  <h4><?= $vendasPix ?></h4>
                </div>
              </div>

            </div>

          </div>
        </div>

        <div class="row" style="margin: 0 0 20px 0">
          <div class="col-md-3 center">
            <div class="card card-body" style="border-bottom: 3px solid #3aaa3a">
              Valor Vendas: <br>
              <!-- <h4>< ?= @$somaVendas ? "R$ " . @number_format(@$somaVendas, 2, ",", ".") : "-"; ?></h4> -->
              <?= @$somaVendas ?>
            </div>

          </div>

          <div class="col-md-3 center">

            <div class="card card-body" style="border-bottom: 3px solid #3aaa3a">
              Tiket Médio: <br>
              <h4><?= @$somaVendas ? "R$ " . number_format($somaVendas / $count, 2, ",", ".") : "-"; ?></h4>
            </div>

          </div>

          <!-- valor comissão -->
          <div class="col-md-3 center">
            <div class="card card-body" style="border-bottom: 3px solid #3aaa3a">
              Valor Comissão: <br>
              <h4>R$ <?= @number_format(@$somaProdutor->soma_produtor, 2, ",", ".") ?></h4>
            </div>
          </div>

          <div class="col-md-3 center">
            <div class="card card-body" style="border-bottom: 3px solid #3aaa3a">
              Tiket Médio Comissão: <br>
              <h4><?= @$somaProdutor->soma_produtor ? "R$ " . number_format($somaProdutor->soma_produtor / $count, 2, ",", ".") : "-"; ?></h4>
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

      <div class="alert bg-info-outline">
        <i class="fas fa-info-circle text-info" style="font-size: 20px"></i>
        <?php if (!$pag_coprodutor) : ?>
          Atenção: Novas vendas levam alguns minutos para aparecerem nesse relatório, após serem realizadas no checkout. <br>
          Vendas do PIX levam alguns minutos para aparecerem a aprovação.
        <?php else : ?>
          Valores atualizados a cada hora
        <?php endif; ?>
      </div>

      <div class="card">

        <div class="card-body p-0" style="overflow-x: auto">
          <table class="table table-striped projects" style="font-size:13px">
            <thead>
              <tr style="font-family:inter">
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

                <tr style="font-family:inter">
                  <td>
                    <?php if ($venda->upsell) : ?>
                      <span class="badge bg-azul-dark">Upsell</span>
                    <?php endif; ?>

                    <?php if (@$venda->planos_order_bump && @$venda->preco_order_bump) : ?>
                      <span class="badge bg-verde-octa2">Order Bump</span>
                    <?php endif; ?>

                    <?php if ($venda->assinatura) : ?>
                      <span class="badge bg-verde-octa3">Assinatura</span>
                    <?php endif; ?>

                    <?php if ($venda->adquirente != "redrocket") : ?>
                      <span class="badge btn-finalizar-tarde">Drop</span>
                    <?php endif; ?>

                    <?php if ($venda->ecommerce == 1) : ?>
                      <span class="badge bg-azul-octa">Ecommerce</span>
                    <?php endif; ?>

                    <a href="#" data-toggle="modal" data-target="#myModal<?= $venda->id; ?>">
                      <small>#<?= $venda->id; ?></small> <br>
                      <small>#<?= $venda->transacao; ?></small>
                    </a>
                  </td>

                  <?php if ($section->getTipo() == 1 || $section->getTipo() == 2 || $section->getTipo() == 3 || $section->getTipo() == 6) : ?>
                    <td>
                      <?= $venda->endereco ? $venda->dataEndereco()->nome : $venda->dataComprador()->nome ?><br>
                      <?= $venda->endereco ? $venda->dataEndereco()->email : $venda->dataComprador()->email ?><br>
                      <?= $venda->endereco ? $venda->dataEndereco()->telefone : $venda->dataComprador()->telefone ?> <a href='https://api.whatsapp.com/send?phone=55<?= $venda->endereco ? $venda->dataEndereco()->telefone : $venda->dataComprador()->telefone ?>' title='Entrar em contato pelo whatsapp' target='_blank'><i class="fab fa-whatsapp" style="color: green"></i></a><br>
                    </td>
                  <?php elseif ($pag_coprodutor == true || $section->getTipo() == 4 || ($section->getTipo() == 5 && @$af1->liberar_comprador == 1)) : ?>
                    <td>
                      <?= $venda->endereco ? $venda->dataEndereco()->nome : $venda->dataComprador()->nome ?><br>
                      <?= $venda->endereco ? $venda->dataEndereco()->email : $venda->dataComprador()->email ?><br>
                      <?= $venda->endereco ? $venda->dataEndereco()->telefone : $venda->dataComprador()->telefone ?> <a href='https://api.whatsapp.com/send?phone=55<?= $venda->endereco ? $venda->dataEndereco()->telefone : $venda->dataComprador()->telefone ?>' title='Entrar em contato pelo whatsapp' target='_blank'><i class="fab fa-whatsapp" style="color: green"></i></a><br>
                    </td>
                  <?php else : ?>
                    <td>Dados ocultado pelo produtor</td>
                  <?php endif; ?>

                  <?php if ($section->getTipo() == 1 || $section->getTipo() == 2 || $section->getTipo() == 3 || $section->getTipo() == 6) : ?>
                    <td>
                      <?= $venda->dataProdutor()->nome ?><br>
                      <?= $venda->dataProdutor()->email ?>
                    </td>
                  <?php endif ?>

                  <?php if ($venda->plano) : ?>
                    <td><a href="<?= url("produtos/detalhe/$venda->produto") ?>"><?= $venda->dataProduto()->nome ?><br><?= $venda->dataPlano()->nome ?></a></td>
                  <?php else : ?>
                    <td><a href="<?= url("produtos/detalhe/$venda->produto") ?>"><?= $venda->dataProduto()->nome ?></a></td>
                  <?php endif ?>

                  <td>
                    <?php if ($venda->afiliado) :
                      echo $venda->dataAfiliado()->nome;
                    endif;
                    ?>
                  </td>

                  <?php if (($_SESSION["tipo"] == 1 || $_SESSION["tipo"] == 2 || $_SESSION["tipo"] == 3) && !$pag_coprodutor) : ?>
                    <td>R$ <?= number_format($venda->valor_bruto, 2, ",", "."); ?></td>
                  <?php else : ?>
                    <td>R$ <?= number_format($valorComissao, 2, ",", "."); ?></td>
                  <?php endif ?>
                  <td>
                    <?php if ($venda->forma_pagamento == 1) : ?>
                      <h4><i class="fa fa-credit-card <?= @$statusT[$venda->status_transacao] ?>" title="<?= $status_transacao[$venda->status_transacao] ?>"></i></h4>
                    <?php elseif ($venda->forma_pagamento == 2) : ?>
                      <h4><i class="fa fa-barcode <?= @$statusT[$venda->status_transacao] ?>" title="<?= $status_transacao[$venda->status_transacao] ?>"></i></h4>
                    <?php elseif ($venda->forma_pagamento == 3) : ?>
                      <img src="<?= url("themes/tema1/images/pix-" . @$statusT[$venda->status_transacao] . ".png") ?>" title="<?= $status_transacao[$venda->status_transacao] ?>" width="20px"><br>
                    <?php endif ?>
                    <!-- <small>< ?= $status_transacao[$venda->status_transacao] ?></small> -->
                  </td>
                  <td><?= formataDataHora($venda->created_at); ?>
                  </td>
                  <td><?php
                      if ($venda->data_finalizada != "") {
                        echo formataDataHora($venda->data_finalizada);
                      } elseif ($venda->status_transacao == 5 || $venda->status_transacao == 6 || $venda->status_transacao == 7) {
                        echo formataDataHora($venda->data_ultimo_evento);
                      }
                      ?>
                  </td>

                  <td><a class="btn btn-warning btn-sm" data-toggle="modal" data-target="#myModal<?= $venda->id; ?>"><i class="fas fa-plus"></i></a></td>
                  <!--td><a class="modalx-slideUp_open" href="#modalx-slideUp<?= $venda->id; ?>"><i class="fas fa-edit"></i></a></td-->

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