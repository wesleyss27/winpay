<?php

$tipo_usuario = array(
    1 => "MASTER",
    2 => "ADMINISTRADOR",
    3 => "FUNCIONÁRIO",
    4 => "PRODUTOR",
    5 => "AFILIADO",
    6 => "COMPRADOR"
);

if($_SESSION["tipo"] != 1 && $_SESSION["tipo"] != 2){
    header("Location: " . URL_BASE);
}
$i = 1;
?>

<?php $v->layout("_theme"); ?>

<div class="principal">
<?= $v->insert("fragmentos/msg-cadastrar-editar", []); ?>
    
    <div class="row">
        <div class="col-md-12">
        <!-- Trigger the modal with a button -->
        <button type="button" class="btn btn-primary btn-lg" data-toggle="modal" data-target="#myModal" id="cadastro">Cadastrar</button>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
        <?php if($usuarios): ?>
            <script> var dados = []; </script>
        <div style="overflow: auto;">
            <table class="table table-striped">
                <thead class="thead-dark">
                    <tr>
                        <th scope="col">ID</th>
                        <th scope="col">Nome</th>
                        <th scope="col">Email</th>
                        <th scope="col">Login</th>
                        <th scope="col">Tipo</th>
                        <th scope="col">Desde</th>
                        <th scope="col">Ação</th>
                    </tr>
                </thead>

                <tbody>
                <?php foreach($usuarios as $usuario): ?>
                    <tr class="user" id="usuario<?= $usuario->id; ?>">
                        <th scope="row"><?= $usuario->id; ?></th>
                        <td><?= $usuario->nome . " " . $usuario->sobrenome; ?></td>
                        <td><?= $usuario->email; ?></td>
                        <td><?= $usuario->login; ?></td>
                        <td><?= $tipo_usuario[$usuario->tipo_usuario]; ?></td>
                        <td><?= formataData($usuario->created_at); ?></td>
                        <?php if($usuario->tipo_usuario != 1 || $_SESSION["tipo"] == 1): ?>
                        <td><button class="btn btn-warning edit" data-id="<?= $usuario->id; ?>" data-target="#myModal" onclick="editar(<?= $i; ?>)" data-toggle="modal">Editar</a></button>
                        <td><button disabled class="btn btn-danger user" data-id="<?= $usuario->id; ?>" onclick="/*return confirmar();*/" data-action="<?= $router->route("appusuarios.inativar"); ?>">Inativar</a></button>
                        <?php endif; ?>
                        <!--td><a href="#" class="btn btn-danger cli" data-id="<?= $usuario->id; ?>" onclick="/*return confirmar();*/" data-action="<?= $router->route("appusuarios.inativar"); ?>">Inativar</a></td-->
                    </tr>

                    <script>
                        dados[<?= $i ?>] = {
                            id : "<?= $usuario->id ?>",
                            email : "<?= $usuario->email ?>",
                            nome : "<?= $usuario->nome ?>",
                            login : "<?= $usuario->login ?>",
                            senha : "<?= $usuario->senha ?>",
                            tipo_usuario : "<?= $usuario->tipo_usuario ?>"
                        }
                    </script>

                <?php $i++; endforeach; ?>
                </tbody>
            </table>
        </div>

        <?php
        //EXIBE A NAVEGAÇÃO DO PAGINADOR
        $link = URL_BASE."/usuarios/";

        //VARIÁVEIS VÊM DO CONTROLADOR AppPedidos
        $paginador->paginas($total, $pagina, $link);
        
        else:
        ?>
        <h4>Não existem usuários cadastrados ou ativos!</h4>
        <?php endif; ?>
        </div>
    </div>
</div>

<!-- CHAMA O MODAL -->
<!--?= $v->insert("fragmentos/modal-usuarios-cadastrar", []); ?-->

