<?php
$v->layout("_theme");
if ($_SESSION["tipo"] == 6) {
  header("Location: " . url("vendas"));
}

$eventos = array(
  0 => "Aguardando Pagamento",
  1 => "Em Análise",
  2 => "Pré-autorizada",
  3 => "Paga",
  4 => "Cancelada",
  5 => "Devolvida",
  6 => "Chargeback",
  7 => "Concluída",
  8 => "Bloqueada",
  9 => "Carrinho Abandonado"
);

?>
<div class="ajax_load">
  <div class="ajax_load_box">
    <div class="ajax_load_box_circle"></div>
    <div class="ajax_load_box_title">Aguarde, carregando!</div>
  </div>
</div>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">

    <?= $section->getTipoSecaoLayout() ?>

    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6" style="margin-bottom:10px">
          <h1 style="font-family:inter"><?= $title ?></h1>
        </div>
        <div class="col-sm-6" style="font-family:inter">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="<?= url() ?>">Início</a></li>
            <li class="breadcrumb-item active">Integrações</li>
          </ol>
        </div>
      </div>

      <div class="card card-body">
        <div class="row">
          <div class="col-12 col-md-3">
            <a href="<?= url("integracoes") ?>" class="btn btn-primary form-control text-white">Disponíveis</a>
          </div>

          <div class="col-md-3">
            <a href="<?= url("integracoes/ativas") ?>" class="btn btn-primary form-control text-white ">Ativas</a>
          </div>

          <div class="col-12 col-md-3">
            <?php
            $assunto = "Sugerir Integração";
            $para = "suporte.oficial@redrocketpay.com";
            $mensagem = "Olá, sou " . $_SESSION["nome"] . " (" . $usuario->email . ") e gostaria de sugerir uma integração na plataforma: ";
            ?>
            <a href="mailto:<?= $para ?>?subject=<?= $assunto ?>&body=<?= $mensagem ?>" class="btn btn-success">Sugerir Integração</a>
            <!-- <button type="submit" class="btn btn-success">Sugerir Integração</button> -->
          </div>
        </div>
      </div>
    </div><!-- /.container-fluid -->
  </section>

  <!-- Main content -->
  <?php if (!@$_SESSION["subusuario"] || $permissao->adicionar == 1) : ?>
    <div class="col-sm-6" style="font-family:inter">
      <li class="breadcrumb-item active">
        <h4>Plataformas Disponíveis</h4>
    </div>
    <style>
      .zoom:hover img {
        -moz-transform: scale(1.2);
        -webkit-transform: scale(1.2);
        transform: scale(1.2);
      }

      .box-imgs {
        padding: 8px;
        background: #fff;
        border-radius: 7px;
        box-shadow: 2px 2px 5px #ccc;
      }
    </style>
    <!-- small box -->
    <section class="content">
      <div class="container-fluid">

        <div class="row">
          <div class="col-sm-3 card-body text-center zoom">
            <a class="btn" data-toggle="modal" data-target="#cadastrarbling" style="font-family:inter" id="cadastrobling"><img src="<?= url("themes/imgs/bling.png") ?>" width='100%' class="box-imgs"></a>
          </div>

          <div class="col-sm-3 card-body text-center zoom">
            <div class="m-potlet m-portlet--rounded">
              <div class="m-portlet__body m-portlet__body--no-padding">
                <div class="m-widget1 text-center">
                  <a class="btn" data-toggle="modal" data-target="#hotzapp" style="font-family:inter" id="cadastrohot"><img src="<?= url("themes/imgs/hotzaap.png") ?>" width='100%' class="box-imgs"></a>
                </div>
              </div>
            </div>
          </div>

          <div class="col-sm-3 card-body text-center zoom">
            <div class="m-potlet m-portlet--rounded">
              <div class="m-portlet__body m-portlet__body--no-padding">
                <div class="m-widget1 text-center">
                  <a class="btn" data-toggle="modal" data-target="#hotjar" style="font-family:inter" id="cadastro-hotjar"><img src="<?= url("themes/imgs/hotjar.png") ?>" width='100%' class="box-imgs"></a>
                </div>
              </div>
            </div>
          </div>

          <div class="col-sm-3 card-body text-center zoom">
            <div class="m-potlet m-portlet--rounded">
              <div class="m-portlet__body m-portlet__body--no-padding">
                <div class="m-widget1 text-center">
                  <a class="btn" data-toggle="modal" data-target="#hotzapp" style="font-family:inter" id="cadastroisei"><img src="<?= url("themes/imgs/isei.png") ?>" width='100%' class="box-imgs"></a>
                </div>
              </div>
            </div>
          </div>

        </div>
        <br>

        <div class="row">
          <div class="col-sm-3 card-body text-center zoom">
            <div class="m-potlet m-portlet--rounded">
              <div class="m-portlet__body m-portlet__body--no-padding">
                <div class="m-widget1 text-center">
                  <a class="btn" data-toggle="modal" data-target="#integracaonotazz" style="font-family:inter" id="cadastronotazz"><img src="<?= url("themes/imgs/notazz.png") ?>" width='100%' class="box-imgs"></a>
                </div>
              </div>
            </div>
          </div>

          <div class="col-sm-3 card-body text-center zoom">
            <div class="m-potlet m-portlet--rounded">
              <div class="m-portlet__body m-portlet__body--no-padding">
                <div class="m-widget1 text-center">
                  <a class="btn" data-toggle="modal" data-target="#cadastrarNI" style="font-family:inter" id="cadastroNI"><img src="<?= url("themes/imgs/Not-inteligentes.png") ?>" width='100%' class="box-imgs"></a>
                </div>
              </div>
            </div>
          </div>

          <div class="col-sm-3 card-body text-center zoom">
            <div class="m-potlet m-portlet--rounded">
              <div class="m-portlet__body m-portlet__body--no-padding">
                <div class="m-widget1 text-center">
                  <a class="btn" data-toggle="modal" data-target="#cadastrarLeadlovers" style="font-family:inter" id="cadastro"><img src="<?= url("themes/imgs/leadlovers.png") ?>" width='100%' class="box-imgs"></a>
                </div>
              </div>
            </div>
          </div>

          <div class="col-sm-3 card-body text-center zoom">
            <div class="m-potlet m-portlet--rounded">
              <div class="m-portlet__body m-portlet__body--no-padding">
                <div class="m-widget1 text-center">
                  <a class="btn" data-toggle="modal" data-target="#hotzapp" style="font-family:inter" id="cadastrohotbillet"><img src="<?= url("themes/imgs/Hotbillet.png") ?>" width='100%' class="box-imgs"></a>
                </div>
              </div>
            </div>
          </div>

        </div>

        <div class="row">
          <div class="col-sm-3 card-body text-center zoom">
            <div class="m-potlet m-portlet--rounded">
              <div class="m-portlet__body m-portlet__body--no-padding">
                <div class="m-widget1 text-center">
                  <a class="btn" data-toggle="modal" data-target="#mex" style="font-family:inter" id="cadastromex"><img src="<?= url("themes/imgs/mex.png") ?>" width='100%' class="box-imgs"></a>
                </div>
              </div>
            </div>
          </div>

          <div class="col-sm-3 card-body text-center zoom">
            <div class="m-potlet m-portlet--rounded">
              <div class="m-portlet__body m-portlet__body--no-padding">
                <div class="m-widget1 text-center">
                  <a class="btn" data-toggle="modal" data-target="#cadastrarChatGuru" style="font-family:inter" id="chatguru"><img src="<?= url("themes/imgs/chatguru.png") ?>" width='100%' class="box-imgs"></a>
                </div>
              </div>
            </div>
          </div>

          <!-- < ?php if (!@$shopify) : ?>
          <div class="col-sm-3 card-body text-center zoom">
            <div class="m-potlet m-portlet--rounded">
              <div class="m-portlet__body m-portlet__body--no-padding">
                <div class="m-widget1 text-center">
                  <a class="btn" data-toggle="modal" data-target="#cadastrarshopify" style="font-family:inter" id="shopify"><img src="<?= url("themes/imgs/shopify.png") ?>" width='100%' class="box-imgs"></a>
                </div>
              </div>
            </div>
          </div>
        < ?php endif; ?> -->

          <div class="col-sm-3 card-body text-center zoom">
            <div class="m-potlet m-portlet--rounded">
              <div class="m-portlet__body m-portlet__body--no-padding">
                <div class="m-widget1 text-center">
                  <a class="btn" data-toggle="modal" data-target="#activecampaign" style="font-family:inter" id="cadastroactivecampaign"><img src="<?= url("themes/imgs/activecampaign.png") ?>" width='100%' class="box-imgs"></a>
                </div>
              </div>
            </div>
          </div>

          <div class="col-sm-3 card-body text-center zoom">
            <div class="m-potlet m-portlet--rounded">
              <div class="m-portlet__body m-portlet__body--no-padding">
                <div class="m-widget1 text-center">
                  <a class="btn" data-toggle="modal" data-target="#rdstation" style="font-family:inter" id="cadastrordstation"><img src="<?= url("themes/imgs/rdstation.png") ?>" width='100%' class="box-imgs"></a>
                </div>
              </div>
            </div>
          </div>
        </div>


      </div>
      <br>
      <!-- end single prod -->

      <!-- /.row -->
    </section>
  <?php endif ?>
