<?php
$v->layout("_theme");

//if(strtotime(date("H:i")) >= '17:00') echo date("Y-m-d", strtotime("+3 days")); else echo date("Y-m-d", strtotime("+2 days"));

if (@$saldo == "")
  $saldo = 0;

$status = array(
  1 => "Pendente",
  2 => "Aprovada",
  3 => "Reprovada",
  4 => "Bloqueada"
);

if ($_SESSION["tipo"] == 6) {
  header("Location: " . url("vendas"));
}
include "lista-bancos.php";
?>


<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">

    <?= $section->getTipoSecaoLayout() ?>

    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h3 style="font-family:inter"><?= $title ?></h3>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="./">Início</a></li>

            <li class="breadcrumb-item active">Saque</li>
          </ol>
        </div>

      </div>
    </div><!-- /.container-fluid -->
  </section>

  <!-- Main content -->
  <section class="content">

    <div class="card card-body">
      <form action="#">
        <div class="row">
          <?php if ($_SESSION["tipo"] == 1 || $_SESSION["tipo"] == 2) : ?>
            <div class="col-md-4">
              <label>Nome</label>
              <input type="text" name="nome" id="nome" class="form-control">
            </div>
          <?php endif ?>

          <div class="col-md-2">
            <label>Status</label>
            <select name="status_saque" id="status_saque" class="form-control">
              <option value="">Selecionar</option>
              <option value="1">Pendente</option>
              <option value="2">Aprovado</option>
              <option value="3">Reprovado</option>
            </select>
          </div>

          <div class="col-md-1">
            <label style="color: #fff">|</label>
            <input type="submit" value="Buscar" class="btn btn-primary">
          </div>
        </div>

      </form>
    </div>

    <!-- Default box -->
    <div class="card">
      <div class="card-body p-0">
        <?php if (@$comissao) : ?>
          <script>
            var dados = [];
          </script>
          <div style="overflow-x: auto">
            <table class="table table-striped" style="font-size:13px;">
              <thead>
                <tr>
                  <?php if ($_SESSION["tipo"] == 1) : ?>
                    <th scope="col">#</th>
                  <?php endif; ?>
                  <th scope="col">Valor</th>
                  <th scope="col">Comissionado</th>
                  <th scope="col">Conta</th>
                  <th scope="col">Pix</th>
                  <th scope="col">Status</th>
                  <th scope="col">Código</th>
                  <th scope="col">Data</th>
                  <th scope="col">Disponível Em:</th>
                  <th scope="col">Atualizado</th>
                  <?php if ($_SESSION["tipo"] == 1 || $_SESSION["tipo"] == 2) : ?>
                    <th scope="col"></th>
                  <?php endif; ?>
                </tr>
              </thead>

              <tbody>
                <?php foreach ($comissao as $comissao) :
                  /** CALCULA COMISSÃO MENOS A TAXA DE TRANSAÇÃO */
                  $total = $comissao->valor - ($comissao->valor * $comissao->taxa / 100);
                ?>
                  <tr class="plano" id="plano<?= $comissao->id; ?>">
                    <?php if ($_SESSION["tipo"] == 1) : ?>
                      <th scope="row"><?= $comissao->id; ?></th>
                    <?php endif; ?>
                    <td>R$ <?= number_format($total, 2, ",", "."); ?></td>
                    <td><?= @$comissao->dataUsuario()->nome . "<br>" . @$comissao->dataUsuario()->email; ?></td>
                    <td>
                      <?= @$comissao->dataConta()->banco . " - " . @$lista_bancos[@$comissao->dataConta()->banco] . "<br>" . @$comissao->dataConta()->agencia . " - " .
                        @$comissao->dataConta()->conta;
                      ?>
                    </td>
                    <th><?= @$comissao->dataConta()->chave_pix; ?></th>
                    <td><?= $status[$comissao->status]; ?><br><?= $comissao->observacao; ?></td>
                    <td><?= (@$comissao->codigo ? $comissao->codigo : " - ") ?></td>
                    <?php if ($_SESSION["tipo"] == 1 || $_SESSION["tipo"] == 2) : ?>

                    <?php endif; ?>
                    <td><?= formataData(@$comissao->data_pedido); ?></td>
                    <td><?= formataData(@$comissao->tempo_saque); ?></td>
                    <td><?= formataData(@$comissao->updated_at); ?></td>
                    <?php if ($_SESSION["tipo"] == 1 || $_SESSION["tipo"] == 2) : ?>
                      <td><button class="btn btn-warning btn-sm" data-toggle="modal" data-target="#modal-lg" onclick="editar(<?= $comissao->id ?>)"><i class="fa fa-edit"></i></button></td>
                    <?php endif; ?>
                  </tr>

                  <script>
                    dados[<?= $comissao->id; ?>] = {
                      id: "<?= $comissao->id ?>",
                      titular: "<?= @$comissao->dataConta()->titular ?>",
                      email: "<?= @$comissao->dataUsuario()->email ?>",
                      cpf: "<?= @$comissao->dataConta()->cpf ?>",
                      banco: "<?= @$comissao->dataConta()->banco . " - " . @$lista_bancos[@$comissao->dataConta()->banco] ?>",
                      agencia: "<?= @$comissao->dataConta()->agencia ?>",
                      conta: "<?= @$comissao->dataConta()->conta ?>",
                      tipo: "<?= @$comissao->dataConta()->tipo ?>",
                      chave_pix: "<?= @$comissao->dataConta()->chave_pix ?>",
                      status: "<?= $comissao->status ?>",
                      codigo: "<?= $comissao->codigo ?>",
                      observacao: "<?= $comissao->observacao ?>",
                    }
                    console.log(dados);
                  </script>

                <?php endforeach; ?>

              </tbody>
            </table>
          </div>

        <?php
          //EXIBE A NAVEGAÇÃO DO PAGINADOR
          $link = URL_BASE . "/saques/";

          //VARIÁVEIS VÊM DO CONTROLADOR AppPedidos
          $paginador->paginas($paginas, $pagina, $link);

        else :
        ?>
          <h4>Nenhuma solicitação de saque encontrada!</h4>
        <?php
        endif;
        ?>
      </div>

    </div>


  </section>

