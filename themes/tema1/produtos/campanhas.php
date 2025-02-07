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

$buscaAfiliacao = $afiliacao->find("afiliado = '{$section->getId()}' AND produto = '" . @$produto->id . "'")->fetch();

if ($produto->usuario != $section->getId())
  $BuscaCheckout = $checkout->find("privado = 0 AND produto = '" . @$produto->id . "'")->fetch(true);
else
  $BuscaCheckout = $checkout->find("produto = '" . @$produto->id . "'")->fetch(true);

if ($_SESSION["tipo"] == 6) {
  header("Location: " . url("vendas"));
}
?>


<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">

    <?= $section->getTipoSecaoLayout() ?>

    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h4 style="font-family:inter"><?= $title ?></h4>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="./">Início</a></li>

            <li class="breadcrumb-item active">Campanhas</li>
          </ol>
        </div>
      </div>
    </div><!-- /.container-fluid -->
  </section>

  <!-- Main content -->
  <section class="content">

    <!-- SE PRODUTOR MOSTRAR OPÇÕES DE EDIÇÃO -->
    <div class="callout menu-edicao">
      <?php if ($section->permissaoModuloBool("meus_produtos")) : ?>
        <a href="<?= url("produtos/detalhe/") . $produto->id; ?>">Detalhes</a>
      <?php endif ?>
      <?php if ($produto->usuario == $section->getId()) :
        $v->insert("produtos/menu-produto", ["produto" => $produto]);
      endif; ?>
      <!-- FIM SE PRODUTOR MOSTRAR OPÇÕES DE EDIÇÃO -->

      <?php if ($section->permissaoModuloBool("campanha_produto")) : ?>
        <a href="<?= url("produtos/campanhas/") . $produto->id; ?>">Campanhas</a>
      <?php endif ?>
    </div>

    <!-- Default box -->
    <div class="card">
      <div class="card-header">
        <?php if ($produto->status == 3 || $produto->status == 4) : ?>
          <button disabled class="btn btn-outline-primary" data-toggle="modal" data-target="#modal-lg" style="font-family:inter" id="cadastro">Cadastrar <i class="fa fa-chevron-right"></i> </button>
          <span class="badge bg-danger">Produto REPROVADO/BLOQUEADO</span>
        <?php else : ?>
          <?php if (!@$_SESSION["subusuario"] || $permissao->adicionar == 1) : ?>
            <button class="btn btn-outline-primary" data-toggle="modal" data-target="#modal-lg" style="font-family:inter" id="cadastro">Cadastrar <i class="fa fa-chevron-right"></i> </button>
          <?php endif ?>
        <?php endif; ?>

        <div class="card-tools">

          <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
            <i class="fas fa-minus"></i>
          </button>

        </div>
      </div>
      <div class="card-body p-0">
        <?php if ($campanhas) : ?>
          <script>
            var dados = [];
          </script>
          <div style="overflow-x: auto;">
            <table class="table table-striped projects" style="font-size:13px">
              <thead>
                <tr style="font-family:inter">
                  <th scope="col">Código</th>
                  <th scope="col">Nome</th>
                  <th scope="col">Url de Destino</th>
                  <th scope="col">Pixel</th>
                  <th scope="col">Vendas</th>
                  <th scope="col">Editar/Excluir</th>
                </tr>
              </thead>


              <tbody>
                <?php foreach ($campanhas as $campanha) : ?>
                  <tr style="font-family:inter">
                    <td><?= $campanha->codigo; ?></td>
                    <td><?= $campanha->nome; ?></td>

                    <td>
                      <div class="input-group">
                        <input type="text" class="form-control" value="<?= url("a/$campanha->produto/") . $campanha->url_destino . "&cp=" . $campanha->codigo; ?>">
                        <div class="input-group-append">
                          <a href="<?= url("a/$campanha->produto/") . $campanha->url_destino . "&cp=" . $campanha->codigo; ?>" target="_blank" class="btn btn-sm btn-primary"><i class="fas fa-external-link-alt"></i></a>
                        </div>
                      </div>
                    </td>

                    <td>
                      <?php if (@$campanha->pixel_facebook) echo "<b>Facebook: </b>" . mb_strimwidth(@$campanha->dataPixelFacebook()->nome, 0, 30, "...") . "<br>"; ?>
                      <?php if (@$campanha->pixel_taboola) echo "<b>Taboola: </b>" . mb_strimwidth(@$campanha->dataPixelTaboola()->nome, 0, 30, "...") . "<br>"; ?>
                      <?php if (@$campanha->pixel_google_analitics) echo "<b>Google Analitics: </b>" . mb_strimwidth(@$campanha->dataPixelGoogleAnalitics()->nome, 0, 30, "...") . "<br>"; ?>
                      <?php if (@$campanha->pixel_google_ads) echo "<b>Google Ads: </b>" . mb_strimwidth(@$campanha->dataPixelGoogleAds()->nome, 0, 30, "...") . "<br>"; ?>
                      <?php if (@$campanha->pixel_google_tag_manager) echo "<b>Google Tag Manager: </b>" . mb_strimwidth(@$campanha->dataPixelGoogleTagManager()->nome, 0, 30, "...") . "<br>"; ?>
                      <?php if (@$campanha->pixel_tiktok) echo "<b>TikTok: </b>" . mb_strimwidth(@$campanha->dataPixelTikTok()->nome, 0, 30, "...") . "<br>"; ?>
                    </td>

                    <td><a href="<?= url("vendas?campanha=$campanha->id") ?>" target="_blank"><?= $campanha->countVendas(); ?></a></td>
                    <?php if ($campanha->usuario == $section->getId()) : ?>
                      <td>
                        <?php if (!@$_SESSION["subusuario"] || $permissao->editar == 1) : ?>
                          <button class="btn btn-warning btn-sm" data-target="#modal-lg" onclick="editar(<?= $i; ?>)" data-toggle="modal"><i class="fas fa-edit"></i></button>
                        <?php endif ?>

                        <?php if (!@$_SESSION["subusuario"] || $permissao->excluir == 1) : ?>
                          <button class="btn btn-danger btn-sm" onclick="excluir(<?= $campanha->id; ?>)"><i class="fas fa-times"></i></button>
                        <?php endif ?>
                      </td>
                    <?php endif; ?>
                  </tr>

                  <script>
                    dados[<?= $i ?>] = {
                      id: "<?= $campanha->id ?>",
                      nome: "<?= $campanha->nome ?>",
                      url_destino: "<?= $campanha->url_destino ?>",
                      pixel: "<?= $campanha->pixel ?>",
                      analytics: "<?= @$campanha->analytics ?>",
                      pixel_facebook: "<?= $campanha->pixel_facebook ?>",
                      pixel_facebook_api: "<?= $campanha->pixel_facebook_api ?>",
                      pixel_google_analitics: "<?= $campanha->pixel_google_analitics ?>",
                      pixel_google_ads: "<?= $campanha->pixel_google_ads ?>",
                      pixel_google_tag_manager: "<?= $campanha->pixel_google_tag_manager ?>",
                      pixel_taboola: "<?= $campanha->pixel_taboola ?>",
                      pixel_tiktok: "<?= $campanha->pixel_tiktok ?>",
                    }
                  </script>

                <?php $i++;
                endforeach; ?>

              </tbody>
            </table>
          </div>

        <?php
          //EXIBE A NAVEGAÇÃO DO PAGINADOR
          $link = URL_BASE . "/produtos/campanhas/" . $produto->id . "/";

          //VARIÁVEIS VÊM DO CONTROLADOR AppPedidos
          $paginador->paginas($total, $pagina, $link);

        else :
        ?>
          <div class="card-body">
            <p><b>Você ainda não criou nenhuma campanha</b></p>
          </div>
        <?php
        endif;
        ?>

        <!-- cadastar -->
        <div class="modal fade" id="modal-lg">
          <div class="modal-dialog modal-lg">
            <div class="modal-content">

              <div class="modal-header">
                <h3 class="card-title"><i class="fa fa-info-circle"></i> Cadastrar Campanhas</h3>
                <button type="button" class="close" data-dismiss="modal"> &times;</button>
              </div>

              <form action="#" method="post" style="font-family:inter; text-transform:uppercase; font-size:11px">
                <div class="modal-body" style="background: #f4f6f9">
                  <div class="card card-body">
                    <div class="row">
                      <div class="col-md-6">
                        <label>Nome*</label>
                        <input type="hidden" name="id" id="id">
                        <input type="hidden" name="produto" value="<?= $produto->id ?>">
                        <input type="text" name="nome" id='nome' class="form-control input-lg" placeholder="Nome">
                      </div>

                      <div class="col-md-6">
                        <label>Página de destino*</label>
                        <select name="url_destino" id="url_destino" class="form-control input-lg" required>

                          <?php
                          if ($urls) :
                            foreach ($urls as $url) :
                              if ($url->privada == 0) :
                                if ($buscaAfiliacao) :
                          ?>
                                  <option value="<?= $buscaAfiliacao->codigo ?>/?ch=0&tp=5&pg=<?= $url->id ?>"><?= $url->descricao ?> - <?= $url->url ?></option>
                                <?php
                                else :
                                ?>
                                  <option value="0?ch=0&tp=4&pg=<?= $url->id ?>"><?= $url->descricao ?> - <?= $url->url ?></option>
                          <?php
                                endif;
                              endif;
                            endforeach;
                          endif;
                          ?>

                          <?php
                          if ($BuscaCheckout) :
                            foreach ($BuscaCheckout as $checkout) :
                              if ($buscaAfiliacao) :
                          ?>
                                <option value="<?= $buscaAfiliacao->codigo . "/?ch=" . $checkout->codigo . "&tp=5" ?>"><?= $checkout->descricao ?> - <?= url("a/" . $buscaAfiliacao->codigo . "/?ch=" . $checkout->codigo . "&tp=5") ?></option>

                              <?php else : ?>
                                <option value="<?= $checkout->codigo . "?tp=4&ch=$checkout->codigo" ?>"><?= $checkout->descricao ?> - <?= url("checkout/" . $checkout->codigo . "?tp=4") ?></option>
                          <?php
                              endif;
                            endforeach;
                          endif;
                          ?>
                        </select>
                      </div>

                      <!-- <div class="col-6">
                    <label style="margin-top:10px">Pixel</label>
                    <select name="pixel" id="pixel" class="form-control input-lg">
                        <option value="">Selecionar...</option>
                        < ?php if($pixel): foreach($pixel as $pixel):?>
                        <option value="< ?= $pixel->id ?>">< ?= $pixel->nome . " - " . $pixel->id_pixel ?></option>
                        < ?php endforeach; endif; ?>
                    </select>
                  </div> -->
                    </div>

                    <hr>
                    <label>Selecionar Pixels:</label><br>
                    <div class="row">
                      <div class="col-md-4">
                        <img src="<?= url("themes/imgs/Facebook.png") ?>" width='100px' style="border-radius: 20px; margin-bottom: 5px">
                        <?php if ($pixel) : ?>
                          <select name="pixel_facebook" id="pixel_facebook" class="form-control">
                            <option value="">Selecionar</option>
                            <?php foreach ($pixel as $pixel1) : ?>
                              <?php if ($pixel1->plataforma == "Facebook" && $pixel1->tipo_integracao != "api") : ?>
                                <option value="<?= $pixel1->id ?>"><?= $pixel1->nome ?></option>
                              <?php endif; ?>
                            <?php endforeach; ?>
                          </select>
                        <?php endif; ?>
                      </div>

                      <div class="col-md-4">
                        <img src="<?= url("themes/imgs/Facebook.png") ?>" width='100px' style="border-radius: 20px; margin-bottom: 5px"><small>Api de conversão</small>
                        <?php if ($pixel) : ?>
                          <select name="pixel_facebook_api" id="pixel_facebook_api" class="form-control">
                            <option value="">Selecionar</option>
                            <?php foreach ($pixel as $pixel1) : ?>
                              <?php if ($pixel1->plataforma == "Facebook" && $pixel1->tipo_integracao == "api") : ?>
                                <option value="<?= $pixel1->id ?>"><?= $pixel1->nome ?></option>
                              <?php endif; ?>
                            <?php endforeach; ?>
                          </select>
                        <?php endif; ?>
                      </div>

                      <div class="col-md-4">
                        <img src="<?= url("themes/imgs/Taboola.png") ?>" width='100px' style="margin-bottom: 5px">
                        <?php if ($pixel) : ?>
                          <select name="pixel_taboola" id="pixel_taboola" class="form-control">
                            <option value="">Selecionar</option>
                            <?php foreach ($pixel as $pixel3) : ?>
                              <?php if ($pixel3->plataforma == "Taboola") : ?>
                                <option value="<?= $pixel3->id ?>"><?= $pixel3->nome ?></option>
                              <?php endif; ?>
                            <?php endforeach; ?>
                          </select>
                        <?php endif; ?>
                      </div>
                    </div>

                    <hr>

                    <div class="row">
                      <div class="col-md-4">
                        <img src="<?= url("themes/imgs/GoogleAnalitics.png") ?>" width='70px' style="margin-bottom: 5px">
                        <?php if ($pixel) : ?>
                          <select name="pixel_google_analitics" id="pixel_google_analitics" class="form-control">
                            <option value="">Selecionar</option>
                            <?php foreach ($pixel as $pixel2) : ?>
                              <?php if ($pixel2->plataforma == "GoogleAnalitics") : ?>
                                <option value="<?= $pixel2->id ?>"><?= $pixel2->nome ?></option>
                              <?php endif; ?>
                            <?php endforeach; ?>
                          </select>
                        <?php endif; ?>
                      </div>

                      <div class="col-md-4">
                        <img src="<?= url("themes/imgs/GoogleAds.png") ?>" width='100px' style="border-radius: 20px; margin-bottom: 5px">
                        <?php if ($pixel) : ?>
                          <select name="pixel_google_ads" id="pixel_google_ads" class="form-control">
                            <option value="">Selecionar</option>
                            <?php foreach ($pixel as $pixel2) : ?>
                              <?php if ($pixel2->plataforma == "GoogleAds") : ?>
                                <option value="<?= $pixel2->id ?>"><?= $pixel2->nome ?></option>
                              <?php endif; ?>
                            <?php endforeach; ?>
                          </select>
                        <?php endif; ?>
                      </div>

                      <div class="col-md-4">
                        <img src="<?= url("themes/imgs/GoogleTagManager.png") ?>" width='85px' style="border-radius: 20px; margin-bottom: 5px">
                        <?php if ($pixel) : ?>
                          <select name="pixel_google_tag_manager" id="pixel_google_tag_manager" class="form-control">
                            <option value="">Selecionar</option>
                            <?php foreach ($pixel as $pixel2) : ?>
                              <?php if ($pixel2->plataforma == "GoogleTagManager") : ?>
                                <option value="<?= $pixel2->id ?>"><?= $pixel2->nome ?></option>
                              <?php endif; ?>
                            <?php endforeach; ?>
                          </select>
                        <?php endif; ?>
                      </div>
                    </div>

                    <hr>
                    <div class="row">
                      <div class="col-md-4">
                        <img src="<?= url("themes/imgs/TikTok2.png") ?>" width='85px' style="border-radius: 20px; margin-bottom: 5px">
                        <?php if ($pixel) : ?>
                          <select name="pixel_tiktok" id="pixel_tiktok" class="form-control" style="margin-top: 2%;">
                            <option value="">Selecionar</option>
                            <?php foreach ($pixel as $pixel2) : ?>
                              <?php if ($pixel2->plataforma == "TikTok") : ?>
                                <option value="<?= $pixel2->id ?>"><?= $pixel2->nome ?></option>
                              <?php endif; ?>
                            <?php endforeach; ?>
                          </select>
                        <?php endif; ?>
                      </div>
                    </div>

                    <!-- <div class="row">
                    <div class="col-md-6">
                      <b>Google Analytics:</b>
                      <input type="text" name="analytics" id="analytics" class="form-control">
                    </div>
                  </div> -->

                    <div class="row">
                      <div class="col-6">
                        <br />
                        <label for="checkboxSuccess1"> Não tem Pixel? <a href="<?= url("pixel") ?>" target="_blank">Cadastrar Pixel</a></label>
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
      </div>

    </div>


  </section>

