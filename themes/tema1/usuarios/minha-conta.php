<?php $v->layout("_theme"); ?>

<?php

$tipo_usuario = array(
  1 => "MASTER",
  2 => "ADMINISTRADOR",
  3 => "FUNCION&Aacute;RIO",
  4 => "PRODUTOR",
  5 => "AFILIADO",
  6 => "COMPRADOR"
);

$tipo_doc = array(
  1 => "CPF",
  2 => "CNPJ",
);

$grupo = array(
  1 => "Iniciante",
  2 => "Especialista",
  3 => "Elite",
  4 => "Mestre",
  5 => "Grão Mestre",
  6 => "Guru",
  7 => "Lendário",
);

?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">

  <!--     Fonts and icons     -->
  <link href="https://fonts.googleapis.com/css?family=Poppins:200,300,400,600,700,800" rel="stylesheet" />

  <!-- CSS Files -->
  <link href="<?= url("themes/tema1/dist/css/perfil/black.css?" . time()) ?>" rel="stylesheet" />

  <section class="content">
    <div class="main-panel">
      <br>

      <style>
        .topo {
          /* background: url('<?= url("themes/imgs/topo-perfil.png") ?>') no-repeat; */
          /* background-color: #0b0024; */
          background-size: 85em;
          background-position: center -100px;
          padding: 2em 0;
        }
      </style>

      <div class="content">
        <div class="row">

          <div class="col col-md-12">
            <div class="card card-user author topo bg-azul-gradient">
              <a href="javascript:void(0)" data-toggle="modal" data-target="#myFoto" title="Alterar foto de perfil">
                <?php if ($usuario->foto) : ?>
                  <img class="avatar" src="<?= url("uploads/perfil/$usuario->foto") ?>" alt="...">
                <?php else : ?>
                  <img class="avatar" src="<?= url("uploads/perfil/user.png") ?>" alt="...">
                <?php endif; ?>
                <h3 class="title txt-topo"><?= $usuario->nome ?></h3>
              </a>

              <p class="description txt-topo"> <?= $tipo_usuario[$usuario->tipo_usuario]; ?> <?= strtoupper(@$grupo[$usuario->grupo]); ?> </p>
              <span class="txt-topo"><?= @$_SESSION["verificado"] == 1 ? "<i class='fas fa-thumbs-up text-white'></i> verificado" : "<i class='fas fa-clock text-warning'></i> Conta não verificada" ?></span>

              <!--p class="description1 txt-topo"><b>42</b> produtos lançados e <b>678</b> vendas efetuadas. </p-->
            </div>
          </div>

        </div><!-- FIM ROW -->
      </div><!-- FIM CONTENT -->

      <div class="content">
        <div class="row">
          <div class="col col-md-12 ">
            <div class="card conteudo">
              <div class="card-header">
                <div class="card-tools">
                  <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                    <i class="fas fa-minus"></i>
                  </button>
                </div>
                <h4 class="title">Editar Perfil</h4>
              </div>
              <div class="card-body">

                <!-- < ?php
                if (!$asaasClientes) {
                ?>
                  <div class="alert alert-warning">
                    Você precisa concluir seu cadastro para poder usar todas as funcionalidades. <a href="<?= url("usuarios/perfil/" . $_SESSION["id"]) ?>">Concluir cadastro</a>
                  </div>
                < ?php
                }
                ?> -->

                <?php if (@$_GET["msg"] && @$_GET["classCss"]) : ?>
                  <div class="card card-body <?= $_GET["classCss"] ?>"><?= $_GET["msg"] ?></div>
                <?php endif; ?>
                <p class="alert bg-vermelho-gradient">Preencha os dados corretamente, anexe seus documentos para ter o perfil verificado e poder utilizar todos os nossos serviços</p>

                <form action="#" method="post">
                  <input type="hidden" name="id" id="id" value="<?= $_SESSION["id"] ?>">

                  <div class="row">
                    <div class="col-md-4">
                      Nome Completo:
                      <input type="text" <?= $usuario->verificado == 1 ? "readonly" : "" ?> name="nome" id='nome' class="form-control input-lg" value="<?= $dados->nome ?>">
                    </div>

                    <div class="col-md-4">
                      Nome de Exibição:
                      <input type="text" name="nome_abreviado" id='nome_abreviado' class="form-control input-lg" value="<?= $usuario->nome_abreviado ?>">
                    </div>

                    <div class="col-md-4">
                      Login:
                      <input type="text" name="login" id='login' class="form-control input-lg" value="<?= $usuario->login ?>">
                    </div>
                  </div>

                  <br>

                  <div class="row">
                    <div class="col-md-4">
                      Email:
                      <input type="text" <?= $usuario->verificado == 1 ? "readonly" : "" ?> name="email" id='email' class="form-control input-lg" value="<?= $dados->email ?>">
                    </div>

                    <div class="col-md-4">
                      Senha:
                      <input type="password" name="senha" id='senha' class="form-control input-lg" value="">
                      <input type="hidden" name="senha_atual" value="<?= $usuario->senha ?>">
                      <small>* Deixe em branco para manter a mesma</small>
                    </div>

                    <div class="col-md-4">
                      Telefone/Celular:
                      <input type="text" name="telefone" id='telefone' class="form-control input-lg" value="<?= $dados->telefone ?>">
                    </div>
                  </div>

                  <br>

                  <div class="row">
                    <div class="col-md-4">
                      Tipo de Documento
                      <select name="tipo_doc" id="tipo_doc" class="form-control input-lg" <?= $usuario->verificado == 1 ? "readonly" : "" ?>>
                        <option value="1" <?= ($dados->tipo_doc == 1) ? 'selected' : '' ?>>CPF</option>
                        <option value="2" <?= ($dados->tipo_doc == 2) ? 'selected' : '' ?>>CNPJ</option>
                      </select>
                    </div>

                    <div class="col-md-4">
                      CPF/CNPJ:
                      <input type="text" name="cpf" id='cpf' class="form-control input-lg" value="<?= $dados->cpf_cnpj ?>" <?= $usuario->verificado == 1 ? "readonly" : "" ?>>
                    </div>

                    <div class="col-md-4">
                      Anexar documentos:<br>
                      <a href="javascript:void(0)" data-toggle="modal" data-target="#modalDocs" title="Enviar Documentos">
                        <button class="btn btn-primary"> &nbsp; &nbsp; Escolher documentos &nbsp; &nbsp; </button>
                      </a>
                    </div>
                  </div>

                  <br>

                  <div class="row box-pj">
                    <div class="col-md-4">
                      Razao Social
                      <input type="text" name="razao_social" id='razao_social' class="form-control input-lg" value="<?= $dados->razao_social ?>">
                    </div>

                    <div class="col-md-4">
                      Nome Fantasia:
                      <input type="text" name="nome_fantasia" id='nome_fantasia' class="form-control input-lg" value="<?= $dados->nome_fantasia ?>">
                    </div>

                    <div class="col-md-4">
                      Tipo da empresa:
                      <select name="tipo_empresa" id="tipo_empresa" class="form-control input-lg" <?= $usuario->verificado == 1 ? "readonly" : "" ?>>
                        <option value="MEI" <?= ($dados->tipo_empresa == "MEI") ? 'selected' : '' ?>>Micro Empreendedor Individual - MEI</option>
                        <option value="LIMITED" <?= ($dados->tipo_empresa == "LIMITED") ? 'selected' : '' ?>>Empresa Limitada</option>
                        <option value="INDIVIDUAL" <?= ($dados->tipo_empresa == "INDIVIDUAL") ? 'selected' : '' ?>>Empresa Individual</option>
                        <option value="ASSOCIATION" <?= ($dados->tipo_empresa == "ASSOCIATION") ? 'selected' : '' ?>>Associação</option>
                      </select>
                    </div>
                  </div>

                  <br><br>

                  <div class="nav nav-tabs" id="product-tab" role="tablist" style="font-family:inter">
                    <a class="nav-item nav-link active" id="endereco_tap" data-toggle="tab" href="#endereco" role="tab" aria-controls="product-desc" aria-selected="true">Endereço</a>
                    <!-- <a class="nav-item nav-link" id="entrega_tap" data-toggle="tab" href="#entrega" role="tab" aria-controls="product-comments" aria-selected="false">Endereço de Entrega</a> -->
                  </div>

                  <div class="tab-content p-3" id="nav-tabContent" style="font-family:inter">
                    <div class="tab-pane fade show active" id="endereco" role="tabpanel" aria-labelledby="endereco_tab">

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
                    </div>

                    <!-- <div class="tab-pane fade" id="entrega" role="tabpanel" aria-labelledby="entrega_tab">

                      <div class="row">
                        <div class="col-md-4">
                          CEP:
                          <input type="text" name="cep_entrega" id='cep_entrega' class="form-control input-lg" value="<?= $dados->cep_entrega ?>">
                        </div>

                        <div class="col-md-6">
                          Rua:
                          <input type="text" name="rua_entrega" id='rua_entrega' class="form-control input-lg" value="<?= $dados->rua_entrega ?>">
                        </div>

                        <div class="col-md-2">
                          Número:
                          <input type="text" name="numero_entrega" id='numero_entrega' class="form-control input-lg" value="<?= $dados->numero_entrega ?>">
                        </div>
                      </div>

                      <br>

                      <div class="row">
                        <div class="col-md-6">
                          Complemento:
                          <input type="text" name="complemento_entrega" id='complemento_entrega' class="form-control input-lg" value="<?= $dados->complemento_entrega ?>">
                        </div>

                        <div class="col-md-6">
                          Bairro:
                          <input type="text" name="bairro_entrega" id='bairro_entrega' class="form-control input-lg" value="<?= $dados->bairro_entrega ?>">
                        </div>
                      </div>

                      <br>

                      <div class="row">
                        <div class="col-md-6">
                          Cidade:
                          <input type="text" name="cidade_entrega" id='cidade_entrega' class="form-control input-lg" value="<?= $dados->cidade_entrega ?>">
                        </div>

                        <div class="col-md-3">
                          Estado:
                          <!--input type="text" name="estado" id='estado' class="form-control input-lg"-- >
                          < ?= $v->insert("usuarios/uf", ["estado" => $dados->estado_entrega, "entrega" => "_entrega"]); ?>
                        </div>

                        <div class="col-md-3">
                          País:
                          <input type="text" name="pais_entrega" id='pais_entrega' class="form-control input-lg" value="<?= $dados->pais_entrega ?>">
                        </div>
                      </div>
                    </div> -->
                  </div>

                  <br>
                  <button type="submit" class="btn btn-primary" id="salvar"> &nbsp; &nbsp; Salvar &nbsp; &nbsp; </button>
                  <br>
                </form>
              </div>
            </div>

            <div class="card">
              <div class="card-body">

                <?php if ($_SESSION["tipo"] == 6) : ?>
                  Se você deseja se tornar um produtor ou afiliado RedRocket, clique no botão abaixo <br>
                  <!--button class="btn btn-primary" id="afiliado">Tornar-se Afiliado/Produtor</button-->
                  <button class="btn btn-outline-primary" data-toggle="modal" data-target="#modal-lg" style="font-family:inter" id="cadastro">Tornar-se Afiliado/Produtor <i class="fa fa-chevron-right"></i> </button>
                  <br>
                <?php else : ?>
                  <h4>Taxas Inclusas</h4>
                  <hr>

                  <?php
                  $diferenca = strtotime(date("Y-m-d 23:59:59")) - strtotime($usuario->created_at);
                  $dias = floor($diferenca / (60 * 60 * 24));
                  ?>

                  <table class="table table-striped">
                    <thead>
                      <tr>
                        <th scope="rol">Taxa transação*:</th>
                        <th scope="rol">Taxa assinatura*:</th>
                        <th scope="rol">Saque cartão**:</th>
                        <th scope="rol">Saque boleto**:</th>
                        <th scope="rol">Saque pix**:</th>
                        <th scope="rol"></th>
                      </tr>
                    </thead>

                    <tbody>
                      <tr>
                        <td><?= number_format($usuario->taxa_redrocket, 2, ",", "."); ?>%</td>
                        <td><?= number_format($usuario->taxa_assinatura, 2, ",", "."); ?>%</td>
                        <td><?= $usuario->tempo_saque; ?> dias</td>
                        <td><?= $usuario->tempo_boleto; ?> dias</td>
                        <td><?= $usuario->tempo_pix; ?> dias</td>

                        <!-- < ?php if ($dias > 30) : ?>
                          <td><button class="btn btn-warning" data-toggle="modal" data-target="#modal-taxas"><i class="fa fa-edit"></i></button></td>
                        < ?php endif ?> -->
                      </tr>
                    </tbody>
                  </table>
                  <small>* taxa cobrada uma vez na transação sobre o valor do produto. Somente uma taxa será aplicada de acordo com o tipo de cobraça</small><br>
                  <small>** tempo que a comissão ficará disponível para saque</small>

                  <!-- <a href="<?= url("banco"); ?>">Cadastrar contas bancárias</a><br> -->
                  <br>
                <?php endif; ?>

              </div>

            </div>
          </div>

        </div>
      </div>
    </div>
