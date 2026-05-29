<?php $v->layout("_theme"); ?>

<?= $v->start("links"); ?>
<style>
/* Modal dark — combina com o tema global */
#modal-lg .modal-content {
  background: #1a1a1a;
  color: #f0f0f0;
  border: 1px solid rgba(255,255,255,.1);
}
#modal-lg .modal-header {
  border-bottom: 1px solid rgba(255,255,255,.1);
}
#modal-lg .modal-footer {
  border-top: 1px solid rgba(255,255,255,.1);
}
#modal-lg label {
  color: rgba(255,255,255,.75);
  font-size: 12px;
}
/* Garante que select também fique dark */
#modal-lg select.form-control {
  background-color: #151515 !important;
  color: #fff !important;
}
#modal-lg select.form-control option {
  background: #222;
  color: #fff;
}
/* Corrige autofill do browser — impede fundo branco com letra branca */
#modal-lg input:-webkit-autofill,
#modal-lg input:-webkit-autofill:hover,
#modal-lg input:-webkit-autofill:focus {
  -webkit-box-shadow: 0 0 0px 1000px #1f1f1f inset !important;
  -webkit-text-fill-color: #fff !important;
  caret-color: #fff;
}
</style>
<?= $v->end(); ?>

<?php

$tipo_usuario = array(
  1 => "MASTER",
  2 => "ADMINISTRADOR",
  3 => "FUNCIONÁRIO",
  4 => "PRODUTOR",
  5 => "AFILIADO",
  6 => "COMPRADOR",
  7 => "ATENDENTE",
);

$documento = "";

// Acesso permitido para admin (1/2) e produtor (4)
if (!in_array((int)$_SESSION["tipo"], [1, 2, 4])) {
  header("Location: " . URL_BASE);
}
$i = 1;
?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h4 style="font-family:inter"><?= $is_produtor ? "Meus Atendentes" : "Gerenciar Usuários" ?></h4>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="./">Início</a></li>
            <li class="breadcrumb-item">Configurações</li>
            <li class="breadcrumb-item active"><?= $is_produtor ? "Meus Atendentes" : "Gerenciar Usuários" ?></li>
          </ol>
        </div>
      </div>
    </div><!-- /.container-fluid -->
  </section>

  <!-- Main content -->
  <section class="content">

    <!-- Default box -->
    <div class="card">
      <div class="card-header">
        <a class="btn btn-outline-primary" data-toggle="modal" data-target="#modal-lg" style="font-family:inter" id="cadastro">Cadastrar <i class="fa fa-chevron-right"></i> </a>

        <div class="card-tools">
          <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
            <i class="fas fa-minus"></i>
          </button>

        </div>
      </div>

      <?php if ($usuarios) : ?>
        <script>
          var dados = [];
        </script>

        <div class="card-body p-0" style="overflow-x: auto">
          <table class="table table-striped projects" style="font-size:13px">
            <thead>
              <tr style="font-family:inter">
                <th>ID</th>
                <th>Nome</th>
                <th>E-mail</th>
                <th>Login</th>
                <th>Tipo</th>
                <th>Desde</th>
                <th style="width:10%">Ação</th>
              </tr>
            </thead>

            <tbody>
              <?php foreach ($usuarios as $usuario) : ?>
                <tr style="font-family:inter">
                  <th scope="row"><?= $usuario->id; ?></th>
                  <td>
                    <?php if ($usuario->foto) {
                      $foto = $usuario->foto;
                    } else {
                      $foto = "user.png";
                    } ?>
                    <img src="<?= url("uploads/perfil/$foto"); ?>" alt="" width="30px" style="border-radius: 50%">
                    <?= $usuario->nome; ?>
                  </td>
                  <td><?= $usuario->email; ?></td>
                  <td><?= $usuario->login; ?></td>
                  <td><?= $tipo_usuario[$usuario->tipo_usuario]; ?></td>
                  <td><?= formataData($usuario->created_at); ?></td>
                  <?php if ($usuario->tipo_usuario != 1 || $_SESSION["tipo"] == 1) : ?>
                    <td>
                      <a class="btn btn-warning btn-sm" data-toggle="modal" data-target="#modal-lg" onclick="editar(<?= $i; ?>)"><i class="fas fa-edit"></i></a>
                      <a class="btn btn-danger btn-sm" data-toggle="modal" data-target="#idexcluir" onclick="prepararBloqueio(<?= $usuario->id ?>, '<?= htmlspecialchars($usuario->nome) ?>', <?= $usuario->ativo ?>)">
                        <i class="fas fa-ban"></i>
                      </a>
                    </td>
                  <?php endif; ?>
                </tr>

                <script>
                  dados[<?= $i ?>] = {
                    id: "<?= $usuario->id ?>",
                    email: "<?= $usuario->email ?>",
                    nome: "<?= $usuario->nome ?>",
                    foto: "<?= $foto ?>",
                    login: "<?= $usuario->login ?>",
                    senha: "<?= $usuario->senha ?>",
                    tipo_usuario: "<?= $usuario->tipo_usuario ?>",
                    atendente_produtor: "<?= $usuario->atendente_produtor ?>"
                  }
                </script>

              <?php $i++;
              endforeach; ?>
            </tbody>
          </table>
        </div>

      <?php
        //EXIBE A NAVEGAÇÃO DO PAGINADOR
        $link = URL_BASE . "/funcionarios/";

        //VARIÁVEIS VÊM DO CONTROLADOR AppPedidos
        $paginador->paginas($total, $pagina, $link);

      else :
      ?>
        <h4>Não existem usuários cadastrados ou ativos!</h4>
      <?php endif; ?>
    </div>
  </section>
