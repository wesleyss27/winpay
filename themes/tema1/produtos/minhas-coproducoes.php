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
    <section class="content-header">

        <?= $section->getTipoSecaoLayout() ?>

        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h4 style="font-family:inter"><?= $title ?></h4>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="./">Início</a></li>

                        <li class="breadcrumb-item active">Coproduções</li>
                    </ol>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">

        <!-- Default box -->
        <div class="card">

            <div class="card-body" style="overflow-x: auto">
                <?php if ($coproducao) : ?>
                    <script>
                        var dados = [];
                    </script>
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th scope="col">Produto</th>
                                <th scope="col">Tipo</th>
                                <th scope="col" style="text-align: center">Comissão</th>
                                <th scope="col" style="text-align: center">Desde</th>
                                <th scope="col" style="text-align: center">Data Limite</th>
                                <th scope="col">Aceito</th>
                                <th scope="col">Clique Para Aceitar</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php
                            $tipo = array(
                                0 => "Coprodutor",
                                1 => "Gerente"
                            );
                            $aceito = array(
                                0 => "Não",
                                1 => "Sim"
                            );
                            $tipoComissao = array(
                                0 => "%",
                                1 => "R$"
                            );

                            function formataComissao($tipo, $valor)
                            {
                                if ($tipo == 0) {
                                    return number_format($valor, 2, ",", ".") . "%";
                                } else {
                                    return "R$ " . number_format($valor, 2, ",", ".");
                                }
                            }

                            $i = 0;

                            foreach ($coproducao as $coproducao) :

                            ?>
                                <tr class="cli" id="afiliacao<?= $coproducao->co_produtor; ?>">
                                    <td>
                                        <?= $coproducao->dataProduto()->nome; ?>
                                    </td>
                                    <td><?= $tipo[$coproducao->gerente]; ?></td>

                                    <?php if ($coproducao->gerente == 1) : ?>
                                        <td align="center">
                                            COM afililiado: <?= formataComissao($coproducao->tipo_comissao_afiliados, $coproducao->comissao_afiliados) ?><br>
                                            SEM afililiado: <?= formataComissao($coproducao->tipo_comissao, $coproducao->comissao) ?>
                                        </td>
                                    <?php else : ?>
                                        <td align="center"><?= formataComissao($coproducao->tipo_comissao, $coproducao->comissao) ?></td>
                                    <?php endif; ?>
                                    <td align="center"><?= formataData($coproducao->created_at); ?></td>
                                    <td align="center"><?php if ($coproducao->data_limite == "0000-00-00") echo " eterno ";
                                                        else echo formataData($coproducao->data_limite); ?></td>
                                    <td>
                                        <?php if ($coproducao->aceito == 0) : ?>
                                            <i class="far fa-thumbs-down text-danger"></i>
                                        <?php elseif ($coproducao->aceito == 1) : ?>
                                            <i class="far fa-thumbs-up text-success"></i>
                                        <?php endif; ?>
                                        <?= $aceito[$coproducao->aceito]; ?>
                                    </td>
                                    <?php if (!@$_SESSION["subusuario"] || $permissao->editar == 1) : ?>
                                        <td><button class="btn btn-warning" data-target="#modal-lg" data-toggle="modal" onclick="editar(<?= $i; ?>)"><i class="fas fa-edit"></i></button></td>
                                    <?php endif ?>
                                </tr>

                                <script>
                                    dados[<?= $i ?>] = {
                                        id: "<?= $coproducao->id ?>",
                                        aceito: "<?= $coproducao->aceito ?>",
                                    }
                                </script>

                            <?php $i++;
                            endforeach; ?>
                        </tbody>
                    </table>
                <?php else : ?>
                    <h4>Você ainda não tem coproduções para esse produto</h4>
                <?php endif; ?>
            </div>
            <!-- /.card-body -->
        </div>
        <!-- /.card -->


    </section>

</div>

<!-- cadastar -->
<div class="modal fade" id="modal-lg">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header">
                <h3 class="card-title"><i class="fa fa-info-circle"></i> Aceitar coprodução?</h3>
            </div>

            <form action="" method="post" style="font-family:inter; text-transform:uppercase; font-size:11px">
                <div class="modal-body">
                    <input type="hidden" name="id" id="id">

                    <select name="aceito" id="aceito" class="form-control input-lg">
                        <option value="0">Não</option>
                        <option value="1">Sim</option>
                    </select>
                </div>

                <div class="modal-footer justify-content-between" style="background:#f1f1f1">
                    <button type="button" class="btn btn-danger" data-dismiss="modal" id="cancelar1">Cancelar</button>
                    <button class="btn btn-success" id="salvar">Salvar</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- end cadastrar -->

<?= $v->start("scripts") ?>

<script>
    function editar(i) {
        var id = dados[i]["id"];
        var aceito = dados[i]["aceito"];

        $("#id").attr({
            "value": id
        });
        $("#aceito option[value=" + aceito + "]").prop("selected", "true");
    }

    $("#salvar").click(function(e) {
        e.preventDefault();

        var form = $("form");
        $.ajax({
            url: "<?= $router->route("produtos.aceitarCoproducao") ?>",
            data: form.serialize(),
            type: "POST",
            beforeSend: function() {
                console.log("atualizando");
            },
            error: function() {
                console.log("O servidor não conseguiu processar o pedido");
            },
            success: function(callback) {
                console.log(callback);
                alert(callback);
                location.reload();
            }
        });
    });
</script>
<?= $v->end() ?>