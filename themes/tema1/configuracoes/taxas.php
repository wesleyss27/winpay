<?php
$v->layout("_theme");

if ($_SESSION["tipo"] != 1) {
  header("Location: " . URL_BASE);
}

$ativo = array(
  0 => "NÃO",
  1 => "SIM"
);
?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h4 style="font-family:inter">Taxas</h4>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="./">Início</a></li>
            <li class="breadcrumb-item">Taxas</li>

          </ol>
        </div>
      </div>
    </div><!-- /.container-fluid -->
  </section>

  <!-- Main content -->
  <section class="content">

    <!-- Default box -->
    <div class="card">

      <div class="card-bod">
        <?php if ($taxa) : ?>
          <div style="overflow-x: auto;">
            <table class="table table-striped projects" style="font-size:13px">
              <thead>
                <tr style="font-family:inter">
                  <th scope="col">Parcelamento</th>
                  <th scope="col">Taxa REDROCKET</th>
                  <th scope="col">Taxa Assinatura</th>
                  <th scope="col">Valor Transação</th>
                  <th scope="col">Última Alteração</th>
                  <th scope="col">Ação</th>
                </tr>
              </thead>

              <tbody>
                <tr style="font-family:inter">
                  <td><?= $taxa->parcelamento; ?>%</td>
                  <td><?= $taxa->taxa_redrocket; ?>%</td>
                  <td><?= $taxa->taxa_assinatura; ?>%</td>
                  <td>R$ <?= number_format($taxa->valor_transacao, 2, ",", "."); ?></td>
                  <td><?= $taxa->dataUsuario()->nome; ?></td>
                  <td>
                    <a class="btn btn-warning btn-sm" data-toggle="modal" onclick="editar(1)" data-target="#ideditar"><i class="fas fa-edit"></i></a>
                  </td>
                </tr>
                <tr>
                  <td>
                    <b>Tempo Saque</b><br>
                    <?= $taxa->tempo_saque; ?> dias
                  </td>
                  <td>
                    <b>Valor Mínimo</b><br>
                    R$ <?= number_format($taxa->saque_minimo, 2, ",", "."); ?>
                  </td>
                  <td>
                    <b>Tempo Saque Pix</b><br>
                    <?= $taxa->tempo_pix; ?> dias
                  </td>
                  <td>
                    <b>Tempo Saque Boleto</b><br>
                    <?= $taxa->tempo_boleto; ?> dias
                  </td>
                  <td>
                    <b>Taxa Drop</b><br>
                    <?= $taxa->taxa_drop; ?>%
                  </td>
                </tr>

              </tbody>
            </table>

          <?php
        else :
          ?>
            <h4>Não existem taxas cadastradas!</h4>
          <?php
        endif;
          ?>
          </div>

          <!-- cadastar -->
          <!-- end cadastrar -->

          <!-- editar -->
          <div class="modal fade" id="ideditar">

            <div class="modal-dialog modal-xl">
              <div class="modal-content">

                <div class="card-header">
                  <h3 class="card-title"><i class="fa fa-info-circle"></i> Configurações de Taxas</h3>
                  <button type="button" class="close" data-dismiss="modal"> &times;</button>
                </div>

                <form action="#" method="post" style="font-family:inter; font-size:12px">
                  <div class="modal-body" style="background: #f4f6f9">

                    <input type="hidden" name="id" id="id" value="<?= $taxa->id ?>">

                    <div class="card card-body">
                      <div class="row">
                        <div class="col-md-3">
                          <label>Taxa de parcelamento %</label>
                          <input type="text" name="parcelamento" id='parcelamento' class="form-control input-lg" value="<?= $taxa->parcelamento ?>">
                        </div>

                        <div class="col-md-3">
                          <label>Taxa REDROCKET</label>
                          <input type="text" name="taxa_redrocket" id='taxa_redrocket' class="form-control input-lg" value="<?= $taxa->taxa_redrocket ?>">
                        </div>

                        <div class="col-md-3">
                          <label>Taxa Assinatura</label>
                          <input type="text" name="taxa_assinatura" id='taxa_assinatura' class="form-control input-lg" value="<?= $taxa->taxa_assinatura ?>">
                        </div>

                        <div class="col-md-3">
                          <label>Valor transação R$</label>
                          <input type="text" name="valor_transacao" id='valor_transacao' class="form-control input-lg" value="<?= $taxa->valor_transacao ?>">
                        </div>
                      </div>
                    </div>

                    <hr>
                    <div class="card card-body">
                      <div class="row">
                        <div class="col-md-3">
                          <label>Tempo de saque geral</label>
                          <input type="text" name="tempo_saque" id='tempo_saque' class="form-control input-lg" value="<?= $taxa->tempo_saque ?>">
                        </div>

                        <div class="col-md-3">
                          <label>Saque mínimo </label>
                          <input type="text" name="saque_minimo" id='saque_minimo' class="form-control input-lg" value="<?= $taxa->saque_minimo ?>">
                        </div>

                        <div class="col-md-3">
                          <label>Tempo de saque pix </label>
                          <input type="text" name="tempo_pix" id='tempo_pix' class="form-control input-lg" value="<?= $taxa->tempo_pix ?>">
                        </div>

                        <div class="col-md-3">
                          <label>Tempo de saque boleto </label>
                          <input type="text" name="tempo_boleto" id='tempo_boleto' class="form-control input-lg" value="<?= $taxa->tempo_boleto ?>">
                        </div>
                      </div>
                    </div>

                    <hr>
                    <div class="card card-body">
                      <div class="row">
                        <div class="col-md-3">
                          <label>Taxa Drop </label>
                          <input type="text" name="taxa_drop" id='taxa_drop' class="form-control input-lg" value="<?= $taxa->taxa_drop ?>">
                        </div>

                        <div class="col-md-3">
                          <label>Incluir Também</label>
                          <select name="usuarios" id="usuarios" class="form-control input-lg">
                            <option value="novos">Novos Usuários</option>
                            <option value="antigos">Usuários Antigos</option>
                            <option value="produtores">Produtores Antigos</option>
                          </select>
                        </div>

                        <div class="col-md-3">
                          <label>Taxa Ecommerce %</label>
                          <input type="text" name="taxa_ecommerce" id='taxa_ecommerce' class="form-control input-lg" value="<?= $taxa->taxa_ecommerce ?>">
                        </div>

                        <div class="col-md-3">
                          <label>Valor Assinatura Ecommerce R$ </label>
                          <input type="text" name="valor_ecommerce" id='valor_ecommerce' class="form-control input-lg" value="<?= $taxa->valor_ecommerce ?>">
                        </div>
                      </div>
                    </div>

                    <hr>
                    <div class="card card-body">
                      <div class="row">
                        <div class="col-md-3">
                          <label>Taxa Saque PagarMe </label>
                          <input type="text" name="taxa_saque_pagarme" id='taxa_saque_pagarme' class="form-control input-lg" value="<?= $taxa->taxa_saque_pagarme ?>">
                        </div>

                        <div class="col-md-3">
                          <label>Valor Mínimo para Saque </label>
                          <input type="text" name="vlr_min_saque" id='vlr_min_saque' class="form-control input-lg" value="<?= $taxa->vlr_min_saque ?>">
                        </div>

                      </div>
                    </div>
                  </div>
                  <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-danger" data-dismiss="modal" id="cancelar">Cancelar</button>
                    <button class="btn btn-primary" id="salvar">Salvar</button>
                  </div>
                </form>
              </div>

            </div>

          </div>
          <!-- end editar -->
      </div>

    </div>

    <!-- Default box -->
    <h3>Antecipação</h3>
    <div class="card">
      <br>

      <div class="card-bod">

        <?php if ($antecipacao) : ?>
          <div style="overflow-x: auto;">
            <table class="table table-striped projects" style="font-size:13px">
              <thead>
                <tr style="font-family:inter">
                  <th scope="col">A partir de</th>
                  <th scope="col">Periodo</th>
                  <th scope="col">Porcentagem</th>
                  <th scope="col">Taxa antecipação</th>
                  <th scope="col">Última Alteração</th>
                  <th scope="col">Ação</th>
                </tr>
              </thead>


              <tbody>
                <tr style="font-family:inter">
                  <td><?= $antecipacao->dias_antecipar; ?> dias</td>
                  <td><?= $antecipacao->periodo_antecipacao; ?> dias</td>
                  <td><?= $antecipacao->porcentagem; ?>%</td>
                  <td><?= $antecipacao->taxa_antecipacao; ?>%</td>
                  <td><?= $antecipacao->dataUsuario()->nome; ?></td>
                  <td>
                    <a class="btn btn-warning btn-sm" data-toggle="modal" onclick="editarAntecipacao(1)" data-target="#antecipar"><i class="fas fa-edit"></i></a>
                  </td>
                </tr>

              </tbody>
            </table>

          <?php
        else :
          ?>
            <h4>Não existem taxas cadastradas!</h4>
          <?php
        endif;
          ?>

          <!-- cadastar -->
          <!-- end cadastrar -->

          <!-- editar -->
          <div class="modal fade" id="antecipar">

            <div class="modal-dialog modal-xl">
              <div class="modal-content">

                <div class="card-header">
                  <h3 class="card-title"><i class="fa fa-info-circle"></i> Configurações de Antecipaçao</h3>
                  <button type="button" class="close" data-dismiss="modal"> &times;</button>
                </div>

                <form action="#" method="post" style="font-family:inter; font-size:12px">
                  <div class="modal-body" style="background: #f4f6f9">

                    <input type="hidden" name="idA" id="idA">

                    <div class="card card-body">
                      <div class="row">
                        <div class="col-md-3">
                          <label>A partir de</label>
                          <input type="text" name="dias_antecipar" id='dias_antecipar' class="form-control input-lg">
                        </div>

                        <div class="col-md-3">
                          <label>Periodo</label>
                          <input type="text" name="periodo_antecipacao" id='periodo_antecipacao' class="form-control input-lg">
                        </div>

                        <div class="col-md-3">
                          <label>Porcentagem</label>
                          <input type="text" name="porcentagem" id='porcentagem' class="form-control input-lg">
                        </div>

                        <div class="col-md-3">
                          <label>Taxa antecipação</label>
                          <input type="text" name="taxa_antecipacao" id='taxa_antecipacao' class="form-control input-lg">
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-danger" data-dismiss="modal" id="cancelarA">Cancelar</button>
                    <button class="btn btn-primary" id="salvarAntecipacao">Salvar</button>
                  </div>
                </form>
              </div>

            </div>

          </div>
          <!-- end editar -->

          <!-- excluir plano -->
          <!-- end excluir plano -->




          </div>

      </div>


  </section>

