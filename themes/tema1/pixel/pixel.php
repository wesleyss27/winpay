<?php

$v->layout("_theme");
$i = 1;

$status = array(
  1 => "EM ANÁLISE",
  2 => "APROVADO",
  3 => "REPROVADO",
  4 => "BLOQUEADO"
);
$ativo = array(
  1 => "SIM",
  0 => "NÃO"
);

if ($_SESSION["tipo"] == 6) {
  header("Location: " . url("vendas"));
}
?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <div class="container-fluid">
    <section class="content-header">

      <?= $section->getTipoSecaoLayout() ?>

      <!-- TÍTULO -->
      <div class="row mb-2">
        <div class="col-sm-6" style="margin-bottom:10px">
          <h1 style="font-family:inter"><?= $title ?></h1>
        </div>
        <div class="col-sm-6" style="font-family:inter">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="<?= url() ?>">Início</a></li>
            <li class="breadcrumb-item active">Pixel</li>
          </ol>
        </div>
      </div>
    </section>
    <!-- FIM TÍTULO -->

    <!-- Main content -->

    <style>
      .zoom:hover img {
        -moz-transform: scale(1.2);
        -webkit-transform: scale(1.2);
        transform: scale(1.2);
      }

      .mobile-n {
        display: block;
      }

      .desktop-n {
        display: none;
      }

      .hr-mobile {
        border-bottom: 1px solid #eee;
        margin: 10px 0;
      }

      @media(max-width: 500px) {
        .mobile-n {
          display: none;
        }

        .desktop-n {
          display: block;
        }
      }
    </style>

    <section class="content">

      <div class="card card-body bg-success">
        <span>O pixel <b>só será ativado</b> dentro de um <b>checkout (produtor)</b> ou de uma <b>campanha</b></span>
      </div>

      <!-- CARD -->
      <div class="card">

        <!-- CARD HEADER -->
        <?php if (!@$_SESSION["subusuario"] || $permissao->adicionar == 1) : ?>
          <div class="card-header">

            <!-- LINHA BOTÕES -->
            <div class="row">

              <div class="col-sm-2 text-center zoom">
                <div class="card" style="border-radius: 30px">
                  <a class="btn" data-toggle="modal" data-target="#modal-lg" style="font-family:inter" id="cadastrofb">
                    <img src="<?= url('themes/imgs/Facebook.png') ?>" width='100%' style="border-radius: 20px">
                  </a>
                </div>
              </div>

              <div class="col-sm-2 text-center zoom">
                <div class="card" style="border-radius: 30px">
                  <a class="btn" data-toggle="modal" data-target="#modal-lg" style="font-family:inter" id="cadastrotb">
                    <img src="<?= url('themes/imgs/Taboola.png') ?>" width='100%'>
                  </a>
                </div>
              </div>

              <div class="col-sm-2 text-center zoom">
                <div class="card" style="border-radius: 30px">
                  <a class="btn" data-toggle="modal" data-target="#modal-lg" style="font-family:inter" id="googleAnalitics">
                    <img src="<?= url('themes/imgs/GoogleAnalitics.png') ?>" width='100%'>
                  </a>
                </div>
              </div>

              <div class="col-sm-2 text-center zoom">
                <div class="card" style="border-radius: 30px">
                  <a class="btn" data-toggle="modal" data-target="#modal-lg" style="font-family:inter" id="googleAs">
                    <img src="<?= url('themes/imgs/GoogleAs.png') ?>" width='100%'>
                  </a>
                </div>
              </div>

              <div class="col-sm-2 text-center zoom">
                <div class="card" style="border-radius: 30px">
                  <a class="btn" data-toggle="modal" data-target="#modal-lg" style="font-family:inter" id="gtm">
                    <img src="<?= url('themes/imgs/GoogleTagManager.png') ?>" width='100%'>
                  </a>
                </div>
              </div>

              <div class="col-sm-2 text-center zoom">
                <div class="card" style="border-radius: 30px">
                  <a class="btn" data-toggle="modal" data-target="#modal-lg" style="font-family:inter;" id="TikTok">
                    <img src="<?= url('themes/imgs/TikTok2.png') ?>" width="100%">
                  </a>
                </div>
              </div>

            </div>
            <!-- FIM LINHA BOTÕES -->

          </div>
        <?php endif ?>
        <!-- FIM CARD HEADER -->

        <div class="card-body" style="background: #f4f6f9">
          <?php if ($pixel) : ?>
            <script>
              var dados = [];
            </script>
            <div style="overflow-x: auto;">

              <div class="mobile-n card card-body bg-verde-octa3" style="font-size:13px; font-family:inter">
                <div class="row">
                  <div class="col">Plataforma</div>
                  <div class="col">Pixel</div>
                  <div class="col">Nome</div>
                  <div class="col">Valor</div>
                  <div class="col">Ativo</div>
                  <div class="col">Executar no Checkout</div>
                  <div class="col">Executar no Boleto</div>
                  <div class="col">Ação</div>
                </div>
              </div>

              <?php foreach ($pixel as $pixel) : ?>
                <!-- <div class="carregar card card-body">
                  <div class="bg-azul-octa2 carrega-content" style="font-size: 5px; border-radius: 50px;"></div>
                </div> -->

                <div class="card card-body efeito-slow">
                  <div class="row" style="font-family:inter; font-size:12px; margin: 0 15px">
                    <div class="col-md col-4">
                      <?php if (!@$_SESSION["subusuario"] || $permissao->editar == 1) : ?>
                        <button class="btn btn-link" data-target="#modal-lg" onclick="editar(<?= $i; ?>)" data-toggle="modal"><img src="<?= url('themes/imgs/') . $pixel->plataforma; ?>.png" width='90px'></button>
                      <?php else : ?>
                        <img src="<?= url('themes/imgs/') . $pixel->plataforma; ?>.png" width='90px'>
                      <?php endif ?>
                    </div>
                    <div class="col-md col-4"><?= mb_strimwidth($pixel->id_pixel, 0, 19, "..."); ?></div>
                    <div class="col-md col-4"><?= $pixel->nome; ?></div>

                    <div class="desktop-n hr-mobile col-md-12"></div>

                    <div class="col-md col-4 mobile-n">R$ <?= number_format($pixel->valor, 2, ",", "."); ?></div>
                    <div class="col-md col-4"><span class="desktop-n">Checkout? </span><?= $ativo[$pixel->executar_checkout] ?></div>
                    <div class="col-md col-4"><span class="desktop-n">Boleto? </span><?= $ativo[$pixel->status] ?></div>
                    <div class="col-md col-4 mobile-n"><?= $ativo[$pixel->boleto] ?></div>
                    <?php if ($pixel->usuario == $section->getId()) : ?>
                      <div class="col-md col-4">
                        <!--
                      <a class="btn btn-warning btn-sm" data-toggle="modal" data-target="#ideditar"><i class="fas fa-edit"></i></a>-->
                        <?php if (!@$_SESSION["subusuario"] || $permissao->editar == 1) : ?>
                          <button class="btn btn-warning edit btn-sm" data-target="#modal-lg" onclick="editar(<?= $i; ?>)" data-toggle="modal"><i class="fas fa-edit"></i></button>
                        <?php endif ?>

                        <?php if (!@$_SESSION["subusuario"] || $permissao->excluir == 1) : ?>
                          <button class="btn btn-danger btn-excluir btn-sm" onclick="excluir(<?= $pixel->id; ?>)"><i class="fas fa-times"></i></button>
                        <?php endif ?>
                      </div>
                    <?php endif; ?>
                  </div>
                </div>

                <script>
                  dados[<?= $i ?>] = {
                    plataforma: "<?= $pixel->plataforma ?>",
                    id: "<?= $pixel->id ?>",
                    id_pixel: "<?= $pixel->id_pixel ?>",
                    tag_conversao: "<?= $pixel->tag_conversao ?>",
                    evento_taboola: "<?= $pixel->evento_taboola ?>",
                    purchase_taboola: "<?= $pixel->purchase_taboola ?>",
                    nome: "<?= $pixel->nome ?>",
                    observacao: "<?= $pixel->observacao ?>",
                    valor: "<?= $pixel->valor ?>",
                    boleto: "<?= $pixel->boleto ?>",
                    status: "<?= $pixel->status ?>",
                    executar_checkout: "<?= $pixel->executar_checkout ?>",
                    tipo_integracao: "<?= $pixel->tipo_integracao ?>",
                    token_acesso: "<?= $pixel->token_acesso ?>",
                    dominio_url: "<?= $pixel->dominio_url ?>",
                  }
                </script>

              <?php $i++;
              endforeach; ?>
            </div>

          <?php
            //EXIBE A NAVEGAÇÃO DO PAGINADOR
            $link = URL_BASE . "/pixel/";

            //VARIÁVEIS VÊM DO CONTROLADOR AppPedidos
            $paginador->paginas($total, $pagina, $link);

          else :
          ?>
            <div class="card card-body">
              <p>Nenhum pixel cadastrado!</p>
            </div>
          <?php endif; ?>
        </div>
      </div>
      <!-- FIM CARD -->

    </section>
  </div>
