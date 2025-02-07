<?php
$v->layout("_theme");

if ($_SESSION["tipo"] != 1) {
  header("Location: " . URL_BASE);
}
?>

<div class="content-wrapper">

  <section class="content">

    <div class="card">
      <div class="card-body">
        <form action="#" method="post">
          <div class="row">

            <div class="col-md-2">
              Data Início:
              <input type="date" class="form-control" name="inicio" value="<?= @$_POST["inicio"] ?>">
            </div>
            <div class="col-md-2">
              Data Fim:
              <input type="date" class="form-control" name="fim" value="<?= @$_POST["fim"] ?>">
            </div>
            <div class="col-md-1">
              <br>
              <button type="submit" class="btn btn-primary">Buscar</button>
            </div>
          </div>

        </form>
      </div>
    </div>

    <div class="container-fluid">
      <?php if ($vendas) : ?>
        <div class="card">
          <div class="card-header bg-azul-octa">
            <b>Valores de Taxa de Transação</b>
          </div>

          <table class="table table-striped">
            <thead>
              <tr>
                <th scope="col">Nome</th>
                <th scope="col">CPF/CNPJ</th>
                <th scope="col">Valor Cobrado</th>
                <th scope="col">Taxa Cobrada</th>
                <th scope="col">Valor Sacado</th>
              </tr>
            </thead>

            <tbody>

              <?php
              foreach ($vendas as $venda) :
                $dados = $usuarioDados->find("usuario = $venda->produtor")->fetch();
                $sacado = $comissao->find("updated_at BETWEEN $periodo2 AND tipo = 2 AND usuario = $venda->produtor", null, "SUM(valor) as sacado")->fetch();
              ?>
                <tr>
                  <td><?= $venda->dataProdutor()->nome ?></td>
                  <td><?= $dados->cpf_cnpj ?></td>
                  <td>R$ <?= number_format($venda->taxa, 2, ",", ".") ?></td>
                  <td><?= number_format($venda->taxa_media, 2, ",", ".") . "%" ?></td>
                  <td>R$ <?= number_format($sacado->sacado, 2, ",", ".") ?></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      <?php endif ?>

      <?php if ($antecipacao) : ?>
        <div class="card">
          <div class="card-header bg-azul-octa">
            <b>Valores de Antecipações</b>
          </div>

          <table class="table table-striped">
            <thead>
              <tr>
                <th scope="col">Nome</th>
                <th scope="col">CPF/CNPJ</th>
                <th scope="col">Valor Cobrado</th>
              </tr>
            </thead>

            <tbody>

              <?php
              foreach ($antecipacao as $antecipacao) :
                $dados = $usuarioDados->find("usuario = $antecipacao->antecipado")->fetch();
                // $antecipacao = $comissao->find("usuario = 1 AND descricao LIKE '%Taxa antecipação%U$venda->produtor%' data_pedido BETWEEN $periodo", null, "SUM(valor) as soma_antecipado")->fetch();
                // var_dump($antecipacao);
              ?>
                <tr>
                  <td><?= $dados->nome ?></td>
                  <td><?= $dados->cpf_cnpj ?></td>
                  <td>R$ <?= number_format($antecipacao->valor, 2, ",", ".") ?></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      <?php endif ?>
    </div>

    <!-- FIM TOTAL DE VENDAS -->
  </section>
</div>