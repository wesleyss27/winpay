<!-- <aside class="main-sidebar sidebar-dark-primary elevation-4" style="background: #333366">  -->
<!-- <aside class="main-sidebar sidebar-dark-primary elevation-4" style="background-image: linear-gradient(to bottom, #0b0024, #b73234, #b73234)"> -->

<style>
  #aside .nav-link.active {
    background: #ddd !important;
  }
</style>
<aside class="main-sidebar sidebar-dark-primary elevation-4" style="position: fixed; background-color: rgb(0, 0, 0);">
  <?php

  use Source\Models\Usuarios\Usuario;
  use Source\Models\Permissao\Section;

  $section = new Section();

  if ($section->getTipo() != 6) :
  ?>
    <a href="<?= url() ?>" class="brand-link" style="border:0">
      <div align="center"><img id="logo-menu" src="<?= url("themes/imgs/logos/logo4.png") ?>" style="width:145px; margin-top:10px; margin-left:-20px"></div>
    </a>
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
                <i class="nav-icon fas fa-th"></i>
                <p>Início </p>
              </a>
            </li>
          <?php endif ?>

          <?php if (!@$_SESSION["subusuario"] || $section->permissaoModuloBool("marketplace")) : ?>
            <li class="nav-item" title="Veja produtos para poder se afiliar a eles">
              <a href="<?= url('marketplace') ?>" class="nav-link txt-branco-octa" style="text-shadow:#343a40">
                <i class="nav-icon fas fa-shopping-cart"></i>
                <p>Marketplace</p>
              </a>
            </li>
          <?php endif ?>
        <?php endif ?>

        <?php if ($section->getTipo() == 6) : ?>
          <li class="nav-item" title="Veja suas compras">
            <a href="<?= url('vendas') ?>" class="nav-link txt-branco-octa" style="text-shadow:#343a40">
              <i class="nav-icon fas fa-check"></i>
              <p> Minhas Compras </p>
            </a>
          </li>

          <li class="nav-item" title="Veja e edite seus dados pessoais">
            <a href="<?= url('usuarios/perfil/' . $_SESSION["id"]) ?>" class="nav-link txt-branco-octa" style="text-shadow:#343a40">
              <i class="nav-icon fas fa-user"></i>
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

                <i class="nav-icon fas fa-check-circle txt-branco-octa"></i>
                <p class="txt-branco-octa" style="text-shadow:#343a40">
                  Vendas
                  <i class="right fas fa-angle-left"></i>
                </p>
                </a>

                <ul class="nav nav-treeview">
                  <?php if (!@$_SESSION["subusuario"] || $section->permissaoModuloBool("vendas")) : ?>
                    <li class="nav-item sub-item" title="Veja suas vendas">
                      <a href="<?= url('vendas') ?>" class="nav-link txt-branco-octa" style="text-shadow:#343a40">
                        <i class="fas fa-check nav-icon"></i>
                        <p>Todas As Transações</p>
                      </a>
                    </li>
                  <?php endif ?>

                  <?php if (!@$_SESSION["subusuario"] || $section->permissaoModuloBool("vendas")) : ?>
                    <li class="nav-item sub-item" title="Veja suas vendas">
                      <a href="<?= url('vendas?status=4&order=data_finalizada') ?>" class="nav-link txt-branco-octa" style="text-shadow:#343a40">
                        <i class="fas fa-check nav-icon"></i>
                        <p>Vendas Realizadas</p>
                      </a>
                    </li>
                  <?php endif ?>

                  <?php if (!@$_SESSION["subusuario"] || $section->permissaoModuloBool("vendas")) : ?>
                    <li class="nav-item sub-item" title="Veja suas vendas">
                      <a href="<?= url('vendas?pagamento=2&status=1') ?>" class="nav-link txt-branco-octa" style="text-shadow:#343a40">
                        <i class="fas fa-check nav-icon"></i>
                        <p>Boletos Gerados</p>
                      </a>
                    </li>
                  <?php endif ?>
                  <?php if (!@$_SESSION["subusuario"] || $section->permissaoModuloBool("vendas")) : ?>
                    <li class="nav-item sub-item" title="Veja suas vendas">
                      <a href="<?= url('vendas?pagamento=2&status=5') ?>" class="nav-link txt-branco-octa" style="text-shadow:#343a40">
                        <i class="fas fa-check nav-icon"></i>
                        <p>Boletos Cancelados</p>
                      </a>
                    </li>
                  <?php endif ?>

                  <?php if (!@$_SESSION["subusuario"] || $section->permissaoModuloBool("vendas_coprodutor")) : ?>
                    <li class="nav-item sub-item" title="Veja suas vendas">
                      <a href="<?= url('vendas/coprodutor') ?>" class="nav-link txt-branco-octa" style="text-shadow:#343a40">
                        <i class="far fa-check-circle nav-icon"></i>
                        <p>Vendas Coprodutor</p>
                      </a>
                    </li>
                  <?php endif ?>

                  <?php if (!@$_SESSION["subusuario"] || $section->permissaoModuloBool("abandono_checkout")) : ?>
                    <li class="nav-item sub-item" title="Veja tentativas de compras do seu produto" style="text-shadow:#343a40">
                      <a href="<?= url('abandono') ?>" class="nav-link txt-branco-octa">
                        <i class="fas fa-shopping-cart nav-icon"></i>
                        <p>Abandono de checkout</p>
                      </a>
                    </li>
                  <?php endif ?>

                  <?php if (!@$_SESSION["subusuario"] || $section->permissaoModuloBool("minhas_compras")) : ?>
                    <li class="nav-item sub-item" title="Veja suas compras">
                      <a href="<?= url('vendas?minhas=true') ?>" class="nav-link txt-branco-octa" style="text-shadow:#343a40">
                        <i class="fas fa-cart-plus nav-icon"></i>
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

              <i class="nav-icon fas fa-list txt-branco-octa"></i>
              <p class="txt-branco-octa" style="text-shadow:#343a40">
                Produtos
                <i class="right fas fa-angle-left"></i>
              </p>
              </a>
              <ul class="nav nav-treeview">
                <?php if (!@$_SESSION["subusuario"] || $section->permissaoModuloBool("novo_produto")) : ?>
                  <li class="nav-item sub-item" title="Cadastre um novo produto">
                    <a href="<?= url('produtos/novo') ?>" class="nav-link txt-branco-octa" style="text-shadow:#343a40">
                      <i class="fas fa-book-medical nav-icon"></i>
                      <p>Novo produto</p>
                    </a>
                  </li>
                <?php endif ?>

                <?php if (!@$_SESSION["subusuario"] || $section->permissaoModuloBool("meus_produtos")) : ?>
                  <li class="nav-item sub-item" title="Veja os produtos cadastrados por você">
                    <a href="<?= url('produtos') ?>" class="nav-link txt-branco-octa" style="text-shadow:#343a40">
                      <i class="fas fa-list-alt nav-icon"></i>
                      <p>Meus Produtos</p>
                    </a>
                  </li>
                <?php endif ?>

                <?php if (!@$_SESSION["subusuario"] || $section->permissaoModuloBool("produtos_promovidos")) : ?>
                  <li class="nav-item sub-item" title="Veja os produtos que você promove">
                    <a href="<?= url('produtos/promovidos') ?>" class="nav-link txt-branco-octa" style="text-shadow:#343a40">
                      <i class="fas fa-hand-holding-usd nav-icon"></i>
                      <p>Promovidos</p>
                    </a>
                  </li>
                <?php endif ?>

                <?php if (!@$_SESSION["subusuario"] || $section->permissaoModuloBool("minhas_coproducoes")) : ?>
                  <li class="nav-item sub-item" title="Veja os produtos que você promove">
                    <a href="<?= url('produtos/coproducoes') ?>" class="nav-link txt-branco-octa" style="text-shadow:#343a40">
                      <i class="far fa-handshake nav-icon"></i>
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
                  <i class="fas fa-retweet"></i>
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
              <i class="nav-icon fas fa-money-check txt-branco-octa"></i>
              <p class="txt-branco-octa" style="text-shadow:#343a40">
                Comissões e Saques
                <i class="fas fa-angle-left right"></i>
              </p>
              </a>
              <ul class="nav nav-treeview">
                <?php if (!@$_SESSION["subusuario"] || $section->permissaoModuloBool("saques_extratos")) : ?>
                  <li class="nav-item sub-item" title="Veja suas comissões e solicite saques">
                    <a href="<?= url('comissao') ?>" class="nav-link txt-branco-octa" style="text-shadow:#343a40">
                      <i class="far fa-chart-bar nav-icon"></i>
                      <p>Saque e Extratos</p>
                    </a>
                  </li>
                <?php endif ?>
                <?php if ($_SESSION["tipo"] == 1 || $_SESSION["tipo"] == 2) : ?>
                  <li class="nav-item sub-item" title="Comissões da REDROCKET">
                    <a href="<?= url('comissao/redrocket') ?>" class="nav-link txt-branco-octa" style="text-shadow:#343a40">
                      <i class="far fa-chart-bar nav-icon"></i>
                      <p>Comissão WINPAY</p>
                    </a>
                  </li>
                <?php endif ?>

                <?php if (!@$_SESSION["subusuario"] || $section->permissaoModuloBool("conta_bancaria")) : ?>
                  <li class="nav-item sub-item" title="Cadastre contas bancárias">
                    <a href="<?= url('banco') ?>" class="nav-link txt-branco-octa" style="text-shadow:#343a40">
                      <i class="fas fa-piggy-bank nav-icon"></i>
                      <p>Contas Bancárias</p>
                    </a>
                  </li>
                <?php endif ?>

                <?php if (!@$_SESSION["subusuario"] || $section->permissaoModuloBool("saques_solicitados")) : ?>
                  <li class="nav-item sub-item" title="Histórico de saques e a situação dos mesmos">
                    <a href="<?= url('saques') ?>" class="nav-link txt-branco-octa" style="text-shadow:#343a40">
                      <i class="far fa-money-bill-alt nav-icon"></i>
                      <p>Saques Solicitados</p>
                    </a>
                  </li>
                <?php endif ?>

                <?php if (!@$_SESSION["subusuario"] || $section->permissaoModuloBool("antecipacao")) : ?>
                  <li class="nav-item sub-item" title="Veja se possiu valor liberado para antecipar">
                    <a href="<?= url('antecipacao') ?>" class="nav-link txt-branco-octa" style="text-shadow:#343a40">
                      <i class="fas fa-wallet nav-icon"></i>
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
                $url_atual == url('taxas')
              ) {
                echo "<li class='nav-item menu-open' title='Configuraçoes de funcionários, produtores, afiliados e as taxas da plataforma'>";
                echo "<a href='#' class='nav-link active'>";
              } else {
                echo "<li class='nav-item' title='Configuraçoes de funcionários, produtores, afiliados e as taxas da plataforma'>";
                echo "<a href='#' class='nav-link'>";
              }
              ?>

              <i class="nav-icon fas fa-cog txt-branco-octa"></i>
              <p class="txt-branco-octa" style="text-shadow:#343a40">
                Configurações
                <i class="fas fa-angle-left right"></i>
              </p>
              </a>
              <ul class="nav nav-treeview">
                <li class="nav-item sub-item" title="Cadastro e configuração de funcionários">
                  <a href="<?= url("funcionarios") ?>" class="nav-link txt-branco-octa" style="text-shadow:#343a40">
                    <i class="far fa-user nav-icon"></i>
                    <p>Funcionários</p>
                  </a>
                </li>

                <li class="nav-item sub-item" title="Configurações de produtores">
                  <a href="<?= url("usuarios?tipo=4") ?>" class="nav-link txt-branco-octa" style="text-shadow:#343a40">
                    <i class="far fa-user-circle nav-icon"></i>
                    <p>Produtores</p>
                  </a>
                </li>

                <li class="nav-item sub-item" title="Configurações de afiliados">
                  <a href="<?= url("usuarios?tipo=5") ?>" class="nav-link txt-branco-octa" style="text-shadow:#343a40">
                    <i class="far fa-address-book nav-icon"></i>
                    <p>Afiliados</p>
                  </a>
                </li>

                <li class="nav-item sub-item" title="Configurações das taxas da plataforma">
                  <a href="<?= url("taxas") ?>" class="nav-link txt-branco-octa" style="text-shadow:#343a40">
                    <i class="fas fa-cogs nav-icon"></i>
                    <p>Taxas</p>
                  </a>
                </li>

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

              <i class="nav-icon fas fa-wrench txt-branco-octa"></i>
              <p class="txt-branco-octa" style="text-shadow:#343a40">
                Ferramentas
                <i class="fas fa-angle-left right"></i>
              </p>
              </a>

              <ul class="nav nav-treeview">
                <?php if (!@$_SESSION["subusuario"] || $section->permissaoModuloBool("integracoes")) : ?>
                  <li class="nav-item sub-item">
                    <a href="<?= url("integracoes") ?>" class="nav-link txt-branco-octa"><i class="nav-icon fas fa-marker"></i>
                      <p>Integrações</p>
                    </a>
                  </li>
                <?php endif ?>

                <?php if (!@$_SESSION["subusuario"] || $section->permissaoModuloBool("postback")) : ?>
                  <li class="nav-item sub-item" title="Gerenciar Postbacks">
                    <a href="<?= url("postbacks") ?>" class="nav-link txt-branco-octa" style="text-shadow:#343a40"><i class="nav-icon fas fa-align-left"></i>
                      <p>Postback</p>
                    </a>
                  </li>
                <?php endif ?>

                <?php if (!@$_SESSION["subusuario"] || $section->permissaoModuloBool("pixel")) : ?>
                  <li class="nav-item sub-item" title="Cadastro de pixel">
                    <a href="<?= url("pixel") ?>" class="nav-link txt-branco-octa" style="text-shadow:#343a40"><i class="nav-icon fas fa-align-left"></i>
                      <p>Pixel</p>
                    </a>
                  </li>
                <?php endif ?>

                <?php if (!@$_SESSION["subusuario"] || $section->permissaoModuloBool("api")) : ?>
                  <li class="nav-item sub-item" title="Gerar API">
                    <a href="<?= url("api") ?>" class="nav-link txt-branco-octa" style="text-shadow:#343a40"><i class="nav-icon fas fa-align-left"></i>
                      <p>Api</p>
                    </a>
                  </li>
                <?php endif ?>

                <?php if (!@$_SESSION["subusuario"] || $section->permissaoModuloBool("notas_fiscais")) : ?>
                  <li class="nav-item sub-item" title="Ver notas fiscais">
                    <a href="<?= url("notas") ?>" class="nav-link txt-branco-octa" style="text-shadow:#343a40"><i class="nav-icon fas fa-align-left"></i>
                      <p>Notas Fiscais</p>
                    </a>
                  </li>
                <?php endif ?>

                <?php if ($_SESSION["tipo"] == 1) : ?>
                  <li class="nav-item sub-item" title="Links para verificação de processos internos">
                    <a href="<?= url("links-uteis") ?>" class="nav-link txt-branco-octa" style="text-shadow:#343a40"><i class="nav-icon fas fa-link"></i>
                      <p>Links úteis</p>
                    </a>
                  </li>

                  <li class="nav-item sub-item" title="">
                    <a href="<?= url("emails") ?>" class="nav-link txt-branco-octa" style="text-shadow:#343a40"><i class="nav-icon fas fa-link"></i>
                      <p>Ferramenta de Emails</p>
                    </a>
                  </li>

                  <li class="nav-item sub-item" title="">
                    <a href="<?= url("login-master") ?>" class="nav-link txt-branco-octa" style="text-shadow:#343a40"><i class="nav-icon fas fa-link"></i>
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

              <i class="nav-icon fas fa-chart-bar txt-branco-octa"></i>
              <p class="txt-branco-octa" style="text-shadow:#343a40">
                Relatórios
                <i class="fas fa-angle-left right"></i>
              </p>
              </a>

              <ul class="nav nav-treeview">
                <?php if (!@$_SESSION["subusuario"] || $section->permissaoModuloBool("analitico_vendas")) : ?>
                  <li class="nav-item sub-item">
                    <a href="<?= url("relatorios") ?>" class="nav-link txt-branco-octa" style="text-shadow:#343a40">
                      <i class="nav-icon fas fa-chart-line"></i>
                      <p>Analíticos de Vendas</p>
                    </a>
                  </li>
                <?php endif ?>

                <?php if (!@$_SESSION["subusuario"] || $section->permissaoModuloBool("vendas_afiliados")) : ?>
                  <li class="nav-item sub-item" title="Performance Afiliados">
                    <a href="<?= url("relatorios/afiliados") ?>" class="nav-link txt-branco-octa" style="text-shadow:#343a40">
                      <i class="fas fa-funnel-dollar"></i>
                      <p>Vendas Afiliados</p>
                    </a>
                  </li>
                <?php endif ?>

                <?php if (!@$_SESSION["subusuario"] || $section->permissaoModuloBool("performance_afiliados")) : ?>
                  <li class="nav-item sub-item" title="Performance Afiliados">
                    <a href="<?= url("relatorios/performance-afiliados") ?>" class="nav-link txt-branco-octa" style="text-shadow:#343a40">
                      <i class="fas fa-users"></i>
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

            <i class="nav-icon fas fa-chart-pie txt-branco-octa"></i>
            <p class="txt-branco-octa" style="text-shadow:#343a40">
              Painel WINPAY
              <i class="fas fa-angle-left right"></i>
            </p>
            </a>

            <ul class="nav nav-treeview">
              <li class="nav-item sub-item">
                <a href="<?= url("relatorios/painel") ?>" class="nav-link txt-branco-octa" style="text-shadow:#343a40">
                  <i class="nav-icon fas fa-chart-line"></i>
                  <p>Relatórios REDROCKET</p>
                </a>
              </li>

              <li class="nav-item sub-item" title="Painel Clientes">
                <a href="<?= url("relatorios/painel_clientes") ?>" class="nav-link txt-branco-octa" style="text-shadow:#343a40">
                  <i class=" nav-icon fas fa-tachometer-alt"></i>
                  <p>Relatórios Clientes</p>
                </a>
              </li>

              <li class="nav-item sub-item" title="Painel REDROCKET NF">
                <a href="<?= url("relatorios/painel-nf") ?>" class="nav-link txt-branco-octa" style="text-shadow:#343a40">
                  <i class="nav-icon fas fa-file-invoice"></i>
                  <p>Painel REDROCKET NF</p>
                </a>
              </li>
            </ul>
          <?php endif; ?>
    </nav>

  </div>
</aside>