</div>

</section>

<!-- Modal -->
<div id="myFoto" class="modal fade" role="dialog">
  <div class="modal-dialog modal-lg">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Editar <?= $usuario->nome; ?></h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>

      <form action="<?= $router->route("appusuarios.fotoPerfil"); ?>" method="post" enctype="multipart/form-data">

        <div class="modal-body">
          <input type="hidden" name="idU" value="<?= $usuario->id ?>">
          <input type="hidden" name="foto_atual" value="<?= $usuario->foto ?>">

          Dimensões da foto: máximo 500px X 500px <br>
          Tamanho máximo: 1 MB <br>
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

<!-- Modal -->
<div id="modalDocs" class="modal fade" role="dialog">
  <div class="modal-dialog modal-lg">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Editar <?= $usuario->nome; ?></h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>

      <form action="<?= $router->route("appusuarios.documentoPerfil"); ?>" method="post" enctype="multipart/form-data">
        <div class="modal-body">
          <input type="hidden" name="idU" value="<?= $usuario->id ?>">
          <input type="hidden" name="foto_atual" value="<?= $usuario->foto ?>">
          Formatos aceitos: pdf, png, jpg, jpeg, zip, rar <br>
          Documentos necessários: RG ou CNH (frente e verso) <br>
          Para pessoas júridicas Cartão CNPJ e Selfie com documento de identificação <br><br>

          Enviar RG/CNH (Frente)/Cartão CNPJ:
          <input type="file" name="documento1" id="" class="form-control" <?= $usuario->verificado == 1 ? "disabled" : "" ?>>
          <?= $usuario->documento1 != "" ? "<a href='" . url("uploads/documentos/$usuario->documento1") . "' target='_blank'>Ver documento</a>" : "" ?><br><br>
          Enviar RG/CNH (Verso):
          <input type="file" name="documento2" id="" class="form-control" <?= $usuario->verificado == 1 ? "disabled" : "" ?>>
          <?= $usuario->documento2 != "" ? "<a href='" . url("uploads/documentos/$usuario->documento2") . "' target='_blank'>Ver documento</a>" : "" ?>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-danger" data-dismiss="modal" id="cancelar">Cancelar</button>
          <button class="btn btn-primary">Salvar</button>
        </div>
      </form>

    </div>

  </div>
