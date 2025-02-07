<?php $v->layout("_theme"); ?>

<?= $v->start("links"); ?>
<link rel="stylesheet" href="<?= url("themes/tema1/dist/css/modal.css") ?>">
<?= $v->end(); ?>

<?php
$sim = array(
  1 => "SIM",
  0 => "NÃO",
  "" => "NÃO"
);

$simCor = array(
  1 => "bg-danger",
  0 => "bg-success",
  "" => "bg-success"
);

$status_transacao = array(
  1 => "AGUARDANDO PAGAMENTO",
  2 => "EM ANÁLISE",
  3 => "PRÉ AUTORIZADA",
  4 => "PAGA",
  5 => "CANCELADA",
  6 => "DEVOLVIDA",
  7 => "CHARGEBACK",
  8 => "CONCLUÍDA",
  9 => "BLOQUEADA",
  10 => "ANTIFRAUDE",
);

$statusT = array(
  "" => "text-warning",
  0 => "text-warning",
  1 => "text-warning",
  2 => "text-info",
  3 => "text-info",
  4 => "text-success",
  5 => "text-danger",
  6 => "text-danger",
  7 => "text-danger",
  8 => "text-success",
  9 => "text-danger"
);

$status_bg = array(
  1 => "bg-warning",
  2 => "bg-info",
  3 => "bg-info",
  4 => "bg-success",
  5 => "bg-danger",
  6 => "bg-danger",
  7 => "bg-danger",
  8 => "bg-success",
  9 => "bg-danger"
);

// MENSAGEM PARA EMAIL E WHATSAPP DO SUPORTE
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

<div class="ajax_load">
  <div class="ajax_load_box">
    <div class="ajax_load_box_circle"></div>
    <div class="ajax_load_box_title">Aguarde, carregando!</div>
  </div>
</div>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- FORMULARIO -->
  <section class="content-header">

    <?= $section->getTipoSecaoLayout() ?>

    <div class="col-sm-6">
      <h4 style="font-family:inter"><b><?= $title ?></b></h4>
    </div>
  </section>
  <!-- FIM FORMULARIO -->

  <!-- Main content -->

  <?php if (!@$usuario) : ?>
    <section class="content">
      <div class="card">
        <div class="card-body">
          <p>
          <h2>Você ainda não tem permissão para utilizar Dropshipping!</h2><br> Caso queira transacionar via modelo Dropshipping, entre em contato com o suporte via E-mail ou Whatsapp.<br>
          <b>E-mail:</b><a href="mailto:<?= $para ?>?subject=<?= $assunto ?>&body=<?= $mensagem ?>" target="_blank"> suporte.oficial@redrocketpay.com</a><br>
          <b>Whatsapp:</b><a href="https://api.whatsapp.com/send?phone=5533998787884&text=<?= $mensagem_whats ?>" target="_blank"> (33) 99878-7884</a></p>
        </div>
      </div>
    </section>
  <?php else : ?>
    <section class="content">

      <?php if ($cobrancas) : ?>
        <div class="card">
          <table class="table table-striped">
            <thead class="thead-dark">
              <tr>
                <th scope="col">Transação</th>
                <th scope="col">Valor</th>
                <th scope="col">Produtor</th>
                <th scope="col">Status</th>
                <th scope="col">Data Transação</th>
                <th scope="col">Data Finalizada</th>
                <th scope="col">Detalhes</th>
              </tr>
            </thead>

            <tbody>
              <?php foreach ($cobrancas as $cobranca) : ?>
                <tr>
                  <td>#<?= $cobranca->id ?><br> #<?= $cobranca->transacao ?></td>
                  <td>R$ <?= number_format($cobranca->valor_cobrado, 2, ",", ".") ?></td>
                  <td>
                    <?= $cobranca->dataProdutor()->nome ?><br>
                    <?= $cobranca->dataProdutor()->email ?>
                  </td>
                  <td><?= @$status_transacao[$cobranca->status_cobranca] ?></td>
                  <td><?= formataDataHora($cobranca->created_at) ?></td>
                  <td><?= formataDataHora($cobranca->data_finalizada) ?></td>
                  <td><a class="btn btn-warning btn-sm" data-toggle="modal" data-target="#myModal<?= $cobranca->id; ?>"><i class="fas fa-plus"></i></a></td>
                </tr>

                <?php include "modal-info.php" ?>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>

        <?php
        //EXIBE A NAVEGAÇÃO DO PAGINADOR
        $uri = explode("?", $_SERVER["REQUEST_URI"], 2);
        $link = URL_BASE . "/cobrancas/";

        //VARIÁVEIS VÊM DO CONTROLADOR AppPedidos
        $paginador->paginas($total, $pagina, $link, @$uri[1]);

        $url = explode("?", $_SERVER["REQUEST_URI"]);
        if (@$url[1] != "") {
          $conc = "&";
        } else {
          $conc = "";
        }
        ?>

      <?php else : ?>
        <div class="card card-body">
          Nenhuma cobrança encontrada
        </div>
      <?php endif; ?>

      <br>
    </section>
  <?php endif; ?>
</div>

<?= $v->start("scripts"); ?>
<script src="<?= url("themes/tema1/dist/js/modal.js") ?>"></script>
<script>
  function retentar(id) {
    // load("open", 6000);

    var url = "<?= $router->route("cobrar.drop1") ?>";

    console.log(url);

    $.ajax({
      url: url,
      method: "POST",
      data: {
        cobranca: id
      },
      success: function(callback) {
        console.log(callback);
        // load();
        alert(callback);

        location.reload();
      }
    });
  }
</script>
<?= $v->end(); ?>