<!-- Modal -->
<div id="myModal" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Cadastrar Novo Usuário</h4>
      </div>
      <form class="form_ajax cadastro" action="#" method="post">
      <input type="hidden" name="id" id="id">
      <div class="modal-body">
        
        <div class="input-group">
            <span class="input-group-addon"><i class="material-icons">person</i></span>
            <input type="text" name="nome" id='nome' class="form-control input-lg" placeholder="Nome completo">
        </div>

        <br>

        <div class="row">
            <div class="col-md-6">
            <div class="input-group">
                <span class="input-group-addon" id="add-email"><i class="material-icons">email</i></span>
                <input type="text" name="email" id='email' class="form-control input-lg" placeholder="Email *" required>
            </div>
            <p id="msg-email"></p>
            </div>

            <div class="col-md-6">
            <div class="input-group">
                <span class="input-group-addon" id="add-login"><i class="material-icons">person</i></span>
                <input type="text" name="login" id='login' class="form-control input-lg" placeholder="Login de usuário">
            </div>
            <p id="msg-login"></p>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
            <div class="input-group">
                <span class="input-group-addon"><i class="material-icons">lock</i></span>
                <input type="password" name="senha" id='senha' class="form-control input-lg" placeholder="Senha *" required>
            </div>
            <p id="aviso">*deixe em branco para manter</p>
            <input type="hidden" name="senha_atual" id='senha_atual'>
            </div>

            <div class="col-md-6">
            <div class="input-group">
                <span class="input-group-addon"><i class="material-icons">lock</i></span>
                <select name="tipo_usuario" id="tipo_usuario" class="form-control input-lg" required="required">
                    <option disabled selected>Permissão *</option>
                    <?php if($_SESSION["tipo"] == 1): ?>
                    <option value="1">Master</option>
                    <?php endif; ?>
                    <option value="2">Administrador</option>
                    <option value="3">Funcionário</option>
                </select>
            </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-danger" data-dismiss="modal" id="cancelar">Cancelar</button>
        <button class="btn btn-primary" id="salvar">Salvar</button>
        <button class="btn btn-warning" id="editar">Editar</button>
      </div>

      </form>
    </div>

  </div>
</div>

<script>

    $("#cadastro").click(() => {

        $(".modal-title").html("Cadastrar Novo Usuário");
        $("#salvar").css("display", "inline");
        $("#cancelar").css("display", "inline");
        $("#editar").css("display", "none");
        $("#aviso").css("display", "none");

    })

    $("#email").blur(function(){
        var email = $(this).val();
        $.ajax({
            url: "<?=$router->route("appusuarios.email")?>",
            data: { email: email},

            type: "POST",
            beforeSend: function () {
                console.log("atualizando");
            },
            error: function() {
                console.log("O servidor não conseguiu processar o pedido");
            },
            success: function (callback) {
                console.log("callback ", callback);
                if(callback == "Não existe usuário com esse email"){
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

    $("#login").blur(function(){
        var login = $(this).val();
        $.ajax({
            url: "<?=$router->route("appusuarios.login")?>",
            data: {login: login},

            type: "POST",
            beforeSend: function () {
                console.log("atualizando");
            },
            error: function() {
                console.log("O servidor não conseguiu processar o pedido");
            },
            success: function (callback) {
                console.log("callback ", callback);
                if(callback == "Não existe usuário com esse login"){
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
        if(salvar == 1) {
            url = "<?= $router->route("appusuarios.funcionarios"); ?>";
        } else {
            url = "<?=$router->route("appusuarios.editar")?>";
        }

        var email = $("#email").val(),
            senha = $("#senha").val(),
            tipo_usuario = $("#tipo_usuario").val(),
            nome = $("#nome").val(),
            login = $("#login").val();

        var form = $("form");

        if(email == "" || (senha == "" && salvar == 1 ) || tipo_usuario == ""){

            alert("Preecha todos os dados necessários");

        } else {

            $.ajax({

                url: url,
                data: form.serialize(),
                type: "POST",
                //datatype: "json",
                beforeSend: function () {
                    console.log("atualizando");
                },
                error: function() {
                    console.log("O servidor não conseguiu processar o pedido");
                },
                success: function (callback) {
                    console.log("callback ", callback);
                    alert(callback);
                    if(callback == "Cadastrado Com Sucesso!"){
                        $('form').each (function(){
                            this.reset();
                        });
                        location.reload();
                    }
                    if(callback == "Atualizado Com Sucesso!"){
                        $('form').each (function(){
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
        var senha = dados[i]["senha"];
        var login = dados[i]["login"];
        var tipo_usuario = dados[i]["tipo_usuario"];

        console.log(id, email, nome, senha, login, tipo_usuario);

        $(".modal-title").html("Editar Usuário " + nome);
        $("#salvar").css("display", "none");
        $("#editar").css("display", "inline");
        $("#cancelar").css("display", "inline");
        $("#aviso").css("display", "block");
        $("#id").attr({"value": id});
        $("#email").attr({"value": email});
        $("#nome").attr({"value": nome});
        $("#senha_atual").attr({"value": senha});
        $("#login").attr({"value": login});
        $("#tipo_usuario option[value=" + tipo_usuario + "]").attr({"selected": true});

    }

    function cancelar() {
        $("#salvar").css("display", "inline");
        $("#editar").css("display", "none");
        $("#cancelar").css("display", "none");
        $("#id").attr({"value": ""});
        $("#email").attr({"value": ""});
        $("#nome").attr({"value": ""});
        $("#senha_atual").attr({"value": ""});
        $("#login").attr({"value": ""});
        $("#tipo_usuario").attr({"selected": true});

    }
</script>