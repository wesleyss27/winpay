<div class="ajax_load">
  <div class="ajax_load_box">
    <div class="ajax_load_box_circle"></div>
    <div class="ajax_load_box_title">Aguarde, carregando!</div>
  </div>
</div>
<!--
<div class="row">
  <div class="col-md-4">
    <div class="">
      <a href="<?= url("comissao") ?>" class="text-dark">
        <span>Disponível para saque</span>
        <h5 class="text-info">R$ <?= formataNumero( ! empty( $saldoPagarme->available_amount ) ? $saldoPagarme->available_amount / 100 : 0 ) ?></h5>
      </a>
    </div>
  </div>
</div>
<hr>
-->

<div class="row">
  <div class="col-md-4">
    <div class="card l-bg-orange-dark">
      <div class="card-statistic-3 p-4">
        <div class="card-icon card-icon-large">
          <i class="fas fa-dollar-sign" style="animation: flash 1.5s infinite;"></i>
        </div>
    
        <h5>Total em conta</h5>
       
        <h3>R$ <?= formataNumero($valorTotal) ?></h3>
        
        <div class="row">
          <div class="col-md-12">
            <span>Soma do valor disponivel mais recebimentos</span>
          </div>
        </div>

      </div>
    </div>
  </div>

  <style>
    @keyframes flash {
      0%, 100% { opacity: 1; }
      50% { opacity: 0.5; }
    }
  </style>

  <div class="col-md-4">
    <div class="card l-bg-green-dark">
      <div class="card-statistic-3 p-4">
        <div class="card-icon card-icon-large"><i class="fas fa-ticket-alt"></i></div>
        
        <h5>Disponível para saque</h5>
  
        <h3>R$ <?= formataNumero( ! empty( $saldoPagarme->available_amount ) ? $saldoPagarme->available_amount / 100 : 0 ) ?></h3>
         
        <div class="row">
          <div class="col-md-12">
            <span>Soma do valor disponivel mais recebimentos</span>
          </div>

        </div>
       

      </div>
    </div>
  </div>

  <div class="col-md-4">
    <div class="card l-bg-blue-dark">
      <div class="card-statistic-3 p-4">
      <div class="card-icon card-icon-large"><i class="fas fa-users"></i></div>

        <h5>Total cancelados</h5>

        <h3>R$ <?= formataNumero($valorCancelado) ?></h3>

        <div class="row">

          <div class="col-md-12">
            <span>Soma do valor disponivel mais recebimentos</span>
          </div>

        </div>
      </div>
    </div>
  </div>
</div>