</div>

<!-- cadastar -->
<div class="modal fade" id="modal-lg">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">

      <div class="modal-header">
        <h3 class="card-title"><i class="fa fa-info-circle"></i> Cadastrar Usuário</h3>
        <button type="button" class="close" data-dismiss="modal"> &times;</button>
      </div>

      <form action="#" method="post" style="font-family:inter; font-size:12px">
        <div class="modal-body">
          <input type="hidden" name="id" id="id">
          <div class="row">
            <div class="col-md-6" style="text-align: center">
              <img id="fotoP" src="" alt="" width="150px" style="border-radius: 50%">
            </div>

            <div class="col-md-6">
              <label>Nome Completo*</label>
              <input type="text" name="nome" id='nome' class="form-control input-lg" placeholder="Nome completo">
            </div>
          </div>

          <div class="row">
            <div class="col-6">
              <label style="margin-top:10px">Email*</label>
              <input type="email" name="email" id='email' class="form-control input-lg" placeholder="Email *" required>
              <p id="msg-email"></p>
            </div>

            <div class="col-6">
              <label style="margin-top:10px">Senha*</label>
              <input type="password" name="senha" id='senha' class="form-control input-lg" placeholder="Senha *" required>

              <p id="aviso">*deixe em branco para manter a mesma senha</p>
              <input type="hidden" name="senha_atual" id='senha_atual'>
            </div>
          </div>

          <div class="row">

            <div class="col-6">
              <label style="margin-top:10px">Login de usuário*</label>
              <input type="text" name="login" id='login' class="form-control input-lg" placeholder="Login de usuário">
              <p id="msg-login"></p>
            </div>

            <?php if ($is_produtor) : ?>
              <!-- Produtor: tipo fixo = Atendente, sem escolha -->
              <input type="hidden" name="tipo_usuario" id="tipo_usuario" value="7">
              <div class="col-6">
                <label style="margin-top:10px">Tipo</label>
                <input type="text" class="form-control input-lg" value="Atendente" disabled>
              </div>
            <?php else : ?>
              <div class="col-6">
                <label style="margin-top:10px">Permissão*</label>
                <select name="tipo_usuario" id="tipo_usuario" class="form-control input-lg" required="required" onchange="toggleProdutor(this.value)">
                  <option disabled selected>Permissão *</option>
                  <?php if ($_SESSION["tipo"] == 1) : ?>
                    <option value="1">Master</option>
                  <?php endif; ?>
                  <option value="2">Administrador</option>
                  <option value="3">Funcionário</option>
                  <option value="7">Atendente</option>
                </select>
              </div>
            <?php endif ?>
          </div>

          <?php if (!$is_produtor) : ?>
          <div class="row" id="row-atendente-produtor" style="display:none;">
            <div class="col-12">
              <label style="margin-top:10px">Produtor associado* <small style="color:#aaa;">(obrigatório para Atendente)</small></label>
              <select name="atendente_produtor" id="atendente_produtor" class="form-control input-lg">
                <option value="">Selecione o produtor</option>
                <?php foreach ($produtores as $prod) : ?>
                  <option value="<?= $prod->id ?>"><?= htmlspecialchars($prod->nome) ?> (<?= htmlspecialchars($prod->email) ?>)</option>
                <?php endforeach ?>
              </select>
            </div>
          </div>
          <?php endif ?>
        </div>

        <div class="modal-footer justify-content-between">
          <button type="button" class="btn btn-danger" data-dismiss="modal" id="cancelar">Cancelar</button>
          <button class="btn btn-primary" id="salvar">Salvar</button>
          <button class="btn btn-warning" id="editar">Editar</button>
        </div>
      </form>
    </div>
  </div>