</div>
<!-- /.content -->

<a id="back-to-top" href="#" class="btn btn-primary back-to-top" role="button" aria-label="Scroll to top">
  <i class="fas fa-chevron-up"></i>
</a>
</div>
<!-- /.content-wrapper -->

<!--Bling-->
<!-- Modal -->
<div id="cadastrarbling" class="modal fade" role="dialog">

  <!-- Modal dialog -->
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">

      <!-- Modal header-->
      <div class="card-header">
        <h4 class="card-title">Cadastrar Integração Com Bling</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <!-- Fim Modal header-->

      <form action="#">
        <!-- Modal body -->
        <div class="modal-body" style="background: #f4f6f9">

          <div class="card card-body">
            <b>API KEY</b>
            <input type="text" name="api_key" id="api_key" class="form-control">

            <br>

            <b>Produto</b>
            <select name="produtobling" id="produtobling" class="form-control">
              <option value="">Todos</option>
              <?php if ($produtos) : foreach ($produtos as $produto) : ?>
                  <option value="<?= $produto->id ?>"><?= $produto->nome ?></option>
              <?php endforeach;
              endif; ?>
            </select>
          </div>
        </div>
        <!-- Fim Modal body -->

        <!-- Modal footer -->
        <div class="modal-footer justify-content-between">
          <button type="button" class="btn btn-danger" data-dismiss="modal" id="cancelarbling">Fechar</button>
          <button type="button" class="btn btn-primary" id="salvarbling">Salvar</button>
        </div>
        <!-- Fim Modal footer -->
      </form>
    </div>
    <!-- Fim Modal content-->
  </div>
  <!-- Fim Modal dialog -->
</div>
<!-- Fim Modal -->

<!--Hotzapp/iSei/HotBillet-->

<!-- Modal -->

<!-- Fim Modal dialog -->
<!-- Fim Modal -->

<!-- Modal -->

<div id="hotzapp" class="modal fade" role="dialog">

  <!-- Modal dialog -->
  <div class="modal-dialog modal-lg">

    <!-- Modal content-->
    <div class="modal-content">

      <!-- Modal header-->
      <div class="card-header">
        <h4 class="card-title" id="titulo">Cadastrar Hotzapp</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <!-- Fim Modal header-->

      <form action="#">
        <!-- Modal body -->
        <div class="modal-body" style="background: #f4f6f9">
          <input type="hidden" name="descricaoh" id="descricaoh">

          <div class="card card-body">
            <label>Chave Única:</label>
            <input type="text" name="chave_unicah" id="chave_unicah" class="form-control" value="<?= $usuario->chave_unica ?>" readonly>

            <div class="row">
              <div class="col-md-6">
                <b>Produto</b>
                <select name="produto" id="produto" class="form-control">
                  <option value="">Selecionar</option>
                  <?php if ($produtos) : foreach ($produtos as $produto) : ?>
                      <option value="<?= $produto->id ?>"><?= $produto->nome ?></option>
                  <?php endforeach;
                  endif; ?>
                </select>
              </div>

              <div class="col-md-6">
                <b>URL</b>
                <input type="text" name="url" id="url" class="form-control">
                <!-- <small> * Gere a URL na sua conta do<p id="titulo"p>e cole aqui.</small> -->
              </div>
            </div>

            <br>

            <div class="row">
              <div class="col-md-4">
                <b>Enviar no formato JSON?</b>
                <select name="json" id="json" class="form-control">
                  <option value="1">Sim</option>
                  <option value="0">Não</option>
                </select>
                <small>Na dúvida, deixe como SIM</small>
              </div>
            </div>
          </div>

          <hr>
          <div class="card card-body">
            <div class="row">
              <b>Eventos</b>
              <div class="col-md-12">
                <div class="row">
                  <div class="col-md-4">
                    <input type="checkbox" name="eventoh[0]" value="1" id="eventoh1" checked> Aguardando Pagamento <br>
                    <input type="checkbox" name="eventoh[1]" value="2" id="eventoh2" checked> Em Análise <br>
                    <input type="checkbox" name="eventoh[2]" value="3" id="eventoh3" checked> Pré-autorizada <br>
                  </div>

                  <div class="col-md-3">
                    <input type="checkbox" name="eventoh[3]" value="4" id="eventoh4" checked> Paga <br>
                    <input type="checkbox" name="eventoh[4]" value="5" id="eventoh5" checked> Cancelada <br>
                    <input type="checkbox" name="eventoh[5]" value="6" id="eventoh6" checked> Devolvida <br>
                  </div>

                  <div class="col-md-5">
                    <input type="checkbox" name="eventoh[6]" value="7" id="eventoh7" checked> Chargeback <br>
                    <input type="checkbox" name="eventoh[7]" value="8" id="eventoh8" checked> Concluída <br>
                    <input type="checkbox" name="eventoh[8]" value="9" id="eventoh9" checked> Bloqueada <br>
                    <input type="checkbox" name="eventoh[9]" value="10" id="eventoh10" checked> Carrinho Abandonado (produtor) <br>
                  </div>

                </div>
              </div>

            </div>
          </div>

          <br>

          <div class="row">
            <div class="col-md-3">
              <button class="btn btn-primary form-control" id="testar">Testar</button>
            </div>
          </div>

          <br>

          <div class="row">
            <div class="col-md-12">
              <textarea id="resultado" cols="30" rows="10" class="form-control" readonly></textarea>
            </div>
          </div>
        </div>
        <!-- Fim Modal body -->

        <!-- Modal footer -->
        <div class="modal-footer justify-content-between">
          <button type="button" class="btn btn-danger" data-dismiss="modal" id="cancelar">Fechar</button>
          <button type="button" class="btn btn-primary" id="salvar">Salvar</button>
        </div>
        <!-- Fim Modal footer -->
      </form>

    </div>
    <!-- Fim Modal content-->

  </div>
  <!-- Fim Modal dialog -->

</div>
<!-- Fim Modal -->

<!-- HOTJAR -->
<!-- Modal -->

<div id="hotjar" class="modal fade" role="dialog">

  <!-- Modal dialog -->
  <div class="modal-dialog modal-lg">

    <!-- Modal content-->
    <div class="modal-content">

      <!-- Modal header-->
      <div class="card-header">
        <h4 class="card-title" id="titulo">Cadastrar Hotjar</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <!-- Fim Modal header-->

      <form action="#">
        <!-- Modal body -->
        <div class="modal-body" style="background: #f4f6f9">
          <input type="hidden" name="descricao1" id="descricao1">

          <div class="card card-body">

            <div class="row">
              <div class="col-md-6">
                <b>Produto</b>
                <select name="produto_hotjar" id="produto_hotjar" class="form-control">
                  <option value="0">Todos</option>
                  <?php if ($produtos) : foreach ($produtos as $produto) : ?>
                      <option value="<?= $produto->id ?>"><?= $produto->nome ?></option>
                  <?php endforeach;
                  endif; ?>
                </select>
              </div>

              <div class="col-md-6">
                <b>Tracking ID:</b>
                <input type="text" name="tracking_id" id="tracking_id" class="form-control">
                <!-- <small> * Gere a URL na sua conta do<p id="titulo"p>e cole aqui.</small> -->
              </div>
            </div>
          </div>
        </div>
        <!-- Fim Modal body -->

        <!-- Modal footer -->
        <div class="modal-footer justify-content-between">
          <button type="button" class="btn btn-danger" data-dismiss="modal" id="cancelar">Fechar</button>
          <button type="button" class="btn btn-primary" id="salvar-hotjar">Salvar</button>
        </div>
        <!-- Fim Modal footer -->
      </form>

    </div>
    <!-- Fim Modal content-->

  </div>
  <!-- Fim Modal dialog -->

