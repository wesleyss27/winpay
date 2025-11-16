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

/* Modal WINPAY - Inputs Corrigidos */
.modal-fade {
  position: fixed !important;
  top: 0 !important;
  left: 0 !important;
  width: 100% !important;
  height: 100% !important;
  z-index: 1050 !important;
  background-color: #000000 !important;
  background-image: none !important;
  opacity: 1 !important;
  display: flex !important;
  align-items: center !important;
  justify-content: center !important;
}


.modal-fade.show {
  display: flex !important;
}

.modal-dialog {
  width: 100% !important;
  max-width: 900px !important;
  margin: auto !important;
  position: relative !important;
}

.modal-content {
  background: #000000 !important;
  border: 2px solid #FFDD00 !important;
  border-radius: 20px !important;
  box-shadow: 0 25px 50px rgba(0, 0, 0, 0.9) !important;
  overflow: hidden !important;
  position: relative !important;
}

.modal-content::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  height: 3px;
  background: linear-gradient(90deg, #FFDD00, #FFE83D, #FFDD00);
  animation: pulse 2s infinite;
}

.card-header {
  background: linear-gradient(135deg, #1a1a1a, #000000) !important;
  padding: 24px 32px !important;
  border-bottom: 1px solid #FFDD00 !important;
}

.card-title {
  color: #FFDD00 !important;
  font-size: 22px !important;
  font-weight: 600 !important;
  margin: 0 !important;
  font-family: 'Inter', sans-serif !important;
}

.close {
  background: none !important;
  border: none !important;
  color: #FFDD00 !important;
  font-size: 28px !important;
  cursor: pointer !important;
  position: absolute !important;
  right: 20px !important;
  top: 50% !important;
  transform: translateY(-50%) !important;
  width: 40px !important;
  height: 40px !important;
  display: flex !important;
  align-items: center !important;
  justify-content: center !important;
  border-radius: 50% !important;
  transition: all 0.3s ease !important;
}

.close:hover {
  background: rgba(255, 221, 0, 0.2) !important;
  transform: translateY(-50%) scale(1.1) !important;
}

.modal-body {
  background: #000000 !important;
  padding: 32px !important;
  max-height: 70vh !important;
  overflow-y: auto !important;
}

.modal-body::-webkit-scrollbar {
  width: 10px;
}

.modal-body::-webkit-scrollbar-track {
  background: #1a1a1a;
}

.modal-body::-webkit-scrollbar-thumb {
  background: #FFDD00;
  border-radius: 5px;
}

.card.card-body {
  background: linear-gradient(145deg, #1a1a1a, #111111) !important;
  border: 1px solid #333 !important;
  border-radius: 15px !important;
  padding: 24px !important;
  margin-bottom: 20px !important;
  box-shadow: inset 0 1px 0 rgba(255, 221, 0, 0.1) !important;
}

.row {
  display: flex !important;
  flex-wrap: wrap !important;
  gap: 20px !important;
  margin: 0 !important;
  width: 100% !important;
}

.col-md-6 {
  flex: 1 !important;
  min-width: 250px !important;
  display: flex !important;
  flex-direction: column !important;
}

label {
  color: #FFDD00 !important;
  font-size: 14px !important;
  font-weight: 500 !important;
  margin-bottom: 8px !important;
  font-family: 'Inter', sans-serif !important;
  display: block !important;
}

.form-control, 
input.form-control, 
select.form-control, 
textarea.form-control {
  background: #2a2a2a !important;
  border: 2px solid #444 !important;
  border-radius: 8px !important;
  padding: 12px 16px !important;
  color: #ffffff !important;
  font-size: 14px !important;
  font-family: 'Inter', sans-serif !important;
  width: 100% !important;
  height: auto !important;
  min-height: 45px !important;
  box-sizing: border-box !important;
  transition: all 0.3s ease !important;
}

.form-control:focus, 
input.form-control:focus, 
select.form-control:focus, 
textarea.form-control:focus {
  background: #333333 !important;
  border-color: #FFDD00 !important;
  box-shadow: 0 0 0 3px rgba(255, 221, 0, 0.2) !important;
  outline: none !important;
  color: #ffffff !important;
}

.form-control::placeholder {
  color: #888 !important;
}

select.form-control {
  background: #2a2a2a !important;
  background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%23FFDD00' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='m6 8 4 4 4-4'/%3e%3c/svg%3e") !important;
  background-position: right 12px center !important;
  background-repeat: no-repeat !important;
  background-size: 16px 12px !important;
  padding-right: 40px !important;
  cursor: pointer !important;
  appearance: none !important;
  -webkit-appearance: none !important;
  -moz-appearance: none !important;
}

select.form-control option {
  background: #2a2a2a !important;
  color: #ffffff !important;
  padding: 10px !important;
}

small {
  color: #aaa !important;
  font-size: 12px !important;
  margin-top: 5px !important;
  line-height: 1.4 !important;
  display: block !important;
}

.facebook, .taboola, .google, .token {
  display: none;
}

.facebook.show, .taboola.show, .google.show, .token.show {
  display: block;
}

.modal-footer {
  background: linear-gradient(135deg, #1a1a1a, #000000) !important;
  padding: 24px 32px !important;
  border-top: 1px solid #FFDD00 !important;
  display: flex !important;
  justify-content: space-between !important;
  gap: 15px !important;
}

.btn {
  padding: 12px 24px !important;
  border-radius: 8px !important;
  font-size: 14px !important;
  font-weight: 500 !important;
  cursor: pointer !important;
  border: none !important;
  font-family: 'Inter', sans-serif !important;
  transition: all 0.3s ease !important;
  min-width: 100px !important;
}

.btn-primary {
  background: linear-gradient(135deg, #FFDD00, #D4B800) !important;
  color: #000000 !important;
}

.btn-primary:hover {
  background: linear-gradient(135deg, #FFE83D, #FFDD00) !important;
  transform: translateY(-2px) !important;
}

.btn-warning {
  background: linear-gradient(135deg, #ff8c00, #ff7700) !important;
  color: #ffffff !important;
}

.btn-warning:hover {
  background: linear-gradient(135deg, #ff9500, #ff8c00) !important;
  transform: translateY(-2px) !important;
}

.btn-danger {
  background: linear-gradient(135deg, #666, #444) !important;
  color: #ffffff !important;
}

.btn-danger:hover {
  background: linear-gradient(135deg, #777, #555) !important;
  transform: translateY(-2px) !important;
}

.modal input, .modal select, .modal textarea {
  background: #2a2a2a !important;
}

.table {
  border-collapse: separate !important;
  border-spacing: 0 !important;
  width: 100% !important;
}

.table thead th {
  background: linear-gradient(135deg, #1a1a1a, #111111) !important;
  color: #FFDD00 !important;
  border-bottom: 2px solid #FFDD00 !important;
  text-align: center !important;
  vertical-align: middle !important;
  padding: 15px 8px !important;
  font-weight: 600 !important;
  position: sticky !important;
  top: 0 !important;
  z-index: 10 !important;
  font-size: 14px !important;
  font-family: 'Inter', sans-serif !important;
  white-space: nowrap !important;
}

.table tbody td {
  background: rgba(26, 26, 26, 0.8) !important;
  color: #ffffff !important;
  text-align: center !important;
  vertical-align: middle !important;
  padding: 12px 8px !important;
  border-bottom: 1px solid #333 !important;
  font-size: 13px !important;
  font-family: 'Inter', sans-serif !important;
}

.table tbody tr:nth-child(even) td {
  background: rgba(17, 17, 17, 0.8) !important;
}

.table tbody tr:hover td {
  background: rgba(255, 221, 0, 0.1) !important;
}

.table .btn {
  min-width: 80px !important;
  padding: 8px 16px !important;
  margin: 2px !important;
}

.platform-icon {
  width: 28px !important;
  height: 28px !important;
  object-fit: contain !important;
  vertical-align: middle !important;
  margin: 0 auto !important;
  display: block !important;
}

.table tbody td:first-child {
  width: 80px !important;
  text-align: center !important;
}

.table thead th:nth-child(1), .table tbody td:nth-child(1) {
  width: 80px !important;
  min-width: 80px !important;
}

.table thead th:nth-child(2), .table tbody td:nth-child(2) {
  width: 120px !important;
  min-width: 120px !important;
}

.table thead th:nth-child(3), .table tbody td:nth-child(3) {
  width: 150px !important;
  min-width: 150px !important;
}

.table thead th:nth-child(4), .table tbody td:nth-child(4) {
  width: 100px !important;
  min-width: 100px !important;
}

.table thead th:nth-child(5), .table tbody td:nth-child(5) {
  width: 80px !important;
  min-width: 80px !important;
}

.table thead th:nth-child(6), .table tbody td:nth-child(6) {
  width: 140px !important;
  min-width: 140px !important;
}

.table thead th:nth-child(7), .table tbody td:nth-child(7) {
  width: 140px !important;
  min-width: 140px !important;
}

.table thead th:last-child, .table tbody td:last-child {
  width: 120px !important;
  min-width: 120px !important;
}

.pix-form-section {
  background: rgba(10, 10, 10, 0.95);
  border: 1px solid rgba(255, 255, 255, 0.08);
  border-radius: 18px;
  padding: 20px;
  margin-bottom: 18px;
  box-shadow: 0 20px 50px rgba(0, 0, 0, 0.4);
}

.pix-form-title {
  text-transform: uppercase;
  letter-spacing: 0.25rem;
  font-size: 0.72rem;
  color: rgba(255, 255, 255, 0.6);
  margin-bottom: 12px;
}

.pix-form-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
  gap: 18px 24px;
}

.pix-form-group {
  display: flex;
  flex-direction: column;
}

.pix-form-note {
  color: rgba(255, 255, 255, 0.55);
  font-size: 0.75rem;
  margin-top: 6px;
}

@media (max-width: 768px) {
  .row {
    flex-direction: column !important;
  }

  .col-md-6 {
    min-width: 100% !important;
  }

  .modal-footer {
    flex-direction: column !important;
  }

  .btn {
    width: 100% !important;
  }
}

* {
  box-sizing: border-box;
}

.modal-content *, .card * {
  background-color: transparent !important;
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

              <div class="col-sm-2 text-center zoom">
                <div class="card" style="border-radius: 30px">
                  <a class="btn" data-toggle="modal" data-target="#modal-lg" style="font-family:inter;" id="Kwai">
                    <img src="<?= url('themes/imgs/Kwai.png') ?>" width="100%">
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

          <div class="pix-form-section">
            <div class="pix-form-title">Configurações iniciais</div>
            <div class="pix-form-grid">
              <div class="pix-form-group">
                <label>Plataforma: *</label>
                <select name="plataforma" id="plataforma" class="form-control">
                  <option value="Facebook">Facebook</option>
                  <option value="Taboola">Taboola</option>
                  <option value="GoogleAnalitics">Google Analitics</option>
                  <option value="GoogleAs">Google Ads</option>
                  <option value="GoogleTagManager">Google Tag Manager</option>
                  <option value="TikTok">TikTok</option>
                  <option value="Kwai">Kwai</option>
                </select>
              </div>

              <div class="pix-form-group">
                <label>Nome do Pixel: *</label>
                <input type="text" name="nome" id='nome' class="form-control" placeholder="Nome">
              </div>
            </div>
          </div>

          <!-- < ?php if (@$_SESSION["testador"] == 1) : ?> -->
          <div class="facebook pix-form-section">
            <div class="pix-form-title">Integração Facebook</div>
            <div class="pix-form-grid">
              <div class="pix-form-group">
                <label>Tipo de Integração:</label>
                <select name="tipo_integracao" id="tipo_integracao" class="form-control">
                  <option value="navegador">Navegador</option>
                  <option value="api">API Conversão</option>
                </select>
              </div>

              <div class="pix-form-group token">
                <label>Token de Acesso:</label>
                <input type="text" name="token_acesso" id="token_acesso" class="form-control" placeholder="Token de acesso">
              </div>

              <div class="pix-form-group token">
                <label>Domínio Url:</label>
                <input type="text" name="dominio_url" id="dominio_url" class="form-control" placeholder="Domínio Url">
                <small class="pix-form-note">Caso fique em branco será enviado a url https://pay.redrocketpay.com/...</small>
              </div>
            </div>
          </div>
          <!-- < ?php endif; ?> -->

          <div class="pix-form-section">
            <div class="pix-form-title">Identificação do Pixel</div>
            <div class="pix-form-grid">
              <div class="pix-form-group">
                <label>Id: (pixel)*</label>
                <input type="text" name="id_pixel" id="id_pixel" class="form-control" placeholder="Pixel">
                <small class="pix-form-note google">(Caso seja do Google, não colocar tag de conversão. Ex.: AU-123456789-0,AU-123456789-1)</small>
                <small class="pix-form-note token"><b>Integração por api de conversão, colocar somente 1 pixel</b></small>
                <small class="pix-form-note facebook">Limite máximo de 30 pixels do Facebook caso a integração seja NAVEGADOR. Separe com vírgula(,) e sem espaços em branco.</small>
              </div>

              <div class="pix-form-group google">
                <label>Tag de Conversão Google: (Caso o pixel seja do Google)</label>
                <input type="text" name="tag_conversao" id="tag_conversao" class="form-control" placeholder="ID_do_pixel/Tag_de_conversão">
                <small class="pix-form-note">Ex.: AW-566715011/Y3BCCKGagb4CEIPFnY4C,AW-166715012/P3BCCKGagb4CEIPFnY4B</small>
                <small class="pix-form-note">Limite de 15 tags de conversão. Separe com vírgula(,) e sem espaços em branco.</small>
              </div>
            </div>
          </div>

          <div class="taboola pix-form-section">
            <div class="pix-form-title">Eventos Taboola</div>
            <div class="pix-form-grid">
              <div class="pix-form-group">
                <label>Evento Taboola:</label>
                <input type="text" name="evento_taboola" id="evento_taboola" class="form-control">
              </div>

              <div class="pix-form-group">
                <label>Evento Purchase Taboola:</label>
                <input type="text" name="purchase_taboola" id="purchase_taboola" class="form-control">
              </div>
            </div>
          </div>

          <div class="pix-form-section">
            <div class="pix-form-title">Execução</div>
            <div class="pix-form-grid">
              <div class="pix-form-group">
                <label>Executar no Boleto:</label>
                <select name="boleto" id="boleto" class="form-control">
                  <option value="1" selected>Sim</option>
                  <option value="0">Não</option>
                </select>
              </div>

              <div class="pix-form-group">
                <label>Executar no Checkout:</label>
                <small class="pix-form-note">Evento padrão: InitiateCheckout</small>
                <select name="executar_checkout" id="executar_checkout" class="form-control">
                  <option value="1" selected>Sim</option>
                  <option value="0">Não</option>
                </select>
              </div>
            </div>
          </div>

          <div class="pix-form-section">
            <div class="pix-form-title">Disponibilidade</div>
            <div class="pix-form-grid">
              <div class="pix-form-group">
                <label>Ativo:</label>
                <select name="status" id="status" class="form-control">
                  <option value="1" selected>Sim</option>
                  <option value="0">Não</option>
                </select>
              </div>

              <div class="pix-form-group valor_enviado">
                <label>Valor que será enviado:</label>
                <input type="text" name="valor" id="valor" class="form-control">
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
    $(".valor_enviado").css("display", "block");

  });

  $("#Kwai").click(() => {
    console.log("kwai");
    $(".google").hide();
    $(".taboola").hide();
    $(".facebook").hide();
    $(".kwai").show();

    $(".modal-title").html("Cadastrar Planos");
    $("#salvar").css("display", "inline");
    $("#cancelar").css("display", "inline");
    $("#editar").css("display", "none");
    $("#plataforma option[value=Kwai]").prop("selected", true);
    cancelar();
    $(".valor_enviado").css("display", "block");
  });


  $("#plataforma").change((e) => {

    var plataforma = $("#plataforma").val();

    if (plataforma == "GoogleAnalitics") {
      $(".google").css("display", "block");
      $(".taboola").css("display", "none");
      $(".facebook").css("display", "none");
      $(".valor_enviado").css("display", "block");

    } else if (plataforma == "Taboola") {
      $(".taboola").css("display", "block");
      $(".google").css("display", "none");
      $(".facebook").css("display", "none");
      $(".valor_enviado").css("display", "block");

    } else if (plataforma == "Facebook") {
      $(".facebook").css("display", "block");
      $(".taboola").css("display", "none");
      $(".google").css("display", "none");
      $(".valor_enviado").css("display", "block");
    } else if (plataforma == "TikTok") {
      $(".taboola").css("display", "none");
      $(".google").css("display", "none");
      $(".facebook").css("display", "none");
      $(".valor_enviado").css("display", "block");
    } else if (plataforma == "Kwai") {
      $(".taboola").css("display", "none");
      $(".google").css("display", "none");
      $(".facebook").css("display", "none");
      $(".kwai").css("display", "block");
      $(".valor_enviado").css("display", "block");
    } else {
      $(".taboola").css("display", "none");
      $(".google").css("display", "none");
      $(".valor_enviado").css("display", "block");
    }
  });
  $("#plataforma").trigger("change");


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
        $(".valor_enviado").css("display", "block");
        break;
      case "Kwai":
        $(".google").hide();
        $(".taboola").hide();
        $(".facebook").hide();
        $(".kwai").show();
        console.log(6);
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