</div>

<button class="btn btn-primary btn-ajuda" id="btn-ajuda">
  <i class="far fa-question-circle"></i> <span class="desktop">Ajuda</span>
</button>

<style>

</style>
<?php
$assunto = "Preciso de Ajuda";
$para = "suporte.oficial@redrocketpay.com";
$mensagem = "Email: {$_SESSION["nome"]} %0D" .
  "ID: {$_SESSION["id"]} %0D" .
  "Página: {$_SERVER["REQUEST_URI"]} %0D%0D" .
  "Olá, sou " . $_SESSION["nome"] . " e gostaria de ajuda. Estou com o seguinte problema: ";

$mensagem_whats = "Email: {$_SESSION["nome"]} %0D" .
  "ID: {$_SESSION["id"]} %0D" .
  "Página: {$_SERVER["REQUEST_URI"]} %0D%0D" .
  "Olá, sou " . $_SESSION["nome"] . " e gostaria de ajuda.";
?>

<div class="ajuda">
  <div class="ajuda-titulo">
    Assistente
    <button type="button" class="close" id="ajuda-fechar"> &times;</button>
  </div>

  <div class="ajuda-corpo">
    <div class="mensagens">Olá <?= $_SESSION["nome_abreviado"] ?>, tudo bem? Como posso te ajudar?</div>
    <div class="mensagens"><a href="https://octapay.com.br/tutoriais/configurar-pixel-na-octapay/" target="_blank">Dúvidas sobre o cadastro do pixel</a></div>
    <div class="mensagens"><a href="mailto:<?= $para ?>?subject=<?= $assunto ?>&body=<?= $mensagem ?>" target="_blank">Entrar em contato por email</a></div>
    <div class="mensagens"><a href="https://api.whatsapp.com/send?phone=5533998787884&text=<?= $mensagem_whats ?>" target="_blank">Entrar em contato pelo Whatsapp</a></div>
  </div>