</div>

<!-- cadastar -->
<div class="modal fade" id="modal-lg">
  <div class="modal-dialog">
    <div class="modal-content">

      <div class="modal-header">
        <h3 class="card-title"><i class="fa fa-info-circle"></i> O que é afiliado digital?</h3>
        <button type="button" class="close" data-dismiss="modal"> &times;</button>
      </div>

      <div class="modal-body">
        <p>
          Se você está antenado no mundo digital e se interessa pelo empreendedorismo,
          provavelmente já ouviu falar sobre o programa de Afiliados.
        </p>

        <p>
          <b>Mas afinal, o que é afiliado?</b> <br>
          Bom, trata-se de alguém que indica produtos feitos por outras pessoas e recebe comissões
          pelas vendas realizadas.
        </p>

        Se você deseja se tornar um produtor ou afiliado RedRocket, clique no botão abaixo <br><br>
        <button class="btn btn-outline-primary btn-sm" id="afiliado">Tornar-se Afiliado/Produtor</button>
      </div>
    </div>

  </div>

</div>

<!-- Modal -->
<div id="modal-taxas" class="modal fade" role="dialog">
  <div class="modal-dialog modal-lg">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Alterar taxas de recebimento</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>

      <form action="<?= $router->route("appusuarios.taxas"); ?>" method="post" enctype="multipart/form-data">
        <div class="modal-body" style="background: #f4f6f9">
          <input type="hidden" name="idU" value="<?= $usuario->id ?>">
          <input type="hidden" name="foto_atual" value="<?= $usuario->foto ?>">

          <div class="card card-body">
            Escolha o tempo e a taxa de recebimento:

            <select name="taxas" id="taxas" class="form-control">
              <option value="30" <?= $usuario->tempo_saque == 30 ? "selected" : "" ?>>30 dias - 3,49%</option>
              <option value="15" <?= $usuario->tempo_saque == 15 ? "selected" : "" ?>>15 dias - 5,49%</option>
              <!-- <option value="3" <?= $usuario->tempo_saque == 3 ? "selected" : "" ?>>3 dias - 7,49%</option> -->
            </select>
          </div>
        </div>

        <div class="modal-footer justify-content-between">
          <button type="button" class="btn btn-danger" data-dismiss="modal" id="cancelar">Cancelar</button>
          <button class="btn btn-primary">Salvar</button>
        </div>
      </form>
    </div>
  </div>