</div>
<!-- Fim Modal -->
<!-- FIM HOTJAR -->

<!--Notazz-->
<!-- Modal -->
<div id="integracaonotazz" class="modal fade" role="dialog">

  <!-- Modal dialog -->
  <div class="modal-dialog modal-lg">

    <!-- Modal content-->
    <div class="modal-content">

      <!-- Modal header-->
      <div class="modal-header">
        <h4 class="modal-title">Integração com Notazz</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <!-- Fim Modal header-->

      <form action="#">
        <!-- Modal body -->
        <div class="modal-body">

          <input type="hidden" id="id" name="id">
          <div class="row">
            <div class="col-md-6">
              <b>API Notazz</b> (<small>obrigatório</small>)
              <input type="text" name="api_notazz" id="api_notazz" class="form-control">
            </div>

            <div class="col-md-6">
              <b>Quando gerar a nota?</b> (<small>obrigatorio</small>)
              <select name="data_nota" id="data_nota" class="form-control">
                <option value="0">Manualmente</option>
                <!--option value="1">No momento da venda</option>
                            <option value="10">10 dias após a venda</option-->
              </select>
            </div>
          </div>

          <br>


          <div class="row">
            <div class="col-md-6">
              <b>Produto</b>
              <select name="produtonotazz" id="produtonotazz" class="form-control" required>
                <option value="">Selecionar</option>
                <?php foreach ($produtos as $produto) : ?>
                  <option value="<?= $produto->id ?>"><?= $produto->nome ?></option>
                <?php endforeach; ?>
              </select>
            </div>

            <div class="col-md-6">
              <b>Código CNAE do produto</b> (<small>opcional</small>)
              <input type="text" name="codigo_cnae" id="codigo_cnae" class="form-control">
            </div>
          </div>

          <br>

          <div class="row">
            <div class="col-md-6">
              <b>Código LC116</b> (<small>opcional</small>)
              <input type="text" name="codigo_lc116" id="codigo_lc116" class="form-control">
            </div>
          </div>

          <small>
            Campos opcionais: <br><br>

            Escolha se desejar gerar uma nota para um produto específico; <br>
            Digite o código de atividade econômica do produto selecionado; <br>
            Digite o código da área de serviço na prefeitura. <br><br>

            obs.: caso não seja digitado o código CNAE e LC116, será configurado de acordo com o cadastrado no Notazz <br>
            obs.: você pode pegar o código CNAE e LC116 nas configurações do seu produto no Notazz <br>
          </small>
        </div>
        <!-- Fim Modal body -->

        <!-- Modal footer -->
        <div class="modal-footer justify-content-between">
          <button type="button" class="btn btn-danger" data-dismiss="modal" id="cancelarnotazz">Cancelar</button>
          <button class="btn btn-primary" id="salvarnotazz">Salvar</button>
        </div>
        <!-- Fim Modal footer -->
      </form>

    </div>
    <!-- Fim Modal content-->

  </div>
  <!-- Fim Modal dialog -->

</div>
<!-- Fim Modal -->

<!--Notificações Inteligentes-->

<!-- Modal -->
<div id="cadastrarNI" class="modal fade" role="dialog">

  <!-- Modal dialog -->
  <div class="modal-dialog modal-lg">

    <!-- Modal content-->
    <div class="modal-content">

      <!-- Modal header-->
      <div class="modal-header">
        <h4 class="modal-title">Notificações Inteligentes</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <!-- Fim Modal header-->

      <form action="#">
        <!-- Modal body -->
        <div class="modal-body">

          <div class="row">
            <div class="col-md-6">
              <b>Token:</b>
              <input type="text" name="token" id="token" class="form-control">
              <small>* Gere o token de conexão na sua conta do Notificações Inteligentes e cole aqui.</small>

              <br><br>
              Somente o produtor pode cadastrar uma integração! <br>

              <a href="https://manual.notificacoesinteligentes.com/integrations/" target="_blank">Crie uma integração do tipo API</a><br>
              <a href="https://manual.notificacoesinteligentes.com/integrations/api/" target="_blank">Configure essa integração</a><br>
              <a href="https://manual.notificacoesinteligentes.com/instances/instances/#como-configurar-uma-integracao-em-uma-instancia" target="_blank">Configure uma instância</a><br>
            </div>

            <div class="col-md-6">
              <b>Integração ID:</b>
              <input type="text" name="integracao_id" id="integracao_id" class="form-control">
            </div>
          </div>
        </div>
        <!-- Fim Modal body -->

        <!-- Modal footer -->
        <div class="modal-footer justify-content-between">
          <button type="button" class="btn btn-danger" data-dismiss="modal" id="cancelarni">Fechar</button>
          <button type="button" class="btn btn-primary" id="salvarni">Salvar</button>
        </div>
        <!-- Fim Modal footer -->
      </form>

    </div>
    <!-- Fim Modal content-->

  </div>
  <!-- Fim Modal dialog -->

</div>
<!-- Fim Modal -->

<!--Lead Lovers-->

<div id="cadastrarLeadlovers" class="modal fade" role="dialog">

  <!-- Modal dialog -->
  <div class="modal-dialog modal-lg">

    <!-- Modal content-->
    <div class="modal-content">

      <!-- Modal header-->
      <div class="modal-header">
        <h4 class="modal-title" id="titulo">Cadastrar Conta Leadlovers</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <!-- Fim Modal header-->

      <form id="formLead">
        <!-- Modal body -->
        <input type="hidden" name="url" value="http://llapi.leadlovers.com/webapi/">
        <input type="hidden" name="json" value="0">
        <input type="hidden" name="leadlovers" value="1">
        <!-- <input type="text" name="url" value="< ?= $router->route("integracao.lead")?>"> -->

        <div class="modal-body" style="background: #f4f6f9">
          <div class="card card-body">

            <span><b>Descrição</b></span>
            <input type="text" name="descricao" id="desc_leadlovers" class="form-control">
            <span><b>Token</b></span>
            <input type="text" name="chave_unica" id="token_leadlovers" class="form-control">

          </div>

          <div class="modal-footer justify-content-between">
            <button type="button" class="btn btn-danger" data-dismiss="modal" id="cancelarLead">Fechar</button>
            <button type="button" class="btn btn-primary" id="contaLeadlovers">Verificar Conta</button>
          </div>

          <!-- ESCONDER LEAD -->
          <div id="esconder_lead">
            <div class="card card-body">
              <div class="row">
                <div class="col-md-6">
                  <b>Produto</b>
                  <select name="produto_lead" id="produto" class="form-control">
                    <option value="">Selecionar</option>
                    <?php if ($produtos) : foreach ($produtos as $produto) : ?>
                        <option value="<?= $produto->id ?>"><?= $produto->nome ?></option>
                    <?php endforeach;
                    endif; ?>
                  </select>
                </div>

                <div class="col-md-6">
                  <span><b>Tipo de integração</b></span>
                  <select name="tipo_integracao" id="tipo_integracao" class="form-control">
                    <option value="">-- Selecionar --</option>
                    <option value="plataforma">Integração pela RedRocket</option>
                    <option value="produto">Pelas configurações do produto no LeadLovers</option>
                  </select>
                </div>
              </div>
            </div>

            <hr>
            <div class="card card-body">
              <div class="row">
                <div class="col-md-12">
                  <b>Eventos</b>

                  <div class="row">

                    <div class="col-md-4">
                      <input class="chkLead" type="checkbox" name="evento[]" value="1" id="evento1" checked> Aguardando Pagamento <br>
                      <input class="chkLead" type="checkbox" name="evento[]" value="2" id="evento2" checked> Em Análise <br>
                      <input class="chkLead" type="checkbox" name="evento[]" value="3" id="evento3" checked> Pré-autorizada <br>
                    </div>

                    <div class="col-md-3">
                      <input class="chkLead" type="checkbox" name="evento[]" value="4" id="evento4" checked> Paga <br>
                      <input class="chkLead" type="checkbox" name="evento[]" value="5" id="evento5" checked> Cancelada <br>
                      <input class="chkLead" type="checkbox" name="evento[]" value="6" id="evento6" checked> Devolvida <br>
                    </div>

                    <div class="col-md-5">
                      <input class="chkLead" type="checkbox" name="evento[]" value="7" id="evento7" checked> Chargeback <br>
                      <input class="chkLead" type="checkbox" name="evento[]" value="8" id="evento8" checked> Concluída <br>
                      <input class="chkLead" type="checkbox" name="evento[]" value="9" id="evento9" checked> Bloqueada <br>
                      <input class="chkLead" type="checkbox" name="evento[]" value="10" id="evento10" checked> Carrinho Abandonado (produtor) <br>
                    </div>

                  </div>
                </div>
              </div>
            </div>

            <!-- INTEGRAÇÃO PLATAFORMA -->
            <div id="integracao_plataforma" style="display: none;">
              <hr>
              <div class="card card-body">
                <div class="row">
                  <div class="col-md-6">
                    <span><b>O que fazer</b></span>
                    <select name="acao_lead" id="acao_lead" class="form-control">
                      <option value="0"> Retirar lead do funil</option>
                      <option value="1"> Inserir lead no funil</option>
                    </select>
                  </div>
                </div>
              </div>
              <hr>
              <div class="card card-body">

                <div class="row">
                  <div class="col-md-6">
                    <b>Selecione a Máquina</b>
                    <select name="maquina_lead" id="maquina_lead" class="form-control">
                      <option value="">-- Selecione uma maquina --</option>
                    </select>
                  </div>

                  <div class="col-md-6">
                    <b>Selecione o Funil</b>
                    <select name="funil_lead" id="funil_lead" class="form-control" readonly="true" disabled="true">
                      <option value="">-- Selecione uma máquina para liberar funil --</option>
                    </select>
                  </div>
                </div>

                <div class="row">
                  <div class="col-md-6">
                    <b>Selecione o Nivel</b>
                    <select name="nivel_lead" id="nivel_lead" class="form-control" readonly="true" disabled="true">
                      <option value="">-- Selecione um funil para liberar nivel --</option>
                    </select>
                  </div>
                </div>

              </div>
            </div>
            <!-- FIM INTEGRAÇÃO PLATAFORMA -->

            <br>
            <button type="button" class="btn btn-primary" onclick="salvaLeadlovers(0)" style="display: block; float: right;" id="postbackLeadlovers">Salvar</button>
            <button type="button" class="btn btn-success" style="float:right; margin-right:1%;" onclick="salvaLeadlovers(1)">Adicionar mais Leads <i class="fas fa-plus-circle"></i></button>
            <button type="button" class="btn btn-danger" data-dismiss="modal" id="cancelarLeadlovers">Fechar</button>
            <br>
            <br>
            <!-- FIM ESCONDER LEAD -->
          </div>
        </div>
      </form>
    </div>
  </div>
  <!-- Fim Modal content-->
