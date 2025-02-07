<?php
$v->layout("_theme");
//$pendente->soma -= $liberado->antecipado;
if ($_SESSION["tipo"] == 6) {
  header("Location: " . url("vendas"));
}
?>

<div class="content-wrapper">
  <br />

  <section class="content">

    <?= $section->getTipoSecaoLayout() ?>

    <?php

    $taxa = number_format($liberado * ($antecipacao->taxa_antecipacao / 100), 2, ".", "");
    $liquido = $liberado - $taxa;

    ?>

    <!-- ROW -->
    <div class="row">

      <div class="col-lg-4 col-12">
        <!-- small box -->
        <div class="card card-body">
          <h5>A receber</h5>
          <h3><?= @$pendente->soma ? "R$ " . number_format($pendente->soma, 2, ",", ".") : "R$ 0,00"; ?></h3>

        </div>
      </div>
      <!-- ./col -->

      <div class="col-lg-4 col-12">
        <!-- small box -->
        <div class="card card-body">
          <h5>Disponível</h5>
          <h3><?= @$saldo ? "R$ " . number_format($saldo->valor, 2, ",", ".") : "R$ 0,00"; ?></h3>

        </div>
        <!-- small box -->
      </div>
      <!-- ./col -->

      <div class="col-lg-4 col-12">
        <?php if ($_SESSION["id"] == 1329) :
          $liquido = 0;
        endif ?>
        <!-- small box -->
        <div class="card card-body">
          <h5>Liberado</h5>
          <h3><?= @$liquido ? "R$ " . number_format($liquido, 2, ",", ".") : "R$ 0,00"; ?>*</h3>

          <?php if ($liquido != 0) : ?>
            <hr>
            <a href="#" id="antecipar" data-toggle="modal" data-target="#modal-lg" style="font-family:inter">Antecipar <i class="fas fa-arrow-circle-right"></i></a>
          <?php endif ?>
        </div>
        <!-- small box -->
      </div>
      <!-- ./col -->

    </div>
    <!-- /.row -->

    <?php if ($liquido != 0) : ?>
      <div class="card card-body">
        *valor liberado (R$ <?= number_format($liberado, 2, ",", ".") ?>)
        menos a taxa de antecipação (<?= number_format($antecipacao->taxa_antecipacao, 2, ",", ".") ?>% -
        R$ <?= number_format($taxa, 2, ",", ".") ?>)

        <br> (R$ <?= number_format($liberado, 2, ",", ".") ?> -
        R$ <?= number_format($taxa, 2, ",", ".") ?> = R$ <?= number_format($liquido, 2, ",", ".") ?>)
      </div>
    <?php endif ?>

    <!-- FIM ROW -->

    <!--  ASSINATURA -->
    <?php
    if ($_SESSION["id"] == 1329) :
      // DESCOBRIR O VALOR DE CADA PARCELA E DO TOTAL QUE FALTA
      function falta($inicio, $fim, $periodo)
      {
        $periodicidade = array(
          "Semanal"       => [7, "days"],
          "Mensal"        => [30, "months"],
          "Bimestral"     => [60, "months"],
          "Trimestral"    => [90, "months"],
          "Semestral"     => [180, "months"],
          "Anual"         => [365, "years"],
          "Trienal"       => [1095, "years"]
        );

        $data_inicio = new DateTime($inicio);
        $data_fim = new DateTime($fim);

        // Resgata diferença entre as datas
        $dateInterval = $data_inicio->diff($data_fim);

        $falta = $dateInterval->days / $periodicidade[$periodo][0];
        return floor($falta);
      }

      if ($assinaturas) {
        $j = 0;
    ?>
        <hr>
        <h4>Próximos lançamentos de assinaturas</h4>
        <div class="card card-body" style="position: relative; overflow: scroll; height: 300px">
          <table class="table">
            <thead>
              <tr class="fixed">
                <th scope="col">Assinatura antecipada</th>
                <th scope="col">Assinante</th>
                <th scope="col">Parcelas antecipadas</th>
                <th scope="col">Valor antecipado</th>
              </tr>
            </thead>

            <tbody>
              <?php
              foreach ($assinaturas as $assinatura) {
                if ($assinatura->data_limite) {
                  $venda = $vendas->findById($assinatura->venda);
                  // $limite = $plano->maximo_recorrencia * @$periodicidade[$plano->periodo][0];

                  // QUANTO TEMPO FALTA
                  $falta = falta($assinatura->proxima_cobranca, $assinatura->data_limite, $assinatura->recorrencia);

                  if ($falta >= 4) {
                    $falta50 = floor($falta / 2);
                    $total_restante = $assinatura->valor_plano * $falta;
                    $restante50 = $assinatura->valor_plano * $falta50;

                    $taxa_transacao = $venda->dataProdutor()->taxa_assinatura / 100;
                    $valor_liquido_parcela = $assinatura->valor_plano - ($assinatura->valor_plano * $taxa_transacao);
                    $valor_liquido_restante = $valor_liquido_parcela * $falta50;
                    $valor_liquido_total = $valor_liquido_parcela * $falta;

                    // SOMA COMISSÕES
                    $coproducao = $coprodutor->find("produto = $assinatura->produto AND aceito = 1")->fetch(true);
                    $coproducaoCount = $coprodutor->find("produto = $assinatura->produto  AND aceito = 1")->count();
                    $somaCoprodutor = 0;
                    $i = 1;
                    if ($coproducao) {
                      $valorOcta = $valor_liquido_restante;
                      // echo $valorOcta;
                      foreach ($coproducao as $coprodutor) {
                        if ($coprodutor->tipo_comissao == 0) {
                          $comissao_cop = number_format(($assinatura->valor_plano - $valorOcta) * ($coprodutor->comissao / 100), 2, ".", "");
                        } else {
                          $comissao_cop = number_format($coprodutor->comissao, 2, ".", "");
                        }
                        @$somaCoprodutor += $comissao_cop;
                        // echo $somaCoprodutor;
                        $i++;
                      }
                    }

                    $resto = $valor_liquido_restante - @$somaCoprodutor - $assinatura->comissao_afiliado;
                    $resto = number_format($resto, 2, ".", "");

                    $valor_assinatura[$j]["assinatura"] = $assinatura->id;
                    $valor_assinatura[$j]["parcelas"] = $falta50;
                    $valor_assinatura[$j]["valor"] = $resto;
                    $j++;

                    echo "
              <tr>
                <td>#$assinatura->id</td>
                <td>" . $assinatura->dataComprador()->nome . "</td>
                <td>$falta50</td>
                <td>R$ " . number_format($resto, 2, ",", ".") . "</td>
              </tr>
              ";

                    @$antecipa += $resto;
                    @$proximos += $valor_liquido_total;
                  }
                }
              }
              $taxa_assinatura = number_format(@$antecipa * 0.0549, 2, ".", "");
              @$antecipado = $antecipa ? $antecipa - ($antecipa * 0.0549) : 0;

              ?>
            </tbody>
          </table>
        </div>
      <?php } ?>
      <hr>

      <div class="card card-body">
        <div class="row">
          <div class="col-md-2">
            <div class="small-box">
              <div class="inner">
                Próximos valores <br>
                <b>R$ <?= number_format(@$proximos, 2, ",", ".") ?></b>
              </div>
            </div>

          </div>
          <!-- <div class="col-md-2">
            <div class="small-box bg-azul-octa">
              <div class="inner">
                Liberado <br>
                <b>R$ <?= number_format(@$antecipado, 2, ",", ".") ?></b>
              </div>
              <?php if (@$antecipado > 0) : ?>
                <a href="#" id="antecipar-assinatura" class="small-box-footer" data-toggle="modal" data-target="#modal-assinatura" style="font-family:inter">Antecipar <i class="fas fa-arrow-circle-right"></i></a>
              <?php endif ?>
            </div>

          </div> -->
        </div>
      </div>

      <div class="card card-body">
        <?php if ($assinaturasRestante) :
          $valor = 0;
          foreach ($assinaturasRestante as $assinatura) {
            $taxa_transacao = $assinatura->dataProdutor()->taxa_assinatura / 100;

            $falta = falta($assinatura->proxima_cobranca, $assinatura->data_limite, $assinatura->recorrencia);
            $falta -= $assinatura->parcelas_antecipadas;

            $valor += ($assinatura->valor_plano - ($assinatura->valor_plano * $taxa_transacao)) * $falta;
            // echo $taxa_transacao . "<br>";
          }

          // $valor = $valor - ($valor * 0.0549);
          echo "Valores restantes de assinaturas que foram antecipadas";
          echo "<b>R$ " . number_format($valor, 2, ",", ".") . "</b>";

          if (@$assinaturasAntecipadas) :
            echo "<br>Valor já antecipado: ";
            echo "<b>R$ " . number_format($assinaturasAntecipadas->antecipado - ($assinaturasAntecipadas->antecipado * 0.0549), 2, ",", ".") . "</b>";

            echo "<br>Em débito: ";
            echo "<b>R$ " . number_format(-$devendo->devendo, 2, ",", ".") . "</b>";
          endif;
        ?>

        <?php else : ?>
          <p>Você não possui antecipações futuras</p>
        <?php endif; ?>
      </div>
    <?php endif; ?>
    <!-- FIM ASSINATURA -->
  </section>
  <!-- /.content -->