</div>
<!-- end cadastrar -->

<?= $v->start("scripts") ?>
<script>
  function mostrar() {
    var tipo = $("#tipo_doc").val();
    if (tipo == 1) {
      $(".box-pj").hide();
    } else {
      $(".box-pj").show();
    }
  }
  mostrar();

  $("#tipo_doc").change(() => {
    mostrar();
  });

  $("#salvar").click(function(e) {
    e.preventDefault();
    salvarEditar("<?= $router->route("appusuarios.perfilEditar"); ?>");
  });

  $("#afiliado").click(function(e) {
    e.preventDefault();
    salvarEditar("<?= $router->route("appusuarios.tipoUsuario"); ?>");
  });

  function salvarEditar(url) {

    var form = $("form");

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
        console.log("callback ", callback);
        alert(callback);
        if (callback == "Atualizado Com Sucesso!") {
          location.reload();
        }
      }

    });
  }

  $("#cep").focusout(function() {
    $.ajax({
      url: 'https://api.pagar.me/1/zipcodes/' + $(this).val(),

      dataType: 'json',
      success: function(resposta) {
        $("#rua").val(resposta.street);
        $("#bairro").val(resposta.neighborhood);
        $("#cidade").val(resposta.city);
        $("#estado").val(resposta.state);
        $("#pais").val("BR");
      }
    });
  });

  $("#cep_entrega").focusout(function() {
    $.ajax({
      url: 'https://api.pagar.me/1/zipcodes/' + $(this).val(),

      dataType: 'json',
      success: function(resposta) {
        $("#rua_entrega").val(resposta.street);
        $("#bairro_entrega").val(resposta.neighborhood);
        $("#cidade_entrega").val(resposta.city);
        $("#estado_entrega").val(resposta.state);
        $("#pais_entrega").val("BR");
      }
    });
  });
</script>
<?= $v->end() ?>

</body>

</html>