<!-- LINHA 2 -->
<div class="row">
  <div class="col-md-4">
    <div class="card card-body">
      <div class="row align-items-center justify-content-center">
        <i class="fas fa-credit-card" style="font-size:30px; color:#009788"></i>

        <div class="col text-right">
          <h3>R$ <?= number_format(@$dados["valorCartaoPago"], 2, ",", ".") ?></h3>
          <h5 class="text-c-blue mb-0">
            <?php
            @$analitico[2] = array("Vendas Cart&atilde;o", @$dados["cartoes"], @$dados["valorCartao"], $p)
            ?><br>
            <?php
            if (@$dados["totalVendas"] > 0)
              $p = @$dados["cartoes"] / @$dados["totalVendas"] * 100;
            else
              $p = "0";
            echo number_format(@$p, 2, ",", ".");
            ?>% - <span class="text-muted">Total Cartão</span></h5>
        </div>
      </div>

      <hr>
      <div class="row align-items-center justify-content-center card-active">
        <div class="col-6">
          <h6 class="text-center m-b-10" style="color:#8BC24A"><span class="text-muted m-r-5">Aprovados: </span>
            <?php
            if (@$dados["cartoes"] > 0)
              $p = @$dados["cartoesPagos"] / @$dados["cartoes"] * 100;
            else
              $p = "0";
            echo number_format($p, 2, ",", ".");
            ?>%</h6>
          <div class="progress  mt-1" style="height: 8px;">
            <div class="progress-bar l-bg-wss" role="progressbar" style="width:100%;height:6px;" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100"></div>
          </div>
        </div>
        <div class="col-6">
          <h6 class="text-center  m-b-10" style="color:red"><span class="text-muted m-r-5">Cancelados: </span>
            <?php
            if (@$dados["cartoes"] > 0)
              $p = $dados["cartoesCancelados"] / @$dados["cartoes"] * 100;
            else
              $p = "0";
            echo number_format($p, 2, ",", ".");
            ?>%</h6>
          <div class="progress mt-1" style="height: 8px;">
            <div class="progress-bar l-bg-wss1" role="progressbar" style="width:100%;height:6px;" aria-valuenow="45" aria-valuemin="0" aria-valuemax="100"></div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Conversão boleto -->
  <div class="col-md-4">
    <div class="card card-body">
      <div class="row align-items-center justify-content-center">
        <i class="fas fa-barcode" style="font-size:35px; color:#3F51B5"></i>

        <div class="col text-right">
          <h3>R$ <?= number_format(@$dados["valorBoletoPago"], 2, ",", ".") ?></h3>
          <h5 class="text-c-blue mb-0">
            <?php
            $analitico[6] = array("Boletos", @$dados["boletos"], @$dados["valorBoleto"], @$p)
            ?><br>
            <?php
            if (@$dados["totalVendas"] > 0)
              $p = $dados["boletos"] / $dados["totalVendas"] * 100;
            else
              $p = "0";
            echo number_format($p, 2, ",", ".");
            ?>% - <span class="text-muted">Total Boletos</span></h5>
        </div>
      </div>

      <hr>
      <div class="row align-items-center justify-content-center card-active">
        <div class="col-6">
          <h6 class="text-center m-b-10" style="color:#8BC24A"><span class="text-muted m-r-5">Aprovados: </span>
            <?php
            if (@$dados["boletos"] > 0)
              $p = $dados["boletosPagos"] / $dados["boletos"] * 100;
            else
              $p = "0";
            echo number_format($p, 2, ",", ".");
            ?>%</h6>
          <div class="progress mt-1" style="height: 8px;">
            <div class="progress-bar l-bg-wss" role="progressbar" style="width:100%;height:6px;" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100"></div>
          </div>
        </div>
        <div class="col-6">
          <h6 class="text-center  m-b-10" style="color:red"><span class="text-muted m-r-5">Vencidos: </span>
            <?php
            if (@$dados["boletos"] > 0)
              $p = $dados["boletosCancelados"] / $dados["boletos"] * 100;
            else
              $p = "0";
            echo number_format($p, 2, ",", ".");
            ?>%</h6>
          <div class="progress mt-1" style="height: 8px;">
            <div class="progress-bar l-bg-wss1" role="progressbar" style="width:100%;height:6px;" aria-valuenow="45" aria-valuemin="0" aria-valuemax="100"></div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Conversão Pix -->
  <div class="col-md-4">
    <div class="card card-body">
      <div class="row align-items-center justify-content-center">
        <img src="<?= url("themes/tema1/images/pix-text-info.png"); ?>" style="width:30px"></i>

        <div class="col text-right">
          <h3>R$ <?= number_format(@$dados["valorPixPago"], 2, ",", ".") ?></h3>
          <h5 class="text-c-blue mb-0">
            <?php
            $analitico[2] = array("Vendas Cart&atilde;o", @$dados["pix"], @$dados["valorPix"], $p)
            ?><br>
            <?php
            if (@$dados["totalVendas"] > 0)
              $p = $dados["pix"] / $dados["totalVendas"] * 100;
            else
              $p = "0";
            echo number_format($p, 2, ",", ".");
            ?>% - <span class="text-muted">Total Pix</span></h5>
        </div>
      </div>

      <hr>
      <div class="row align-items-center justify-content-center card-active">
        <div class="col-6">
          <h6 class="text-center m-b-10" style="color:#8BC24A"><span class="text-muted m-r-5">Aprovados: </span>
            <?php
            if (@$dados["pix"] > 0)
              $p = $dados["pixPagos"] / $dados["pix"] * 100;
            else
              $p = "0";
            echo number_format($p, 2, ",", ".");
            ?>%</h6>
          <div class="progress">
            <div class="progress-bar progress-c-blue" role="progressbar" style="width:50%;height:6px;" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100"></div>
          </div>
        </div>
        <div class="col-6">
          <h6 class="text-center  m-b-10" style="color:red"><span class="text-muted m-r-5">Cancelados: </span>
            <?php
            if (@$dados["pix"] > 0)
              $p = $dados["pixCancelados"] / $dados["pix"] * 100;
            else
              $p = "0";
            echo number_format($p, 2, ",", ".");
            ?>%</h6>
          <div class="progress">
            <div class="progress-bar progress-c-green" role="progressbar" style="width:50%;height:6px;" aria-valuenow="45" aria-valuemin="0" aria-valuemax="100"></div>
          </div>
        </div>
      </div>
    </div>
  </div>

</div>

<div class="row">
  <div class="col-md-4">
    <div class="card card-body">
      <h5>Tentativas de compras</h5>
      <h3>R$ <?= formataNumero($valorPendente) ?></h3>

      <hr>
      <div class="row">
        <div class="col-md-6">
          <span class="text-muted">Total: <?= $pendenteVendas ?></span>
        </div>

        <div class="col-md-6">
          <span class="text-muted">Ticket: R$ <?= formataNumero($ticketPendente) ?></span>
        </div>
      </div>

    </div>
  </div>

  <div class="col-md-4">
    <div class="card card-body">
      <h5>Estornos e Chargebacks</h5>

      <hr>
      <div class="row">
        <div class="col-6">
          <span class="text-muted">Estornos: <?= $estornoVendas ?></span>
        </div>

        <div class="col-6">
          <span class="text-muted">R$ <?= formataNumero($valorEstorno) ?></span>
        </div>
      </div>

      <div class="row">
        <div class="col-6">
          <span class="text-muted">Chargebacks: <?= $chargebackVendas ?></span>
        </div>

        <div class="col-6">
          <span class="text-muted">R$ <?= formataNumero($valorChargeback) ?></span>
        </div>
      </div>
    </div>
  </div>

  <div class="col-md-4">
    <div class="card card-body">
      <h5>Abandono de checkout</h5>
      <!-- <h3>R$ <?= formataNumero($valorPendente) ?></h3> -->

      <hr>
      <div class="row">
        <div class="col-md-6">
          <span class="text-muted">Total: <?= $abandonadoVendas ?></span>
        </div>

        <div class="col-md-6">
          <!-- <span class="text-muted">Ticket: R$ <?= formataNumero($ticketPendente) ?></span> -->
        </div>
      </div>

    </div>
  </div>

</div>