</div>

<!-- Modal -->
<div class="modal fade" id="modal-lg">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">

      <div class="card-header">
        <h3 class="card-title"><i class="fa fa-info-circle"></i> Cadastrar Pixel</h3>
        <button type="button" class="close" data-dismiss="modal"> &times;</button>
      </div>

      <form action="#" method="post" style="font-family:inter; font-size:13px">
        <div class="modal-body" style="background: #f4f6f9">

          <input type="hidden" name="id" id="id">

          <div class="card card-body">

            <div class="row">
              <div class="col-md-6">
                <label>Plataforma: *</label>
                <select name="plataforma" id="plataforma" class="form-control">
                  <option value="Facebook">Facebook</option>
                  <option value="Taboola">Taboola</option>
                  <option value="GoogleAnalitics">Google Analitics</option>
                  <option value="GoogleAs">Google Ads</option>
                  <option value="GoogleTagManager">Google Tag Manager</option>
                  <option value="TikTok">TikTok</option>
                </select><br>
              </div>

              <div class="col-md-6">
                <label>Nome do Pixel: *</label>
                <input type="text" name="nome" id='nome' class="form-control" placeholder="Nome"><br>
              </div>
            </div>
          </div>

          <!-- < ?php if (@$_SESSION["testador"] == 1) : ?> -->
          <div class="facebook">
            <hr>
            <div class="card card-body">
              <div class="row">
                <div class="col-md-6">
                  <label>Tipo de Integração:</label>
                  <select name="tipo_integracao" id="tipo_integracao" class="form-control">
                    <option value="navegador">Navegador</option>
                    <option value="api">API Conversão</option>
                  </select>
                </div>

                <div class="col-md-6 token">
                  <label>Token de Acesso:</label>
                  <input type="text" name="token_acesso" id="token_acesso" class="form-control" placeholder="Token de acesso"><br>
                </div>
              </div>

              <div class="row">
                <div class="col-md-6 token">
                  <label>Domínio Url:</label>
                  <input type="text" name="dominio_url" id="dominio_url" class="form-control" placeholder="Domínio Url"><br>
                  <small>Caso fique em branco será enviado a url https://pay.redrocketpay.com/...</small>
                </div>
              </div>
            </div>
          </div>
          <!-- < ?php endif; ?> -->

          <hr>
          <div class="card card-body">
            <div class="row">
              <div class="col-md-6">
                <label>Id: (pixel)*</label>
                <input type="text" name="id_pixel" id="id_pixel" class="form-control" placeholder="Pixel">
                <small class="google">(Caso seja do Google, não colocar tag de conversão. Ex.: AU-123456789-0,AU-123456789-1)</small>
                <small class="token"><b>Integração por api de conversão, colocar somente 1 pixel</b><br><br></small>

                <small class="facebook">Limite máximo de 30 pixels do Facebook caso a integração seja NAVEGADOR. Separe com vírgula(,) e sem espaços em branco.</small>
              </div>

              <div class="col-md-6">
                <div class="google">
                  <label>Tag de Conversão Google: (Caso o pixel seja do Google)</label>
                  <input type="text" name="tag_conversao" id="tag_conversao" class="form-control" placeholder="ID_do_pixel/Tag_de_conversão">
                  <small>Ex.: AW-566715011/Y3BCCKGagb4CEIPFnY4C,AW-166715012/P3BCCKGagb4CEIPFnY4B</small>
                  <small>Limite de 15 tags de conversão. Separe com vírgula(,) e sem espaços em branco.</small>
                  <br><br>
                </div>
              </div>
            </div>

            <div class="taboola">
              <hr>
              <div class="row">
                <div class="col-md-6">
                  <label>Evento Taboola:</label>
                  <input type="text" name="evento_taboola" id="evento_taboola" class="form-control">
                  <br>
                </div>

                <div class="col-md-6">
                  <label>Evento Purchase Taboola:</label>
                  <input type="text" name="purchase_taboola" id="purchase_taboola" class="form-control">
                  <br>
                </div>
              </div>
            </div>
          </div>

          <!-- <label>Observação:</label>
                <textarea  name="observacao" id='observacao' class="form-control input-lg"></textarea><br> -->
          <hr>
          <div class="card card-body">
            <div class="row">
              <div class="col-md-6">
                <label>Executar no Boleto:</label>
                <select name="boleto" id="boleto" class="form-control">
                  <option value="1" selected>Sim</option>
                  <option value="0">Não</option>
                </select>
              </div>

              <div class="col-md-6">
                <label>Executar no Checkout:</label> InitiateCheckout
                <select name="executar_checkout" id="executar_checkout" class="form-control">
                  <option value="1" selected>Sim</option>
                  <option value="0">Não</option>
                </select>
              </div>
            </div>
          </div>

          <hr>
          <div class="card card-body">
            <div class="row">
              <div class="col-md-6">
                <label>Ativo:</label>
                <select name="status" id="status" class="form-control">
                  <option value="1" selected>Sim</option>
                  <option value="0">Não</option>
                </select>
              </div>

              <div class="col-md-6">
                <div class="valor_enviado">
                  <label>Valor que será enviado:</label>
                  <input type="text" name="valor" id="valor" class="form-control"><br>
                </div>
              </div>
            </div>
          </div>

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

