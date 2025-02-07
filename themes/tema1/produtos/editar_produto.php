<?php $v->layout("_theme"); ?>

<?php
if ($produto->usuario != $section->getId()) {
  header("Location: " . url("produtos/detalhe/") . $produto->id);
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
          <h3 style="font-family:inter"><?= $title ?></h3>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="./">Início</a></li>
            <li class="breadcrumb-item active">Cadastrar Produto</li>
          </ol>
        </div>
      </div>
    </div><!-- /.container-fluid -->
  </section>

  <!-- Main content -->
  <section class="content" style="padding:20px">

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

    <?= $v->insert("fragmentos/msg-cadastrar-editar", []); ?>

    <form action="<?= $router->route("produtos.editarPost"); ?>" method="post" enctype="multipart/form-data" style="font-family:inter; text-transform:uppercase; font-size:11px">
      <input type="hidden" name="status" value="<?= $produto->status ?>">

      <?php if (@$produto->status != 2) : ?>
        <div class="rascunho bg-warning alert">
          <label for="rascunho" style="font-size: 16px">
            <input type="checkbox" name="rascunho" id="rascunho" value="1" <?= ($produto->rascunho == 1) ? 'checked' : '' ?>>
            &nbsp; Rascunho**
          </label><br>
          ** <span>quando esta opção estiver marcada, o produto não será enviado para análise</span>
        </div>
      <?php endif; ?>

      <div class="row">
        <div class="col-md-3">
          <label class="control-label">Código: &nbsp;</label>
          <?= $produto->codigo ?>
          <input type="hidden" name="id" value="<?= $produto->id ?>">
        </div>
      </div>


      <!-- ITENS BÁSICOS -->
      <!-- CARD -->
      <div class="card">

        <div class="card-body">
          <!-- .row -->
          <div class="row">
            <!-- NOME PRODUTO -->
            <div class="col-md-4">
              <label>Nome do Produto*</label>
              <input type="text" class="form-control" id="nome" name="nome" placeholder="Digite o nome do seu produto" value="<?= $produto->nome ?>" required />
            </div>
            <!-- FIM NOME PRODUTO -->

            <!-- CATEGORIA -->
            <div class="col-md-4">
              <label>Categoria*</label>
              <select id="categoria" name="categoria" class="form-control form-control" required>
                <option disabled selected>-- Selecione --</option>
                <option value='1' <?= ($produto->categoria == 1) ? 'selected' : '' ?>>Administração e Negócios</option>
                <option value='2' <?= ($produto->categoria == 2) ? 'selected' : '' ?>>Animais de Estimação</option>
                <option value='3' <?= ($produto->categoria == 3) ? 'selected' : '' ?>>Arquitetura e Engenharia</option>
                <option value='4' <?= ($produto->categoria == 4) ? 'selected' : '' ?>>Artes e Música</option>
                <option value='5' <?= ($produto->categoria == 5) ? 'selected' : '' ?>>Auto-ajuda e Desenvolvimento Pessoal</option>
                <option value='6' <?= ($produto->categoria == 6) ? 'selected' : '' ?>>Automóveis</option>
                <option value='7' <?= ($produto->categoria == 7) ? 'selected' : '' ?>>Blogs e Redes Sociais</option>
                <option value='8' <?= ($produto->categoria == 8) ? 'selected' : '' ?>>Casa e Jardinagem</option>
                <option value='9' <?= ($produto->categoria == 9) ? 'selected' : '' ?>>Culinária, Gastronomia, Receitas</option>
                <option value='10' <?= ($produto->categoria == 10) ? 'selected' : '' ?>>Design e Templates PSD, PPT ou HTML</option>
                <option value='11' <?= ($produto->categoria == 11) ? 'selected' : '' ?>>Edição de Áudio, Vídeo ou Imagens</option>
                <option value='12' <?= ($produto->categoria == 12) ? 'selected' : '' ?>>Educacional, Cursos Técnicos e Profissionalizantes</option>
                <option value='13' <?= ($produto->categoria == 13) ? 'selected' : '' ?>>Eletrodomésticos</option>
                <option value='46'>Eletrônicos</option>
                <option value='14' <?= ($produto->categoria == 14) ? 'selected' : '' ?>>Eletroportáteis</option>
                <option value='15' <?= ($produto->categoria == 15) ? 'selected' : '' ?>>Entretenimento, Lazer e Diversão</option>
                <option value='16' <?= ($produto->categoria == 16) ? 'selected' : '' ?>>Esportes e Fitness</option>
                <option value='17' <?= ($produto->categoria == 17) ? 'selected' : '' ?>>Filmes e Cinema</option>
                <option value='18' <?= ($produto->categoria == 18) ? 'selected' : '' ?>>Geral</option>
                <option value='19' <?= ($produto->categoria == 19) ? 'selected' : '' ?>>Histórias em Quadrinhos</option>
                <option value='20' <?= ($produto->categoria == 20) ? 'selected' : '' ?>>Idiomas</option>
                <option value='21' <?= ($produto->categoria == 21) ? 'selected' : '' ?>>Informática</option>
                <option value='22' <?= ($produto->categoria == 22) ? 'selected' : '' ?>>Internet Marketing</option>
                <option value='23' <?= ($produto->categoria == 23) ? 'selected' : '' ?>>Investimentos e Finanças</option>
                <option value='24' <?= ($produto->categoria == 24) ? 'selected' : '' ?>>Jogos de Cartas, Poker, Loterias</option>
                <option value='25' <?= ($produto->categoria == 25) ? 'selected' : '' ?>>Jogos de Computador, Jogos Online</option>
                <option value='26' <?= ($produto->categoria == 26) ? 'selected' : '' ?>>Jurídico</option>
                <option value='27' <?= ($produto->categoria == 27) ? 'selected' : '' ?>>Literatura e Poesia</option>
                <option value='28' <?= ($produto->categoria == 28) ? 'selected' : '' ?>>Marketing de Rede</option>
                <option value='29' <?= ($produto->categoria == 29) ? 'selected' : '' ?>>Marketing e Comunicação</option>
                <option value='30' <?= ($produto->categoria == 30) ? 'selected' : '' ?>>Meio Ambiente</option>
                <option value='31' <?= ($produto->categoria == 31) ? 'selected' : '' ?>>Moda e vestuário</option>
                <option value='32' <?= ($produto->categoria == 32) ? 'selected' : '' ?>>Moveis e decoração</option>
                <option value='33' <?= ($produto->categoria == 33) ? 'selected' : '' ?>>Música, Bandas e Shows</option>
                <option value='34' <?= ($produto->categoria == 34) ? 'selected' : '' ?>>Paquera, Sedução e Relacionamentos</option>
                <option value='35' <?= ($produto->categoria == 35) ? 'selected' : '' ?>>Perfumaria</option>
                <option value='36' <?= ($produto->categoria == 36) ? 'selected' : '' ?>>Pessoas com deficiência</option>
                <option value='37' <?= ($produto->categoria == 37) ? 'selected' : '' ?>>Plugins, Widgets e Extensões</option>
                <option value='38' <?= ($produto->categoria == 38) ? 'selected' : '' ?>>Produtividade e Organização Pessoal</option>
                <option value='39' <?= ($produto->categoria == 39) ? 'selected' : '' ?>>Produtos infantis</option>
                <option value='40' <?= ($produto->categoria == 40) ? 'selected' : '' ?>>Relatórios, Artigos e Pesquisas</option>
                <option value='41' <?= ($produto->categoria == 41) ? 'selected' : '' ?>>Religião e Crenças</option>
                <option value='42' <?= ($produto->categoria == 42) ? 'selected' : '' ?>>Romances, Dramas, Estórias e Contos</option>
                <option value='43' <?= ($produto->categoria == 43) ? 'selected' : '' ?>>RPG e Jogos de Mesa</option>
                <option value='44' <?= ($produto->categoria == 44) ? 'selected' : '' ?>>Saúde, Bem-estar e Beleza</option>
                <option value='45' <?= ($produto->categoria == 45) ? 'selected' : '' ?>>Scripts</option>
                <option value='46' <?= ($produto->categoria == 46) ? 'selected' : '' ?>>Segurança do Trabalho</option>
                <option value='47' <?= ($produto->categoria == 47) ? 'selected' : '' ?>>Sexologia e Sexualidade</option>
                <option value='48' <?= ($produto->categoria == 48) ? 'selected' : '' ?>>Snippets (Trechos de Vídeo)</option>
                <option value='49' <?= ($produto->categoria == 49) ? 'selected' : '' ?>>Trader</option>
                <option value='50' <?= ($produto->categoria == 50) ? 'selected' : '' ?>>Turismo</option>
              </select>
            </div>
            <!-- FIM CATEGORIA -->

            <!-- TIPO -->
            <div class="col-md-4">
              <label>Tipo*</label>
              <select id="tipo_produto" name="tipo_produto" class="form-control form-control" required>
                <option disabled>-- Selecione --</option>
                <option value='1' <?= ($produto->tipo_produto == 1) ? 'selected' : '' ?>>Aplicativos p/ Celular</option>
                <option value='2' <?= ($produto->tipo_produto == 2) ? 'selected' : '' ?>>Áudios, Músicas, Ringtones</option>
                <option value='3' <?= ($produto->tipo_produto == 3) ? 'selected' : '' ?>>Ebooks, Documentos, Texto</option>
                <option value='4' <?= ($produto->tipo_produto == 4) ? 'selected' : '' ?>>Evento</option>
                <option value='5' <?= ($produto->tipo_produto == 5) ? 'selected' : '' ?>>Imagens, Ícones, Fotos</option>
                <option value='6' <?= ($produto->tipo_produto == 6) ? 'selected' : '' ?>>Mentoria/Mastermind</option>
                <option value='7' <?= ($produto->tipo_produto == 7) ? 'selected' : '' ?>>Planilha de Excel</option>
                <?php if ($_SESSION["tipo"] != 6 && $usuarioDados->tipo_doc == 2) : ?>
                  <option value='8' <?= ($produto->tipo_produto == 8) ? 'selected' : '' ?>>Produto Físico</option>
                <?php endif; ?>
                <option value='9' <?= ($produto->tipo_produto == 9) ? 'selected' : '' ?>>Screencasts, Filmes</option>
                <option value='10' <?= ($produto->tipo_produto == 10) ? 'selected' : '' ?>>Serviços</option>
                <option value='11' <?= ($produto->tipo_produto == 11) ? 'selected' : '' ?>>Site Membros (Cursos, Video Aulas)/ Serviço de assinatura</option>
                <option value='12' <?= ($produto->tipo_produto == 12) ? 'selected' : '' ?>>Software</option>
                <option value='13' <?= ($produto->tipo_produto == 13) ? 'selected' : '' ?>>Templates, Códigos Fonte</option>
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
                      <input type="radio" name="membros" id="octa" value="octaclube" <?= ($produto->membros == "octaclube") ? 'checked' : '' ?>> RedClube
                      <img src="<?= url("themes/imgs/logos/logo2-2.png") ?>" alt="" width="100%">
                    </label>
                  </div>
                </div>
                Após cadastrar o produto, acesse <a href="https://club.redrocketpay.com" target="_blank">https://club.redrocketpay.com</a> para configurar seu curso na REDROCKET
              </div>

              <div class="col-md-4">
                <div class="col-md-12">
                  <div class="row bg-default card card-body">
                    <label for="externa">
                      <input type="radio" name="membros" id="externa" value="externa" <?= ($produto->membros == "externa") ? 'checked' : '' ?>> Externa
                      <input type="text" name="url_membros" id="url_mebros" class="form-control" value="<?= $produto->url_membros ?>">
                    </label>

                    <p>Informe o link que seus alunos vão acessar</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <!-- FIM ÁREA DE MEMBROS -->
        </div>
      </div>
      <br>

      <!-- CARD -->
      <div class="card">
        <div class="card-body">
          <!-- .row -->
          <div class="row">
            <div class="col-md-2">
              <label>Tipo de cobrança:</label><br>
              <input type="radio" name="tipo_cobranca" id="tipo_cobranca" value="1" <?= ($produto->tipo_cobranca == 1) ? 'checked' : '' ?> required> Única <br>
              <input type="radio" name="tipo_cobranca" id="tipo_cobranca" value="2" <?= ($produto->tipo_cobranca == 2) ? 'checked' : '' ?> required> Recorrente
            </div>

            <!-- PREÇO -->
            <div class="col-md-4">
              <label>Preço* <small>mínimo: R$ 5,00</small></label>
              <input type="text" name="preco" id="preco" class="form-control form-control" value="<?= $produto->preco ?>" required>
            </div>
            <!-- FIM PREÇO -->

            <div class="col-md-4">
              <label>Nome na fatura do cartão <small>máximo 13 caracteres</small></label>
              <input type="text" name="nome_fatura" id="nome_fatura" class="form-control form-control" maxlength="13" value="<?= $produto->nome_fatura ?>">
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
                <option value='30' <?= ($produto->tempo_garantia == 30) ? 'selected' : '' ?>>30 dias</option>
                <option value='21' <?= ($produto->tempo_garantia == 30) ? 'selected' : '' ?>>21 dias</option>
                <option value='15' <?= ($produto->tempo_garantia == 30) ? 'selected' : '' ?>>15 dias</option>
                <option value='7' <?= ($produto->tempo_garantia == 30) ? 'selected' : '' ?>>7 dias</option>
              </select>
            </div>
            <!-- FIM TEMPO GARANTIA -->

            <!-- WHATSAPP -->
            <div class="col-md-4">
              <label style="margin-top:10px">Whatsapp no checkout?</label>
              <i class="fas fa-question-circle" title="Somente números; Colocar o código do país seguido do DDD e o número do telefone"></i>
              <input type="tel" id="whatsapp_checkout" name="whatsapp_checkout" class="form-control form-control" value="<?= $produto->whatsapp_checkout ?>">
              cod. país (BRA - 55) + DDD + telefone
            </div>
            <!-- FIM WHATSAPP -->

            <!-- EMAIL SUPORTE -->
            <div class="col-md-4">
              <label style="margin-top:10px">E-mail de Suporte *</label>
              <input type="email" id="email_suporte" name="email_suporte" class="form-control form-control" value="<?= $produto->email_suporte ?>" required>
            </div>
            <!-- FIM EMAIL SUPORTE -->
          </div>
          <!-- fim .row -->

          <div class="row">
            <!-- EMAIL SUPORTE -->
            <div class="col-md-4">
              <label style="margin-top:10px">E-mail de Chargeback</label>
              <input type="email" id="email_chargeback" name="email_chargeback" class="form-control form-control" value="<?= $produto->email_chargeback ?>">
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
            <!-- OBRIGADO CARTÃO -->
            <div class="col-md-4">
              <label style="margin-top:10px">Url de obrigado cartão</label>
              <input type="text" class="form-control margin-top-none" id="obrigado_cartao" name="obrigado_cartao" value="<?= $produto->obrigado_cartao ?>" />
            </div>
            <!-- FIM OBRIGADO CARTÃO -->

            <!-- OBRIGADO BOLETOS -->
            <div class="col-md-4">
              <label style="margin-top:10px">Url de obrigado boletos</label>
              <input type="text" class="form-control margin-top-none" id="obrigado_boleto" name="obrigado_boleto" value="<?= $produto->obrigado_boleto ?>" />
            </div>
            <!-- FIM OBRIGADO BOLETOS -->
          </div>
          <!-- fim .row -->

          <!-- .row -->
          <div class="row">
            <!-- OBRIGADO ANÁLISE -->
            <div class="col-md-4">
              <label style="margin-top:10px">Url página obrigado compra análise</label>
              <input type="text" class="form-control margin-top-none" id="obrigado_analise" name="obrigado_analise" value="<?= $produto->obrigado_analise ?>" />
            </div>
            <!-- FIM OBRIGADO ANÁLISE -->

            <!-- MÍDIAS -->
            <div class="col-md-4">
              <label style="margin-top:10px">Url de mídia</label></i>
              <input type="text" class="form-control margin-top-none" id="url_midias" name="url_midias" value="<?= $produto->url_midias ?>" />
              <small>Link em que o afiliado pode ter acesso às mídias de divulgação do produto</small>
            </div>
            <!-- FIM MÍDIAS -->

            <!-- LINK RASTREIO -->
            <div class="col-md-4">
              <label style="margin-top:10px">Link de rastreio (Obrigatório para produtos físicos)</label></i>
              <input type="text" id="link_rastreio" name="link_rastreio" class="form-control form-control" value="<?= $produto->link_rastreio ?>">
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
              <input type="file" name="foto" class="form-control">
              <input type="hidden" name="foto_atual" value="<?= $produto->foto ?>"><br>

              <label class="form-label">Imagem atual:</label><br>
              <?php if ($produto->shopify == 1) : ?>
                <img src="<?= $produto->foto; ?>" width="200px" height="200px"><br>
              <?php else : ?>
                <img src="<?= url("uploads/produtos/") . $produto->foto ?>" width="200px" height="200px"><br>
              <?php endif; ?>
            </div>
            <!-- FIM FOTO -->

            <!-- DOCUMENTOS -->
            <div class="col-12 col-md-6">
              <label style="margin-top:25px">Documentos técnicos do produto</label> <small>Tamanho máximo: 10 mb</small><br>
              Arquivos suportados: <span style="text-transform:lowercase;"> png, jpg, pdf, zip, rar </span>
              <input type="hidden" name="doc_atual" value="<?= $produto->documentos ?>">
              <input type="file" name="documentos" class="form-control"><br>

              <label class="form-label">Documento atual:</label><br>
              <a href="<?= url("uploads/documentos/") . $produto->documentos ?>" target="_blank"><?= $produto->documentos ?></a>

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
      <div class="card card-body">
        <div class="row">
          <!-- DESCRIÇÃO -->
          <div class="col-12">
            <label style="margin-top:25px">Descrição do Produto*</label> <small>máximo 500 caracteres</small>
            <textarea rows="6" class="form-control" id="descricao" name="descricao" required><?= $produto->descricao ?></textarea>
          </div>
          <!-- FIM DESCRIÇÃO -->
        </div>
      </div>
      <!-- fim .card -->

      <div class="card card-body">
        <div class="row">
          <div class="col-md-12">
            <label>Habilitar para ecommerce</label>
            <select name="habilitar_ecommerce" id="habilitar_ecommerce" class="form-control">
              <option value="0" <?= $produto->habilitar_ecommerce == 0 ? "selected" : "" ?>>Não</option>
              <option value="1" <?= $produto->habilitar_ecommerce == 1 ? "selected" : "" ?>>Sim</option>
            </select>
          </div>
        </div>

        <br><br>
        <div class="row" id="mostrar_caracteristica">
          <div class="col-md-12">
            <label>Características do produto</label>
            <textarea name="caracteristicas" id="caracteristicas" cols="30" rows="5" class="form-control"><?= $produto->caracteristicas ?></textarea>
          </div>
        </div>
      </div>

      <?php if (!@$_SESSION["subusuario"] || $permissao->editar == 1) : ?>
        <div class="card card-body">
          <div class="row">
            <div class="col-6">
              <button class="btn btn-primary"><i class="fas fa-check"></i> Atualizar</button>
            </div>
          </div>
        </div>
      <?php endif ?>

    </form>
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
    <div class="mensagens"><a href="https://octapay.com.br/tutoriais/editar-produto/" target="_blank">Dúvidas sobre a Edição de Produto</a></div>
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

  $("#rascunho").click(() => {
    console.log($("#rascunho").val());
  })

  /*$("#editar").click(function(e) {
      e.preventDefault();
      salvarEditar(0);
  });*/

  function salvarEditar(salvar) {
    if (salvar == 1) {
      url = "<?= $router->route("produtos.cadastro"); ?>";
    } else {
      url = "<?= $router->route("produtos.editar"); ?>";
    }

    var form = $("form");

    $.ajax({

      url: url,
      data: form.serialize(),
      type: "POST",
      datatype: "json",
      beforeSend: function() {
        console.log("atualizando");
      },
      error: function() {
        console.log("O servidor não conseguiu processar o pedido");
      },
      success: function(callback) {
        //callback = JSON.parse(callback);
        console.log("callback ", callback);
        alert(callback.msg);

        if (callback.msg == "Atualizado Com Sucesso!") {
          location.href = "<?= URL_BASE . '/produtos/detalhe/' ?>" + callback.id;
        }
      }

    });
  }

  $("#preco").blur(function(e) {
    var valor = $("#preco").val();
    $("#preco").val(valor.replace(",", "."));

    if (valor < 5) {
      $("#preco").focus();
      alert("Valor do produto abaixo do mínimo permitido. Valor mínimo R$5,00");
    }
  });

  function tipo() {
    var tipo = $("#tipo_produto").val();

    if (tipo == 11) {
      $("#area-membros").show();
    } else {
      $("#area-membros").hide();
    }
  }

  tipo();

  $("#tipo_produto").change(() => {
    tipo();
  });

  function mostrarCaracteristica(){
    var ecommerce = $("#habilitar_ecommerce").val();

    if(ecommerce == 0){
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