</div>

<?= $v->start("scripts") ?>
<script>
  $("input").blur(function(e) {
    var valor = $(this).val();
    console.log(valor);
    $(this).val(valor.replace(",", "."));
  });

  function edita(i) {
    console.log(i);
    var id = "<?= $taxa->id ?>";
    var parcelamento = "<?= $taxa->parcelamento ?>";
    //var saque = "<?= $taxa->saque ?>";
    var taxa_redrocket = "<?= $taxa->taxa_redrocket ?>";
    var valor_transacao = "<?= $taxa->valor_transacao ?>";
    /*var taxa_iniciante = "<?= $taxa->taxa_iniciante ?>";
    var vendas_iniciante = "<?= $taxa->vendas_iniciante ?>";
    var dias_iniciante = "<?= $taxa->dias_iniciante ?>";
    var taxa_bronze = "<?= $taxa->taxa_bronze ?>";
    var vendas_bronze = "<?= $taxa->vendas_bronze ?>";
    var dias_bronze = "<?= $taxa->dias_bronze ?>";
    var taxa_prata = "<?= $taxa->taxa_prata ?>";
    var vendas_prata = "<?= $taxa->vendas_prata ?>";
    var dias_prata = "<?= $taxa->dias_prata ?>";
    var taxa_ouro = "<?= $taxa->taxa_ouro ?>";
    var vendas_ouro = "<?= $taxa->vendas_ouro ?>";
    var dias_ouro = "<?= $taxa->dias_ouro ?>";*/

    $("#id").attr("value", id);
    $("#parcelamento").attr("value", parcelamento);
    //$("#saque").attr("value", saque);
    $("#taxa_redrocket").attr("value", taxa_redrocket);
    $("#valor_transacao").attr("value", valor_transacao);
    /*$("#taxa_iniciante").attr("value", taxa_iniciante);
    $("#vendas_iniciante").attr("value", vendas_iniciante);
    $("#dias_iniciante").attr("value", dias_iniciante);
    $("#taxa_bronze").attr("value", taxa_bronze);
    $("#vendas_bronze").attr("value", vendas_bronze);
    $("#dias_bronze").attr("value", dias_bronze);
    $("#taxa_prata").attr("value", taxa_prata);
    $("#vendas_prata").attr("value", vendas_prata);
    $("#dias_prata").attr("value", dias_prata);
    $("#taxa_ouro").attr("value", taxa_ouro);
    $("#vendas_ouro").attr("value", vendas_ouro);
    $("#dias_ouro").attr("value", dias_ouro);*/

  }

  function editarAntecipacao(i) {
    console.log(i);
    var id = "<?= $antecipacao->id ?>";
    var dias_antecipar = "<?= $antecipacao->dias_antecipar ?>";
    var periodo_antecipacao = "<?= $antecipacao->periodo_antecipacao ?>";
    var porcentagem = "<?= $antecipacao->porcentagem ?>";
    var taxa_antecipacao = "<?= $antecipacao->taxa_antecipacao ?>";

    $("#idA").attr("value", id);
    $("#dias_antecipar").attr("value", dias_antecipar);
    $("#periodo_antecipacao").attr("value", periodo_antecipacao);
    $("#porcentagem").attr("value", porcentagem);
    $("#taxa_antecipacao").attr("value", taxa_antecipacao);
  }

  $("#salvar").click(function(e) {
    e.preventDefault();
    salvarEditar(1);
  });

  $("#salvarAntecipacao").click(function(e) {
    e.preventDefault();
    salvarEditar(2);
  });

  function salvarEditar(salvar) {
    if (salvar == 1) {
      url = "<?= $router->route("taxa.editar"); ?>";
    } else {
      url = "<?= $router->route("taxa.antecipar") ?>";
    }

    var form = $("form");
    $.ajax({

      url: url,
      data: form.serialize(),
      type: "POST",
      beforeSend: function() {
        console.log("atualizando");
      },
      error: function() {
        console.log("O servidor não conseguiu processar o pedido");
      },
      success: function(callback) {
        console.log("callback ", callback);
        alert(callback);
        if (callback == "Atualizado Com Sucesso!") {
          $('form').each(function() {
            this.reset();
          });
          location.reload();
        }
      }

    });
  }
</script>
<?= $v->end() ?>