<?= $v->start("scripts") ?>
<script>
  // var width = 0;
  // setInterval(() => {
  //   $(".carrega-content").html(" . ");
  //   $(".carrega-content").css("width", width + "%");
  //   width += 3.33;
  // }, 100);

  // $(".efeito-slow").hide();
  // setTimeout(function() {
  //   $(".carregar").hide("slow");
  //   $(".efeito-slow").show();
  // }, 3000);

  $("#btn-ajuda").click(() => {
    $(".ajuda").toggle("slow");
  });

  $("#ajuda-fechar").click(() => {
    $(".ajuda").hide("slow");
  });

  $("#valor").blur(function(e) {
    var valor = $("#valor").val();
    $("#valor").val(valor.replace(",", "."));
  });

  $("#cadastrofb").click(() => {
    console.log("clicou");
    $(".google").hide();
    $(".taboola").hide();
    $(".facebook").show();

    $(".modal-title").html("Cadastrar Planos");
    $("#salvar").css("display", "inline");
    $("#cancelar").css("display", "inline");
    $("#editar").css("display", "none");
    $("#plataforma option[value=Facebook]").prop("selected", true);
    cancelar();
  });

  $(".token").hide();
  $("#tipo_integracao").change(() => {
    if ($("#tipo_integracao").val() == "api") {
      $(".token").show("slow");
    } else {
      $(".token").hide("slow");
    }
  });

  $("#cadastrotb").click(() => {
    $(".google").hide();
    $(".taboola").show();
    $(".facebook").hide();

    $(".modal-title").html("Cadastrar Planos");
    $("#salvar").css("display", "inline");
    $("#cancelar").css("display", "inline");
    $("#editar").css("display", "none");
    $("#plataforma option[value=Taboola]").prop({
      "selected": true
    });
    cancelar();
  });

  $("#googleAnalitics").click(() => {
    $(".google").hide();
    $(".taboola").hide();
    $(".facebook").hide();

    $(".modal-title").html("Cadastrar Planos");
    $("#salvar").css("display", "inline");
    $("#cancelar").css("display", "inline");
    $("#editar").css("display", "none");
    $("#plataforma option[value=GoogleAnalitics]").prop({
      "selected": true
    });
    cancelar();
  });

  $("#googleAds").click(() => {
    $(".google").show();
    $(".taboola").hide();
    $(".facebook").hide();

    $(".modal-title").html("Cadastrar Planos");
    $("#salvar").css("display", "inline");
    $("#cancelar").css("display", "inline");
    $("#editar").css("display", "none");
    $("#plataforma option[value=GoogleAds]").prop({
      "selected": true
    });
    cancelar();
  });

  $("#gtm").click(() => {
    $(".google").hide();
    $(".taboola").hide();
    $(".facebook").hide();

    $(".modal-title").html("Cadastrar Planos");
    $("#salvar").css("display", "inline");
    $("#cancelar").css("display", "inline");
    $("#editar").css("display", "none");
    $("#plataforma option[value=GoogleTagManager]").prop({
      "selected": true
    });
    cancelar();
  });

  $("#TikTok").click(() => {
    console.log("tiktok");
    $(".google").hide();
    $(".taboola").hide();
    $(".facebook").hide();

    $(".modal-title").html("Cadastrar Planos");
    $("#salvar").css("display", "inline");
    $("#cancelar").css("display", "inline");
    $("#editar").css("display", "none");
    $("#plataforma option[value=TikTok]").prop({
      "selected": true
    });
    cancelar();
    $(".valor_enviado").css("display", "inline");

  });

  $("#plataforma").change((e) => {

    var plataforma = $("#plataforma").val();

    if (plataforma == "GoogleAnalitics") {
      $(".google").css("display", "inline");
      $(".taboola").css("display", "none");
      $(".facebook").css("display", "none");
      $(".valor_enviado").css("display", "inline");

    } else if (plataforma == "Taboola") {
      $(".taboola").css("display", "inline");
      $(".google").css("display", "none");
      $(".facebook").css("display", "none");
      $(".valor_enviado").css("display", "inline");

    } else if (plataforma == "TikTok") {
      $(".taboola").css("display", "none");
      $(".google").css("display", "none");
      $(".facebook").css("display", "none");
      $(".valor_enviado").css("display", "inline");
    } else {
      $(".taboola").css("display", "none");
      $(".google").css("display", "none");
      $(".valor_enviado").css("display", "inline");
    }
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
      url = "<?= $router->route("pixel.cadastro"); ?>";
    } else {
      url = "<?= $router->route("pixel.editar") ?>";
    }

    var nome = $("#nome").val(),
      id_pixel = $("#id_pixel").val();

    var form = $("form");

    if (nome == "" || id_pixel == "") {

      alert("Preecha todos os dados necessários");

    } else {

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
          console.log(form.serialize());
          alert(callback);
          location.reload();
        }

      });
    }
  }

  function editar(i) {
    cancelar();

    var id = dados[i]["id"];
    var nome = dados[i]["nome"];
    var observacao = dados[i]["observacao"];
    var valor = dados[i]["valor"];
    var boleto = dados[i]["boleto"];
    var status = dados[i]["status"];
    var executar_checkout = dados[i]["executar_checkout"];
    var plataforma = dados[i]["plataforma"];
    var id_pixel = dados[i]["id_pixel"];
    var tag_conversao = dados[i]["tag_conversao"];
    var evento_taboola = dados[i]["evento_taboola"];
    var purchase_taboola = dados[i]["purchase_taboola"];
    var tipo_integracao = dados[i]["tipo_integracao"];
    var token_acesso = dados[i]["token_acesso"];
    var dominio_url = dados[i]["dominio_url"];

    console.log(id, nome, valor, boleto, status, executar_checkout, plataforma, id_pixel, tipo_integracao);

    switch (plataforma) {
      case "Facebook":
        $(".google").hide();
        $(".taboola").hide();
        $(".facebook").show();
        console.log(1);
        break;
      case "Taboola":
        $(".google").hide();
        $(".taboola").show();
        $(".facebook").hide();
        console.log(2);
        break;
      case "GoogleAnalitics":
        $(".google").hide();
        $(".taboola").hide();
        $(".facebook").hide();
        console.log(3);
        break;
      case "GoogleAds":
        $(".google").show();
        $(".taboola").hide();
        $(".facebook").hide();
        console.log(4);
        break;
      case "GoogleTagManager":
        $(".google").hide();
        $(".taboola").hide();
        $(".facebook").hide();
        console.log(5);
        break;
      case "TikTok":
        $(".google").hide();
        $(".taboola").hide();
        $(".facebook").hide();
        $(".valor_enviado").css("display", "inline");
        break;
    }

    $(".modal-title").html("Editar Usuário " + nome);
    $("#salvar").css("display", "none");
    $("#editar").css("display", "inline");
    $("#cancelar").css("display", "inline");
    $("#id").attr("value", id);
    $("#nome").attr("value", nome);
    $("#observacao").attr("value", observacao);
    $("#valor").attr("value", valor);
    $("#id_pixel").prop("value", id_pixel);
    $("#tag_conversao").prop("value", tag_conversao);
    $("#evento_taboola").prop("value", evento_taboola);
    $("#purchase_taboola").prop("value", purchase_taboola);
    $("#boleto option[value=" + boleto + "]").prop("selected", true);
    $("#status option[value=" + status + "]").prop("selected", true);
    $("#executar_checkout option[value=" + executar_checkout + "]").prop("selected", true);
    $("#plataforma option[value=" + plataforma + "]").prop("selected", true);

    if (tipo_integracao)
      $("#tipo_integracao option[value=" + tipo_integracao + "]").prop("selected", true);

    if (tipo_integracao == "api")
      $(".token").show();
    else
      $(".token").hide();

    $("#token_acesso").prop("value", token_acesso);
    $("#dominio_url").prop("value", dominio_url);
  }

  function cancelar() {
    $("#salvar").css("display", "inline");
    $("#editar").css("display", "none");
    $("#cancelar").css("display", "inline");
    $("#id").attr("value", "");
    $("#nome").attr("value", "");
    $("#observacao").attr("value", "");
    $("#valor").attr("value", "");
    $("#id_pixel").prop("value", "");
    $("#tag_conversao").prop("value", "");
    $("#evento_taboola").prop("value", "");
    $("#purchase_taboola").prop("value", "");
    $("#boleto option").prop("selected", false);
    $("#status option").prop("selected", false);
    $("#executar_checkout option").prop("selected", false);
    $("#tipo_integracao option").prop("selected", false);
    $("#token_acesso").prop("value", "");
    // $("#plataforma option").prop({"selected": false});
  }

  function excluir(id) {
    console.log(id);
    if (confirm("Tem certeza que deseja excluir esse pixel?")) {
      $.ajax({
        url: "<?= $router->route("pixel.excluir") ?>",
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
</script>
<?= $v->end() ?>

<!-- Main content -->

<!-- /.row -->
</div><!-- /.container-fluid -->
</section>
<!-- /.content -->