</div>
<!-- Fim Leadlovers -->



<!-- ChatGuru -->
<!-- Modal -->
<div id="cadastrarChatGuru" class="modal fade" role="dialog">

  <!-- Modal dialog -->
  <div class="modal-dialog modal-lg">

    <!-- Modal content-->
    <div class="modal-content">

      <!-- Modal header-->
      <div class="modal-header">
        <h4 class="modal-title">Chat Guru</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <!-- Fim Modal header-->

      <form action="#">
        <!-- Modal body -->
        <div class="modal-body" style="background: #f4f6f9">

          <div class="card card-body">
            <div class="row">
              <div class="col-md-6">
                <b>Endpoint:</b> <small> (obrigatório)</small>
                <input type="text" name="endpoint" id="endpoint" class="form-control">
                <!-- <small></small> -->
                <br><br>
              </div>

              <div class="col-md-6">
                <b>Key:</b><small> (obrigatório)</small>
                <input type="text" name="key" id="key" class="form-control">
              </div>
            </div>

            <div class="row">
              <div class="col-md-6">
                <b>ID da Conta:</b><small> (obrigatório)</small>
                <input type="text" name="id_conta" id="id_conta" class="form-control">
                <!-- <small></small> -->
                <br><br>
              </div>

              <div class="col-md-6">
                <b>ID do Aparelho:</b><small> (obrigatório)</small>
                <input type="text" name="id_aparelho" id="id_aparelho" class="form-control">
              </div>
            </div>
          </div>

          <hr>

          <div class="card card-body">
            <div class="row">
              <div class="col-md-12">
                * Deixe em branco para não enviar <br>

                <b>Mensagem para enviar caso compra no CARTÃO esteja em ANÁLISE:</b>
                <br>
                <textarea name="cartao_analise" id="cartao_analise" maxlength="300" rows="3" cols="95" class="form-control">Olá {nome}, sua compra do {produto} está em análise.</textarea>
                <br>
                <small>Exemplo: Olá <b>{nome}</b>, sua compra do <b>{produto}</b> está em análise.
                  <br>
                  <br>
                  <b>**</b>Caso digite <b>{nome}</b>, irá aparecer o nome do cliente na mensagem.
                  <br>
                  <b>**</b>Caso digite <b>{produto}</b>, irá aparecer o nome do produto na mensagem.
                </small>
              </div>
            </div>
          </div>
          <hr>

          <div class="card card-body">
            <div class="row">
              <div class="col-md-12">
                <b>Mensagem para enviar caso compra no CARTÃO esteja APROVADA:</b>
                <br>
                <textarea name="cartao_aprovado" id="cartao_aprovado" maxlength="300" rows="3" cols="95" class="form-control">Olá {nome}, sua compra do {produto} foi aprovada.</textarea>
                <br>
                <small>Exemplo: Olá <b>{nome}</b>, sua compra do <b>{produto}</b> foi aprovada.
                  <br>
                  <br>
                  <b>**</b>Caso digite <b>{nome}</b>, irá aparecer o nome do cliente na mensagem.
                  <br>
                  <b>**</b>Caso digite <b>{produto}</b>, irá aparecer o nome do produto na mensagem.
                </small>
              </div>
            </div>
          </div>
          <hr>

          <div class="card card-body">
            <div class="row">
              <div class="col-md-12">
                <b>Mensagem para enviar caso compra no CARTÃO esteja CANCELADA:</b>
                <br>
                <textarea name="cartao_cancelado" id="cartao_cancelado" maxlength="300" rows="3" cols="95" class="form-control">Olá {nome}, o pagamento do {produto} foi cancelado.</textarea>
                <br>
                <small>Exemplo: Olá <b>{nome}</b>, o pagamento do <b>{produto}</b> foi cancelado.
                  <br>
                  <br>
                  <b>**</b>Caso digite <b>{nome}</b>, irá aparecer o nome do cliente na mensagem.
                  <br>
                  <b>**</b>Caso digite <b>{produto}</b>, irá aparecer o nome do produto na mensagem.
                </small>
              </div>
            </div>
          </div>
          <hr>

          <div class="card card-body">
            <div class="row">
              <div class="col-md-12">
                <b>Mensagem para enviar caso compra no BOLETO tenha sido GERADO:</b>
                <br>
                <textarea name="boleto_gerado" id="boleto_gerado" maxlength="300" rows="3" cols="95" class="form-control">Olá {nome}, foi gerado um boleto do {produto}</textarea>
                <br>
                <small>Exemplo: Olá <b>{nome}</b>, foi gerado um boleto do <b>{produto}</b>.
                  <br>
                  <br>
                  <b>**</b>Caso digite <b>{nome}</b>, irá aparecer o nome do cliente na mensagem.
                  <br>
                  <b>**</b>Caso digite <b>{produto}</b>, irá aparecer o nome do produto na mensagem.</small>
                <br>
                <small><b>Observação: </b>Irá ser enviado junto link e a linha digitavel do boleto.</small>
              </div>
            </div>
          </div>
          <hr>

          <div class="card card-body">
            <div class="row">
              <div class="col-md-12">
                <b>Mensagem para enviar caso compra no BOLETO tenha sido PAGO:</b>
                <br>
                <textarea name="boleto_pago" id="boleto_pago" maxlength="300" rows="3" cols="95" class="form-control">Olá {nome}, recebemos o pagamento do {produto} via boleto.</textarea>
                <br>
                <small>Exemplo: Olá <b>{nome}</b>, recebemos o pagamento do <b>{produto}</b> via boleto.
                  <br>
                  <br>
                  <b>**</b>Caso digite <b>{nome}</b>, irá aparecer o nome do cliente na mensagem.
                  <br>
                  <b>**</b>Caso digite <b>{produto}</b>, irá aparecer o nome do produto na mensagem.
                </small>
              </div>
            </div>
          </div>
          <hr>

          <div class="card card-body">
            <div class="row">
              <div class="col-md-12">
                <b>Mensagem para enviar caso compra no BOLETO tenha sido CANCELADO:</b>
                <br>
                <textarea name="boleto_cancelado" id="boleto_cancelado" maxlength="300" rows="3" cols="95" class="form-control">Olá {nome}, o boleto de pagamento do {produto} está cancelado.</textarea>
                <br>
                <small>Exemplo: Olá <b>{nome}</b>, o boleto de pagamento do <b>{produto}</b> está cancelado.
                  <br>
                  <br>
                  <b>**</b>Caso digite <b>{nome}</b>, irá aparecer o nome do cliente na mensagem.
                  <br>
                  <b>**</b>Caso digite <b>{produto}</b>, irá aparecer o nome do produto na mensagem.
                </small>
              </div>
            </div>
          </div>
          <hr>

          <div class="card card-body">
            <div class="row">
              <div class="col-md-12">
                <b>Mensagem para enviar caso compra no PIX tenha sido GERADA:</b>
                <br>
                <textarea name="pix_gerado" id="pix_gerado" maxlength="300" rows="3" cols="95" class="form-control">Olá {nome}, foi gerado um PIX do {produto}</textarea>
                <br>
                <small>Exemplo: Olá <b>{nome}</b>, foi gerado um PIX do <b>{produto}</b>.
                  <br>
                  <br>
                  <b>**</b>Caso digite <b>{nome}</b>, irá aparecer o nome do cliente na mensagem.
                  <br>
                  <b>**</b>Caso digite <b>{produto}</b>, irá aparecer o nome do produto na mensagem.
                </small>
              </div>
            </div>
          </div>
          <hr>

          <div class="card card-body">
            <div class="row">
              <div class="col-md-12">
                <b>Mensagem para enviar caso compra no PIX tenha sido PAGO:</b>
                <br>
                <textarea name="pix_pago" id="pix_pago" maxlength="300" rows="3" cols="95" class="form-control">Olá {nome}, seu pagamento via PIX do {produto} foi pago.</textarea>
                <br>
                <small>Exemplo: Olá <b>{nome}</b>, seu pagamento via PIX do <b>{produto}</b> foi pago.
                  <br>
                  <br>
                  <b>**</b>Caso digite <b>{nome}</b>, irá aparecer o nome do cliente na mensagem.
                  <br>
                  <b>**</b>Caso digite <b>{produto}</b>, irá aparecer o nome do produto na mensagem.
                </small>
              </div>
            </div>
          </div>
          <hr>

          <div class="card card-body">
            <div class="row">
              <div class="col-md-12">
                <b>Mensagem para enviar caso compra no PIX tenha sido CANCELADA:</b>
                <br>
                <textarea name="pix_cancelado" id="pix_cancelado" maxlength="300" rows="3" cols="95" class="form-control">Olá {nome}, seu pagamento via PIX do {produto} foi cancelado.</textarea>
                <br>
                <small>Exemplo: Olá <b>{nome}</b>, seu pagamento via PIX do <b>{produto}</b> foi cancelado.
                  <br>
                  <br>
                  <b>**</b>Caso digite <b>{nome}</b>, irá aparecer o nome do cliente na mensagem.
                  <br>
                  <b>**</b>Caso digite <b>{produto}</b>, irá aparecer o nome do produto na mensagem.
                </small>
              </div>
            </div>
          </div>
          <hr>
          <div class="card card-body">
            <div class="row">
              <div class="col-md-12">
                <b>Mensagem para enviar caso tenha carrinho abandonado:</b>
                <br>
                <textarea name="abandono_carrinho" id="abandono_carrinho" maxlength="300" rows="3" cols="95" class="form-control">Olá {nome}, vi que você demonstrou interesse no {produto} e gostaria de saber se ficou com alguma dúvida.</textarea>
                <br>
                <small>Exemplo: Olá <b>{nome}</b>, vi que você demonstrou interesse no <b>{produto}</b> e gostaria de saber se ficou com alguma dúvida.</small>
                <br>
                <small>
                  <br>
                  <b>**</b>Caso digite <b>{nome}</b>, irá aparecer o nome do cliente na mensagem.
                  <br>
                  <b>**</b>Caso digite <b>{produto}</b>, irá aparecer o nome do produto na mensagem.
                </small>
                <br>
              </div>
            </div>
          </div>
          <hr>
          <div class="card card-body">
            <span> <b>*Todos esses dados ficam disponível na página "Celulares".<br>
                *Para utilizar a integração é necessario solicitar a ativação na sua conta do ChatGuru.</b></span>
          </div>
        </div>
        <!-- Fim Modal body -->

        <!-- Modal footer -->
        <div class="modal-footer justify-content-between">
          <button type="button" class="btn btn-danger" data-dismiss="modal" id="cancelarChatGuru">Fechar</button>
          <button type="button" class="btn btn-primary" id="salvarChatGuru">Salvar</button>
        </div>
        <!-- Fim Modal footer -->
      </form>

    </div>
    <!-- Fim Modal content-->

  </div>
  <!-- Fim Modal dialog -->

