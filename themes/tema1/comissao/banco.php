<?php
$v->layout("_theme");

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

<?= $v->start("links") ?>

<link href="//cdnjs.cloudflare.com/ajax/libs/select2/4.0.0/css/select2.min.css" rel="stylesheet" />

<?= $v->end() ?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">

    <?= $section->getTipoSecaoLayout() ?>


    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
            <h1 style="font-family:inter; color:white">Contas Bancárias</h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="./">Início</a></li>
            <li class="breadcrumb-item active">Contas Bancárias</li>
          </ol>
        </div>
      </div>
    </div><!-- /.container-fluid -->
  </section>

  <!-- Main content -->
  <section class="content">
    <div class="callout callout-primary">
      <form action="#">
        <div class="row">
          <div class="col-12 col-md-3">
            <h6 style="font-family:inter">Conta ou CPF/CNPJ:</h6>
            <input type="text" name="conta" id="form_conta" class="form-control" value="<?= @$_GET["conta"]; ?>">
          </div>

          <?php if ($_SESSION["tipo"] == 1 || $_SESSION["tipo"] == 2) : ?>
            <div class="col-12 col-md-3">
              <h6 style="font-family:inter">Usuário:</h6>
              <input type="text" name="usuario" id="usuario" class="form-control" placeholder="(nome ou email)" value="<?= @$_GET["usuario"]; ?>">
            </div>
          <?php endif; ?>

          <div class="col-12 col-md-3">
            <h6 style="font-family:inter; color:black">|</h6>
            <button type="submit" name="" class="form-control form-control btn-primary"> <i class="fa fa-check"></i> Buscar</button>
          </div>
        </div>
      </form>
    </div>

    <?= $v->insert("fragmentos/msg-cadastrar-editar", []); ?>

    <div class="card">

      <div class="card-header">
        <?php if (!@$_SESSION["subusuario"] || $permissao->adicionar == 1) : ?>
          <a class="btn btn-outline-primary" data-toggle="modal" data-target="#modal-lg" style="font-family:inter" id="cadastro">Cadastrar <i class="fa fa-chevron-right"></i> </a>
        <?php endif ?>
      </div>

      <?php if (@$bancos) : ?>

        <script>
          var dados = [];
        </script>

        <div style="overflow-x: auto;">
          <table class="table table-striped projects" style="font-size:13px">
            <thead>
              <tr style="font-family:inter">
                <?php if ($_SESSION["tipo"] == 1) : ?>
                  <th scope="col">#</th>
                <?php endif; ?>
                <th scope="col">Status</th>
                <th scope="col">Titular</th>
                <th scope="col">CPF/CNPJ</th>
                <th scope="col">Banco</th>
                <th scope="col">Agência</th>
                <th scope="col">Conta</th>
                <th scope="col">Pix</th>
                <th scope="col">Tipo</th>
                <th scope="col"></th>
              </tr>
            </thead>

            <tbody>
              <?php
              $i = 1;
              foreach ($bancos as $banco) :
              ?>
                <tr style="font-family:inter;">
                  <?php if ($_SESSION["tipo"] == 1) : ?>
                    <th scope="row"><?= $banco->id; ?></th>
                  <?php endif; ?>
                  <td><?= $status[$banco->status]; ?></td>
                  <td><?= $banco->titular; ?></td>
                  <td><?= $banco->cpf; ?></td>
                  <td><?= @$banco->banco . " - " . @$lista_bancos[$banco->banco]; ?></td>
                  <td><?= $banco->agencia; ?> <?= $banco->digito_agencia; ?></td>
                  <td><?= $banco->conta; ?> <?= $banco->digito_conta; ?></td>
                  <td><?= $banco->chave_pix; ?></td>
                  <td><?= $banco->tipo; ?></td>
                  <td>
                    <?php if ($_SESSION["tipo"] == 1) : ?>
                      <button type="button" class="btn btn-warning btn-sm" data-toggle="modal" data-target="#status" onclick="edtStatus(<?= $banco->id; ?>)">S</button>
                    <?php endif; ?>
                    <?php if ($section->getId() == $banco->usuario) : ?>
                      <?php if (!@$_SESSION["subusuario"] || $permissao->editar == 1) : ?>
                        <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#modal-lg" onclick="editar(<?= $banco->id; ?>)"><i class="fas fa-edit"></i></button>
                      <?php endif ?>
                      
                      <?php if (!@$_SESSION["subusuario"] || $permissao->excluir == 1) : ?>
                        <button type="button" class="btn btn-danger btn-sm" onclick="excluir(<?= $banco->id; ?>)"><i class="fas fa-times"></i></button>
                      <?php endif ?>
                    <?php endif; ?>
                  </td>
                </tr>

                <?php $usuario = $usuarioDados->find("usuario = $banco->usuario")->fetch() ?>
                <tr style="border-bottom: 5px solid rgb(217, 192, 0);">
                  <td colspan="5">
                    <b> Usuário </b> &nbsp; <?= $usuario->nome; ?> - <?= $usuario->cpf_cnpj; ?>
                    imagem: <a href="<?= url("uploads/banco/" . $banco->imagem_conta) ?>" target="_blank"><?= $banco->imagem_conta ?></a><br>
                    <b>Observação:</b> <?= $banco->observacao; ?>
                  </td>
                </tr>

                <!-- <tr class="bg-success"><td colspan="10"></td></tr> -->

                <script>
                  dados[<?= $banco->id ?>] = {
                    id: "<?= $banco->id ?>",
                    titular: "<?= $banco->titular ?>",
                    cpf: "<?= $banco->cpf ?>",
                    banco: "<?= @$banco->banco ?>",
                    banco1: "<?= @$banco->banco . " - " . @$lista_bancos[$banco->banco] ?>",
                    agencia: "<?= $banco->agencia ?>",
                    digito_agencia: "<?= $banco->digito_agencia ?>",
                    conta: "<?= $banco->conta ?>",
                    digito_conta: "<?= $banco->digito_conta ?>",
                    tipo: "<?= $banco->tipo ?>",
                    chave_pix: "<?= $banco->chave_pix ?>",
                    status: "<?= $banco->status ?>",
                    observacao: "<?= $banco->observacao ?>",
                    imagem_conta: "<?= $banco->imagem_conta ?>",
                    principal: "<?= $banco->principal ?>"
                  }
                </script>

              <?php
                $i++;
              endforeach;
              ?>
            </tbody>
          </table>
        </div>
      <?php
        //EXIBE A NAVEGAÇÃO DO PAGINADOR
        $link = URL_BASE . "/banco/";

        //VARIÁVEIS VÊM DO CONTROLADOR AppPedidos
        $paginador->paginas($paginas, $pagina, $link);

      else :
      ?>
        <h4>Você ainda não possiu conta bancária!</h4>
        <p>Você precisa cadastrar uma conta para realizar saques</p>
      <?php endif ?>

    </div>
  </section>