</div>

<!-- INICIO BOTÃO AJUDA E SUAS FUNCIONALIDADES -->
<button class="btn btn-primary btn-ajuda" id="btn-ajuda">
  <i class="far fa-question-circle"></i> <span class="desktop">Ajuda</span>
</button>

<?php
$assunto = "Preciso de Ajuda";
$para = "suporte.oficial@redrocketpay.com";
$mensagem = "Email: {$_SESSION["email"]} %0D" .
  "ID: {$_SESSION["id"]} %0D" .
  "Página: {$_SERVER["REQUEST_URI"]} %0D%0D" .
  "Olá, sou " . $_SESSION["nome"] . " e gostaria de ajuda. Estou com o seguinte problema: ";

$mensagem_whats = "Email: {$_SESSION["email"]} %0D" .
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
    <div class="mensagens"><a href="https://octapay.com.br/tutoriais/campanhas/" target="_blank">Dúvidas sobre criação de Campanha</a></div>
    <div class="mensagens"><a href="mailto:<?= $para ?>?subject=<?= $assunto ?>&body=<?= $mensagem ?>" target="_blank">Entrar em contato por email</a></div>
    <div class="mensagens"><a href="https://api.whatsapp.com/send?phone=5533998131343&text=<?= $mensagem_whats ?>" target="_blank">Entrar em contato pelo Whatsapp</a></div>
  </div>