</div>
<!-- Fim Modal -->
<!--  ChatGuru -->


<!--MEX-->
<!-- Modal -->
<div id="mex" class="modal fade" role="dialog">

  <!-- Modal dialog -->
  <div class="modal-dialog modal-lg">

    <!-- Modal content-->
    <div class="modal-content">

      <!-- Modal header-->
      <div class="modal-header">
        <h4 class="modal-title">Cadastrar Integração Com a MEX10</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <!-- Fim Modal header-->

      <form action="#">
        <!-- Modal body -->
        <div class="modal-body" style="background: #f4f6f9">

          <div class="card card-body">
            <b>TOKEN</b>
            <input type="text" name="mex_token" id="mex_token" class="form-control">
            <b>* Para gerar um token, vá até a guia Developers > Gerar Token, no Mex10.<br></b>
          </div>

          <hr>

          <div class="card card-body">
            <div class="row">
              <div class="col-md-12">
                <b>* Deixe em branco caso não queira enviar determinada mensagem </b><br>

                <b>Mensagem para enviar caso compra no CARTÃO esteja em ANÁLISE:</b>
                <br>
                <input type="text" name="mex_cartao_analise" id="mex_cartao_analise" maxlength="300" class="form-control" value="Olá {nome}, sua compra do {produto} está em análise."><br>
                <small>
                  Exemplo: Olá <b>{nome}</b>, sua compra do <b>{produto}</b> está em análise.
                  <br><br>
                  <b>**</b>Caso digite <b>{nome}</b>, irá aparecer o nome do cliente na mensagem.
                  <br>
                  <b>**</b>Caso digite <b>{produto}</b>, irá aparecer o nome do produto na mensagem.
                </small>
              </div>
            </div>
          </div>
          <hr>

          <div class="card card-body">
            <div class="row">
              <div class="col-md-12">
                <b>Mensagem para enviar caso compra no CARTÃO esteja APROVADA:</b>
                <br>
                <input type="text" name="mex_cartao_aprovado" id="mex_cartao_aprovado" maxlength="300" class="form-control" value="Olá {nome}, sua compra do {produto} foi aprovada.">
                <br>
                <small>Exemplo: Olá <b>{nome}</b>, sua compra do <b>{produto}</b> foi aprovada.
                  <br>
                  <br>
                  <b>**</b>Caso digite <b>{nome}</b>, irá aparecer o nome do cliente na mensagem.
                  <br>
                  <b>**</b>Caso digite <b>{produto}</b>, irá aparecer o nome do produto na mensagem.
                </small>
              </div>
            </div>
          </div>
          <hr>

          <div class="card card-body">
            <div class="row">
              <div class="col-md-12">
                <b>Mensagem para enviar caso compra no CARTÃO esteja CANCELADA:</b>
                <br>
                <input type="text" name="mex_cartao_cancelado" id="mex_cartao_cancelado" maxlength="300" class="form-control" value="Olá {nome}, o pagamento do {produto} foi cancelado.">
                <br>
                <small>Exemplo: Olá <b>{nome}</b>, o pagamento do <b>{produto}</b> foi cancelado.
                  <br>
                  <br>
                  <b>**</b>Caso digite <b>{nome}</b>, irá aparecer o nome do cliente na mensagem.
                  <br>
                  <b>**</b>Caso digite <b>{produto}</b>, irá aparecer o nome do produto na mensagem.
                </small>
              </div>
            </div>
          </div>
          <hr>

          <div class="card card-body">
            <div class="row">
              <div class="col-md-12">
                <b>Mensagem para enviar caso compra no BOLETO tenha sido GERADO:</b>
                <br>
                <input type="text" name="mex_boleto_gerado" id="mex_boleto_gerado" maxlength="300" class="form-control" value="Olá {nome}, foi gerado um boleto do {produto}">
                <br>
                <small>Exemplo: Olá <b>{nome}</b>, foi gerado um boleto do <b>{produto}</b>.
                  <br>
                  <br>
                  <b>**</b>Caso digite <b>{nome}</b>, irá aparecer o nome do cliente na mensagem.
                  <br>
                  <b>**</b>Caso digite <b>{produto}</b>, irá aparecer o nome do produto na mensagem.</small>
                <br>
                <small><b>Observação: </b>Irá ser enviado junto link e a linha digitavel do boleto.</small>
              </div>
            </div>
          </div>
          <hr>

          <div class="card card-body">
            <div class="row">
              <div class="col-md-12">
                <b>Mensagem para enviar caso compra no BOLETO tenha sido PAGO:</b>
                <br>
                <input type="text" name="mex_boleto_pago" id="mex_boleto_pago" maxlength="300" class="form-control" value="Olá {nome}, recebemos o pagamento do {produto} via boleto.">
                <br>
                <small>Exemplo: Olá <b>{nome}</b>, recebemos o pagamento do <b>{produto}</b> via boleto.
                  <br>
                  <br>
                  <b>**</b>Caso digite <b>{nome}</b>, irá aparecer o nome do cliente na mensagem.
                  <br>
                  <b>**</b>Caso digite <b>{produto}</b>, irá aparecer o nome do produto na mensagem.
                </small>
              </div>
            </div>
          </div>
          <hr>

          <div class="card card-body">
            <div class="row">
              <div class="col-md-12">
                <b>Mensagem para enviar caso compra no BOLETO tenha sido CANCELADO:</b>
                <br>
                <input type="text" name="mex_boleto_cancelado" id="mex_boleto_cancelado" maxlength="300" class="form-control" value="Olá {nome}, o boleto de pagamento do {produto} está cancelado.">
                <br>
                <small>Exemplo: Olá <b>{nome}</b>, o boleto de pagamento do <b>{produto}</b> está cancelado.
                  <br>
                  <br>
                  <b>**</b>Caso digite <b>{nome}</b>, irá aparecer o nome do cliente na mensagem.
                  <br>
                  <b>**</b>Caso digite <b>{produto}</b>, irá aparecer o nome do produto na mensagem.
                </small>
              </div>
            </div>
          </div>
          <hr>

          <div class="card card-body">
            <div class="row">
              <div class="col-md-12">
                <b>Mensagem para enviar caso compra no PIX tenha sido GERADA:</b>
                <br>
                <input type="text" name="mex_pix_gerado" id="mex_pix_gerado" maxlength="300" class="form-control" value="Olá {nome}, foi gerado um PIX do {produto}">
                <br>
                <small>Exemplo: Olá <b>{nome}</b>, foi gerado um PIX do <b>{produto}</b>.
                  <br>
                  <br>
                  <b>**</b>Caso digite <b>{nome}</b>, irá aparecer o nome do cliente na mensagem.
                  <br>
                  <b>**</b>Caso digite <b>{produto}</b>, irá aparecer o nome do produto na mensagem.
                </small>
              </div>
            </div>
          </div>
          <hr>

          <div class="card card-body">
            <div class="row">
              <div class="col-md-12">
                <b>Mensagem para enviar caso compra no PIX tenha sido PAGO:</b>
                <br>
                <input type="text" name="mex_pix_pago" id="mex_pix_pago" maxlength="300" class="form-control" value="Olá {nome}, seu pagamento via PIX do {produto} foi pago.">
                <br>
                <small>Exemplo: Olá <b>{nome}</b>, seu pagamento via PIX do <b>{produto}</b> foi pago.
                  <br>
                  <br>
                  <b>**</b>Caso digite <b>{nome}</b>, irá aparecer o nome do cliente na mensagem.
                  <br>
                  <b>**</b>Caso digite <b>{produto}</b>, irá aparecer o nome do produto na mensagem.
                </small>
              </div>
            </div>
          </div>
          <hr>

          <div class="card card-body">
            <div class="row">
              <div class="col-md-12">
                <b>Mensagem para enviar caso compra no PIX tenha sido CANCELADA:</b>
                <br>
                <input type="text" name="mex_pix_cancelado" id="mex_pix_cancelado" maxlength="300" class="form-control" value="Olá {nome}, seu pagamento via PIX do {produto} foi cancelado.">
                <br>
                <small>Exemplo: Olá <b>{nome}</b>, seu pagamento via PIX do <b>{produto}</b> foi cancelado.
                  <br>
                  <br>
                  <b>**</b>Caso digite <b>{nome}</b>, irá aparecer o nome do cliente na mensagem.
                  <br>
                  <b>**</b>Caso digite <b>{produto}</b>, irá aparecer o nome do produto na mensagem.
                </small>
              </div>
            </div>
          </div>
          <hr>
          <div class="card card-body">
            <div class="row">
              <div class="col-md-12">
                <b>Mensagem para enviar caso tenha carrinho abandonado:</b>
                <br>
                <input type="text" name="mex_abandono_carrinho" id="mex_abandono_carrinho" maxlength="300" class="form-control" value="Olá {nome}, vi que você demonstrou interesse no {produto} e gostaria de saber se ficou com alguma dúvida.">
                <br>
                <small>Exemplo: Olá <b>{nome}</b>, vi que você demonstrou interesse no <b>{produto}</b> e gostaria de saber se ficou com alguma dúvida.</small>
                <br>
                <small>
                  <br>
                  <b>**</b>Caso digite <b>{nome}</b>, irá aparecer o nome do cliente na mensagem.
                  <br>
                  <b>**</b>Caso digite <b>{produto}</b>, irá aparecer o nome do produto na mensagem.
                </small>
                <br>
              </div>
            </div>
          </div>
        </div>
        <!-- Fim Modal body -->

        <!-- Modal footer -->
        <div class="modal-footer justify-content-between">
          <button type="button" class="btn btn-danger" data-dismiss="modal" id="cancelarmex">Fechar</button>
          <button type="button" class="btn btn-primary" id="salvarmex">Salvar</button>
        </div>
        <!-- Fim Modal footer -->
      </form>

    </div>
    <!-- Fim Modal content-->

  </div>
  <!-- Fim Modal dialog -->

