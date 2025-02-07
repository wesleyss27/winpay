<?php
//var_dump($_SESSION);
?>

<?php $v->layout("_theme"); ?>

<div class="principal">

    <!-- SALDOS -->
    <div class="row">
        <div class="tab">
            <div class="tab_content">
                <h4>Saldos</h4>
            </div>
        </div>
        
        <!-- HOJE -->
        <div class="col-md-2">
            <div class="col-md-12 card">
                <div class="row">
                <div class="bg-azul1 card-head">
                    <strong>Hoje</strong>
                </div>
                
                <div class="card-body">
                    <?php if(@$hoje): ?>
                    <article class="users_user">
                        <p class="valor"><?= "R$ " . number_format(@$hoje, 2, ",", "."); ?></p>
                    </article>
                    <?php else: ?>
                        <p class="valor">R$ 0,00</p>
                    <?php endif; ?>
                </div>
                </div>
            </div>
        </div>
        <!-- FIM HOJE -->

        <!-- ONTEM -->
        <div class="col-md-2">
            <div class="col-md-12 card">
                <div class="row">
                <div class="bg-rosa1 card-head">
                    <strong>Ontem</strong>
                </div>
                
                <div class="card-body">
                    <?php if(@$ontem):
                        ?>
                    <article class="users_user">
                        <p class="valor"><?= "R$ " . number_format(@$ontem, 2, ",", "."); ?></p>
                    </article>
                    <?php else: ?>
                        <p class="valor">R$ 0,00</p>
                    <?php endif; ?>
                </div>
                </div>
            </div>
        </div>
        <!-- FIM ONTEM -->

        <!-- ESSE MÊS -->
        <div class="col-md-2">
            <div class="col-md-12 card">
                <div class="row">
                <div class="bg-azul1 card-head">
                    <strong>Esse Mês</strong>
                </div>
                
                <div class="card-body">
                    <?php if($mes): ?>
                    <article class="users_user">
                        <p class="valor"><?= "R$ " . number_format($mes, 2, ",", "."); ?></p>
                    </article>
                    <?php else: ?>
                        <p class="valor">R$ 0,00</p>
                    <?php endif; ?>
                </div>
                </div>
            </div>
        </div>
        <!-- FIM ESSE MÊS -->

        <!-- PENDENTE -->
        <div class="col-md-2">
            <div class="col-md-12 card">
                <div class="row">
                <div class="bg-laranja card-head">
                    <strong>Saldo Pendente</strong>
                </div>
                
                <div class="card-body">
                    <?php if($pendente): ?>
                    <article class="users_user">
                        <p class="valor"><?= "R$ " . number_format($pendente, 2, ",", "."); ?></p>
                    </article>
                    <?php else: ?>
                        <p class="valor">R$ 0,00</p>
                    <?php endif; ?>
                </div>
                </div>
            </div>
        </div>
        <!-- FIM PENDENTE -->

        <!-- DISPONIVEL -->
        <div class="col-md-2">
            <div class="col-md-12 card">
                <div class="row">
                <article class="users_user">
                <div class="bg-verde2 card-head">
                    <strong>Disponível</strong>
                </div>

                <div class="card-body">
                    <?php if(@$saldo->valor > 0): ?>
                    <article class="users_user">
                        <p class="valor"><?= "R$ " . number_format(@$saldo->valor, 2, ",", "."); ?></p>
                    </article>
                    <?php else: ?>
                        <p class="valor">R$ 0,00</p>
                    <?php endif; ?>
                </div>
                </article>
                </div>
            </div>
        </div>
        <!-- FIM DISPONÍVEL -->

        <!-- TOTAL -->
        <div class="col-md-2">
            <div class="col-md-12 card">
                <div class="row">
                <article class="users_user">
                <div class="bg-verde2 card-head">
                    <strong>Total</strong>
                </div>

                <div class="card-body">
                    <article class="users_user">
                        <p class="valor"><?= "R$ " . number_format(@$saldo->valor + @$pendente, 2, ",", "."); ?></p>
                    </article>
                </div>
                </article>
                </div>
            </div>
        </div>
        <!-- FIM TOTAL -->
    </div>
    <!-- FIM SALDOS -->

    <!-- HISTÓRICO COMISSÕES -->
    <?php if($comissao): ?>
    <div class="row">
        <div class="tab">
            <div class="tab_content">
                <h4>Últimas Comissões</h4>
            </div>
        </div>

        <div class="row">
            <table class="table table-striped">
                <tr>
                    <?php foreach($comissao as $comissao): ?>
                    <td>
                        <b><?= formataData($comissao->created_at) ?></b><br>
                        <?= "R$ " . number_format($comissao->valor, 2, ",", ".") ?><br>
                        <?= $comissao->dataVenda()->transacao ?><br>
                    </td>
                    <?php endforeach ?>
                </tr>
            </table>
        </div>
    </div>
    <?php endif ?>
</div>