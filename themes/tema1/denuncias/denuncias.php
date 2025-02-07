<?php
$v->layout("_theme");

if ($_SESSION["tipo"] != 1 && $_SESSION["tipo"] != 2) {
    header("Location: " . URL_BASE);
}

$status = array(
    0 => "NÃO",
    1 => "SIM"
);

$assuntos = array(
    0 => "Outro Assunto",
    1 => "Produto Enganoso"
);
?>


<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- HEADER PAGE -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h3 style="font-family:inter"><?= $title ?></h3>
                </div>

                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="./">Início</a></li>
                        <li class="breadcrumb-item active">Notas</li>
                    </ol>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>
    <!-- FIM HEADER PAGE -->

    <!-- Main content -->
    <section class="content">
        <!-- Default box -->
        <div class="card">

            <!-- CARD BODY -->
            <div class="card-body" style="overflow-x: auto">
                <?php if ($denuncias) : ?>
                    <script>
                        var dados = [];
                    </script>

                    <table class="table table-striped projects" style="font-size:13px">
                        <thead>
                            <tr style="font-family:inter">
                                <th scope="col">#</th>
                                <th scope="col">Produto</th>
                                <th scope="col">Denunciante</th>
                                <th scope="col">Alterado</th>
                                <th scope="col">Criado</th>
                                <th scope="col">Alterado</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php
                            $i = 1;
                            foreach ($denuncias as $denuncia) :
                            ?>
                                <tr style="font-family:inter">
                                    <th scope="row"><?= $denuncia->id; ?></th>
                                    <td><?= $denuncia->produto ? $denuncia->dataProduto()->nome : ""; ?></td>
                                    <td><?= $denuncia->usuario ? $denuncia->dataUsuario()->nome : ""; ?></td>
                                    <td><?= $denuncia->usuario_ultima_alteracao ? $denuncia->dataUsuarioAlteracao()->nome : ""; ?></td>
                                    <td><?= formataData($denuncia->created_at); ?></td>
                                    <td><?= formataData($denuncia->updated_at); ?></td>
                                </tr>
                                <tr>
                                    <td colspan="6">
                                        <?= @$assuntos[$denuncia->assunto]; ?><br>
                                        <?= $denuncia->denuncia; ?>
                                    </td>
                                </tr>

                                <script>
                                    dados[<?= $i ?>] = {
                                        id: "<?= $denuncia->id ?>",
                                        email: "<?= $denuncia->email ?>",
                                        nome: "<?= $denuncia->nome ?>",
                                        tempo_saque: "<?= $denuncia->tempo_saque ?>",
                                        taxa_saque: "<?= $denuncia->taxa_saque ?>",
                                        observacao: "<?= $denuncia->observacao ?>",
                                        ativo: "<?= $denuncia->ativo ?>",
                                        grupo: "<?= $denuncia->grupo ?>",

                                    }
                                </script>
                            <?php $i++;
                            endforeach; ?>

                        </tbody>
                    </table>

                <?php
                    //EXIBE A NAVEGAÇÃO DO PAGINADOR
                    $link = URL_BASE . "/denuncias/";

                    //VARIÁVEIS VÊM DO CONTROLADOR AppProdutor
                    $paginador->paginas($total, $pagina, $link);

                else :
                ?>
                    <h4>Não existem denúncias cadastradas!</h4>
                <?php
                endif;
                ?>
            </div>
            <!-- FIM CARD BODY -->

        </div>
    </section>
</div>