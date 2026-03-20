<!-- <aside class="main-sidebar sidebar-dark-primary elevation-4" style="background: #333366">  -->
<!-- <aside class="main-sidebar sidebar-dark-primary elevation-4" style="background-image: linear-gradient(to bottom, #0b0024, #b73234, #b73234)"> -->

<style>
/* Importação das fontes */
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');
@import url('https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@24,400,0,0');

/* -------- Animação Glow -------- */
@keyframes glow {
  0% { text-shadow: 0 0 5px rgba(217, 192, 0, 0.5); }
  50% { text-shadow: 0 0 15px rgba(217, 192, 0, 0.9); }
  100% { text-shadow: 0 0 5px rgba(217, 192, 0, 0.5); }
}

/* -------- Brand/Logo -------- */
.brand-logo {
  font-size: 26px;
  font-weight: bold;
  text-align: center;
  color: white;
  text-transform: uppercase;
  font-family: 'Inter', sans-serif;
  letter-spacing: 2px;
  transition: opacity 0.3s ease-in-out;
  position: relative;
  display: inline-block;
}

.brand-logo .highlight {
  color: rgb(217, 192, 0);
  animation: glow 1.5s infinite alternate;
}

.brand-logo .underline {
  width: 45px;
  height: 2px;
  background: linear-gradient(90deg, rgb(217, 192, 0), #f4d03f);
  position: absolute;
  bottom: -5px;
  left: 75px;
  border-radius: 1px;
}

/* -------- Sidebar -------- */
.main-sidebar {
  width: 256px;
  height: 100vh;
  background-color: #111;
  color: white;
  position: fixed;
  top: 0;
  left: 0;
  display: flex;
  flex-direction: column;
  box-shadow: 2px 0 10px rgba(0, 0, 0, 0.6);
  transition: all 0.3s ease;
}

/* -------- Itens de navegação -------- */
.nav-sidebar .nav-link {
  display: flex;
  align-items: center;
  color: rgba(255, 255, 255, 0.85);
  text-decoration: none;
  font-size: 15px;
  padding: 10px;
  border-radius: 8px;
  border: 1px solid transparent;
  transition: all 0.3s ease;
}

.nav-sidebar .nav-link:hover {
  background: linear-gradient(135deg, rgba(217, 192, 0, 0.1), rgba(244, 180, 63, 0.1));
  color: #f4b400;
  border-color: rgba(217, 192, 0, 0.2);
  transform: translateX(4px);
  box-shadow: 0 2px 8px rgba(217, 192, 0, 0.15);
}

.nav-sidebar .nav-link.active {
  background: linear-gradient(135deg, rgb(217, 192, 0), #f4d03f);
  color: #111;
  font-weight: 600;
  box-shadow: 0 4px 12px rgba(217, 192, 0, 0.3);
  border-color: #f4d03f;
}

/* -------- Ícones -------- */
.material-symbols-rounded {
  font-family: 'Material Symbols Rounded';
  font-weight: normal;
  font-style: normal;
  font-size: 20px;
  line-height: 1;
  letter-spacing: normal;
  text-transform: none;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  vertical-align: middle;
  -webkit-font-feature-settings: 'liga';
  -webkit-font-smoothing: antialiased;
  font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
}

.nav-sidebar .nav-link .material-symbols-rounded {
  font-size: 20px;
  margin-right: 12px;
  color: rgb(217, 192, 0);
  transition: all 0.3s ease;
}

.nav-sidebar .nav-link:hover .material-symbols-rounded {
  color: #f4d03f;
  transform: scale(1.1);
}

.nav-sidebar .nav-link.active .material-symbols-rounded {
  color: #111;
}

/* -------- Texto dos links -------- */
.nav-sidebar .nav-link p {
  margin: 0;
  flex: 1;
  font-weight: 500;
}

/* -------- Submenus -------- */
.nav-treeview {
  padding: 0.5rem 0;
  background: rgba(0, 0, 0, 0.2);
  border-radius: 8px;
  margin-left: 1rem;
  border-left: 2px solid rgba(217, 192, 0, 0.3);
}

.sub-item .nav-link {
  padding: 0.5rem 1rem;
  font-size: 13px;
  color: rgba(255, 255, 255, 0.75);
}

.sub-item .nav-link:hover {
  background: rgba(217, 192, 0, 0.1);
  color: #f4d03f;
  transform: translateX(8px);
}

.sub-item .nav-link.active {
  background: linear-gradient(135deg, rgba(217, 192, 0, 0.2), rgba(244, 180, 63, 0.2));
  color: #f4d03f;
  border-left: 3px solid #f4d03f;
}

/* -------- Estado Colapsado (mantém lógica original) -------- */
.sidebar-mini.sidebar-collapse .nav-sidebar .nav-link {
  justify-content: flex-end !important;
  padding: 10px 0 !important;
}

.sidebar-mini.sidebar-collapse .nav-sidebar .nav-link .material-symbols-rounded {
  margin-right: 0 !important;
  font-size: 24px !important;
}

.sidebar-mini.sidebar-collapse .nav-sidebar .nav-link p {
  display: none !important;
}

.sidebar-mini.sidebar-collapse .main-sidebar:hover {
  width: 250px !important;
}

.sidebar-mini.sidebar-collapse .main-sidebar:hover .nav-sidebar .nav-link {
  justify-content: flex-start !important;
  padding: 10px !important;
}

.sidebar-mini.sidebar-collapse .main-sidebar:hover .nav-sidebar .nav-link .material-symbols-rounded {
  margin-right: 10px !important;
}

.sidebar-mini.sidebar-collapse .main-sidebar:hover .nav-sidebar .nav-link p {
  display: inline !important;
}

.sidebar-mini.sidebar-collapse .main-sidebar:not(:hover) {
  width: 80px !important;
}

/* -------- Mantém efeito no logo -------- */
.sidebar-mini.sidebar-collapse .brand-logo {
  opacity: 0;
  transition: opacity 0.2s ease-in-out;
}

.sidebar-mini.sidebar-collapse .main-sidebar:hover .brand-logo {
  opacity: 1;
}

/* -------- Ativo no aside -------- */
#aside .nav-link.active {
  background: linear-gradient(135deg, rgb(217, 192, 0), #f4d03f) !important;
  color: #111 !important;
  font-weight: bold;
}


/* 🔥 Tudo acima mantém a lógica de colapsar igual ao seu original. Só alterou cor, hover, ícones e animações. */



</style>



<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@24,400,0,0" rel="stylesheet">

<aside class="main-sidebar sidebar-dark-primary elevation-4" style="position: fixed; background-color: rgb(0, 0, 0);">
  <?php

  use Source\Models\Usuarios\Usuario;
  use Source\Models\Permissao\Section;

  $section = new Section();
  if ($section->getTipo() != 6) :
  ?>
   <div class="brand-logo" style="margin-bottom: 20px;">
    <span class="win-text">WIN</span><span class="highlight">PAY</span>
    <div class="underline"></div>
</div>

  <?php elseif ($section->getTipo() == 6) : ?>
    <div align="center"><img id="logo-menu" src="<?= url("themes/imgs/logos/logo-icon.png") ?>" style="width:145px; margin-top:10px; margin-left:-20px"></div>
  <?php endif ?>

  <div class="sidebar">
    <nav class="mt-2">
      <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false" style="font-family:inter">
        <?php if ($_SESSION["tipo"] != 6) : ?>

          <?php if (!@$_SESSION["subusuario"] || $section->permissaoModuloBool("inicio")) : ?>
            <li class="nav-item">
              <a href="<?= url() ?>" class="nav-link txt-branco-octa" style="text-shadow:#343a40">
                <!-- <a href="<?= url() ?>" class="nav-link txt-azul-dark" style="color:#fff"> -->
                <span class="material-symbols-rounded">home</span>
                <p>Início </p>
              </a>
            </li>
          <?php endif ?>

          <?php if (!@$_SESSION["subusuario"] || $section->permissaoModuloBool("marketplace")) : ?>
            <li class="nav-item" title="Veja produtos para poder se afiliar a eles">
              <a href="<?= url('marketplace') ?>" class="nav-link txt-branco-octa" style="text-shadow:#343a40">
                <span class="material-symbols-rounded">storefront</span>
                <p>Marketplace</p>
              </a>
            </li>
          <?php endif ?>
        <?php endif ?>

        <?php if ($section->getTipo() == 6) : ?>
          <li class="nav-item" title="Veja suas compras">
            <a href="<?= url('vendas') ?>" class="nav-link txt-branco-octa" style="text-shadow:#343a40">
              <span class="material-symbols-rounded nav-icon">check_circle</span>
              <p> Minhas Compras </p>
            </a>
          </li>

          <li class="nav-item" title="Solicitar reembolso de uma compra">
            <a href="<?= url('pagamentos/solicitar-reembolso') ?>" class="nav-link txt-branco-octa" style="text-shadow:#343a40">
              <span class="material-symbols-rounded nav-icon">swap_horizontal_circle</span>
              <p> Solicitar Reembolso </p>
            </a>
          </li>

          <li class="nav-item" title="Veja e edite seus dados pessoais">
            <a href="<?= url('usuarios/perfil/' . $_SESSION["id"]) ?>" class="nav-link txt-branco-octa" style="text-shadow:#343a40">
              <span class="material-symbols-rounded nav-icon">person</span>
              <p> Meus dados </p>
            </a>
          </li>
        <?php endif ?>

        <?php if ($section->getTipo() != 6) : ?>
          <?php if (!@$_SESSION["subusuario"] || $section->permissaoModuloBool("venda_menu")) : ?>

            <?php
            $url_atual = HTTP . "$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
            $url = explode("?", $url_atual, 2);
            if (
              $url[0] == url("vendas") ||
              $url[0] == url("vendas?pagamento=2&status=1") ||
              $url[0] == url("vendas?pagamento=2&status=5") ||
              $url[0] == url("vendas?status=4") ||
              $url[0] == url("vendas?status=4&order=data_finalizada") ||
              $url[0] == url("vendas/coprodutor") ||
              $url[0] == url("abandono")
            ) :
            ?>
              <li class='nav-item menu-open' title='Veja suas vendas'>
                <a href='#' class='nav-link active'>
                <?php else : ?>
              <li class='nav-item' title='Veja suas vendas'>
                <a href='#' class='nav-link'>
                <?php endif; ?>

                <span class="material-symbols-rounded">shopping_bag</span>
                <p class="txt-branco-octa" style="text-shadow:#343a40">
                  Vendas
                  <span class="material-symbols-rounded right">chevron_left</span>
                </p>
                </a>

                <ul class="nav nav-treeview">
                  <?php if (!@$_SESSION["subusuario"] || $section->permissaoModuloBool("vendas")) : ?>
                    <li class="nav-item sub-item" title="Veja suas vendas">
                      <a href="<?= url('vendas') ?>" class="nav-link txt-branco-octa" style="text-shadow:#343a40">
                        <span class="material-symbols-rounded nav-icon">check_circle</span>
                        <p>Todas As Transações</p>
                      </a>
                    </li>
                  <?php endif ?>

                  <?php if (!@$_SESSION["subusuario"] || $section->permissaoModuloBool("vendas")) : ?>
                    <li class="nav-item sub-item" title="Veja suas vendas">
                      <a href="<?= url('vendas?status=4&order=data_finalizada') ?>" class="nav-link txt-branco-octa" style="text-shadow:#343a40">
                        <span class="material-symbols-rounded nav-icon">check_circle</span>
                        <p>Vendas Realizadas</p>
                      </a>
                    </li>
                  <?php endif ?>

                  <?php if (!@$_SESSION["subusuario"] || $section->permissaoModuloBool("vendas")) : ?>
                    <li class="nav-item sub-item" title="Veja suas vendas">
                      <a href="<?= url('vendas?pagamento=2&status=1') ?>" class="nav-link txt-branco-octa" style="text-shadow:#343a40">
                        <span class="material-symbols-rounded nav-icon">check_circle</span>
                        <p>Boletos Gerados</p>
                      </a>
                    </li>
                  <?php endif ?>
                  <?php if (!@$_SESSION["subusuario"] || $section->permissaoModuloBool("vendas")) : ?>
                    <li class="nav-item sub-item" title="Veja suas vendas">
                      <a href="<?= url('vendas?pagamento=2&status=5') ?>" class="nav-link txt-branco-octa" style="text-shadow:#343a40">
                        <span class="material-symbols-rounded nav-icon">check_circle</span>
                        <p>Boletos Cancelados</p>
                      </a>
                    </li>
                  <?php endif ?>

                  <li class="nav-item sub-item" title="Solicitações de reembolso / chargeback">
                    <a href="<?= url('reembolsos') ?>" class="nav-link txt-branco-octa" style="text-shadow:#343a40">
                      <span class="material-symbols-rounded nav-icon">swap_horizontal_circle</span>
                      <p>Reembolsos &amp; Chargeback</p>
                    </a>
                  </li>

                  <?php if (!@$_SESSION["subusuario"] || $section->permissaoModuloBool("vendas_coprodutor")) : ?>
                    <li class="nav-item sub-item" title="Veja suas vendas">
                      <a href="<?= url('vendas/coprodutor') ?>" class="nav-link txt-branco-octa" style="text-shadow:#343a40">
                        <span class="material-symbols-rounded nav-icon">verified</span>
                        <p>Vendas Coprodutor</p>
                      </a>
                    </li>
                  <?php endif ?>

                  <?php if (!@$_SESSION["subusuario"] || $section->permissaoModuloBool("abandono_checkout")) : ?>
                    <li class="nav-item sub-item" title="Veja tentativas de compras do seu produto" style="text-shadow:#343a40">
                      <a href="<?= url('abandono') ?>" class="nav-link txt-branco-octa">
                        <span class="material-symbols-rounded nav-icon">shopping_cart_checkout</span>
                        <p>Abandono de checkout</p>
                      </a>
                    </li>
                  <?php endif ?>

                  <?php if (!@$_SESSION["subusuario"] || $section->permissaoModuloBool("minhas_compras")) : ?>
                    <li class="nav-item sub-item" title="Veja suas compras">
                      <a href="<?= url('vendas?minhas=true') ?>" class="nav-link txt-branco-octa" style="text-shadow:#343a40">
                        <span class="material-symbols-rounded nav-icon">add_shopping_cart</span>
                        <p>Minhas Compras</p>
                      </a>
                    </li>
                  <?php endif ?>
                </ul>
              </li>
            <?php endif ?>
          <?php endif ?>

          <?php if ($section->getTipo() != 6) : ?>
            <?php if (!@$_SESSION["subusuario"] || $section->permissaoModuloBool("produto_menu")) : ?>
              <?php $url_atual = HTTP . "$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
              if (
                $url_atual == url("produtos") ||
                $url_atual == url("produtos/promovidos") ||
                $url_atual == url("produtos/novo")
              ) {
                echo "<li class='nav-item menu-open' title='Cadastre e veja seus produtos'>";
                echo "<a href='#' class='nav-link active'>";
              } else {
                echo "<li class='nav-item' title='Cadastre e veja seus produtos'>";
                echo "<a href='#' class='nav-link'>";
              }
              ?>

              <span class="material-symbols-rounded">inventory_2</span>
              <p class="txt-branco-octa" style="text-shadow:#343a40">
                Produtos
                <span class="material-symbols-rounded right">chevron_left</span>
              </p>
              </a>
              <ul class="nav nav-treeview">
                <?php if (!@$_SESSION["subusuario"] || $section->permissaoModuloBool("novo_produto")) : ?>
                  <li class="nav-item sub-item" title="Cadastre um novo produto">
                    <a href="<?= url('produtos/novo') ?>" class="nav-link txt-branco-octa" style="text-shadow:#343a40">
                      <span class="material-symbols-rounded nav-icon">library_add</span>
                      <p>Novo produto</p>
                    </a>
                  </li>
                <?php endif ?>

                <?php if (!@$_SESSION["subusuario"] || $section->permissaoModuloBool("meus_produtos")) : ?>
                  <li class="nav-item sub-item" title="Veja os produtos cadastrados por você">
                    <a href="<?= url('produtos') ?>" class="nav-link txt-branco-octa" style="text-shadow:#343a40">
                      <span class="material-symbols-rounded nav-icon">view_list</span>
                      <p>Meus Produtos</p>
                    </a>
                  </li>
                <?php endif ?>

                <?php if (!@$_SESSION["subusuario"] || $section->permissaoModuloBool("produtos_promovidos")) : ?>
                  <li class="nav-item sub-item" title="Veja os produtos que você promove">
                    <a href="<?= url('produtos/promovidos') ?>" class="nav-link txt-branco-octa" style="text-shadow:#343a40">
                      <span class="material-symbols-rounded nav-icon">savings</span>
                      <p>Promovidos</p>
                    </a>
                  </li>
                <?php endif ?>

                <?php if (!@$_SESSION["subusuario"] || $section->permissaoModuloBool("minhas_coproducoes")) : ?>
                  <li class="nav-item sub-item" title="Veja os produtos que você promove">
                    <a href="<?= url('produtos/coproducoes') ?>" class="nav-link txt-branco-octa" style="text-shadow:#343a40">
                      <span class="material-symbols-rounded nav-icon">handshake</span>
                      <p>Minhas Coproduções</p>
                    </a>
                  </li>
                <?php endif ?>
              </ul>
              </li>
            <?php endif ?>

            <?php if (!@$_SESSION["subusuario"] || $section->permissaoModuloBool("assinaturas")) : ?>
              <li class="nav-item">
                <a href="<?= url("assinaturas") ?>" class="nav-link txt-branco-octa" style="text-shadow:#343a40">
                  <span class="material-symbols-rounded">subscriptions</span>
                  <p> Assinaturas</p>
                </a>
              </li>
            <?php endif ?>

            <?php if (!@$_SESSION["subusuario"] || $section->permissaoModuloBool("comissao_menu")) : ?>
              <?php $url_atual = HTTP . "$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
              if (
                $url_atual == url("comissao") ||
                $url_atual == url("comissao/redrocket") ||
                $url_atual == url("banco") ||
                $url_atual == url("saques") ||
                $url_atual == url('antecipacao')
              ) {
                echo "<li class='nav-item menu-open' title='Veja suas comissões, saques e antecipações'>";
                echo "<a href='#' class='nav-link active'>";
              } else {
                echo "<li class='nav-item' title='Veja suas comissões, saques e antecipações e cadastre sua conta bancária'>";
                echo "<a href='#' class='nav-link'>";
              }
              ?>
              <span class="material-symbols-rounded">payments</span>
              <p class="txt-branco-octa" style="text-shadow:#343a40">
                Comissões e Saques
                <span class="material-symbols-rounded right">chevron_left</span>
              </p>
              </a>
              <ul class="nav nav-treeview">
                <?php if (!@$_SESSION["subusuario"] || $section->permissaoModuloBool("saques_extratos")) : ?>
                  <li class="nav-item sub-item" title="Veja suas comissões e solicite saques">
                    <a href="<?= url('comissao') ?>" class="nav-link txt-branco-octa" style="text-shadow:#343a40">
                      <span class="material-symbols-rounded nav-icon">insights</span>
                      <p>Saque e Extratos</p>
                    </a>
                  </li>
                <?php endif ?>
                <?php if ($_SESSION["tipo"] == 1 || $_SESSION["tipo"] == 2) : ?>
                  <li class="nav-item sub-item" title="Comissões da REDROCKET">
                    <a href="<?= url('comissao/redrocket') ?>" class="nav-link txt-branco-octa" style="text-shadow:#343a40">
                      <span class="material-symbols-rounded nav-icon">workspace_premium</span>
                      <p>Comissão WINPAY</p>
                    </a>
                  </li>
                <?php endif ?>

                <?php if (!@$_SESSION["subusuario"] || $section->permissaoModuloBool("conta_bancaria")) : ?>
                  <li class="nav-item sub-item" title="Cadastre contas bancárias">
                    <a href="<?= url('banco') ?>" class="nav-link txt-branco-octa" style="text-shadow:#343a40">
                      <span class="material-symbols-rounded nav-icon">savings</span>
                      <p>Contas Bancárias</p>
                    </a>
                  </li>
                <?php endif ?>

                <?php if (!@$_SESSION["subusuario"] || $section->permissaoModuloBool("saques_solicitados")) : ?>
                  <li class="nav-item sub-item" title="Histórico de saques e a situação dos mesmos">
                    <a href="<?= url('saques') ?>" class="nav-link txt-branco-octa" style="text-shadow:#343a40">
                      <span class="material-symbols-rounded nav-icon">request_quote</span>
                      <p>Saques Solicitados</p>
                    </a>
                  </li>
                <?php endif ?>

                <?php if (!@$_SESSION["subusuario"] || $section->permissaoModuloBool("antecipacao")) : ?>
                  <li class="nav-item sub-item" title="Veja se possiu valor liberado para antecipar">
                    <a href="<?= url('antecipacao') ?>" class="nav-link txt-branco-octa" style="text-shadow:#343a40">
                      <span class="material-symbols-rounded nav-icon">account_balance_wallet</span>
                      <p>Antecipação</p>
                    </a>
                  </li>
                <?php endif ?>
              </ul>
              </li>
            <?php endif ?>

            <?php if ($_SESSION["tipo"] == 1 || $_SESSION["tipo"] == 2) : ?>
              <?php $url_atual = HTTP . "$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
              if (
                $url_atual == url('funcionarios') ||
                $url_atual == url('produtor') ||
                $url_atual == url('afiliados') ||
                $url_atual == url('taxas') ||
                $url_atual == url('gateway')
              ) {
                echo "<li class='nav-item menu-open' title='Configuraçoes de funcionários, produtores, afiliados e as taxas da plataforma'>";
                echo "<a href='#' class='nav-link active'>";
              } else {
                echo "<li class='nav-item' title='Configuraçoes de funcionários, produtores, afiliados e as taxas da plataforma'>";
                echo "<a href='#' class='nav-link'>";
              }
              ?>

              <span class="material-symbols-rounded">settings</span>
              <p class="txt-branco-octa" style="text-shadow:#343a40">
                Configurações
                <span class="material-symbols-rounded right">chevron_left</span>
              </p>
              </a>
              <ul class="nav nav-treeview">
                <li class="nav-item sub-item" title="Cadastro e configuração de funcionários">
                  <a href="<?= url("funcionarios") ?>" class="nav-link txt-branco-octa" style="text-shadow:#343a40">
                    <span class="material-symbols-rounded nav-icon">badge</span>
                    <p>Funcionários</p>
                  </a>
                </li>

                <li class="nav-item sub-item" title="Configurações de produtores">
                  <a href="<?= url("usuarios?tipo=4") ?>" class="nav-link txt-branco-octa" style="text-shadow:#343a40">
                    <span class="material-symbols-rounded nav-icon">groups</span>
                    <p>Produtores</p>
                  </a>
                </li>

                <li class="nav-item sub-item" title="Configurações de afiliados">
                  <a href="<?= url("usuarios?tipo=5") ?>" class="nav-link txt-branco-octa" style="text-shadow:#343a40">
                    <span class="material-symbols-rounded nav-icon">contact_page</span>
                    <p>Afiliados</p>
                  </a>
                </li>

                <li class="nav-item sub-item" title="Configurações das taxas da plataforma">
                  <a href="<?= url("taxas") ?>" class="nav-link txt-branco-octa" style="text-shadow:#343a40">
                    <span class="material-symbols-rounded nav-icon">settings_suggest</span>
                    <p>Taxas</p>
                  </a>
                </li>

                <?php if ($_SESSION["tipo"] == 1) : ?>
                <li class="nav-item sub-item" title="Configuração do gateway de pagamento">
                  <a href="<?= url("gateway") ?>" class="nav-link txt-branco-octa" style="text-shadow:#343a40">
                    <span class="material-symbols-rounded nav-icon">payment</span>
                    <p>Gateway</p>
                  </a>
                </li>
                <?php endif; ?>

              </ul>
              </li>
            <?php endif; ?>

            <?php if (!@$_SESSION["subusuario"] || $section->permissaoModuloBool("ferramenta_menu")) : ?>
              <?php $url_atual = HTTP . "$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
              if (
                $url_atual == url("links-uteis") ||
                $url_atual == url("emails") ||
                $url_atual == url("integracoes") ||
                $url_atual == url("api") ||
                $url_atual == url("notas") ||
                $url_atual == url("bling") ||
                $url_atual == url("notas/integracao") ||
                $url_atual == url("pixel") ||
                $url_atual == url("webhooks") ||
                $url_atual == url("postbacks")
              ) {
                echo "<li class='nav-item menu-open'>";
                echo "<a href='#' class='nav-link active'>";
              } else {
                echo "<li class='nav-item'>";
                echo "<a href='#' class='nav-link'>";
              }
              ?>

              <span class="material-symbols-rounded">handyman</span>
              <p class="txt-branco-octa" style="text-shadow:#343a40">
                Ferramentas
                <span class="material-symbols-rounded right">chevron_left</span>
              </p>
              </a>

              <ul class="nav nav-treeview">
                <?php if (!@$_SESSION["subusuario"] || $section->permissaoModuloBool("integracoes")) : ?>
                  <li class="nav-item sub-item" title="">
                    <a href="<?= url("emails") ?>" class="nav-link txt-branco-octa" style="text-shadow:#343a40"><span class="material-symbols-rounded nav-icon">mail</span>
                      <p>Envio de Emails</p>
                    </a>
                  </li>
                  <li class="nav-item sub-item">
                    <a href="<?= url("integracoes") ?>" class="nav-link txt-branco-octa"><span class="material-symbols-rounded nav-icon">hub</span>
                      <p>Integrações</p>
                    </a>
                  </li>
                <?php endif ?>

                <?php if (!@$_SESSION["subusuario"] || $section->permissaoModuloBool("postback")) : ?>
                  <li class="nav-item sub-item" title="Gerenciar Postbacks">
                    <a href="<?= url("postbacks") ?>" class="nav-link txt-branco-octa" style="text-shadow:#343a40"><span class="material-symbols-rounded nav-icon">sync_alt</span>
                      <p>Postback</p>
                    </a>
                  </li>
                <?php endif ?>

                <?php if (!@$_SESSION["subusuario"] || $section->permissaoModuloBool("pixel")) : ?>
                  <li class="nav-item sub-item" title="Cadastro de pixel">
                    <a href="<?= url("pixel") ?>" class="nav-link txt-branco-octa" style="text-shadow:#343a40"><span class="material-symbols-rounded nav-icon">blur_on</span>
                      <p>Pixel</p>
                    </a>
                  </li>
                <?php endif ?>

                <?php if (!@$_SESSION["subusuario"] || $section->permissaoModuloBool("api")) : ?>
                  <li class="nav-item sub-item" title="Gerar API">
                    <a href="<?= url("api") ?>" class="nav-link txt-branco-octa" style="text-shadow:#343a40"><span class="material-symbols-rounded nav-icon">api</span>
                      <p>Api</p>
                    </a>
                  </li>
                <?php endif ?>

                <?php if (!@$_SESSION["subusuario"] || $section->permissaoModuloBool("notas_fiscais")) : ?>
                  <li class="nav-item sub-item" title="Ver notas fiscais">
                    <a href="<?= url("notas") ?>" class="nav-link txt-branco-octa" style="text-shadow:#343a40"><span class="material-symbols-rounded nav-icon">description</span>
                      <p>Notas Fiscais</p>
                    </a>
                  </li>
                <?php endif ?>

                <?php if ($_SESSION["tipo"] == 1) : ?>
                  <li class="nav-item sub-item" title="Links para verificação de processos internos">
                    <a href="<?= url("links-uteis") ?>" class="nav-link txt-branco-octa" style="text-shadow:#343a40"><span class="material-symbols-rounded nav-icon">link</span>
                      <p>Links úteis</p>
                    </a>
                  </li>

                  <li class="nav-item sub-item" title="">
                    <a href="<?= url("login-master") ?>" class="nav-link txt-branco-octa" style="text-shadow:#343a40"><span class="material-symbols-rounded nav-icon">admin_panel_settings</span>
                      <p>Login Master</p>
                    </a>
                  </li>
                <?php endif; ?>

              </ul>
              </li>
            <?php endif ?>

            <?php if (!@$_SESSION["subusuario"] || $section->permissaoModuloBool("relatorio_menu")) : ?>
              <?php $url_atual = HTTP . "$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
              if (
                $url_atual == url('relatorios') ||
                $url_atual == url('relatorios/afiliados') ||
                $url_atual == url('relatorios/performance-afiliados')
              ) {
                echo "<li class='nav-item menu-open' title='relatórios geral, relatórios afiliados'>";
                echo "<a href='#' class='nav-link active'>";
              } else {
                echo "<li class='nav-item' title='relatórios geral, relatórios afiliados'>";
                echo "<a href='#' class='nav-link'>";
              }
              ?>

              <span class="material-symbols-rounded">query_stats</span>
              <p class="txt-branco-octa" style="text-shadow:#343a40">
                Relatórios
                <span class="material-symbols-rounded right">chevron_left</span>
              </p>
              </a>

              <ul class="nav nav-treeview">
                <?php if (!@$_SESSION["subusuario"] || $section->permissaoModuloBool("analitico_vendas")) : ?>
                  <li class="nav-item sub-item">
                    <a href="<?= url("relatorios") ?>" class="nav-link txt-branco-octa" style="text-shadow:#343a40">
                      <span class="material-symbols-rounded nav-icon">query_stats</span>
                      <p>Analíticos de Vendas</p>
                    </a>
                  </li>
                <?php endif ?>

                <?php if (!@$_SESSION["subusuario"] || $section->permissaoModuloBool("vendas_afiliados")) : ?>
                  <li class="nav-item sub-item" title="Performance Afiliados">
                    <a href="<?= url("relatorios/afiliados") ?>" class="nav-link txt-branco-octa" style="text-shadow:#343a40">
                      <span class="material-symbols-rounded nav-icon">paid</span>
                      <p>Vendas Afiliados</p>
                    </a>
                  </li>
                <?php endif ?>

                <?php if (!@$_SESSION["subusuario"] || $section->permissaoModuloBool("performance_afiliados")) : ?>
                  <li class="nav-item sub-item" title="Performance Afiliados">
                    <a href="<?= url("relatorios/performance-afiliados") ?>" class="nav-link txt-branco-octa" style="text-shadow:#343a40">
                      <span class="material-symbols-rounded nav-icon">diversity_3</span>
                      <p>Performance Afiliados</p>
                    </a>
                  </li>
                <?php endif ?>
              </ul>
            <?php endif; ?>
          <?php endif ?>
          
          <?php if ($_SESSION["tipo"] == 1) : ?>
            <?php $url_atual = HTTP . "$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
            if (
              $url_atual == url('relatorios/painel') ||
              $url_atual == url('relatorios/painel_clientes') ||
              $url_atual == url('relatorios/painel-nf')
            ) {
              echo "<li class='nav-item menu-open' title='painel REDROCKET, painel clientes, painel nf'>";
              echo "<a href='#' class='nav-link active'>";
            } else {
              echo "<li class='nav-item' title='relatórios REDROCKET, relatórios geral, painel nf'>";
              echo "<a href='#' class='nav-link'>";
            }
            ?>

            <span class="material-symbols-rounded">space_dashboard</span>
            <p class="txt-branco-octa" style="text-shadow:#343a40">
              Painel WINPAY
              <span class="material-symbols-rounded right">chevron_left</span>
            </p>
            </a>

            <ul class="nav nav-treeview">
              <li class="nav-item sub-item">
                <a href="<?= url("relatorios/painel") ?>" class="nav-link txt-branco-octa" style="text-shadow:#343a40">
                  <span class="material-symbols-rounded nav-icon">query_stats</span>
                  <p>Relatórios REDROCKET</p>
                </a>
              </li>

              <li class="nav-item sub-item" title="Painel Clientes">
                <a href="<?= url("relatorios/painel_clientes") ?>" class="nav-link txt-branco-octa" style="text-shadow:#343a40">
                  <span class="material-symbols-rounded nav-icon">dashboard</span>
                  <p>Relatórios Clientes</p>
                </a>
              </li>

              <li class="nav-item sub-item" title="Painel REDROCKET NF">
                <a href="<?= url("relatorios/painel-nf") ?>" class="nav-link txt-branco-octa" style="text-shadow:#343a40">
                  <span class="material-symbols-rounded nav-icon">receipt_long</span>
                  <p>Painel REDROCKET NF</p>
                </a>
              </li>
            </ul>
          <?php endif; ?>
    </nav>

  </div>
</aside>