</div>
<!-- /.content-wrapper -->

<!-- antecipar -->
<div class="modal fade" id="modal-lg">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Antecipar comissão?</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>

      <div class="modal-body">
        Será cobrada uma taxa de <b><?= number_format($antecipacao->taxa_antecipacao, 2, ",", ".") ?>% </b><br>
        Total liberado: R$ <?= number_format($liberado, 2, ",", ".") ?> <br>
        Total líquido: R$ <?= number_format($liberado, 2, ",", ".") ?> -
        R$ <?= number_format($taxa, 2, ",", ".") ?> = <b> R$ <?= number_format($liquido, 2, ",", ".") ?></b> <br>
      </div>

      <div class="modal-footer justify-content-between">
        <button type="button" class="btn btn-danger" data-dismiss="modal" id="fechar"> Fechar</button>
        <button type="button" class="btn btn-danger" data-dismiss="modal" id="nao"> NÃO</button>
        <button class="btn btn-success" id="salvar">SIM</button>
      </div>

    </div>
  </div>
</div>
<!-- end cantecipar -->

<!-- assinatura -->
<div class="modal fade" id="modal-assinatura">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Antecipar lançamentos?</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>

      <div class="modal-body">
        Será cobrada uma taxa de <b><?= number_format($antecipacao->taxa_antecipacao, 2, ",", ".") ?>% </b><br>
        Total liberado: R$ <?= number_format($antecipa, 2, ",", ".") ?> <br>
        Total líquido: R$ <?= number_format($antecipa, 2, ",", ".") ?> -
        R$ <?= number_format($taxa_assinatura, 2, ",", ".") ?> = <b> R$ <?= number_format($antecipado, 2, ",", ".") ?></b> <br>

        <!-- < ?= var_dump($valor_assinatura) ?> -->
        <form action="">
          <input type="hidden" name="data_antecipacao" id="data_antecipacao" value="hj">
          <?php if (@$valor_assinatura) : $a = 0;
            foreach ($valor_assinatura as $valor) : ?>
              <input type="hidden" name="valor_antecipado[<?= $a ?>]" id="valor_antecipado" value="<?= number_format($valor["valor"], 2, ".", "") ?>">
              <input type="hidden" name="parcelas_antecipadas[<?= $a ?>]" id="parcelas_antecipadas" value="<?= $valor["parcelas"] ?>">
              <input type="hidden" name="assinatura[<?= $a ?>]" id="assinatura" value="<?= $valor["assinatura"] ?>">
          <?php $a++;
            endforeach;
          endif; ?>
          <input type="hidden" name="total_antecipado" id="total_antecipado" value="<?= $antecipado ?>">
          <input type="hidden" name="taxa_antecipacao" id="taxa_antecipacao" value="<?= $taxa_assinatura ?>">
        </form>
      </div>

      <div class="modal-footer justify-content-between">
        <button type="button" class="btn btn-danger" data-dismiss="modal" id="fecharAss"> Fechar</button>
        <button type="button" class="btn btn-danger" data-dismiss="modal" id="naoAss"> NÃO</button>
        <button class="btn btn-success" id="salvarAss">SIM</button>
      </div>

    </div>
  </div>
</div>
<!-- end assinatura -->

<?= $v->start("scripts") ?>
<script>
  $("#fechar").hide();
  $("#fecharAss").hide();

  $("#salvar").click(function(e) {
    e.preventDefault();
    antecipar("<?= $router->route("appcomissao.antecipar") ?>");
  });

  $("#salvarAss").click(function(e) {
    e.preventDefault();
    antecipar("<?= $router->route("appcomissao.anteciparAssinatura") ?>", $("form").serialize());
  });

  function antecipar(url, dados = null) {

    $.ajax({

      url: url,
      type: "POST",
      data: dados,
      //datatype: "json",
      beforeSend: function() {
        console.log("atualizando");
      },
      error: function() {
        console.log("O servidor não conseguiu processar o pedido");
      },
      success: function(callback) {
        console.log("callback ", callback);
        $(".modal-body").html(callback);
        $("#fechar").show();
        $("#fecharAss").show();
        $("#nao").hide();
        $("#naoAss").hide();
        $("#salvar").hide();
        $("#salvarAss").hide();

        $("#fechar, #fecharAss").click((e) => {
          e.preventDefault();
          location.reload();
        });
      }

    });
  }
</script>
<?= $v->end() ?>