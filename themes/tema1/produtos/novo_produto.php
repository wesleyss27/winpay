<?php
$v->layout("_theme");
if ($_SESSION["tipo"] == 6) {
  header("Location: " . url("vendas"));
}
?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- SEÇÃO TITULO -->
  <section class="content-header">

    <?= $section->getTipoSecaoLayout() ?>

    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-10">
          <h4 style="font-family:inter"><?= $title ?></h4>
        </div>
        <div class="col-sm-2">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="./">Início</a></li>
            <li class="breadcrumb-item active">Novo Produto</li>
          </ol>
        </div>
      </div>
      <div class="row">
        <div class="col">
          <ul id="progressbar">
            <li class="active" id="step1"><strong>Cadastrar Produto</strong></li>
            <li id="step2"><strong>Cadastrar Plano</strong></li>
            <li id="step3"><strong>Cadastrar Checkout</strong></li>
          </ul>
        </div>
      </div>
    </div><!-- /.container-fluid -->
  </section>
  <!-- FIM SEÇÃO TITULO -->

  <style>
    /* Modernização local - mantém a paleta WinPay (amarelo #FFDD00) */
    :root {
      --winpay-primary: #FFDD00;
      --winpay-primary-700: #D4B800;
      --winpay-bg-elevated: #0b0b0b;
      --winpay-bg-surface: #101010;
      --winpay-border: rgba(255, 255, 255, 0.08);
      --winpay-text: #f2f2f2;
      --winpay-text-dim: #bdbdbd;
      --winpay-focus: rgba(255, 221, 0, 0.35);
      --winpay-shadow: 0 10px 30px rgba(0,0,0,0.35);
    }

    /* Breadcrumb (existente) deixa mais elegante sem mudar markup */
    .breadcrumb {
      background: transparent;
      margin: 0;
      padding: 0;
    }

    /* Cards */
    .card {
      background: linear-gradient(135deg, var(--winpay-bg-surface) 0%, #060606 100%);
      border: 1px solid var(--winpay-border);
      border-radius: 14px;
      box-shadow: var(--winpay-shadow);
      overflow: hidden;
    }
    .card > .card-body { padding: 18px; }

    /* Inputs */
    .form-control {
      background: #111;
      border: 1px solid rgba(255,255,255,0.08);
      color: var(--winpay-text);
      border-radius: 10px;
      height: 43px;
      transition: border-color .2s ease, box-shadow .2s ease, background .2s ease;
    }
    .form-control::placeholder { color: #8c8c8c; }
    .form-control:focus {
      background: #141414;
      color: #fff;
      border-color: rgba(255, 221, 0, 0.45);
      box-shadow: 0 0 0 3px var(--winpay-focus);
    }

    label { color: var(--winpay-text); font-weight: 500; }
    small { color: var(--winpay-text-dim); }

    /* Callouts */
    .callout.callout-primary {
      background: linear-gradient(145deg, rgba(255, 221, 0, 0.08), rgba(255, 221, 0, 0.02));
      border-left: 4px solid var(--winpay-primary);
      border-radius: 12px;
      border: 1px solid rgba(255, 221, 0, 0.2);
      color: var(--winpay-text);
    }

    /* Botões */
    .btn.btn-primary {
      background: linear-gradient(135deg, var(--winpay-primary) 0%, #ffe866 100%);
      color: #1a1a1a;
      border: none;
      border-radius: 10px;
      padding: 10px 18px;
      font-weight: 600;
      transition: transform .15s ease, box-shadow .2s ease, filter .2s ease;
    }
    .btn.btn-primary:hover {
      transform: translateY(-1px);
      box-shadow: 0 8px 22px rgba(255, 221, 0, 0.25);
      filter: brightness(1.02);
    }
    .btn.btn-outline-primary {
      border: 1px solid var(--winpay-primary);
      color: var(--winpay-primary);
      border-radius: 10px;
      background: transparent;
    }
    .btn.btn-outline-primary:hover {
      background: rgba(255, 221, 0, 0.1);
      color: var(--winpay-primary);
      box-shadow: 0 6px 18px rgba(255,221,0,0.18);
    }

    /* Progressbar existente (#progressbar) */
    #progressbar {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 10px;
      counter-reset: step;
      padding: 0;
      margin: 10px 0 0;
      list-style: none;
    }
    #progressbar li {
      position: relative;
      text-align: center;
      color: var(--winpay-text-dim);
      font-weight: 600;
      background: linear-gradient(145deg, #0f0f0f, #0a0a0a);
      border: 1px solid var(--winpay-border);
      padding: 12px 8px;
      border-radius: 12px;
      transition: all .25s ease;
    }
    #progressbar li::before {
      counter-increment: step;
      content: counter(step);
      width: 28px;
      height: 28px;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      border-radius: 50%;
      margin-right: 8px;
      background: rgba(255, 221, 0, 0.15);
      color: var(--winpay-primary-700);
      border: 1px solid rgba(255,221,0,0.25);
      box-shadow: inset 0 0 10px rgba(255,221,0,0.08);
    }
    #progressbar li.active {
      color: var(--winpay-primary);
      border-color: rgba(255,221,0,0.35);
      box-shadow: 0 8px 26px rgba(255,221,0,0.12);
    }
    #progressbar li.active::before {
      background: var(--winpay-primary);
      color: #1a1a1a;
      border-color: var(--winpay-primary);
    }

    /* Checkbox rascunho */
    .rascunho.alert {
      background: linear-gradient(145deg, rgba(255, 193, 7, 0.15), rgba(255, 193, 7, 0.05));
      border: 1px solid rgba(255, 193, 7, 0.3);
      border-radius: 12px;
      color: var(--winpay-text);
    }

    /* Seções de upload */
    input[type="file"].form-control { background: #111; }

    /* Ajuda (botão flutuante) */
    .btn-ajuda {
      border-radius: 999px;
      background: linear-gradient(135deg, var(--winpay-primary) 0%, #ffe866 100%);
      color: #151515;
      border: none;
      box-shadow: 0 10px 24px rgba(255,221,0,0.28);
      font-weight: 700;
    }
    .ajuda {
      background: linear-gradient(135deg, #0f0f0f, #0a0a0a);
      border: 1px solid var(--winpay-border);
      border-radius: 14px;
      color: var(--winpay-text);
      box-shadow: var(--winpay-shadow);
    }
    .ajuda-titulo { color: var(--winpay-primary); }
    .mensagens a { color: var(--winpay-primary); }
  </style>

  <!-- < ?php
  if (!$asaasClientes) {
  ?> -->
    <!-- <div class="alert alert-warning">
      Você precisa concluir seu cadastro para poder usar todas as funcionalidades. <a href="<?= url("usuarios/perfil/" . $_SESSION["id"]) ?>">Concluir cadastro</a>
    </div> -->
  <!-- < ?php
  } else {
  ?> -->

    <!-- Main content -->
    <section class="content" style="padding:20px">
      <?= $v->insert("fragmentos/msg-cadastrar-editar", []); ?>
      <form action="<?= $router->route("produtos.cadastro"); ?>" method="post" enctype="multipart/form-data" style="font-family:inter; text-transform:uppercase; font-size:11px">

        <!-- RASCUNHO -->
        <div class="rascunho bg-warning alert">

          <label for="rascunho" style="font-size: 16px">
            <input type="checkbox" name="rascunho" id="rascunho" value="1" checked>
            &nbsp; Rascunho**
          </label><br>

          ** <span>quando esta opção estiver marcada, o produto não será enviado para análise</span>
        </div>
        <!-- FIM RASCUNHO -->

        <!-- ITENS BÁSICOS -->
        <!-- CARD -->
        <div class="card">

          <div class="card-body">
            <!-- .row -->
            <div class="row">
              <!-- NOME PRODUTO -->
              <div class="col-md-4">
                <label>Nome do Produto*</label>
                <input type="text" class="form-control" id="nome" name="nome" placeholder="Digite o nome do seu produto" required />
              </div>
              <!-- FIM NOME PRODUTO -->

              <!-- CATEGORIA -->
              <div class="col-md-4">
                <label>Categoria*</label>
                <select id="categoria" name="categoria" class="form-control form-control" required>
                  <option disabled selected>-- Selecione --</option>
                  <option value='1'>Administração e Negócios</option>
                  <option value='2'>Animais de Estimação</option>
                  <option value='3'>Arquitetura e Engenharia</option>
                  <option value='4'>Artes e Música</option>
                  <option value='5'>Auto-ajuda e Desenvolvimento Pessoal</option>
                  <option value='6'>Automóveis</option>
                  <option value='7'>Blogs e Redes Sociais</option>
                  <option value='8'>Casa e Jardinagem</option>
                  <option value='9'>Culinária, Gastronomia, Receitas</option>
                  <option value='10'>Design e Templates PSD, PPT ou HTML</option>
                  <option value='11'>Edição de Áudio, Vídeo ou Imagens</option>
                  <option value='12'>Educacional, Cursos Técnicos e Profissionalizantes</option>
                  <option value='13'>Eletrodomésticos</option>
                  <option value='46'>Eletrônicos</option>
                  <option value='14'>Eletroportáteis</option>
                  <option value='15'>Entretenimento, Lazer e Diversão</option>
                  <option value='16'>Esportes e Fitness</option>
                  <option value='17'>Filmes e Cinema</option>
                  <option value='18'>Geral</option>
                  <option value='19'>Histórias em Quadrinhos</option>
                  <option value='20'>Idiomas</option>
                  <option value='21'>Informática</option>
                  <option value='22'>Internet Marketing</option>
                  <option value='23'>Investimentos e Finanças</option>
                  <option value='24'>Jogos de Cartas, Poker, Loterias</option>
                  <option value='25'>Jogos de Computador, Jogos Online</option>
                  <option value='26'>Jurídico</option>
                  <option value='27'>Literatura e Poesia</option>
                  <option value='28'>Marketing de Rede</option>
                  <option value='29'>Marketing e Comunicação</option>
                  <option value='30'>Meio Ambiente</option>
                  <option value='31'>Moda e vestuário</option>
                  <option value='32'>Moveis e decoração</option>
                  <option value='33'>Música, Bandas e Shows</option>
                  <option value='34'>Paquera, Sedução e Relacionamentos</option>
                  <option value='35'>Perfumaria</option>
                  <option value='36'>Pessoas com deficiência</option>
                  <option value='37'>Plugins, Widgets e Extensões</option>
                  <option value='38'>Produtividade e Organização Pessoal</option>
                  <option value='39'>Produtos infantis</option>
                  <option value='40'>Relatórios, Artigos e Pesquisas</option>
                  <option value='41'>Religião e Crenças</option>
                  <option value='42'>Romances, Dramas, Estórias e Contos</option>
                  <option value='43'>RPG e Jogos de Mesa</option>
                  <option value='44'>Saúde, Bem-estar e Beleza</option>
                  <option value='45'>Scripts</option>
                  <option value='46'>Segurança do Trabalho</option>
                  <option value='47'>Sexologia e Sexualidade</option>
                  <option value='48'>Snippets (Trechos de Vídeo)</option>
                  <option value='49'>Trader</option>
                  <option value='50'>Turismo</option>
                </select>
              </div>
              <!-- FIM CATEGORIA -->

              <!-- TIPO -->
              <div class="col-md-4">
                <label>Tipo*</label>
                <select id="tipo_produto" name="tipo_produto" class="form-control form-control" required>
                  <option disabled selected>-- Selecione --</option>
                  <option value='1'>Aplicativos p/ Celular</option>
                  <option value='2'>Áudios, Músicas, Ringtones</option>
                  <?php if ($_SESSION["tipo"] != 6 && $usuario->dropshipper == 1) : ?>
                    <option value='14'>Dropshipping</option>
                  <?php endif; ?>
                  <option value='3'>Ebooks, Documentos, Texto</option>
                  <option value='4'>Evento</option>
                  <option value='5'>Imagens, Ícones, Fotos</option>
                  <option value='6'>Mentoria/Mastermind</option>
                  <option value='7'>Planilha de Excel</option>
                  <?php if ($_SESSION["tipo"] != 6 && $usuarioDados->tipo_doc == 2) : ?>
                    <option value='8'>Produto Físico</option>
                  <?php endif; ?>
                  <option value='9'>Screencasts, Filmes</option>
                  <option value='10'>Serviços</option>
                  <option value='11'>Site Membros (Cursos, Video Aulas)/ Serviço de assinatura</option>
                  <option value='12'>Software</option>
                  <option value='13'>Templates, Códigos Fonte</option>
                </select>
                <small>Para cadastrar produtos físicos, você precisa ter um CNPJ e <a href="<?= url("usuarios/perfil/" . $_SESSION["id"]) ?>">Concluir cadastro</a></small>
              </div>
              <!-- FIM TIPO -->
            </div>
            <!-- fim .row -->

            <br>
            <!-- ÁREA DE MEMBROS -->
            <div id="area-membros">
              <label>Onde você quer disponibilizar o seu conteúdo? *</label> <br><br>
              <div class="row">
                <div class="col-md-4">
                  <div class="col-md-12">
                    <div class="row bg-azul-gradient card card-body">
                      <label for="octa">
                        <input type="radio" name="membros" id="octa" value="octaclube"> RedClube
                        <img src="<?= url("themes/imgs/logos/logo2-2.png") ?>" alt="" width="100%">
                      </label>
                    </div>
                  </div>
                  Após cadastrar o produto, acesse <a href="https://club.winpay.digital" target="_blank">https://club.winpay.digital</a> para configurar seu curso na REDROCKET
                </div>

                <div class="col-md-4">
                  <div class="col-md-12">
                    <div class="row bg-default card card-body">
                      <label for="externa">
                        <input type="radio" name="membros" id="externa" value="externa"> Externa
                        <input type="text" name="url_membros" id="url_mebros" class="form-control">
                      </label>

                      <p>Informe o link que seus alunos vão acessar</p>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <!-- FIM ÁREA DE MEMBROS -->
            <br>
          </div>
          <!-- FIM CARD-BODY -->
        </div>
        <!-- FIM CARD -->

        <!-- CARD -->
        <div class="card">

          <div class="card-body">
            <!-- .row -->
            <div class="row">
              <div class="col-md-2">
                <label>Tipo de cobrança:</label><br>
                <input type="radio" name="tipo_cobranca" id="tipo_cobranca" value="1" checked required> Única <br>
                <input type="radio" name="tipo_cobranca" id="tipo_cobranca" value="2" required> Recorrente
              </div>

              <!-- PREÇO -->
              <div class="col-md-4">
                <label>Preço* <small>mínimo: R$ 5,00</small></label>
                <input type="text" name="preco" id="preco" class="form-control form-control" placeholder="0,00" required>
                <p id="msg-preco" style="color: red"></p>
              </div>
              <!-- FIM PREÇO -->

              <div class="col-md-4">
                <label>Nome na fatura do cartão <small>máximo 13 caracteres</small></label>
                <input type="text" name="nome_fatura" id="nome_fatura" class="form-control form-control" maxlength="13">
              </div>
            </div>
          </div>
          <!-- FIM CARD -->
        </div>
        <!-- FIM CARD -->

        <!-- CARD -->
        <div class="card">
          <div class="card-body">
            <div class="row">
              <!-- TEMPO GARANTIA -->
              <div class="col-md-4">
                <label style="margin-top:10px">Tempo de Garantia*</label>
                <select id="garantia" name="garantia" class="form-control form-control" required>
                  <option value='30' selected>30 dias</option>
                  <option value='21'>21 dias</option>
                  <option value='15'>15 dias</option>
                  <option value='7'>7 dias</option>
                </select>
              </div>
              <!-- FIM TEMPO GARANTIA -->

              <!-- WHATSAPP -->
              <div class="col-md-4">
                <label style="margin-top:10px">Whatsapp no checkout?</label>
                <i class="fas fa-question-circle" title="Somente números; Colocar o código do país seguido do DDD e o número do telefone"></i>
                <input type="tel" id="whatsapp_checkout" name="whatsapp_checkout" class="form-control form-control" placeholder="EX.: 5533999999999">
                cod. país (BRA - 55) + DDD + telefone
              </div>
              <!-- FIM WHATSAPP -->

              <!-- EMAIL SUPORTE -->
              <div class="col-md-4">
                <label style="margin-top:10px">E-mail de Suporte *</label>
                <input type="email" id="email_suporte" name="email_suporte" class="form-control form-control" placeholder="E-mail" required>
              </div>
              <!-- FIM EMAIL SUPORTE -->
            </div>
            <!-- fim .row -->

            <div class="row">
              <!-- EMAIL SUPORTE -->
              <div class="col-md-4">
                <label style="margin-top:10px">E-mail de Chargeback</label>
                <input type="email" id="email_chargeback" name="email_chargeback" class="form-control form-control">
                <small>Email usado para recebimento de chargebacks além do email do produtor. Se não preenchido será usado o email de suporte</small>
              </div>
              <!-- FIM EMAIL SUPORTE -->
            </div>
          </div>
          <!-- fim .card-body -->
        </div>
        <!-- FIM CARD -->
        <!-- FIM ITENS BÁSICOS -->

        <hr>
        <!-- SEÇÃO URLs -->
        <!-- .card -->
        <div class="card">
          <!-- .card-body -->
          <div class="card-body">
            <!-- .row -->
            <div class="row">
              <!-- PAGINA DE VENDAS -->
              <div class="col-md-4">
                <label style="margin-top:10px">Url da página de venda*</label>
                <input type="text" id="pagina_venda" name="pagina_venda" class="form-control form-control" required>
              </div>
              <!-- FIM PÁGINA DE VENDAS -->

              <!-- OBRIGADO CARTÃO -->
              <div class="col-md-4">
                <label style="margin-top:10px">Url de obrigado cartão</label>
                <input type="text" id="obrigado_cartao" name="obrigado_cartao" class="form-control form-control">
              </div>
              <!-- FIM OBRIGADO CARTÃO -->

              <!-- OBRIGADO BOLETOS -->
              <div class="col-md-4">
                <label style="margin-top:10px">Url de obrigado boletos</label>
                <input type="text" id="obrigado_boleto" name="obrigado_boleto" class="form-control form-control">
              </div>
              <!-- FIM OBRIGADO BOLETOS -->
            </div>
            <!-- fim .row -->

            <!-- .row -->
            <div class="row">
              <!-- OBRIGADO ANÁLISE -->
              <div class="col-md-4">
                <label style="margin-top:10px">Url página obrigado compra análise</label>
                <input type="text" id="obrigado_analise" name="obrigado_analise" class="form-control form-control">
              </div>
              <!-- FIM OBRIGADO ANÁLISE -->

              <!-- MÍDIAS -->
              <div class="col-md-4">
                <label style="margin-top:10px">Url de mídia</label></i>
                <input type="text" id="url_midia" name="url_midias" class="form-control form-control">
                <small>Link em que o afiliado pode ter acesso às mídias de divulgação do produto</small>
              </div>
              <!-- FIM MÍDIAS -->

              <!-- LINK RASTREIO -->
              <div class="col-md-4">
                <label style="margin-top:10px">Link de rastreio (Obrigatório para produtos físicos)</label></i>
                <input type="text" id="link_rastreio" name="link_rastreio" class="form-control form-control">
              </div>
              <!-- FIM LINK RASTREIO -->
            </div>
            <!-- fim .row -->
          </div>
          <!-- fim .card-body -->
        </div>
        <!-- fim .card -->
        <!-- FIM SEÇÃO URLs -->

        <hr>
        <!-- SEÇÃO DOCUMENTOS -->
        <!-- card -->
        <div class="card">
          <!-- card-body -->
          <div class="card-body">
            <!-- .row -->
            <div class="row">
              <!-- FOTO -->
              <div class="col-12 col-md-6">
                <label style="margin-top:25px">FOTO DO PRODUTO: 600 X 600 pixels*</label> <small>Tamanho máximo: 2 mb</small><br>
                Arquivos suportados: <span style="text-transform:lowercase;"> png, jpg</span>
                <input type="file" name="foto" class="form-control form-control" style="height:43px;" required>
              </div>
              <!-- FIM FOTO -->

              <!-- DOCUMENTOS -->
              <div class="col-12 col-md-6">
                <label style="margin-top:25px">Documentos técnicos do produto</label> <small>Tamanho máximo: 10 mb</small><br>
                Arquivos suportados: <span style="text-transform:lowercase;"> png, jpg, pdf, zip, rar </span>
                <input type="file" name="documentos" class="form-control form-control" style="height:43px;">

                Para aprovação de produtos físicos é necessário enviar: <br>
                Fotos do estoque;
                Documentos técnicos do produto;<br>
                Print da conta no Reclame Aqui;
                Alvará de vigilância sanitária. <br>
                Coloque todos em um arquivo de no máximo 10 MB
              </div>
              <!-- FIM DOCUMENTOS -->
            </div>
            <!-- .row -->
          </div>
          <!-- fim .card-body -->
        </div>
        <!-- fim .card -->
        <!-- FIM SEÇÃO DOCUMENTOS -->

        <hr>
        <!-- card -->
        <div class="card">
          <div class="card-body">
            <!-- .row -->
            <div class="row">
              <!-- DESCRIÇÃO -->
              <div class="col-12">
                <label style="margin-top:25px">Descrição do Produto*</label> <small>máximo 500 caracteres</small>
                <textarea name="descricao" rows="5" class="form-control form-control" placeholder="Descreva bem o seu produto. Quanto melhor a descrição maior a change de conseguir afiliados. Máximo 1000 caracteres" required style="resize:none"></textarea>
              </div>
              <!-- FIM DESCRIÇÃO -->
            </div>
            <!-- .fim row -->
          </div>
          <!-- fim .card-body -->
        </div>
        <!-- fim .card -->

        <div class="card card-body">
          <div class="row">
            <div class="col-md-12">
              <label>Habilitar para ecommerce</label>
              <select name="habilitar_ecommerce" id="habilitar_ecommerce" class="form-control">
                <option value="0">Não</option>
                <option value="1">Sim</option>
              </select>
            </div>
          </div>

          <br><br>
          <div class="row" id="mostrar_caracteristica">
            <div class="col-md-12">
              <label>Características do produto</label>
              <textarea name="caracteristicas" id="caracteristicas" cols="30" rows="5" class="form-control"></textarea>
            </div>
          </div>
        </div>

        <!-- .card -->
        <div class="card card-body">
          <div class="row">
            <div class="col-6">

              <!-- CHECKBOX -->
              <div class="icheck-success d-inline">
                <input type="checkbox" id="checkboxSuccess1" name="termos" required>
                <label for="checkboxSuccess1" style="display: block"> Aceito os termos de uso
                </label>
              </div>
              <!-- FIM CHECKBOX -->

              <a href="<?= url("termos-uso") ?>" target="_blank"><small>Termos de Uso</small></a>
            </div>

            <?php if (!@$_SESSION["subusuario"] || $permissao->adicionar == 1) : ?>
              <div class="col-6">
                <button type="submit" data-target="#modal-lg" class="btn btn-primary" style="float:right" id="next-step">Continuar <i class="fas fa-arrow-right"></i></button>
              </div>
            <?php endif ?>
          </div>
        </div>

      </form>
      <!-- Fim Formulário de informações do novo produto -->
    </section>

  <!-- < ?php } ?> -->
</div>

<!-- INICIO BOTÃO DE AJUDA E SUAS FUNCIONALIDADES -->
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
    <div class="mensagens"><a href="https://octapay.com.br/tutoriais/cadastro-de-produtos/" target="_blank">Dúvidas sobre cadastro de produtos</a></div>
    <div class="mensagens"><a href="mailto:<?= $para ?>?subject=<?= $assunto ?>&body=<?= $mensagem ?>" target="_blank">Entrar em contato por email</a></div>
    <div class="mensagens"><a href="https://api.whatsapp.com/send?phone=5533998131343&text=<?= $mensagem_whats ?>" target="_blank">Entrar em contato pelo Whatsapp</a></div>
  </div>
</div>


<!-- FIM BOTÃO AJUDA E SUAS FUNCIONALIDADES -->

<?= $v->start("scripts") ?>
<script>
  // INICIO BOTÃO AJUDA
  $("#btn-ajuda").click(() => {
    $(".ajuda").toggle("slow");
  });

  $("#ajuda-fechar").click(() => {
    $(".ajuda").hide("slow");
  });
  // FIM BOTÃO AJUDA

  $("#preco").blur(function(e) {
    var valor = $("#preco").val();
    $("#preco").val(valor.replace(",", "."));

    if (valor < 5) {
      $("#preco").focus();
      $("#msg-preco").html("Valor do produto abaixo do mínimo permitido. Valor mínimo R$5,00");
    } else {
      $("#msg-preco").hide("slow");
    }
  });

  $("#area-membros").hide();
  $("#tipo_produto").change(() => {
    var tipo = $("#tipo_produto").val();

    if (tipo == 11) {
      $("#area-membros").show();
    } else {
      $("#area-membros").hide();
    }
  });

  function mostrarCaracteristica() {
    var ecommerce = $("#habilitar_ecommerce").val();

    if (ecommerce == 0) {
      $("#mostrar_caracteristica").hide();
    } else {
      $("#mostrar_caracteristica").show();
    }
  }

  mostrarCaracteristica()

  $("#habilitar_ecommerce").change(() => {
    mostrarCaracteristica();
  });
</script>
<?= $v->end() ?>