</div>
<!-- Fim Modal -->

<!--activecampaign-->
<!-- Modal -->
<div id="activecampaign" class="modal fade" role="dialog">

  <!-- Modal dialog -->
  <div class="modal-dialog modal-lg">

    <!-- Modal content-->
    <div class="modal-content">

      <!-- Modal header-->
      <div class="modal-header">
        <h4 class="modal-title">Cadastrar Integração Com a activecampaign</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <!-- Fim Modal header-->

      <form action="#">
        <!-- Modal body -->
        <div class="modal-body" style="background: #f4f6f9">

          <div class="card card-body">
            <b>TOKEN</b>
            <input type="text" name="activecampaign_token" id="activecampaign_token" class="form-control">
            <b>* Para gerar um token, vá até a guia Configuração > Desenvolvedor > Chave , na activecampaign.<br></b>
            <br>

            <b>LINK</b>
            <input type="text" name="activecampaign_link" id="activecampaign_link" class="form-control">
          </div>
          <hr>
        </div>
        <!-- Fim Modal body -->

        <!-- Modal footer -->
        <div class="modal-footer justify-content-between">
          <button type="button" class="btn btn-danger" data-dismiss="modal" id="cancelaractivecampaign">Fechar</button>
          <button type="button" class="btn btn-primary" id="salvaractivecampaign">Salvar</button>
        </div>
        <!-- Fim Modal footer -->
      </form>

    </div>
    <!-- Fim Modal content-->

  </div>
  <!-- Fim Modal dialog -->

  <!-- Shopify -->