</div>
<!-- end cadastrar -->

<!-- modal bloquear/ativar -->
<div class="modal fade" id="idexcluir">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title" id="titulo-bloqueio">Bloquear usuário?</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <p id="msg-bloqueio" style="font-family:inter; font-size:14px;"></p>
      </div>
      <div class="modal-footer justify-content-between">
        <button class="btn btn-default" data-dismiss="modal">Cancelar</button>
        <button class="btn btn-danger" id="btn-confirmar-bloqueio" onclick="confirmarBloqueio()">
          <i class="fa fa-check"></i> Confirmar
        </button>
      </div>
    </div>
  </div>
</div>
<!-- end modal bloquear -->

<?= $v->start("scripts") ?>
<script>
  $("#cadastro").click(() => {

    $(".card-title").html("Cadastrar Novo Usuário");
    $("#salvar").css("display", "inline");
    $("#editar").css("display", "none");
    $("#aviso").css("display", "none");
    cancelar();
  })

  $("#email").blur(function() {
    var email = $(this).val();
    $.ajax({
      url: "<?= $router->route("appusuarios.email") ?>",
      data: {
        email: email
      },

      type: "POST",
      beforeSend: function() {
        console.log("atualizando");
      },
      error: function() {
        console.log("O servidor não conseguiu processar o pedido");
      },
      success: function(callback) {
        console.log("callback ", callback);
        if (callback == "Não existe usuário com esse email") {
          $("#cadastrar").attr("disabled", false);
          $("#add-email").css("background", "#eeeeee");
          $("#msg-email").html("");
        } else {
          $("#add-email").css("background", "#ed1c24");
          $("#msg-email").html("Email já cadastrado");
          $("#cadastrar").attr("disabled", true);
        }
      }
    });
  });

  $("#login").blur(function() {
    var login = $(this).val();
    $.ajax({
      url: "<?= $router->route("appusuarios.login") ?>",
      data: {
        login: login
      },

      type: "POST",
      beforeSend: function() {
        console.log("atualizando");
      },
      error: function() {
        console.log("O servidor não conseguiu processar o pedido");
      },
      success: function(callback) {
        console.log("callback ", callback);
        if (callback == "Não existe usuário com esse login") {
          $("#add-login").css("background", "#eeeeee");
          $("#msg-login").html("");
          $("#cadastrar").attr("disabled", false);
        } else {
          $("#add-login").css("background", "#ed1c24");
          $("#msg-login").html("Login não disponível");
          $("#cadastrar").attr("disabled", true);
        }
      }
    });
  });

  $("#salvar").click(function(e) {
    e.preventDefault();
    salvarEditar(1);
  });

  $("#editar").click(function(e) {
    e.preventDefault();
    salvarEditar(0);
  });

  $("#cancelar").click(function(e) {
    e.preventDefault();
    cancelar();
  });

  function salvarEditar(salvar) {
    if (salvar == 1) {
      url = "<?= $router->route("appusuarios.funcionarios"); ?>";
    } else {
      url = "<?= $router->route("appusuarios.editar") ?>";
    }

    var email = $("#email").val(),
      senha = $("#senha").val(),
      tipo_usuario = $("#tipo_usuario").val(),
      nome = $("#nome").val(),
      login = $("#login").val();

    var form = $("form");

    if (email == "" || (senha == "" && salvar == 1) || tipo_usuario == "") {

      alert("Preecha todos os dados necessários");

    } else {

      $.ajax({

        url: url,
        data: form.serialize(),
        type: "POST",
        //datatype: "json",
        beforeSend: function() {
          console.log("atualizando");
        },
        error: function() {
          console.log("O servidor não conseguiu processar o pedido");
        },
        success: function(callback) {
          console.log(form.serialize());
          console.log("callback ", callback);
          alert(callback);
          if (callback == "Cadastrado Com Sucesso!") {
            $('form').each(function() {
              this.reset();
            });
            location.reload();
          }
          if (callback == "Atualizado Com Sucesso!") {
            $('form').each(function() {
              this.reset();
            });
            location.reload();
          }
        }

      });
    }
  }

  function editar(i) {
    var id = dados[i]["id"];
    var email = dados[i]["email"];
    var nome = dados[i]["nome"];
    var foto = dados[i]["foto"];
    var senha = dados[i]["senha"];
    var login = dados[i]["login"];
    var tipo_usuario = dados[i]["tipo_usuario"];
    var src = "<?= url("uploads/perfil/"); ?>";

    console.log(id, email, nome, senha, login, tipo_usuario);

    $(".card-title").html("Editar Usuário " + nome);
    $("#salvar").css("display", "none");
    $("#editar").css("display", "inline");
    $("#aviso").css("display", "block");
    $("#id").attr({
      "value": id
    });
    $("#email").attr({
      "value": email
    });
    $("#nome").attr({
      "value": nome
    });
    $("#fotoP").attr("src", src + foto);
    $("#senha_atual").attr({
      "value": senha
    });
    $("#login").attr({
      "value": login
    });
    $("#tipo_usuario option[value=" + tipo_usuario + "]").attr({
      "selected": true
    });

    toggleProdutor(tipo_usuario);

    var atendente_produtor = dados[i]["atendente_produtor"];
    if (atendente_produtor) {
      $("#atendente_produtor option[value=" + atendente_produtor + "]").prop("selected", true);
    }
  }

  function toggleProdutor(tipo) {
    if (tipo == "7") {
      $("#row-atendente-produtor").show();
    } else {
      $("#row-atendente-produtor").hide();
      $("#atendente_produtor").val("");
    }
  }

  var _bloqueioId   = null;
  var _bloqueioAtivo = null;

  function prepararBloqueio(id, nome, ativo) {
    _bloqueioId    = id;
    _bloqueioAtivo = parseInt(ativo);
    var acao = _bloqueioAtivo === 1 ? "Bloquear" : "Ativar";
    $("#titulo-bloqueio").text(acao + " usuário?");
    $("#msg-bloqueio").text(acao + " o usuário \"" + nome + "\"?");
    var btnCls = _bloqueioAtivo === 1 ? "btn-danger" : "btn-success";
    $("#btn-confirmar-bloqueio").removeClass("btn-danger btn-success").addClass(btnCls);
  }

  function confirmarBloqueio() {
    if (!_bloqueioId) return;
    $.ajax({
      url: "<?= $router->route("appusuarios.bloquear") ?>",
      type: "POST",
      data: { id: _bloqueioId },
      success: function(resp) {
        alert(resp);
        location.reload();
      },
      error: function() {
        alert("Erro ao processar. Tente novamente.");
      }
    });
    $("#idexcluir").modal("hide");
  }

  function cancelar() {
    $("#row-atendente-produtor").hide();
    $("#atendente_produtor").val("");
    $("#salvar").css("display", "inline");
    $("#editar").css("display", "none");
    $("#id").attr({
      "value": ""
    });
    $("#email").attr({
      "value": ""
    });
    $("#nome").attr({
      "value": ""
    });
    $("#senha_atual").attr({
      "value": ""
    });
    $("#login").attr({
      "value": ""
    });
    $("#tipo_usuario").attr({
      "selected": true
    });

  }
</script>
<?= $v->end() ?>