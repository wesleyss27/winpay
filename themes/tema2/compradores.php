<?php
//session_start();
$acesso = access($_SESSION["permissao"]);
?>

<?php $v->layout("_theme"); ?>

<div class="conteudo">
    <?= $v->insert("fragmentos/msg-cadastrar-editar", []); ?>

    <div class="row">
        <div class="col-md-12">
            <div class="filtros">
                <?= $v->insert("fragmentos/filtros/filtros-compradores", ["compradores" => $compradores]); ?>
            </div>
        </div>
    </div>
    
    <div class="row">
        <?php if($acesso == 4 || $acesso == 5 || $acesso == 3): ?>
            <div class="col-md-2">
                <button type="button" class="btn btn-primary col-md-12" data-toggle="modal" data-target="#myModal" id="cadastro">Cadastrar</button>
            </div>
        <?php endif; ?>
        Total de Compradores: <?= @$count; ?>
    </div>

    <div class="row">
        <div class="col-md-12">
        <?php if($compradores): ?>
            <table class="table table-striped">
                <thead class="thead-dark">
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Nome</th>
                        <th scope="col">Email</th>
                        <th scope="col">Telefone</th>
                        <th scope="col">CPF</th>
                        <th scope="col">Local</th>

                        <?php if($acesso == 4 || $acesso == 5): ?>
                        <th scope="col">Ação</th>
                        <?php endif; ?>
                    </tr>
                </thead>

                <tbody>
                <?php
                    foreach($compradores as $comprador):
                        $v->insert("fragmentos/listar-compradores", ["comprador" => $comprador, "acesso" => $acesso]);
                    endforeach;
                ?>
                </tbody>
            </table>

        <?php

        //EXIBE A NAVEGAÇÃO DO PAGINADOR
        $uri = explode("?", $_SERVER["REQUEST_URI"], 2);
        $link = URL_BASE."/compradores/";

        //VARIÁVEIS VÊM DO CONTROLADOR AppPedidos
        $paginador->paginas($total, $pagina, $link, @$uri[1]);
        
        else:
        ?>
        <h4>Não existem usuários cadastrados ou ativos!</h4>
        <p>Lorem ipsum, dolor sit amet consectetur adipisicing elit.</p>
        <?php
        endif;
        ?>
        </div>
    </div>
</div>

<!-- CHAMA O MODAL -->
<?= $v->insert("fragmentos/modal-compradores-cadastrar", []); ?>
