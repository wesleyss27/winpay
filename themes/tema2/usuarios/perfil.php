<?php

$tipo_usuario = array(
    1 => "MASTER",
    2 => "ADMINISTRADOR",
    3 => "FUNCIONÁRIO",
    4 => "PRODUTOR",
    5 => "AFILIADO",
    6 => "COMPRADOR"
);

$tipo_doc = array(
    1 => "CPF",
    2 => "CNPJ",
);

$grupo = array(
    1 => "Grupo 1",
    2 => "Bronze",
    3 => "Prata",
    4 => "Ouro"
);

$i = 1;
?>

<?php $v->layout("_theme"); ?>
    
    <div class="principal bg-white">
        <?= $v->insert("fragmentos/msg-cadastrar-editar", []); ?>
        <button type="button" class="btn btn-primary btn-lg" data-toggle="modal" data-target="#myModal" id="cadastro">Editar Perfil</button>
    </div>

    <div class="principal bg-white row">
        <div class="col-md-3 center">
            <?php if($usuario->foto): ?>
            <img src="<?= url("uploads/perfil/$usuario->foto"); ?>" alt="" width="200px">
            <?php else: ?>
            <img src="<?= url("uploads/perfil/user.png"); ?>" alt="" width="200px">
            <?php endif; ?>
            
            <button class="btn btn-primary" data-toggle="modal" data-target="#myFoto">Alterar Foto</button>
        </div>

        <div class="col-md-9">
            
            <h4><?= $usuario->nome; ?></h4>
            <b>Nome de Exibição: </b><?= $usuario->nome_abreviado; ?><br>
            <b>Email: </b><?= $usuario->email; ?><br>
            <b>Login:</b> <?= $usuario->login; ?><br><br>

            <b>Telefone:</b> <?= $dados->telefone; ?><br>
            <b>Documento: </b><?= $tipo_doc[$dados->tipo_doc]; ?> <?= $dados->cpf_cnpj; ?><br><br>

            <b>Tipo: </b><?= $tipo_usuario[$usuario->tipo_usuario]; ?><br>
            <b>Data de cadastro: </b><?= formataData($usuario->created_at); ?><br>

            <h4>Endereço</h4>
            <?php if($dados->rua): ?>
            <?= $dados->rua; ?>, <?= $dados->numero; ?>, <?= $dados->complemento; ?> - <?= $dados->complemento; ?><br>
            <?= $dados->cidade; ?> - <?= $dados->estado; ?>, <?= $dados->pais; ?><br><br>
            <?php else: ?>
            Complete o seu cadastro
            <?php endif; ?>

            <h4>Taxas Inclusas</h4>
            <b>Taxa de saque: </b><?= number_format($usuario->taxa_saque, 2, ",", "."); ?>%<br>
            <b>Tempo de saque:</b> <?= $usuario->tempo_saque; ?> dias<br>
            <b>Grupo:</b> <?= $grupo[$usuario->grupo]; ?><br><br>

        </div>
    </div>