</div>
</div>

<!-- cadastar -->
<div class="modal fade" id="modal-lg">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">

      <div class="modal-header">
        <h3 class="card-title"><i class="fa fa-info-circle"></i> Solicitar Saque</h3>
        <button type="button" class="close" data-dismiss="modal"> &times;</button>
      </div>

      <form action="#" method="post" style="font-family:inter">
        <div class="modal-body">

          <input type="hidden" name="id" id="id">

          <p><b>Titular</b> <span id="titular"></span> - <b>CPF/CNPJ</b> <span id="cpf"> <span id="email"></span></span></p>
          <p><b>Banco</b> <span id="banco"></span> <b>Agência</b> <span id="agencia"></span> <b>Conta</b> <span id="conta"></span> <b>Tipo</b> <span id="tipo"></span></p>
          <p><b>Pix</b> <span id="chave_pix"></span>

          <div id="esconde">
            <label>Status:</label>
            <select name="status" id="status" class="form-control">
              <option value="1">Pendente</option>
              <option value="2">Aprovada</option>
              <option value="3">Reprovada</option>
            </select>

            <label>Código de Transferência</label>
            <input type="text" name="codigo" id="codigo" class="form-control">

            <label>Observação</label>
            <textarea name="observacao" id="observacao" class="form-control"></textarea>
          </div>

        </div>

        <div class="modal-footer justify-content-between">
          <button type="button" class="btn btn-danger" data-dismiss="modal">Cancelar</button>
          <button class="btn btn-primary" id="salvar">Salvar</button>
        </div>
      </form>

    </div>
  </div>
</div>
<!-- end cadastrar -->




<?= $v->start("scripts") ?>
<script>
  $("#salvar").click(function(e) {
    e.preventDefault();
    salvarEditar("<?= $router->route("appcomissao.statusSaque"); ?>");
  });

  function salvarEditar(url) {
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
        location.reload();
      }

    });
  }

  function editar(i) {
    var id = dados[i]["id"];
    var titular = dados[i]["titular"];
    var email = dados[i]["email"];
    var cpf = dados[i]["cpf"];
    var banco = dados[i]["banco"];
    var agencia = dados[i]["agencia"];
    var conta = dados[i]["conta"];
    var chave_pix = dados[i]["chave_pix"];
    var tipo = dados[i]["tipo"];
    var status = dados[i]["status"];
    var codigo = dados[i]["codigo"];
    var observacao = dados[i]["observacao"];

    console.log(i, titular, banco, status);

    $("#id").attr({
      "value": id
    });
    $("#codigo").attr({
      "value": codigo
    });
    $("#observacao").prop({
      "value": observacao
    });
    $("#status option[value=" + status + "]").prop({
      "selected": true
    });
    $("#titular").html(titular);
    $("#email").html(email);
    $("#cpf").html(cpf);
    $("#banco").html(banco);
    $("#agencia").html(agencia);
    $("#conta").html(conta);
    $("#chave_pix").html(chave_pix);
    $("#tipo").html(tipo);

    if (status != 1) {
      $("#esconde").hide();
      $("#salvar").hide();
    } else {
      $("#esconde").show();
      $("#salvar").show();
    }
  }
</script>
<?= $v->end() ?>