</div>
<!-- FIM BOTÃO AJUDA E SUAS FUNCIONALIDADES -->

<?= $v->start("scripts") ?>
<script>
  // INICIO BOTÃO DE AJUDA
  $("#btn-ajuda").click(() => {
    $(".ajuda").toggle("slow");
  });

  $("#ajuda-fechar").click(() => {
    $(".ajuda").hide("slow");
  });
  // FIM BOTÃO DE AJUDA
  $("#cadastro").click(() => {

    $(".modal-title").html("Cadastrar campanhas");
    $("#salvar").css("display", "inline");
    $("#editar").css("display", "none");
    cancelar();
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
      url = "<?= $router->route("produtos.campanha"); ?>";
    } else {
      url = "<?= $router->route("produtos.campanhaEditar") ?>";
    }

    var nome = $("#nome").val();

    var form = $("form");

    if (nome == "") {

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
          console.log("callback ", callback);
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
    var url_destino = dados[i]["url_destino"];
    var pixel = dados[i]["pixel"];
    var analytics = dados[i]["analytics"];
    var pixel_facebook = dados[i]["pixel_facebook"];
    var pixel_facebook_api = dados[i]["pixel_facebook_api"];
    var pixel_google_analitics = dados[i]["pixel_google_analitics"];
    var pixel_google_ads = dados[i]["pixel_google_ads"];
    var pixel_google_tag_manager = dados[i]["pixel_google_tag_manager"];
    var pixel_taboola = dados[i]["pixel_taboola"];
    var pixel_tiktok = dados[i]["pixel_tiktok"];

    console.log(id, nome, url_destino, pixel);

    $(".modal-title").html("Editar Usuário " + nome);
    $("#salvar").css("display", "none");
    $("#editar").css("display", "inline");
    $("#cancelar").css("display", "inline");
    $("#id").attr({
      "value": id
    });
    $("#nome").attr({
      "value": nome
    });
    $("#analytics").attr({
      "value": analytics
    });
    $("#url_destino option[value='" + url_destino + "']").attr({
      "selected": true
    });

    if (pixel)
      $("#pixel option[value=" + pixel + "]").attr("selected", true);

    if (pixel_facebook)
      $("#pixel_facebook option[value=" + pixel_facebook + "]").prop("selected", true);

    if (pixel_facebook_api)
      $("#pixel_facebook_api option[value=" + pixel_facebook_api + "]").prop("selected", true);

    if (pixel_google_analitics)
      $("#pixel_google_analitics option[value=" + pixel_google_analitics + "]").prop("selected", true);

    if (pixel_google_ads)
      $("#pixel_google_ads option[value=" + pixel_google_ads + "]").prop("selected", true);

    if (pixel_google_tag_manager)
      $("#pixel_google_tag_manager option[value=" + pixel_google_tag_manager + "]").prop("selected", true);

    if (pixel_taboola)
      $("#pixel_taboola option[value=" + pixel_taboola + "]").prop("selected", true);

    if (pixel_tiktok)
      $("#pixel_tiktok option[value=" + pixel_tiktok + "]").prop("selected", true);
  }

  function cancelar() {
    $("#salvar").css("display", "inline");
    $("#editar").css("display", "none");
    $("#id").attr("value", "");
    $("#nome").attr("value", "");
    $("#analytics").attr("value", "");
    $("#url_destino option").attr("selected", false);
    $("#pixel option").attr("selected", false);
    $("#pixel_facebook option").prop("selected", false);
    $("#pixel_facebook_api option").prop("selected", false);
    $("#pixel_google_analitics option").prop("selected", false);
    $("#pixel_taboola option").prop("selected", false);
    $("#pixel_google_ads option").prop("selected", false);
    $("#pixel_google_tag_manager option").prop("selected", false);
    $("#pixel_tiktok option").prop("selected", false);
  }

  function excluir(id) {
    console.log(id);
    if (confirm("Tem certeza que deseja excluir esse campanha?")) {
      $.ajax({
        url: "<?= $router->route("produtos.campanhaExcluir") ?>",
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