</div>
<!-- Fim Modal -->
<div id="cadastrarshopify" class="modal fade" role="dialog">
  <!-- Modal dialog -->
  <div class="modal-dialog modal-lg">
    <!-- Modal content-->
    <div class="modal-content">
      <!-- Modal header-->
      <div class="modal-header">
        <h4 class="modal-title">Integração com Shopify</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <!-- Fim Modal header-->
      <form id="formShopify" action="#">
        <!-- Modal body -->
        <div class="modal-body">
          <div class="col-md-5">
            <input type="checkbox" name="carrinho_shopify" value="1" id="carrinho_shopify" checked> Pular carrinho de compras? <br>
          </div>
          <br>
          <div class="row">
            <div class="col-md-6">
              <b>URL da loja</b>
              <input type="text" name="url_shopify" id="url_shopify" class="form-control">
              <small>Exemplo: https://loja-teste.myshopify.com</small>
            </div>

            <div class="col-md-6">
              <b>Chave de API</b>
              <input type="text" name="api_shopify" id="api_shopify" class="form-control">
            </div>

            <div class="col-md-6">
              <b>Senha</b>
              <input type="text" name="senha_shopify" id="senha_shopify" class="form-control">
            </div>

            <div class="col-md-6">
              <b>Segredo Compartilhado</b>
              <input type="text" name="segredo_shopify" id="segredo_shopify" class="form-control">
            </div>
          </div>
          <br>
        </div>
        <!-- Fim Modal body -->
        <!-- Modal footer -->
        <div class="modal-footer justify-content-between">

          <button type="button" class="btn btn-danger" data-dismiss="modal" id="cancelarnotazz">Cancelar</button>
          <button class="btn btn-primary" id="salvarshopify">Salvar</button>
        </div>
        <!-- Fim Modal footer -->
      </form>
    </div>
    <!-- Fim Modal content-->
  </div>
  <!-- Fim Modal dialog -->
</div>
<!-- Fim Shopify -->

<!--rdstation-->
<!-- Modal -->
<div id="rdstation" class="modal fade" role="dialog">

  <!-- Modal dialog -->
  <div class="modal-dialog modal-lg">

    <!-- Modal content-->
    <div class="modal-content">

      <!-- Modal header-->
      <div class="modal-header">
        <h4 class="modal-title">Cadastrar Integração Com a rdstation</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <!-- Fim Modal header-->

      <form action="#">
        <!-- Modal body -->
        <div class="modal-body" style="background: #f4f6f9">

          <div class="card card-body">
            <b>Client_id:</b>
            <input type="text" name="rdstation_id" id="rdstation_id" class="form-control"><br>

            <b>Client_secret:</b>
            <input type="text" name="rdstation_secret" id="rdstation_secret" class="form-control"><br>

            <b>Code:</b>
            <input type="text" name="rdstation_code" id="rdstation_code" class="form-control"><br>

            <span><br>* Para obter os dados, siga os passos no link <b><a href="https://developers.rdstation.com/pt-BR/authentication" target="_blank">https://developers.rdstation.com/pt-BR/authentication</a><br></b></span>
            <span>Video: <b><a href="https://youtu.be/Xj49d7V3Gkw?t=412" target="_blank">https://youtu.be/Xj49d7V3Gkw?t=412</a></b><br><br></span>

            Substitua os seus dados nessa url para obter o CODE: <b>https://api.rd.services/auth/dialog?client_id={client_id}&redirect_uri={redirect_uri}</b>
          </div>
        </div>
        <!-- Fim Modal body -->

        <!-- Modal footer -->
        <div class="modal-footer justify-content-between">
          <button type="button" class="btn btn-danger" data-dismiss="modal" id="cancelarrdstation">Fechar</button>
          <button type="button" class="btn btn-primary" id="salvarrdstation">Salvar</button>
        </div>
        <!-- Fim Modal footer -->
      </form>

    </div>
    <!-- Fim Modal content-->

  </div>
  <!-- Fim Modal dialog -->

</div>
<!-- Fim Modal -->

<!--Funções-->

