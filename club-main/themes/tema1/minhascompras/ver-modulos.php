<!-- 8 tabs para ficar aninhado com o restante do codigo -->

<div id="34811" class="card mb-5">
    <div class="card-header">
        <h2 class="mb-0"><?= $modulos->nome; ?></h2>
    </div>

    <div class="card-body">
        <ul class="list-group list-group-flush my-n3">
            <?php
            $count = [];
            if (!empty($aulas)) :
                foreach ($aulas as $aula) :
                    if ($aula->modulo == $modulos->id) :
                        $count[] = $modulos->id;
            ?>
                        <li data-id="233811" class="list-group-item text-base d-flex pr-0 border-light sortable-hover">
                            <div class="mr-3">
                                <i class="far fa-play-circle icon_color"></i>
                            </div>

                            <div>
                                <a class="text-gray align-middle" href="<?= url("minhas-compras/viewaulas/" . $aula->id) ?>"><?= $aula->nome; ?></a>
                            </div>
                        </li>
            <?php
                    endif;
                endforeach;
            endif;
            ?>
        </ul>
    </div>
</div>