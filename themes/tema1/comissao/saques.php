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
<style>
/* ===== Tema dark WinPay apenas para #modal-lg ===== */
#modal-lg {
  /* Dialog central + animação */
  .modal-dialog{
    width:100% !important; max-width:900px !important; margin:20px !important;
    animation: slideUp .3s ease-out !important;
  }

  /* Conteúdo */
  .modal-content{
    background: linear-gradient(145deg,#1a1a1a,#111111) !important;
    border:1px solid rgba(255,221,0,.3) !important;
    border-radius:20px !important;
    box-shadow:
      0 25px 50px rgba(0,0,0,.9),
      0 0 0 1px rgba(255,221,0,.1),
      inset 0 1px 0 rgba(255,255,255,.1) !important;
    overflow:hidden !important; position:relative !important;
  }
  .modal-content::before{
    content:''; position:absolute; top:0; left:0; right:0; height:2px;
    background:linear-gradient(90deg,transparent,#FFDD00,transparent);
    animation: shimmer 2s infinite;
  }

  /* Header */
  .modal-header{
    background: linear-gradient(135deg,#2a2a2a,#1a1a1a) !important;
    padding:24px 32px !important;
    border-bottom:1px solid rgba(255,221,0,.2) !important;
    display:flex; align-items:center; gap:10px;
  }
  .modal-header h3, .modal-header h4{
    color:#FFDD00 !important; font-size:20px !important; font-weight:600 !important; margin:0 !important;
    font-family:'Inter',sans-serif !important;
  }
  .modal-header .close{
    background:none !important; border:none !important; color:#FFDD00 !important; font-size:24px !important;
    cursor:pointer !important; margin-left:auto; width:36px !important; height:36px !important;
    display:flex !important; align-items:center !important; justify-content:center !important; border-radius:50% !important;
    transition:all .3s ease !important;
  }
  .modal-header .close:hover{ background:rgba(255,221,0,.1) !important; transform:scale(1.1); }

  /* Body */
  .modal-body{
    background:#111111 !important; color:#f1f1f1 !important;
    padding:32px !important; max-height:70vh !important; overflow-y:auto !important;
    font-family:'Inter',sans-serif;
  }
  .modal-body::-webkit-scrollbar{ width:8px; }
  .modal-body::-webkit-scrollbar-track{ background:#1a1a1a; border-radius:4px; }
  .modal-body::-webkit-scrollbar-thumb{ background:linear-gradient(135deg,#FFDD00,#D4B800); border-radius:4px; }
  .modal-body::-webkit-scrollbar-thumb:hover{ background:linear-gradient(135deg,#FFE83D,#FFDD00); }

  /* Footer */
  .modal-footer{
    background:linear-gradient(135deg,#2a2a2a,#1a1a1a) !important;
    padding:20px 32px !important; border-top:1px solid rgba(255,221,0,.2) !important;
    display:flex !important; justify-content:space-between !important; gap:12px !important;
  }

  /* “cards” dentro do modal (reaproveita teu HTML) */
  .card.card-body{
    background:linear-gradient(145deg,#2a2a2a,#1a1a1a) !important; color:#f1f1f1 !important;
    border:1px solid rgba(255,221,0,.1) !important; border-radius:15px !important;
    padding:24px !important; margin-bottom:20px !important;
    box-shadow:0 8px 32px rgba(0,0,0,.3), inset 0 1px 0 rgba(255,255,255,.05) !important;
    position:relative !important; overflow:hidden !important;
  }
  .card.card-body::before{
    content:''; position:absolute; top:0; left:0; right:0; height:1px;
    background:linear-gradient(90deg,transparent,rgba(255,221,0,.3),transparent);
  }

  /* Linhas quevc usa como “borda1”, etc. (se existir nesse modal) */
  .row.borda1{
    padding:12px 0 !important; border-bottom:1px solid rgba(255,221,0,.1) !important; transition:background .3s ease !important;
  }
  .row.borda1:hover{ background:rgba(255,221,0,.05) !important; border-radius:8px !important; margin:0 -12px !important; padding:12px !important; }
  .row.borda1:last-child{ border-bottom:none !important; }

  /* Responsivo */
  @media (max-width:768px){
    .modal-dialog{ margin:10px !important; max-width:calc(100vw - 20px) !important; }
    .modal-body{ padding:20px !important; max-height:60vh !important; }
    .modal-header, .modal-footer{ padding:16px 20px !important; }
  }
}

/* Centralização robusta (vertical + horizontal) */
#modal-lg .modal-dialog{
  margin: 0 auto !important;
  min-height: calc(100vh - 1rem) !important;
  display: flex !important;
  align-items: center !important;
}
#modal-lg .modal-content{ width:100%; }

/* Animações */
@keyframes slideUp{ from{opacity:0; transform:translateY(30px) scale(.95);} to{opacity:1; transform:translateY(0) scale(1);} }
@keyframes shimmer{ 0%{transform:translateX(-100%);} 100%{transform:translateX(100%);} }
</style>


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