</div>
<!-- /.content-wrapper -->

<!-- cadastar -->
<div class="modal fade" id="modal-lg">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">

      <div class="card-header">
        <h3 id="modal-title" class="card-title"><i class="fa fa-info-circle"></i> Cadastrar Conta Bancária</h3>
        <button type="button" class="close" data-dismiss="modal"> &times;</button>
      </div>

      <form action="#" method="post" id="form" enctype="multipart/form-data" style="font-family:inter">
        <div class="modal-body" style="background: #f4f6f9">

          <input type="hidden" name="id" id="id">
          <input type="hidden" name="cpf_usuario" id="cpf_usuario">

          <div class="card card-body">
            <p id="message">Para validarmos sua conta, nos envie um print que contenha o número da conta e o nome do titular, com CPF/CNPJ do mesmo.</p>
          </div>

          <hr>
          <div class="card card-body">
            <div class="row">
              <div class="col-md-6">
                <label>Imagem da conta:</label>
                <input type="file" name="imagem" id="imagem" class="form-control">
                <input type="hidden" name="imagem_conta" id="imagem_conta" required>
                <small>Imagem no formato jpg ou png. Tamanha máximo de 2 mb</small><br>
              </div>

              <div class="col-md-6">
                <label>Titular:</label>
                <input type="text" name="titular" id="titular" class="form-control" maxlength="30" required>
                <small>A conta bancária deve pertencer a você ou sua empresa</small><br>
              </div>
            </div>

            <hr>
            <div class="row">
              <div class="col-md-6">
                <label>CPF/CNPJ do Titular:</label>
                <input type="text" name="cpf" id="cpf" class="form-control" required>
              </div>

              <div class="col-md-6">
                <?php include "lista-bancos.php" ?>
                <label>Banco:</label> <small>colocar código do banco</small><br>
                <select name="banco" id="banco" class="form-control" required>
                  <?php foreach ($lista_bancos as $cod => $lista) : ?>
                    <option value="<?= $cod ?>"><?= $cod . " - " . $lista ?> </option>
                  <?php endforeach; ?>
                </select>
              </div>
            </div>
          </div>

          <hr>
          <div class="card card-body">
            <div class="row">
              <div class="col-md-3">
                <label>Agência:</label>
                <input type="text" name="agencia" id="agencia" class="form-control" required>
              </div>

              <div class="col-md-3">
                <label>Digito Agência:</label>
                <input type="text" name="digito_agencia" id="digito_agencia" class="form-control" value="0" required>
              </div>

              <div class="col-md-3">
                <label>Conta:</label>
                <input type="text" name="conta" id="conta" class="form-control" required>
              </div>

              <div class="col-md-3">
                <label>Dígito Conta:</label>
                <input type="text" name="digito_conta" id="digito_conta" class="form-control" required>
              </div>
            </div>
            <small class="mt-2">Para agência sem digito mantenha o valor 0 no campo Digito Agência.</small>
            <hr>
            <div class="row">
              <div class="col-md-4">
                <label>Chave Pix:</label>
                <input type="text" name="chave_pix" id="chave_pix" class="form-control">
              </div>

              <div class="col-md-4">
                <label>Tipo:</label>
                <select name="tipo" id="tipo" class="form-control" required>
                  <option value="poupanca">Poupança</option>
                  <option value="corrente">Corrente</option>
                </select>
              </div>

              <div class="col-md-4">
                <label>Conta Principal:</label>
                <select name="principal" id="principal" class="form-control" required>
                  <option value="1">Sim</option>
                  <option value="0">Não</option>
                </select>
              </div>
            </div>
          </div>

          <hr>
          <div class="card card-body">
            Para realizar alteração dos dados da conta bancaria, enviar email para suporte@winpay.digital com o Assunto: Alteração de conta bancaria.
          </div>

        </div>

        <div class="modal-footer justify-content-between">
          <button type="button" class="btn btn-danger" data-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-success" name="salvar" id="salvar"><i class="fa fa-check"></i> Salvar</button>
          <button type="submit" class="btn btn-warning" name="editar" id="atualizar"><i class="fa fa-check"></i> Atualizar</button>
        </div>
        <!-- /.row -->
      </form>

    </div>

  </div>