<!--Funções-->
<?php $v->start("scripts") ?>
<script>
  function load(action, tempo = 1000) {
    var load_div = $(".ajax_load");
    if (action === "open") {
      load_div.fadeIn().css("display", "flex");
      setTimeout(() => {
        load_div.fadeOut();
      }, tempo);
    } else {
      load_div.fadeOut();
    }
  }

  $("#cadastrobling").click((e) => {
    console.log("teste1");
  });

  $("#salvarbling").click((e) => {
    e.preventDefault();
    console.log("teste");
    salvarEditarbling("<?= $router->route("bling.cadastrar") ?>");
  });

  $("#salvar-hotjar").click((e) => {
    e.preventDefault();
    console.log("teste");
    salvarEditarbling("<?= $router->route("hotjar.cadastrar") ?>");
  });

  function salvarEditarbling(url) {

    var form = $("form");
    console.log(form.serialize());

    $.ajax({
      url: url,
      type: "POST",
      data: form.serialize(),
      success: (callback) => {
        console.log(callback);

        alert(callback);
        if (callback == "Cadastrado com sucesso" || callback == "Salvo com sucesso") {
          location.reload();
        }
      }
    })
  }


  $("#cadastrohot").click((e) => {
    $("#titulo").html("Cadastrar Hotzapp");
    $("#descricaoh").attr("value", "hotzapp");
  });

  $("#cadastroisei").click((e) => {
    $("#titulo").html("Cadastrar iSei");
    $("#descricaoh").attr("value", "isei");
  });

  $("#cadastrohotbillet").click((e) => {
    $("#titulo").html("Cadastrar HotBillet");
    $("#descricaoh").attr("value", "hotbillet");
  });

  $("#salvar").click((e) => {
    e.preventDefault();
    console.log("clicou");
    salvarEditar("<?= $router->route("postback.cadastrar") ?>");
  });

  function salvarEditar(url) {

    var form = $("form");
    console.log(form.serialize());

    $.ajax({
      url: url,
      type: "POST",
      data: form.serialize(),
      success: (callback) => {
        console.log(callback);

        alert(callback);
        if (callback == "Cadastrado com sucesso") {
          location.reload();
        }
      }
    })
  }

  $("#testar").click((e) => {
    e.preventDefault();
    console.log("clique");
    testar();
    console.log("clique");
  });

  function testar() {

    var url = $("#url").val();
    var json = $("#json").val();
    var chave_unica = $("#chave_unicah").val();

    $.ajax({
      url: "<?= $router->route("postback.testar") ?>",
      type: "POST",
      data: {
        url: url,
        json: json,
        chave_unica: chave_unica
      },
      success: (callback) => {
        console.log(callback);
        $("#resultado").css("display", "block");
        $("#resultado").prop("value", callback);
      },
      error: (err) => {
        console.log(err);
        $("#resultado").css("display", "block");
        $("#resultado").prop("value", err);
        alert("Houve um erro ao realizar o teste. Verifique se a url está correta!");
      }
    });

  }

  $("#cadastronotazz").click((e) => {
    cancelar();
  });

  $("#salvarnotazz").click((e) => {
    e.preventDefault();
    salvarEditarnotazz("<?= $router->route("notazz.cadastrar") ?>");
  });

  function salvarEditarnotazz(url) {

    var form = $("form");

    $.ajax({
      url: url,
      type: "POST",
      data: form.serialize(),
      success: (callback) => {
        console.log(callback);

        alert(callback);
        if (callback == "Salvo com sucesso") {
          location.reload();
        }
      }
    })
  }

  $("#cadastroNI").click((e) => {
    cancelar();
  });

  $("#salvarni").click((e) => {
    e.preventDefault();
    salvarEditarni("<?= $router->route("whatsapp.cadastrarNI") ?>");
  });

  function salvarEditarni(url) {

    var form = $("form");
    console.log(form.serialize());

    $.ajax({
      url: url,
      type: "POST",
      data: form.serialize(),
      success: (callback) => {
        console.log(callback);

        alert(callback);
        if (callback == "Salvo com sucesso") {
          location.reload();
        }
      }
    })
  }

  $("#salvarmex").click((e) => {
    e.preventDefault();
    salvarMex("<?= $router->route("mex.cadastrar") ?>");
  });

  function salvarMex(url) {

    var form = $("form");
    console.log(form.serialize());

    $.ajax({
      url: url,
      type: "POST",
      data: form.serialize(),
      success: (callback) => {
        console.log(callback);

        alert(callback);
        if (callback == "Cadastrado com sucesso" || callback == "Salvo com sucesso") {
          location.reload();
        }
      }
    })
  }

  $("#salvaractivecampaign").click((e) => {
    e.preventDefault();
    salvarActiveCamp("<?= $router->route("activecampaign.cadastrar") ?>");
  });

  function salvarActiveCamp(url) {

    var form = $("form");
    console.log(form.serialize());

    $.ajax({
      url: url,
      type: "POST",
      data: form.serialize(),
      success: (callback) => {
        console.log(callback);

        alert(callback);
        if (callback == "Cadastrado com sucesso" || callback == "Salvo com sucesso") {
          location.reload();
        }
      }
    })
  }

  $("#salvarrdstation").click((e) => {
    e.preventDefault();
    salvarActiveCamp("<?= $router->route("rdstation.cadastrar") ?>");
  });

  function salvarActiveCamp(url) {

    var form = $("form");
    console.log(form.serialize());

    $.ajax({
      url: url,
      type: "POST",
      data: form.serialize(),
      success: (callback) => {
        console.log(callback);

        alert(callback);
        if (callback == "Cadastrado com sucesso" || callback == "Salvo com sucesso") {
          location.reload();
        }
      }
    })
  }

  $("#salvarChatGuru").click((e) => {
    e.preventDefault();
    salvarChatGuru("<?= $router->route("chat.guru") ?>");
  });

  function salvarChatGuru(url) {

    var form = $("form");
    console.log(url);

    console.log(form.serialize());

    $.ajax({
      url: url,
      type: "POST",
      data: form.serialize(),
      success: (callback) => {
        console.log(callback);

        alert(callback);
        if (callback == "Salvo com sucesso") {
          location.reload();
        }
      }
    })
  }

  // ----------INICIO LEADLOVERS--------------
  function tipoIntegracao() {
    if ($("#tipo_integracao").val() == "plataforma") {
      $("#integracao_plataforma").show();
    } else {
      $("#integracao_plataforma").hide();
    }
  }
  tipoIntegracao();
  $("#tipo_integracao").change(() => {
    tipoIntegracao();
  });

  $("#esconder_lead").hide();
  $("#contaLeadlovers").click((e) => {
    e.preventDefault();

    var desc = $("#desc_leadlovers").val();
    var token = $("#token_leadlovers").val();

    if (token == "") {
      alert("Token inválido");
      return
    }

    var url = "<?= $router->route("conta.lead") ?>";
    // console.log("URL:", url);

    $.ajax({
      url: url,
      type: "POST",
      data: {
        token: token,
        descricao: desc,
      },
      beforeSend: function() {
        $(".ajax_load_box_title").html("Verificando");
        load('open', 1000);
      },
      success: (callback) => {
        console.log(callback);

        resposta = JSON.parse(callback);
        if (resposta.UserCode != 0 && resposta.UserName != "") {
          $("#esconder_lead").show();
          $("#contaLeadlovers").hide();
          $("#cancelarLead").hide();
          carregaMaquinas(token);
        } else {
          alert("Token Inválido");
        }
      }
    })
  })

  $("#salvarshopify").click((e) => {
    e.preventDefault();

    var form = $("#formShopify");
    var url = "<?= $router->route("shopify.cadastro") ?>";
    // console.log(url);
    $.ajax({
      url: url,
      type: "POST",
      data: form.serialize(),
      success: (callback) => {
        // console.log(callback);
        alert(callback);
        if (callback == "Salvo com sucesso") {
          location.reload();
        }
      }
    })
  });

  function carregaMaquinas(token) {

    var url = "<?= $router->route("maquina.lead") ?>";

    $.ajax({
      url: url,
      type: "POST",
      data: {
        token: token,
      },
      success: (callback) => {
        // console.log(callback);


        resposta = JSON.parse(callback);
        // console.log(resposta[0].MachineCode);

        if (resposta[0].MachineCode != 0) {
          var option = "<option>Selecione uma máquina </option>";
          var option = "";
          $.each(resposta, function(i, obj) {
            // console.log("I:", i);
            // console.log("obj:", obj);

            option += "<option name=" + obj.MachineName + " value=" + obj.MachineCode + ">" + obj.MachineName + "</option>";
          })
          $('#maquina_lead').html(option);
          var cod_maquina = $("#maquina_lead").val();
          carregaFunil(token, cod_maquina);
        } else {
          alert("Erro ao carregar Máquinas");
        }
      }
    })
  }

  function carregaFunil(token, maquina) {

    var url = "<?= $router->route("funil.lead") ?>";

    $.ajax({
      url: url,
      type: "POST",
      data: {
        token: token,
        maquina: maquina,
      },
      success: (callback) => {
        // console.log(callback);

        resposta = JSON.parse(callback);
        // console.log(resposta[0].SequenceCode);
        $('#funil_lead').attr('readonly', false);
        $('#funil_lead').attr('disabled', false);

        if (resposta[0].SequenceCode != 0) {
          var option = "<option>-- Selecione um funil </option>";
          var option = "";

          $.each(resposta, function(i, obj) {
            // console.log("I:", i);
            // console.log("obj:", obj);

            option += "<option value=" + obj.SequenceCode + ">" + obj.SequenceName + "</option>";
          })
          $('#funil_lead').html(option);
          var cod_funil = $("#funil_lead").val();

          carregaNivel(token, maquina, cod_funil);
        } else {
          alert("Erro ao carregar Máquinas");
        }
      }
    })
  }



  function carregaNivel(token, maquina, funil) {

    var url = "<?= $router->route("nivel.lead") ?>";

    $.ajax({
      url: url,
      type: "POST",
      data: {
        token: token,
        maquina: maquina,
        funil: funil,
      },
      success: (callback) => {
        console.log(callback);
        $('#nivel_lead').attr('readonly', false);
        $('#nivel_lead').attr('disabled', false);

        resposta = JSON.parse(callback);
        // console.log(resposta[0].SequenceCode);

        if (resposta[0].Sequence != 0) {
          var option = "<option>-- Selecione um Nivel --</option>";
          var option = "";

          $.each(resposta, function(i, obj) {
            console.log("I:", i);
            console.log("obj:", obj);

            option += "<option value=" + obj.Sequence + ">" + obj.Subject + "</option>";
          })
          $('#nivel_lead').html(option);
        } else {
          alert("Erro ao carregar Nivel");
        }
      }
    })
  }

  function salvaLeadlovers(tipo) {
    // alert(tipo);
    // tipo = 0 -> SALVAR
    // tipo = 1 -> Adiciona mais Lead (Não atualiza a página após salvar)

    var token = $("#token_leadlovers").val();
    var descricao = $("#desc_leadlovers").val();
    var produto = $("#formLead [name=produto_lead] :selected").val();
    var tipo_integracao = $("#tipo_integracao").val();

    if (produto == "") {
      alert("Selecione um produto");
      $("#formLead [name=produto_lead]").focus();
    } else {
      var acao = $("#acao_lead").val();

      var eventos = new Array();
      $("input[name='evento[]']:checked").each(function() {
        // valores inteiros usa-se parseInt
        eventos.push(parseInt($(this).val()));
        // string
        // eventos.push($(this).val());
      });

      var cod_maquina = $("#maquina_lead").val();
      var maquina = $('#maquina_lead :selected').text();

      var cod_funil = $("#funil_lead").val();
      var funil = $("#funil_lead :selected").text();

      var cod_nivel = $("#nivel_lead").val();
      var nivel = $("#nivel_lead :selected").text();
      // alert(cod_maquina, maquina);

      var url = "<?= $router->route("cadastro.lead") ?>";

      $.ajax({
        url: url,
        type: "POST",
        data: {
          token: token,
          descricao: descricao,
          produto: produto,
          tipo_integracao: tipo_integracao,
          acao: acao,
          eventos: eventos,
          cod_maquina: cod_maquina,
          maquina: maquina,
          cod_funil: cod_funil,
          funil: funil,
          cod_nivel: cod_nivel,
          nivel: nivel,
        },
        success: (callback) => {
          console.log(callback);
          if (callback == "Salvo com sucesso") {
            if (tipo == 1) {
              alert("Lead Salvo com Sucesso!");
              $("#formLead [name=produto_lead]").focus();
            } else {
              alert("Salvo com sucesso!");
              location.reload();
            }
          } else {
            alert("Erro." + callback);
          }
        }
      })
    }
  }

  $("#funil_lead").change(function() {
    atualizaFunil();
  });


  $("#maquina_lead").change(function() {
    atualizaMaquina();
  });

  function atualizaFunil() {
    var token = $("#token_leadlovers").val();
    var cod_maquina = $("#maquina_lead").val();
    var cod_funil = $("#funil_lead").val();

    if (cod_funil > 0) {
      carregaNivel(token, cod_maquina, cod_funil);
    }
  }

  function atualizaMaquina() {
    var cod_maquina = $("#maquina_lead").val();

    if (cod_maquina > 0) {
      var token = $("#token_leadlovers").val();
      carregaFunil(token, cod_maquina);
    }
  }
</script>
<?php $v->end() ?>