<!-- Modal -->
<div id="myModal" class="modal fade" role="dialog">
  <div class="modal-dialog modal-lg">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Editar <?= $usuario->nome; ?></h4>
      </div>
      <form class="form_ajax cadastro" action="#" method="post">
      <div class="modal-body">
        
        <input type="hidden" name="id" id="id" value="<?= $_SESSION["id"] ?>">

        <div class="row">
            <div class="col-md-6">
                Nome Completo:
                <input type="text" name="nome" id='nome' class="form-control input-lg" value="<?= $dados->nome ?>">
            </div>

            <div class="col-md-6">
                Nome de Exibição:
                <input type="text" name="nome_abreviado" id='nome_abreviado' class="form-control input-lg" value="<?= $usuario->nome_abreviado ?>">
            </div>
        </div>

        <br>

        <div class="row">
            <div class="col-md-6">
                Email:
                <input type="text" name="email" id='email' class="form-control input-lg" value="<?= $dados->email ?>">
            </div>

            <div class="col-md-6">
                Login:
                <input type="text" name="login" id='login' class="form-control input-lg" value="<?= $usuario->login ?>">
            </div>
        </div>

        <br>

        <div class="row">
            <div class="col-md-6">
                Senha: <small>* Deixe em branco para manter a mesma</small>
                <input type="password" name="senha" id='senha' class="form-control input-lg" value="">
                <input type="hidden" name="senha_atual" value="<?= $usuario->senha ?>">
            </div>
            
            <div class="col-md-6">
                Telefone/Celular:
                <input type="text" name="telefone" id='telefone' class="form-control input-lg" value="<?= $dados->telefone ?>">
            </div>
        </div>
        
        <br>

        <div class="row">
            <div class="col-md-6">
                Tipo de Documento
                <select name="tipo_doc" id="tipo_doc" class="form-control input-lg">
                    <option value="1" <?=($dados->tipo_doc == 1)?'selected':''?>>CPF</option>
                    <option value="2" <?=($dados->tipo_doc == 2)?'selected':''?>>CNPJ</option>
                </select>
            </div>

            <div class="col-md-6">
                CPF/CNPJ:
                <input type="text" name="cpf" id='cpf' class="form-control input-lg" value="<?= $dados->cpf_cnpj ?>">
            </div>
        </div>

        <h4>Endereço</h4>

        <div class="row">
            <div class="col-md-4">
                CEP:
                <input type="text" name="cep" id='cep' class="form-control input-lg" value="<?= $dados->cep ?>">
            </div>

            <div class="col-md-6">
                Rua:
                <input type="text" name="rua" id='rua' class="form-control input-lg" value="<?= $dados->rua ?>">
            </div>

            <div class="col-md-2">
                Número:
                <input type="text" name="numero" id='numero' class="form-control input-lg" value="<?= $dados->numero ?>">
            </div>
        </div>

        <br>

        <div class="row">
            <div class="col-md-6">
                Complemento:
                <input type="text" name="complemento" id='complemento' class="form-control input-lg" value="<?= $dados->complemento ?>">
            </div>

            <div class="col-md-6">
                Bairro:
                <input type="text" name="bairro" id='bairro' class="form-control input-lg" value="<?= $dados->bairro ?>">
            </div>
        </div>

        <br>

        <div class="row">
            <div class="col-md-6">
                Cidade:
                <input type="text" name="cidade" id='cidade' class="form-control input-lg" value="<?= $dados->cidade ?>">
            </div>

            <div class="col-md-3">
                Estado:
                <!--input type="text" name="estado" id='estado' class="form-control input-lg"-->
                <?= $v->insert("usuarios/uf", ["estado" => $dados->estado]); ?>
            </div>

            <div class="col-md-3">
                País:
                <input type="text" name="pais" id='pais' class="form-control input-lg" value="<?= $dados->pais ?>">
            </div>
        </div>

        <br>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-danger" data-dismiss="modal" id="cancelar">Cancelar</button>
        <button class="btn btn-primary" id="salvar">Salvar</button>
      </div>

      </form>
    </div>

  </div>
</div>

<!-- Modal -->
<div id="myFoto" class="modal fade" role="dialog">
  <div class="modal-dialog modal-lg">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Editar <?= $usuario->nome; ?></h4>
      </div>
      
      <form action="<?= $router->route("appusuarios.fotoPerfil"); ?>" method="post" enctype="multipart/form-data">

      <div class="modal-body">
        <input type="hidden" name="idU" value="<?= $usuario->id ?>">

        Escolher Foto:
        <input type="file" name="foto" id="" class="form-control">
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-danger" data-dismiss="modal" id="cancelar">Cancelar</button>
        <button class="btn btn-primary">Salvar</button>
      </div>

      </form>

    </div>

  </div>
</div>



<script>

    $("#salvar").click(function(e) {
        e.preventDefault();
        salvarEditar("<?= $router->route("appusuarios.perfilEditar"); ?>");
    });
    
    function salvarEditar(url) {

        var form = $("form");

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
                if(callback == "Atualizado Com Sucesso!"){
                    location.reload();
                }
            }

        });
    }

    $("#cep").focusout(function(){
        $.ajax({
            url: 'https://api.pagar.me/1/zipcodes/' + $(this).val(),
            
            dataType: 'json',
            success: function(resposta){
                $("#rua").val(resposta.street);
				$("#bairro").val(resposta.neighborhood);
				$("#cidade").val(resposta.city);
				$("#estado").val(resposta.state);
				$("#pais").val("BR");
            }
        });
    });
</script>