<?php $v->layout("_theme"); ?>

<?php
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

function dadosComprador($venda)
{
  echo "
  <h3>Dados do Comprador</h3>
  <b>{$venda->nome} - CPF: </b> {$venda->cpf_cnpj}<br>
  <b>Tel: </b> <a href='https://api.whatsapp.com/send?phone=55{$venda->telefone}' title='Entrar em contato pelo whatsapp' target='_blank'>{$venda->telefone}</a> - 
  <b>email: </b><a href='mailto:{$venda->email}'>{$venda->email}</a><br>

  Rua {$venda->rua}, nº {$venda->numero} - {$venda->complemento}<br>
  {$venda->bairro}, {$venda->cidade}, {$venda->estado}<br>
  CEP: {$venda->cep}";
}

if ($_SESSION["tipo"] == 4) {
  $produto = $produtos->find("status != 0 AND prdutor = " . $_SESSION["id"])->fetch(true);
} else {
  $produto = $produtos->find("status != 0")->fetch(true);
}

if ($_SESSION["tipo"] == 6) {
  header("Location: " . url("vendas"));
}
?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <?= $section->getTipoSecaoLayout() ?>

    <div class="col-sm-6">
      <h4 style="font-family:inter"><?= $title ?></h4>
    </div>
    <hr />

    <form action="<?= $router->route("abandono.abandonos"); ?>" style="font-family:inter; font-size:10px">
      <div class="callout callout-primary">
        <div class="row">
          <div class="col-12 col-md-2">
            <h6 style="font-family:inter">Início:</h6>
            <input type="date" name="inicio" id="inicio" class="form-control" style="cursor:pointer;" value="<?= @$_GET["inicio"]; ?>">
          </div>
          <div class="col-12 col-md-2">
            <h6 style="font-family:inter">Fim:</h6>
            <input type="date" name="fim" id="fim" class="form-control" style="cursor:pointer;" value="<?= @$_GET["fim"]; ?>">
          </div>

          <div class="col-12 col-md-3">
            <h6 style="font-family:inter">Produto:</h6>

            <select name="produto" id="produto" class="form-control">
              <option value="">Produto...</option>
              <?php foreach ($produto as $produto) : ?>
                <option value="<?= $produto->id ?>" <?php if (@$_GET["produto"] == $produto->id) echo "selected";
                                                    echo "" ?>><?= $produto->nome ?></option>
              <?php endforeach; ?>
            </select>
          </div>


          <div class="col-12 col-md-3">
            <h6 style="font-family:inter">Comprador:</h6>
            <input type="text" name="comprador" id="cpf" class="form-control" placeholder="(nome ou CPF/CNPJ ou email)" value="<?= @$_GET["comprador"]; ?>">
          </div>

          <div class="col-12 col-md-2">
            <h6 style="font-family:inter; color:#fff">|</h6>
            <button type="submit" name="" class="form-control form-control btn-primary"> <i class="fa fa-check"></i> Filtar</button>
          </div>
        </div>
      </div>
    </form>


    <!--</div>-->
  </section>

  <!-- Main content -->

  <section class="content">

    <!-- Default box -->
    <?php
    if ($vendas) :
    ?>
      <div class="row" style="margin: 0 0 10px 0">
        <div class="col-md-9">
          <b>Total de registros: </b> <?= $count; ?>
        </div>

        <div class="col-md-3">
          Exportar:
          <a href="<?= url("abandono/exportar") ?>" class="badge badge-success" target="_blank">xls</a>
        </div>

      </div>

      <div class="card">

        <div class="card-body p-0" style="overflow-x: auto">
          <table class="table table-striped projects" style="font-size:13px">
            <thead>
              <tr style="font-family:inter">
                <th scope="col">Comprador</th>
                <th scope="col">Produtor</th>
                <th scope="col">Afiliado</th>
                <th scope="col">Produto</th>
                <th scope="col">Data Transação</th>
                <th scope="col">Ação</th>
              </tr>
            </thead>

            <tbody>
              <?php
              $forma_pagamento = array(
                1 => "CARTÃO DE CRÉDITO",
                2 => "BOLETO"
              );

              $i = 1;
              foreach ($vendas as $venda) :

              ?>

                <tr style="font-family:inter">

                  <td>
                    <?= $venda->nome ?><br>
                    <?= $venda->email ?><br>
                    <?= $venda->telefone ?> <a href='https://api.whatsapp.com/send?phone=55<?= $venda->telefone ?>&text=Olá <?= $venda->nome ?>, tudo bem? Percebi que você ainda não finalizou sua compra, posso ajudar de alguma forma?' title='Entrar em contato pelo whatsapp' target='_blank'><i class="fab fa-whatsapp"></i></a><br>
                  </td>

                  <td><?= $venda->dataProdutor()->nome ?></td>
                  <td><?= $venda->dataAfiliado()->nome ?></td>
                  <td><?= $venda->dataProduto()->nome ?></td>
                  <td><?= formataDataHora($venda->created_at); ?></td>
                  <td><a class="btn btn-warning btn-sm" data-toggle="modal" data-target="#myModal<?= $venda->id; ?>"><i class="fas fa-edit"></i></a></td>
                </tr>

                <?= $v->insert("vendas/modal-carrinho", [
                  "venda" => $venda,
                  "pagina" => $pagina,
                  "status_transacao" => $status_transacao,
                  "forma_pagamento" => $forma_pagamento,
                  "i" => $i,
                  //"dadosComprador" => dadosComprador($venda)
                ]); ?>
              <?php $i++;
              endforeach; ?>
            </tbody>
          </table>

          <?php
          //EXIBE A NAVEGAÇÃO DO PAGINADOR
          $uri = explode("?", $_SERVER["REQUEST_URI"], 2);
          $link = URL_BASE . "/abandono/";

          //VARIÁVEIS VÊM DO CONTROLADOR AppPedidos
          $paginador->paginas($total, $pagina, $link, @$uri[1]);

          $url = explode("?", $_SERVER["REQUEST_URI"]);
          if (@$url[1] != "") {
            $conc = "&";
          } else {
            $conc = "";
          }
          ?>

        <?php
      else :
        ?>
          <h4>Não existem pedidos cadastrados!</h4>
        <?php
      endif;
        ?>

        </div>
      </div>
  </section>
</div>