</div>
<!-- end cadastrar -->

<!-- status -->
<div class="modal fade" id="status">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">

      <div class="card-header">
        <h3 class="card-title"><i class="fa fa-info-circle"></i> Editar </h3>
        <button type="button" class="close" data-dismiss="modal"> &times;</button>
      </div>

      <form action="#" method="post" style="font-family:inter">
        <div class="modal-body" style="background: #f4f6f9">

          <input type="hidden" name="idStatus" id="idStatus">

          <div class="card card-body">
            <label>Status:</label>
            <select name="status" id="mudaStatus" class="form-control">
              <option value="1">Pendente</option>
              <option value="2">Aprovada</option>
              <option value="3">Reprovada</option>
              <option value="4">Bloqueada</option>
            </select>

            <hr>
            <label>Observação:</label>
            <textarea name="observacao" id="observacao" class="form-control" cols="30" rows="3"></textarea>
          </div>
        </div>
        <div class="modal-footer justify-content-between">
          <button type="button" class="btn btn-danger" data-dismiss="modal">Cancelar</button>
          <button class="btn btn-success" id="atlStatus">Salvar</button>
        </div>
      </form>

    </div>
  </div>
</div>
<!-- end editar -->

<?= $v->start("scripts") ?>
<script src="//cdnjs.cloudflare.com/ajax/libs/select2/4.0.0/js/select2.min.js"></script>
<script>
  $("#cadastro").click(function() {
    $("#modal-title").html('<i class="fa fa-info-circle"></i> Cadastrar Conta Bancária');
    $("#message").text("Para validarmos sua conta, nos envie um print que contenha o número da conta e o nome do titular, com CPF/CNPJ do mesmo.");

    $("form input, form select").attr("disabled", false);

    $('form').each(function() {
      this.reset();
    });

    $("#salvar").css("display", "inline");
    $("#atualizar").css("display", "none");
    $("form[action]").attr({
      "action": "<?= $router->route('appcomissao.bancoCadastro') ?>"
    });
  });

  // $("#salvar").click(function(e) {
  //     e.preventDefault();
  //     /*var cont = 0;
  //     $("#form_cadastro input").each(function(){
  //         if($(this).val() == "") {
  //             $(this).css({"border" : "1px solid #F00", "padding": "2px"});
  //             cont++;
  //             console.log("Cont: ", cont);
  //         }
  //     });
  //     console.log(cont);
  //     if(cont == 1) {
  //         salvar("<?= $router->route('appcomissao.bancoCadastro') ?>");
  //     }*/

  //     salvar("<?= $router->route('appcomissao.bancoCadastro') ?>");
  // });

  // $("#atualizar").click(function(e) {
  //     e.preventDefault();
  //     salvar("<?= $router->route('appcomissao.bancoEditar') ?>");
  // });

  $("#atlStatus").click(function(e) {
    e.preventDefault();
    salvar("<?= $router->route('appcomissao.bancoStatus') ?>");
  });

  function salvar(url) {
    var form = $("form");
    $.ajax({
      url: url,
      data: form.serialize(),
      type: "POST",
      //datatype: "json",
      success: function(callback) {
        alert(callback);
        if (callback == "Salvo com sucesso" || callback == "Atualizado com sucesso") {
          location.reload();
        }
      }

    });
  };

  $("#cpf").blur(() => {
    <?php $usuario = $usuarioDados->find("usuario = {$_SESSION["id"]}")->fetch() ?>

    $("#cpf_usuario").prop("value", "<?= $usuario->cpf_cnpj ?>");

    /*if($("#cpf").val() != <?= $usuario->cpf_cnpj ?>){
      $("#salvar").attr("disabled", true);
      alert("CPF/CNPJ informado diferente do usuário");
    }*/
  })

  function editar(i) {
    var action = "";
    var id = dados[i]["id"];
    var titular = dados[i]["titular"];
    var cpf = dados[i]["cpf"];
    var banco = dados[i]["banco"];
    var banco1 = dados[i]["banco1"];
    var agencia = dados[i]["agencia"];
    var digito_agencia = dados[i]["digito_agencia"];
    var conta = dados[i]["conta"];
    var digito_conta = dados[i]["digito_conta"];
    var tipo = dados[i]["tipo"];
    var status = dados[i]["status"];
    var chave_pix = dados[i]["chave_pix"];
    var imagem_conta = dados[i]["imagem_conta"];
    var principal = dados[i]["principal"];
    console.log(i, titular, banco, agencia, conta);

    $("#modal-title").html('<i class="fa fa-info-circle"></i> Atualizar Conta Bancária');
    $("#salvar").css("display", "none");
    $("#atualizar").css("display", "inline");
    $("#atualizar").attr('disabled', 'true');
    $("#message").text("Para atualizar sua conta entre em contato com a RedRocket.");

    $("form[action]").attr({
      "action": action
    });

    $("form input, form select").attr("disabled", true);

    $("#id").attr({
      "value": id
    });
    $("#titular").attr({
      "value": titular
    });
    $("#cpf").attr({
      "value": cpf
    });
    $("#banco option[value=" + banco + "]").prop({
      "selected": true
    });
    $("#principal option[value=" + principal + "]").prop({
      "selected": true
    });
    $("#agencia").attr({
      "value": agencia
    });
    $("#digito_agencia").attr({
      "value": digito_agencia
    });
    $("#conta").attr({
      "value": conta
    });
    $("#digito_conta").attr({
      "value": digito_conta
    });
    $("#tipo option[value=" + tipo + "]").prop({
      "selected": true
    });
    $("#chave_pix").attr({
      "value": chave_pix
    });
    $("#imagem_conta").attr({
      "value": imagem_conta
    });

    if (status == 2) {
      $("#atualizar").hide();
    }
  }

  function edtStatus(i) {
    var status = dados[i]["status"];
    var observacao = dados[i]["observacao"];

    $("#idStatus").attr({
      "value": i
    });
    $("#mudaStatus option[value=" + status + "]").prop({
      "selected": true
    });
    $("#observacao").prop({
      "value": observacao
    });
  }

  function excluir(id) {
    console.log(id);
    if (confirm("Tem certeza que deseja excluir essa conta bancária?")) {
      $.ajax({
        url: "<?= $router->route("appcomissao.bancoExcluir") ?>",
        data: {
          id: id
        },

        type: "POST",
        beforeSend: function() {
          console.log("atualizando");
        },
        error: function() {
          console.log("O servidor não conseguiu processar o pedido");
        },
        success: function(callback) {
          console.log(callback);
          if (callback == "Excluido com sucesso") {
            console.log("sucesso");
            alert(callback);
            location.reload();
          } else {
            console.log("fracasso");
            alert(callback);
          }
        }
      });
    }
  }

  $(document).ready(function() {
    $(".meuselect2").select2();
  });
</script>
<?= $v->end() ?>