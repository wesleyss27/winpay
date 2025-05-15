<?php

$v->layout("_theme");

if ($produto->usuario != $section->getId()) {
    header("Location: " . url("produtos/detalhe/") . $produto->id);
}

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

                        <li class="breadcrumb-item active">Planos</li>
                    </ol>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">

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

        <!-- <section class="card">
            <div class="card-body">
                * Não é necessário cadastrar planos para vender o produto, basta cadastrar um checkout.
            </div>
        </section> -->

        <?= $v->insert("fragmentos/msg-cadastrar-editar", []); ?>

        <form action="#">
            <div class="card card-body">
                <div class="row">
                    <div class="col-md-4">
                        <label>Nome ou código</label>
                        <input type="text" name="nome" id="name" class="form-control">
                    </div>

                    <div class="col-md-1">
                        <label style="color: #fff">|</label>
                        <button class="btn btn-primary form-control">Buscar</button>
                    </div>
                </div>
            </div>
        </form>
        <!-- Default box -->
        <div class="card">
            <div class="card-header">

                <?php if ($produto->status == 3 || $produto->status == 4) : ?>
                    <button disabled class="btn btn-outline-primary" data-toggle="modal" data-target="#modal-lg" style="font-family:inter" id="cadastro">Cadastrar <i class="fa fa-chevron-right"></i> </button>
                    <span class="badge bg-danger">Produto REPROVADO/BLOQUEADO</span>
                <?php else : ?>
                    <?php if (!@$_SESSION["subusuario"] || $permissao->adicionar == 1) : ?>
                        <button class="btn btn-outline-primary" data-toggle="modal" data-target="#modal-lg" style="font-family:inter" id="cadastro">Cadastrar <i class="fa fa-chevron-right"></i> </button>
                    <?php endif ?>
                <?php endif; ?>

                &nbsp; Total de registros: <?= $count1 ?>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                </div>
            </div>

            <style>
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
            </style>

            <?php if ($planos) : ?>
                <div class="card-body" style="background: #f4f6f9">
                    <script>
                        var dados = [];
                    </script>
                    <div style="overflow-x: auto;">

                        <div class="mobile-n card card-body bg-azul-gradient" style="font-size:13px; font-family:inter">
                            <div class="row">
                                <!-- <div class="col">ID</div> -->
                                <div class="col">Código</div>
                                <div class="col">Nome</div>
                                <div class="col">Preço</div>
                                <?php if ($produto->tipo_cobranca == 2) : ?>
                                    <div class="col">Assinatura</div>
                                <?php endif ?>
                                <?php if ($produto->tipo_cobranca != 2) : ?>
                                    <div class="col">Un.</div>
                                <?php endif ?>
                                <div class="col">Status</div>
                                <div class="col">Duplicar</div>
                                <div class="col">Editar/Excluir</div>
                            </div>
                        </div>

                        <?php foreach ($planos as $plano) : ?>
                            <!-- <div class="carregar card card-body">
                                <div class="bg-azul-octa2 carrega-content" style="font-size: 5px; border-radius: 50px;"></div>
                            </div> -->
                            <div class="card card-body efeito-slow">
                                <div class="row" style="font-family:inter; font-size:12px; margin: 0 15px">
                                    <!-- <div class="col"><b><?= $plano->id; ?></b></div> -->
                                    <div class="col-md col-4">
                                        <span class="mobile-n">#<b><?= $plano->id; ?></b><br></span>
                                        código: <span class="badge bg-vermelho-gradient" style="font-size: 12px;"><b><?= $plano->codigo; ?></b></span>
                                    </div>
                                    <div class="col-md col-4">
                                        <?php if ($plano->foto) {
                                            $foto = $plano->foto;
                                        } else {
                                            $foto = "default.png";
                                        } ?>
                                        <?php if ($plano->shopify == 0) : ?>
                                            <img src="<?= url("uploads/produtos/$foto"); ?>" alt="" width="30px" style="border-radius: 50%">
                                        <?php else : ?>
                                            <img src="<?= $plano->foto; ?>" alt="" width="30px" style="border-radius: 50%">
                                        <?php endif; ?>
                                        <?= $plano->nome; ?>
                                    </div>
                                    <div class="col-md col-4">
                                        R$ <?= number_format($plano->preco, 2, ",", "."); ?><br>
                                        até: <?= $plano->parcelas; ?>X
                                    </div>

                                    <div class="desktop-n hr-mobile col-md-12"></div>
                                    <?php if ($produto->tipo_cobranca == 2) : ?>
                                        <div class="col">
                                            <?= $plano->periodo; ?><br>
                                            <?= $plano->maximo_recorrencia > 0 ? $plano->maximo_recorrencia . " cobranças" : "Eterno"; ?>
                                        </div>
                                    <?php endif ?>
                                    <?php if ($produto->tipo_cobranca != 2) : ?>
                                        <div class="col"><?= $plano->quantidade; ?></div>
                                    <?php endif ?>
                                    <div class="col mobile-n">
                                        Ativo: <?= $ativo[$plano->ativo] ?><br>
                                        Privado: <?= $ativo[$plano->privado] ?><br>
                                        <!-- Televendas: <?= $ativo[$plano->televendas] ?> -->
                                    </div>
                                    <div class="col">
                                        <form action="#" method="post" name="linha<?= $plano->id; ?>">
                                            <input type="hidden" name="status" value="<?= $produto->status ?>">
                                            <input type="hidden" name="produto" value="<?= $produto->id ?>">
                                            <input type="hidden" name="nome" value="<?= $plano->nome ?> - copy">
                                            <input type="hidden" name="preco" value="<?= $plano->preco ?>">
                                            <input type="hidden" name="foto_atual" value="<?= $plano->foto ?>">
                                            <input type="hidden" name="assumir_juros" value="<?= $plano->assumir_juros ?>">
                                            <input type="hidden" name="parcelas" value="<?= $plano->parcelas ?>">
                                            <input type="hidden" name="periodo" value="<?= $plano->periodo ?>">
                                            <input type="hidden" name="maximo_recorrencia" value="<?= $plano->maximo_recorrencia ?>">
                                            <input type="hidden" name="primeira_parcela" value="<?= $plano->primeira_parcela ?>">
                                            <input type="hidden" name="preco_parcela" value="<?= $plano->preco_parcela ?>">
                                            <input type="hidden" name="carencia" value="<?= $plano->carencia ?>">
                                            <input type="hidden" name="comissao_afiliado_assinatura" value="<?= $plano->comissao_afiliado_assinatura ?>">
                                            <input type="hidden" name="habilitar_com_cop_dup" value="<?= $plano->habilitar_comissao_coprodutores ?>">
                                            <input type="hidden" name="comissao_coprodutores" value="<?= $plano->comissao_coprodutores ?>">
                                            <input type="hidden" name="habilitar_comissao_afiliado" value="<?= $plano->habilitar_comissao_afiliado ?>">
                                            <input type="hidden" name="tipo_comissao_afiliado" value="<?= $plano->tipo_comissao_afiliado ?>">
                                            <input type="hidden" name="comissao_afiliado" value="<?= $plano->comissao_afiliado ?>">
                                            <input type="hidden" name="quantidade" value="<?= $plano->quantidade ?>">
                                            <input type="hidden" name="descricao" id="descricao<?= $i ?>" value="<?= $plano->descricao ?>">
                                            <input type="hidden" name="privado" value="<?= $plano->privado ?>">
                                            <input type="hidden" name="ativo" value="<?= $plano->ativo ?>">
                                            <input type="hidden" name="televendas" value="<?= $plano->televendas ?>">
                                            <input type="hidden" name="tipo_comissao1" value="<?= $plano->televendas ?>">
                                            <input type="hidden" name="comissao1" value="<?= $plano->televendas ?>">
                                            <input type="hidden" name="pagina" value="<?= $pagina ?>">

                                            <?php if (!@$_SESSION["subusuario"] || $permissao->adicionar == 1) : ?>
                                                <button class="btn btn-link" title="Duplicar plano" onclick="duplicar(<?= $plano->id; ?>)"><i class="far fa-copy"></i></button>
                                            <?php endif ?>
                                        </form>
                                    </div>
                                    <?php if ($plano->usuario == $section->getId()) : ?>
                                        <div class="col">
                                            <?php if (!@$_SESSION["subusuario"] || $permissao->editar == 1) : ?>
                                                <button class="btn btn-warning btn-sm" data-target="#modal-lg" onclick="editar(<?= $i; ?>)" data-toggle="modal"><i class="fas fa-edit"></i></button>
                                            <?php endif ?>

                                            <?php if (!@$_SESSION["subusuario"] || $permissao->excluir == 1) : ?>
                                                <button class="btn btn-danger btn-sm" onclick="excluir(<?= $plano->id; ?>)"><i class="fas fa-times"></i></button>
                                            <?php endif ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <script>
                                dados[<?= $i ?>] = {
                                    id: "<?= $plano->id ?>",
                                    nome: "<?= $plano->nome ?>",
                                    preco: "<?= $plano->preco ?>",
                                    foto: "<?= $plano->foto ?>",
                                    assumir_juros: "<?= $plano->assumir_juros ?>",
                                    parcelas: "<?= $plano->parcelas ?>",
                                    periodo: "<?= $plano->periodo ?>",
                                    maximo_recorrencia: "<?= $plano->maximo_recorrencia ?>",
                                    primeira_parcela: "<?= $plano->primeira_parcela ?>",
                                    preco_parcela: "<?= $plano->preco_parcela ?>",
                                    carencia: "<?= $plano->carencia ?>",
                                    comissao_afiliado_assinatura: "<?= $plano->comissao_afiliado_assinatura ?>",
                                    habilitar_comissao_coprodutores: "<?= $plano->habilitar_comissao_coprodutores ?>",
                                    comissao_coprodutores: "<?= $plano->comissao_coprodutores ?>",
                                    habilitar_comissao_afiliado: "<?= $plano->habilitar_comissao_afiliado ?>",
                                    tipo_comissao_afiliado: "<?= $plano->tipo_comissao_afiliado ?>",
                                    comissao_afiliado: "<?= $plano->comissao_afiliado ?>",
                                    quantidade: "<?= $plano->quantidade ?>",
                                    descricao: "<?= $plano->descricao ?>",
                                    privado: "<?= $plano->privado ?>",
                                    ativo: "<?= $plano->ativo ?>",
                                    habilitar_ecommerce: "<?= $plano->habilitar_ecommerce ?>",
                                    televendas: "<?= $plano->televendas ?>",
                                    pagina: "<?= $pagina ?>",
                                }
                            </script>

                        <?php $i++;
                        endforeach; ?>
                    </div>
                </div>

                <?php
                //EXIBE A NAVEGAÇÃO DO PAGINADOR
                $link = URL_BASE . "/produtos/planos/$produto->id/p/";

                //VARIÁVEIS VÊM DO CONTROLADOR AppPedidos
                $paginador->paginas($total, $pagina, $link);
                echo "<br>";
                ?>
        </div>
    <?php else :
    ?>
        <div class="card-body">
            <p><b>Não existem planos cadastrados!</b></p>
        </div>
    <?php endif; ?>

</div>
<!-- FIM CARD -->

</section>

</div>

<!-- cadastar -->
<div class="modal fade" id="modal-lg">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <div class="modal-header" style="background:rgb(2, 2, 2)">
                <h3 class="card-title"><i class="fa fa-info-circle"></i> Cadastrar Planos</h3>
                <button type="button" class="close" data-dismiss="modal"> &times;</button>
            </div>

            <form action="<?= $router->route("produtos.planosCadastro") ?>" method="post" style="font-family:inter; text-transform:uppercase; font-size:11px" name="plano" enctype="multipart/form-data">
                <div class="modal-body" style="background: #f4f6f9">

                    <input type="hidden" name="id" id="id">
                    <input type="hidden" name="status" value="<?= $produto->status ?>">
                    <input type="hidden" name="produto" value="<?= $produto->id ?>">
                    <input type="hidden" name="pagina" value="<?= $pagina ?>">

                    <div class="card card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <label>Nome do plano: *</label>
                                <input type="text" name="nome" id='nome' class="form-control input-lg" placeholder="Nome">
                            </div>

                            <div class="col-md-4">
                                <label>Preço: * <small>mínimo: R$ 5,00</small></label>
                                <input type="text" name="preco" id='preco' class="form-control input-lg" placeholder="Preço *" required>
                            </div>

                            <div class="col-md-4">
                                <label>Quantidade: </label>
                                <input type="text" name="quantidade" id='quantidade' class="form-control input-lg" placeholder="Quantidade *">
                                <small>Quantidade de itens que compõe esse plano</small>
                            </div>
                        </div>

                        <hr>

                        <div class="row">
                            <div class="col-md-4">
                                <label>Máximo de parcelas:</label>
                                <input type="number" max="12" min="1" name="parcelas" id="parcelas" class="form-control">
                                <small>O máximo de parcelas que deseja disponibilizar no checkout. Não tem relação com assumir juros do cartão</small>
                            </div>

                            <div class="col-md-4">
                                <label>Assumir juros do parcelamento:</label>
                                <select name="assumir_juros" id="assumir_juros" class="form-control">
                                    <option value="0">Não</option>
                                    <option value="1">Sim</option>
                                </select>
                                <small>Caso deseje oferecer parcelamento sem juros. O produtor assume os juros do parcelamento</small>
                            </div>

                            <div class="col-md-4" id="parcelamento">
                                <label>Simulação Parcelamento</label>

                                <select name="parcelamento1" id="parcelamento1" class="form-control newform3">
                                    <?php for ($parcela = 1; $parcela <= 12; $parcela++) : ?>
                                        <option value="<?= $parcela ?>" id="parcelas<?= $parcela ?>"></option>
                                    <?php endfor ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <hr>
                    <div class="card card-body">
                        <span><label>Breve Descrição:</label> <small>Não inserir quebra de linha</small></span>
                        <input type="text" name="descricao" id="descricao" class="form-control">
                        <small>máximo 200 carcteres</small>
                    </div>

                    <hr>
                    <div class="card card-body">
                        <!-- COMISSÃO AFILIADO -->
                        <?php if (@$produto->tipo_cobranca != 2) : ?>
                            <div class="row">
                                <!-- <small><b>*Atenção! Caso o plano esteja ativo para o televendas e foi definido uma comissao personalizada para o afiliado no televendas, será priorizado a comissão que está definido no televendas!</b></small> -->
                                <div class="col-md-6">
                                    <br>
                                    <b>Habilitar comissão personalizada para o afiliado?*</b>
                                    <br><br>
                                    <select name="habilitar_comissao_afiliado" id="habilitar_comissao" class="form-control">
                                        <option value="0">Não</option>
                                        <option value="1">Sim</option>
                                    </select>
                                </div>
                                <div class="mostrar_comissao" id="mostrar_comissao" style="margin-left: 5px">
                                    <br>
                                    <div class="row">
                                        <label><b>Comissão Afiliado:</b></label>
                                    </div>

                                    <div class="row" style="margin-top: 2%;">
                                        <div class="col-md-3">
                                            <select name="tipo_comissao1" id="tipo_comissao1" class="form-control">
                                                <option value="0">%</option>
                                                <option value="1">R$</option>
                                            </select>
                                        </div>

                                        <div class="col-md-3">
                                            <input type="text" name="comissao1" id="comissao1" class="form-control" placeholder="0,00">
                                            <br>

                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- FIM COMISSÃO AFILIADO -->

                        <?php endif; ?>

                        <!-- COMISSÃO COPRODUTORES -->
                        <?php if ($coproducao) : $i = 0; ?>
                            <hr>
                            <div class="row">
                                <div class="col-md-4">
                                    <label>Habilitar comissão personalizada de coprodutores cadastrados</label>

                                    <select name="habilitar_comissao_coprodutores" id="habilitar_comissao_coprodutores" class="form-control">
                                        <option value="0">Não</option>
                                        <option value="1">Sim</option>
                                    </select>
                                    <small>Para habilitar é necessário ter coprodutores cadastrados na aba coprodutores e os mesmos tenham aceitado a coprodução</small>
                                </div>

                                <div class="col-md-8 mostrar_comissao_coprodutor">

                                    <?php foreach ($coproducao as $coprodutor) : ?>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <input type="hidden" class="form-control" name="coprodutores[<?= $i ?>]" id="coprodutores<?= $coprodutor->co_produtor ?>" readonly>
                                                <b>Coprodutor:<input type="text" class="form-control" name="coprodutor<?= $coprodutor->co_produtor ?>" id="coprodutor<?= $coprodutor->co_produtor ?>" readonly></b>
                                            </div>

                                            <div class="col-md-2">
                                                <br>
                                                <select name="tipo_comissao_coprodutor[<?= $i ?>]" id="tipo_comissao_coprodutor<?= $coprodutor->co_produtor ?>" class="form-control">
                                                    <option value="0">%</option>
                                                    <option value="1">R$</option>
                                                </select>
                                            </div>

                                            <div class="col-md-4"><br><input type="text" name="comissao_coprodutor[<?= $i ?>]" id="comissao_coprodutor<?= $coprodutor->co_produtor ?>" class="form-control"></div>
                                        </div>
                                        <br>
                                    <?php $i++;
                                    endforeach; ?>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>

                    <?php if ($produto->tipo_cobranca == 2) : ?>
                        <hr>
                        <div class="card card-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <label>Periodicidade:</label>
                                    <select name="periodo" id="periodo" class="form-control">
                                        <option value="Semanal">Semanal</option>
                                        <option value="Mensal" selected>Mensal</option>
                                        <option value="Bimestral">Bimestral</option>
                                        <option value="Trimestral">Trimestral</option>
                                        <option value="Semestral">Semestral</option>
                                        <option value="Anual">Anual</option>
                                        <option value="Trienal">Trienal</option>
                                    </select>
                                </div>

                                <div class="col-md-4">
                                    <label>Máximo de recorrência: </label>
                                    <input type="text" name="maximo_recorrencia" id='maximo_recorrencia' class="form-control input-lg" placeholder="">
                                    <small>Deixe em branco para a assinatura ser cobrada por tempo inderteminado</small>
                                </div>

                                <div class="col-md-4">
                                    <label>Primeira parcela:</label>
                                    <select name="primeira_parcela" id="primeira_parcela" class="form-control">
                                        <option value="igual">Igual as demais</option>
                                        <option value="gratis">Grátis</option>
                                        <option value="preco">Preço diferenciado</option>
                                        <option value="carencia">Carência</option>
                                    </select>
                                </div>

                                <div class="col-md-4">
                                    <br>
                                    <div class="preco1">
                                        <label>Preço primeira parcela: *</label>

                                        <div class="input-group mb-3">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text" id="basic-addon1">R$</span>
                                            </div>
                                            <input type="text" name="preco_parcela" id='preco_parcela' class="form-control input-lg">
                                        </div>
                                    </div>

                                    <div class="carencia1">
                                        <label>Tempo de carência: *</label>

                                        <div class="input-group mb-3">
                                            <input type="text" name="carencia" id='carencia' class="form-control input-lg">
                                            <div class="input-group-append">
                                                <span class="input-group-text" id="basic-addon1">dias</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                    <label>Comissão Afiliado: (%)</label>
                                    <input type="text" name="comissao_afiliado_assinatura" id="comissao_afiliado_assinatura" class="form-control">
                                    <small>Comissão para afiliado a partir da segunda cobrança. Coloque 0 (zero) caso não queira que o afiliado receba comissão</small>
                                </div>
                                <div class="col-md-4"></div>
                                <div class="col-md-4"></div>
                            </div>
                        </div>
                    <?php endif ?>

                    <hr>
                    <div class="card card-body">
                        <div class="row">
                            <div class="col-md-6">

                                <label>Imagem do plano:</label> 200px X 200px, max.: 2mb
                                <input type="file" name="foto" id="foto" class="form-control"><br>

                                <input type="hidden" class="form-control" id="foto_atual" name="foto_atual">
                                <input type="hidden" class="form-control" id="ft_atual" name="ft_atual">

                                <button id="excluir-foto" class="btn btn-link text-danger" title="Remover imagem atual">&times;</button>
                                <img src="" alt="" id="imagem_foto_atual" width="100%">

                                <br>
                            </div>
                        </div>
                    </div>

                    <hr>
                    <div class="card card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <label>Plano privado:</label>
                                <select name="privado" id="privado" class="form-control">
                                    <option value="0">Não</option>
                                    <option value="1">Sim</option>
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label>Ativar Plano:</label>
                                <select name="ativo" id="ativo" class="form-control">
                                    <option value="0">Não</option>
                                    <option value="1" selected>Sim</option>
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label>Habilitar para ecommerce</label>
                                <select name="habilitar_ecommerce" id="habilitar_ecommerce" class="form-control">
                                    <option value="0">Não</option>
                                    <option value="1">Sim</option>
                                </select>
                            </div>

                            <!-- <div class="col-md-4">
                                <label>Ativar plano para televendas: <span class="badge bg-azul-dark">Em breve</span></label>
                                <select name="televendas" id="televendas" class="form-control">
                                    <option value="0">Não</option>
                                    <option value="1">Sim</option>
                                </select>
                            </div> -->
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
        <div class="mensagens"><a href="https://octapay.com.br/tutoriais/cadastrar-ou-editar-um-plano/" target="_blank">Dúvidas sobre o Cadastro ou Edição de Planos</a></div>
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
    function juros() {
        if ($("#assumir_juros").val() == 1) {

            $("#parcelamento").show();

            var capital = parseFloat($("#preco").val());
            // var capital = 150;
            var i = 0.0299;
            var valor = 0;
            var total = 0;

            for (n = 1; n <= 12; n++) {
                if (n == 1) {
                    juros = capital;
                } else if (n == 2) {
                    valor = capital + (capital * 0.0519);
                    valor = valor / n;
                    juros = valor;
                } else {
                    t = 1 + i;
                    exp = Math.pow(t, (-n));
                    juros = (i / (1 - exp)) * capital;
                }

                total = juros * n;
                total = total.toLocaleString("pt-br", {
                    style: 'currency',
                    currency: 'BRL'
                });
                juros = juros.toLocaleString("pt-br", {
                    style: 'currency',
                    currency: 'BRL'
                });

                console.log(juros);
                $("#parcelas" + n).html(n + "X de " + juros + " = " + total);
            }
        } else {
            $("#parcelamento").hide();
        }
    }

    $("#parcelamento").hide();

    $("#assumir_juros,#preco").change(() => {
        if ($("#assumir_juros").val() == 1) {
            $("#parcelamento").show();
            juros();
        } else {
            $("#parcelamento").hide();
        }
    });

    // var width = 0;
    // setInterval(() => {
    //     $(".carrega-content").html(" . ");
    //     $(".carrega-content").css("width", width + "%");
    //     width += 3.33;
    // }, 100);

    // $(".efeito-slow").hide();
    // setTimeout(function() {
    //     $(".carregar").hide("slow");
    //     $(".efeito-slow").show();
    // }, 3000);

    $("#preco").blur(function(e) {
        var valor = $("#preco").val();
        $("#preco").val(valor.replace(",", "."));

        if (valor < 5) {
            $("#preco").focus();
            alert("Valor do produto abaixo do mínimo permitido. Valor mínimo R$5,00");
        }
    });

    $("#parcelas").blur(function(e) {
        var valor = $("#parcelas").val();

        if (valor > 12) {
            $("#parcelas").prop("value", 12);
        }

        if (valor < 1) {
            $("#parcelas").prop("value", 1);
        }

        var val = $("#preco").val();

        if ((val / valor) < 5) {
            var minimo = val / 5;
            alert("O valor mínimo da parcela precisa ser de R$ 5,00. Para isso você precisa colocar o máximo de parcelas em " + minimo + " vezes");
        }
    });

    $("#comissao1").blur(function(e) {
        var valor = $("#comissao1").val();
        $("#comissao1").val(valor.replace(",", "."));
    });

    $("#cadastro").click(() => {

        $(".modal-title").html("Cadastrar Planos");
        $("#salvar").css("display", "inline");
        $("#cancelar").css("display", "inline");
        $("#editar").css("display", "none");
        $(".mostrar_comissao").css("display", "none");
        $("#habilitar_comissao option[value=" + 0 + "]").prop({
            "selected": true
        });
        $("#habilitar_comissao_coprodutores[value= " + 0 + "]").prop({
            "selected": true
        });
        // console.log()
        $("#comissao1").val(0.00);
        cancelar();
        console.log($("#valor_desconto").val());

    });

    $("#salvar1").click(function(e) {
        e.preventDefault();
        salvarEditar(1);
    });

    $("#editar1").click(function(e) {
        e.preventDefault();
        salvarEditar(0);
    });

    $("#cancelar").click(function(e) {
        e.preventDefault();
        cancelar();
    });

    function salvarEditar(salvar) {

        if (salvar == 1) {
            url = "<?= $router->route("produtos.planosCadastro"); ?>";
        } else {
            url = "<?= $router->route("produtos.planosEditar") ?>";
        }

        console.log(url);
        var nome = $("#nome").val(),
            preco = $("#preco").val(),
            quantidade = $("#quantidade").val();

        var form = $("form");

        if (nome == "" || preco == "" || quantidade == "") {

            alert("Preecha todos os dados necessários");

        } else {
            console.log(form.serialize());
            $.ajax({

                url: url,
                data: form.serialize(),
                type: "POST",
                //datatype: "json",
                beforeSend: function() {
                    console.log("atualizando");
                },
                error: function() {
                    console.log("O servidor não conseguiu processar o pedido");
                },
                success: function(callback) {
                    console.log("callback ", callback);
                    // alert(callback);
                    // location.reload();
                }

            });
        }
    }

    $(".preco1").hide();
    $(".carencia1").hide();
    $("#primeira_parcela").change(() => {
        var tipo = $("#primeira_parcela").val();

        if (tipo == "preco") {
            $(".preco1").show();
            $(".carencia1").hide();
        } else if (tipo == "carencia") {
            $(".preco1").hide();
            $(".carencia1").show();
        } else {
            $(".preco1").hide();
            $(".carencia1").hide();
        }
    });

    $("#excluir-foto").hide();
    $(".mostrar_comissao_coprodutor").css("display", "none");

    // INICIO SESSAO COMISSAO AFILIADO
    $("#habilitar_comissao").change((e) => {
        var hab_comissao = $("#habilitar_comissao").val();
        console.log(hab_comissao);
        if (hab_comissao == 1) {
            $(".mostrar_comissao").css("display", "inline");
            // televendas = 0;
        } else {
            $(".mostrar_comissao").css("display", "none");
            // televendas = 1;
        }
    });

    $("#habilitar_comissao_coprodutores").change((e) => {
        var hab_comissao = $("#habilitar_comissao_coprodutores").val();
        console.log(hab_comissao);
        if (hab_comissao == 1) {
            $(".mostrar_comissao_coprodutor").css("display", "inline");
            hab_comissao = 0;
        } else {
            $(".mostrar_comissao_coprodutor").css("display", "none");
            hab_comissao = 1;
        }
    });
    // FIM SESSAO COMISSAO AFILIADO

    function editar(i) {
        cancelar();

        $("form[name=plano]").attr("action", "<?= $router->route("produtos.planosEditar") ?>");

        var id = dados[i]["id"];
        var nome = dados[i]["nome"];
        var preco = dados[i]["preco"];
        var foto = dados[i]["foto"];
        var assumir_juros = dados[i]["assumir_juros"];
        var parcelas = dados[i]["parcelas"];
        var periodo = dados[i]["periodo"];
        var maximo_recorrencia = dados[i]["maximo_recorrencia"];
        var primeira_parcela = dados[i]["primeira_parcela"];
        var preco_parcela = dados[i]["preco_parcela"];
        var carencia = dados[i]["carencia"];

        var habilitar_comissao_coprodutores = dados[i]["habilitar_comissao_coprodutores"];
        var comissao_coprodutores = dados[i]["comissao_coprodutores"];

        var habilitar_comissao_afiliado = dados[i]["habilitar_comissao_afiliado"];
        var comissao_afiliado_assinatura = dados[i]["comissao_afiliado_assinatura"];

        var tipo_comissao_afiliado = dados[i]["tipo_comissao_afiliado"];
        var comissao_afiliado = dados[i]["comissao_afiliado"];
        var quantidade = dados[i]["quantidade"];
        var descricao = dados[i]["descricao"];
        var privado = dados[i]["privado"];
        var ativo = dados[i]["ativo"];
        var habilitar_ecommerce = dados[i]["habilitar_ecommerce"];
        var televendas = dados[i]["televendas"];

        console.log(id, preco, nome, quantidade, foto, privado, ativo, comissao_coprodutores);

        $(".modal-title").html("Editar Usuário " + nome);
        $("#salvar").css("display", "none");
        $("#editar").css("display", "inline");
        $("#cancelar").css("display", "inline");
        $("#id").attr("value", id);
        $("nome").attr("value", nome);
        $("#preco").attr("value", preco);
        $("#descricao").attr("value", descricao);
        $("#assumir_juros option[value=" + assumir_juros + "]").prop("selected", true);

        if ($("#assumir_juros").val() == 1)
            juros();

        if (tipo_comissao_afiliado) {
            $("#tipo_comissao1 option[value=" + tipo_comissao_afiliado + "]").prop("selected", true);
        }
        // SESSÃO COMISSAO AFILIADO
        if (habilitar_comissao_afiliado == 0) {
            $(".mostrar_comissao").css("display", "none");
        } else {
            $(".mostrar_comissao").css("display", "inline");
        }

        if (habilitar_comissao_afiliado) {
            $("#habilitar_comissao option[value=" + habilitar_comissao_afiliado + "]").prop("selected", true);
        }
        //  FIM SESSAO COMISSAO AFILIADO

        // COMISSÃO COPRODUTORES
        if (habilitar_comissao_coprodutores == 0) {
            $(".mostrar_comissao_coprodutor").css("display", "none");
        } else {
            $(".mostrar_comissao_coprodutor").css("display", "inline");
        }

        if (habilitar_comissao_coprodutores) {
            $("#habilitar_comissao_coprodutores option[value=" + habilitar_comissao_coprodutores + "]").prop("selected", true);

            var com = comissao_coprodutores.split(",");
            console.log(com, com.length);
            for (var i = 0; i < com.length; i++) {
                var coprodutor = com[i].split("|");

                $("#coprodutores" + coprodutor[0]).attr("value", coprodutor[0]);
                $("#tipo_comissao_coprodutor" + coprodutor[0] + " option[value=" + coprodutor[1] + "]").prop("selected", true);
                $("#comissao_coprodutor" + coprodutor[0]).attr("value", coprodutor[2]);
            }
        }
        // FIM COMISSÃO COPRODUTORES

        $("#comissao1").prop("value", comissao_afiliado);

        $("#parcelas").prop("value", parcelas);

        if (periodo) {
            $("#periodo option[value=" + periodo + "]").prop("selected", true);
            $("#maximo_recorrencia").attr("value", maximo_recorrencia);
            $("#primeira_parcela option[value=" + primeira_parcela + "]").prop("selected", true);
            $("#preco_parcela").prop("value", preco_parcela);
            $("#carencia").prop("value", carencia);
            $("#comissao_afiliado_assinatura").prop("value", comissao_afiliado_assinatura);
        }

        if (primeira_parcela == "preco") {
            $(".preco1").show();
            $(".carencia1").hide();
        } else if (primeira_parcela == "carencia") {
            $(".preco1").hide();
            $(".carencia1").show();
        } else {
            $(".preco1").hide();
            $(".carencia1").hide();
        }
        console.log(nome);

        $("#nome").attr("value", nome);
        $("#quantidade").attr("value", quantidade);

        $("#descricao").prop("value", descricao);
        $("#privado option[value=" + privado + "]").prop("selected", true);
        $("#ativo option[value=" + ativo + "]").prop("selected", true);
        $("#habilitar_ecommerce option[value=" + habilitar_ecommerce + "]").prop("selected", true);
        // $("#televendas option[value=" + televendas + "]").prop("selected", true);

        if (foto) {
            $("#excluir-foto").show();
            $("#foto_atual, #ft_atual").attr("value", foto);

            $("#imagem_foto_atual").prop({
                "src": "<?= url("uploads/produtos/") ?>" + foto,
                "width": 150,
                "height": 150
            });
        }
    }

    $("#excluir-foto").click((e) => {
        e.preventDefault();
        $("#foto_atual").attr("value", "");
        $("#imagem_foto_atual").hide("slow");
        $("#excluir-foto").hide("slow");
    });

    function cancelar() {
        $("form[name=plano]").attr("action", "<?= $router->route("produtos.planosCadastro") ?>");
        $("#salvar").css("display", "inline");
        $("#editar").css("display", "none");
        $("#cancelar").css("display", "inline");
        $("#id").attr("value", "");
        $("#preco").attr("value", "");
        $("#descricao").attr("value", "");
        $("#assumir_juros").prop("selected", false);
        $("#nome").attr("value", "");
        $("#quantidade").attr("value", "");
        $("#parcelas").prop("value", "");

        $("#periodo option").prop("selected", false);
        $("#maximo_recorrencia").attr("value", "");
        $("#primeira_parcela option").prop("selected", false);
        $("#preco_parcela").prop("value", "");
        $("#carencia").prop("value", "");

        $("#descricao").prop("value", "");
        $("#privado option").prop("selected", false);
        $("#ativo option").prop("selected", false);
        $("#habilitar_ecommerce option").prop("selected", false);

        $("#foto_atual, #ft_atual").attr("value", "");

        $("#imagem_foto_atual").prop("src", "");

        $("#habilitar_comissao_afiliados option").prop("selected", false);

        $("#habilitar_comissao_coprodutores option").prop("selected", false);
        <?php if ($coproducao) : ?>
            <?php foreach ($coproducao as $coprodutor) : ?>
                var tipo_comissao = "<?= $coprodutor->tipo_comissao ?>";
                $("#coprodutores" + <?= $coprodutor->co_produtor ?>).attr("value", "<?= $coprodutor->co_produtor ?>");
                $("#coprodutor" + <?= $coprodutor->co_produtor ?>).attr("value", "<?= $coprodutor->dataCoprodutor()->nome ?>");
                $("#tipo_comissao_coprodutor" + <?= $coprodutor->co_produtor ?> + " option[value=" + tipo_comissao + "]").prop("selected", true);
                $("#comissao_coprodutor" + <?= $coprodutor->co_produtor ?>).attr("value", "<?= $coprodutor->comissao ?>");
            <?php endforeach; ?>
        <?php endif; ?>
    }

    function duplicar(id) {
        $("form[name=linha" + id + "]").attr("action", "<?= $router->route("produtos.planosCadastro") ?>");
        // console.log(form.serialize());

        // $.ajax({

        //     url: "<?= $router->route("produtos.planosCadastro"); ?>",
        //     data: form.serialize(),
        //     type: "POST",
        //     //datatype: "json",
        //     beforeSend: function() {
        //         console.log("atualizando");
        //     },
        //     error: function() {
        //         console.log("O servidor não conseguiu processar o pedido");
        //     },
        //     success: function(callback) {
        //         console.log("callback ", callback);
        //         alert(callback);
        //         location.reload();
        //     }

        // });

    }

    function excluir(id) {
        console.log(id);
        if (confirm("Tem certeza que deseja excluir esse plano?")) {
            $.ajax({
                url: "<?= $router->route("produtos.planosExcluir") ?>",
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