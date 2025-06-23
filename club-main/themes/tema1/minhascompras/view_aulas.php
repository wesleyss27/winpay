<?php
$v->layout("_theme");


foreach ($all_aulas as $aul_key => $aul_val) {
  if ($aul_val->id == $id) {
    $current_key = $aul_key;

    $prev_key = $current_key - 1;
    if (!empty($prev_key) && $prev_key > -1) {

      $url_l = url("minhas-compras/viewaulas/" . $all_aulas[$prev_key]->id);
    } elseif ($prev_key == 0) {
      $url_l = url("minhas-compras/viewaulas/" . $all_aulas[0]->id);
    } else {

      $url_l = url("minhas-compras/viewaulas/" . $id);
    }

    $next_key = $current_key + 1;
    if (!empty($next_key) && $next_key < count($all_aulas)) {
      $url_2 = url("minhas-compras/viewaulas/" . $all_aulas[$next_key]->id);
    } else {
      $url_2 = url("minhas-compras/viewaulas/" . $id);
    }
  }
}
?>

<style>
/* Fundo geral */
body, .content-wrapper, .container-fluid, .header, .card {
  background-color: #000 !important;
  color: #fff;
}

/* Player de vídeo */
.video_platform iframe {
  border-radius: 12px;
  border: none;
}

/* Sidebar (playlist de aulas) */
.playlist-item {
  background-color: #141414;
  border-radius: 10px;
  margin-bottom: 8px;
  padding: 8px 12px;
  transition: background-color 0.3s ease;
}

.playlist-item:hover {
  background-color: #1f1f1f;
}

.playlist-item.active {
  background-color: rgb(217, 192, 0);
  color: #000;
  font-weight: bold;
}

.playlist-item.active div,
.playlist-item.active span {
  color: #000 !important;
}

/* Texto na playlist */
.playlist-item .col {
  color: #fff;
}

/* Barra de progresso */
.progress {
  background-color: #333;
  border-radius: 20px;
}

.progress-bar {
  background-color: rgb(217, 192, 0);
  color: #000;
  font-weight: 600;
}

/* Botões */
.btn {
  border-radius: 8px;
  padding: 8px 16px;
  font-weight: 600;
}

.btn-primary,
.btn-success {
  background-color: rgb(217, 192, 0);
  border-color: rgb(217, 192, 0);
  color: #000;
}

.btn-primary:hover,
.btn-success:hover {
  background-color: rgba(217, 192, 0, 0.9);
}

.btn-gray {
  background-color: #1f1f1f;
  border-color: #333;
  color: #fff;
}

.btn-gray:hover {
  background-color: #333;
}

/* Breadcrumb */
.breadcrumb {
  background: none;
}

.breadcrumb-item a {
  color: rgb(217, 192, 0);
}

.breadcrumb-item a:hover {
  text-decoration: underline;
}

/* Títulos */
h1, h2, h5, h4 {
  color: rgb(217, 192, 0);
  font-weight: 600;
}

/* Textos */
p, .comment-text, .comment-time {
  color: #ccc;
}

/* Área de materiais extras */
.card-body div[style*="padding: 15px"] {
  background-color: #141414 !important;
  color: #fff;
  border-radius: 10px;
}

/* Comentários */
.comentario, .resposta {
  border-radius: 10px;
  background-color: #141414 !important;
  padding: 10px;
  color: #fff;
}

/* Input, textarea */
textarea, input {
  background-color: #fff;
  color: #000;
  border-radius: 8px;
  border: 1px solid #444;
}

textarea:focus, input:focus {
  border-color: rgb(217, 192, 0);
  box-shadow: 0 0 0 2px rgba(217, 192, 0, 0.4);
}

/* Avatar */
.avatar img, .avatar-img {
  border-radius: 50%;
  border: 2px solid rgb(217, 192, 0);
}

/* Estrelas */
.estrela {
  color: #bbb;
  cursor: pointer;
}

.estrela:hover, .estrela.active {
  color: #FFD700;
}

/* Scroll playlist */
.scrollbar {
  overflow-y: auto;
}

.scrollbar::-webkit-scrollbar {
  width: 8px;
}

.scrollbar::-webkit-scrollbar-thumb {
  background-color: #555;
  border-radius: 10px;
}

.scrollbar::-webkit-scrollbar-thumb:hover {
  background-color: #777;
}

/* Responsivo */
@media (max-width: 768px) {
  .m-none {
    display: none;
  }

  .playlist-item .col {
    font-size: 14px;
  }
}
</style>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">

  <!-- Content Header (Page header) -->
  <section class="content-header">

    <!-- CONTEINER-FLUID -->
    <div class="container-fluid">

      <!-- SEÇÃO PLAYER VIDEO E AULAS -->
      <div class="header bg-dark-octa mb-0">

        <!-- CONTAINER -->
        <div class="container">

          <!-- ROW NO-GUTTERS -->
          <div class="row no-gutters">
            <div class="col-md-8">

              <!-- VIDEO PLAYER -->
              <div class="video_platform">
                <?php
                $url = $aula->link_video;

                if (strpos($url, 'youtube') !== false || strpos($url, 'youtu.be') !== false) {   // https://www.youtube.com/watch?v=YKKMtGhVdhc

                  $youtube_url = 'https://www.youtube.com/embed/';

                  // Now get the 'v=' value & stick on the end
                  preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $url, $matches);
                  $v1 = $matches[1];

                  // Final result
                  $new_url = '<iframe width="100%" height="480" src="' . $youtube_url . $v1 . '" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>';
                } elseif (strpos($url, 'vimeo') > 0) {   // https://player.vimeo.com/video/116582567

                  $vimeo_url = 'https://player.vimeo.com/video/';

                  // Now get the last part of the url
                  $v1 = substr(strrchr($url, '/'), 1);

                  // Final result
                  $new_url = '<iframe width="560" height="315" src="' . $vimeo_url . $v1 . '" frameborder="0" allow="autoplay; fullscreen" allowfullscreen></iframe>';
                } elseif ($aula->script_player) {
                  $new_url = base64_decode($aula->script_player);
                } else {
                  $new_url = '<iframe width="640" height="360" src="' . $aula->link_video . '" frameborder="0" allow="autoplay; fullscreen" allowfullscreen loading="lazy"></iframe>';
                }

                ?>
                <?= $new_url; ?>
              </div>
              <!-- FIM VIDEO PLAYER -->

            </div>

            <!-- LISTAGEM DE AULAS MÓDULO -->
            <div class="col-lg-4 col-12 col-md-4 col-sm-12 text-white position-relative d-none d-lg-block">
              <div class="position-absolute fixed-top h-100 scroll-vertical scrollbar">
                <?php
                $i = 1;
                foreach ($all_aulas as $aulas_val) {
                  $imagem = $aulas_val->imagem ? $aulas_val->imagem : $curso->imagem;
                ?>
                  <a class="row align-items-center no-gutters py-3 px-4 playlist-item <?= ($aulas_val->id == $id) ? "active" : "" ?>" href="<?= url("minhas-compras/viewaulas/" . $aulas_val->id) ?>">
                    <div class="col-1 font-weight-bold text-white"><?= $i; ?></div>

                    <div class="col-auto">
                      <img width="70" height="40" class="d-block mr-3" src="<?= url("uploads/cursos/" . $imagem); ?>" />
                    </div>

                    <div class="col">
                      <?= $aulas_val->nome; ?>
                    </div>
                  </a>
                <?php $i++;
                } ?>

              </div>
            </div>
            <!-- FIM LISTAGEM AULAS MÓDULO -->

          </div>
          <!-- FIM ROW NO-GUTTERS -->

        </div>
        <!-- FIM CONTAINER -->

      </div>
      <!-- FIM HEADER -->

      <br>

      <!-- MAIN-CONTENT -->
      <div class="card card-body">

        <!-- LEARNING -->
        <div class="row">

          <!-- CONTAINER LEARNING -->
          <div class="col-md-8">

            <!-- NAVEGAÇÃO MÓDULO -->
            <div class="row">
              <div class="col-md-12 col-9">
                <ol class="breadcrumb py-0 mb-1 small leading-sm">
                  <li class="breadcrumb-item d-inline-flex align-items-center">
                    <a href="<?= url("minhas-compras/viewcursos/" . $curso->id) ?>"><?= $curso->nome; ?></a>
                  </li>
                  <li class="breadcrumb-item d-inline-flex align-items-center">
                    <a href="<?= url("minhas-compras/viewcursos/" . $curso->id) ?>"> <?= $modulo->nome; ?></a>
                  </li>
                </ol>
              </div>
              <div class="d-lg-none col-3">
                <!-- PAGINADOR VOLTAR E PRÓXIMO -->
                <div>

                  <div class="col-auto d-flex" style="margin-bottom: 10px">
                    <!-- <a class="btn btn-white d-inline-flex align-items-center mr-2" title="Hora do Show!" @click.prevent="showOverlay" v-b-tooltip.hover="true" href="#!"><img src="<?= url("themes/tema1/assets/imgs/icons/light.svg") ?>" width="20px"></a> -->
                    <div class="btn-group pagination">
                      <a class="btn btn-gray btn-sm" title="Anterior" v-b-tooltip.hover="true" href="<?= $url_l; ?>">
                        <i class="fas fa-chevron-left"></i>
                      </a>
                      <a class="btn btn-gray btn-sm" title="Próximo" v-b-tooltip.hover="true" href="<?= $url_2; ?>">
                        <i class="fas fa-chevron-right"></i>
                      </a>
                    </div>
                  </div>
                </div>
                <!-- FIM PAGINADOR VOLTAR E PRÓXIMO -->
              </div>
            </div>
            <!-- FIM NAVEGAÇÃO MÓDULO -->

            <h1 class="header-title"><?= $aula->nome; ?></h1>

            <p><?= $aula->descricao; ?></p>


            <div style="padding: 15px; width: 100%; background: #ddd; border-radius: 8px">
              <h5>Material Extra</h5>
              <?php if (!empty($aula->arquivo_anexo1)) : ?>
                <i class="fa-solid fa-download"></i> <a href="<?= url("uploads/cursos/" . $aula->arquivo_anexo1); ?>" download>Baixar Anexo 1</a>
              <?php endif; ?>

              <?php if (!empty($aula->arquivo_anexo2)) : ?>
                <i class="fa-solid fa-download"></i> <a href="<?= url("uploads/cursos/" . $aula->arquivo_anexo2); ?>" download>Baixar Anexo 2</a>
              <?php endif ?>

              <?php if (!empty($aula->arquivo_anexo3)) : ?>
                <i class="fa-solid fa-download"></i> <a href="<?= url("uploads/cursos/" . $aula->arquivo_anexo3); ?>" download>Baixar Anexo 3</a>
              <?php endif ?>
            </div>
            <hr>

            <!-- SEÇÃO COMENTÁRIOS E AVALIAÇÃO -->
            <!-- AVALIAÇÃO MOBILE -->

            <div class="d-lg-none mb-5">
              <div class="card">
                <div class="card-body">
                  <div class="d-flex justify-content-between flex-wrap">
                    <h2>Avaliação</h2>
                    <i class="fa-solid fa-star estrela" id="starm1" onmouseover="marcada(1, 'starm')" onmouseout="normal(1, 'starm')" onclick="avaliar(1)"></i>
                    <i class="fa-solid fa-star estrela" id="starm2" onmouseover="marcada(2, 'starm')" onmouseout="normal(2, 'starm')" onclick="avaliar(2)"></i>
                    <i class="fa-solid fa-star estrela" id="starm3" onmouseover="marcada(3, 'starm')" onmouseout="normal(3, 'starm')" onclick="avaliar(3)"></i>
                    <i class="fa-solid fa-star estrela" id="starm4" onmouseover="marcada(4, 'starm')" onmouseout="normal(4, 'starm')" onclick="avaliar(4)"></i>
                    <i class="fa-solid fa-star estrela" id="starm5" onmouseover="marcada(5, 'starm')" onmouseout="normal(5, 'starm')" onclick="avaliar(5)"></i>

                    <?php if (!$aulaConcluida) : ?>
                      <button type="button" id="btn-concluido-mobile" class="btn btn-block btn-success btn-bordered">Concluído</button>
                    <?php endif ?>
                  </div>
                </div>
              </div>
            </div>

            <!-- FIM AVALIAÇÃO MOBILE -->

            <!-- COMENTÁRIOS -->

            <div class="d-flex justify-content-between align-items-center mb-4">
              <h2 class="mb-0">Comentários</h2>
            </div>

            <div class="comentarios">
              <form action="<?= $router->route("aulas.comentar") ?>" name="comentar" method="post">
                <input type="hidden" name="produtor" id="produtor" value="<?= $curso->usuario ?>">
                <input type="hidden" name="modulo" id="modulo" value="<?= $aula->modulo ?>">
                <input type="hidden" name="curso" id="curso" value="<?= $curso->id ?>">
                <input type="hidden" name="aula" id="aula" value="<?= $aula->id ?>">
                <input type="hidden" name="avaliacao" id="avaliacao">

                <?php if ($avaliacao) : ?>
                  <input type="hidden" name="avaliacao_id" id="avaliacao_id" value="<?= $avaliacao->id ?>">
                  <input type="hidden" name="nota" id="nota" value="<?= $avaliacao->avaliacao ?>">
                <?php endif ?>

                <div class="row">
                  <div class="col-md-1">
                    <div class="avatar avatar-sm">
                      <?php if (@$usuario->foto) : ?>
                        <img src="<?= url_octa("uploads/perfil/{$usuario->foto}"); ?>" alt="" width="40px" height="40px" style="border-radius: 50%">
                      <?php else : ?>
                        <img width="40" height="40" class="avatar-img rounded-circle" src="https://static-00.iconduck.com/assets.00/avatar-icon-2048x2048-ilrgk6vk.png" />
                      <?php endif; ?>
                    </div>
                  </div>
                  <div class="col-md-11">
                    <textarea name="comentario" id="comentario" class="form-control" cols="30" rows="2"></textarea><br>

                    <div class="right" style="float: right; margin-top: 10px">
                      <button type="button" class="btn btn-primary" id="btn_comentar">Comentar</button>
                    </div>
                  </div>
                </div>
              </form>
            </div>
            <br>
            <?php if ($comentarios) : ?>
              <div class="my-4 comments">
                  <?php foreach ($comentarios as $comentario) : ?>
                      <?php 
                          $respostas = $resposta->find(
                              "resposta IS NOT NULL AND aula = {$aula->id} AND resposta = {$comentario->id}"
                          )->fetch(true); 
                      ?>

                      <div class="comment mb-3">
                          <div class="row">
                              <div class="col-auto">
                                  <div class="avatar">
                                      <img src="<?= $comentario->dataAluno()->foto ? url_octa("uploads/perfil/{$comentario->dataAluno()->foto}") : 'https://static-00.iconduck.com/assets.00/avatar-icon-2048x2048-ilrgk6vk.png' ?>" width="40" height="40" alt="Avatar">
                                  </div>
                              </div>
                              <div class="col ml-n2">
                                  <div class="comment-body">
                                      <div class="d-flex justify-content-between">
                                          <h5 class="comment-title"><?= $comentario->dataAluno()->nome ?></h5>
                                          <div class="comment-time"><?= formataDataHora($comentario->created_at) ?></div>
                                      </div>
                                      <div class="comment-text"><?= $comentario->comentario ?></div>

                                      <button class="btn-responder" onclick="responder(<?= $comentario->id ?>)">Responder</button>

                                      <div id="div_responder<?= $comentario->id ?>" style="display: none;" class="mt-2">
                                          <form action="<?= $router->route("aulas.comentar") ?>" method="post">
                                              <input type="hidden" name="produtor" value="<?= $curso->usuario ?>">
                                              <input type="hidden" name="modulo" value="<?= $aula->modulo ?>">
                                              <input type="hidden" name="curso" value="<?= $curso->id ?>">
                                              <input type="hidden" name="aula" value="<?= $aula->id ?>">
                                              <input type="hidden" name="resposta" value="<?= $comentario->id ?>">

                                              <input type="text" class="input-resposta" name="comentario" placeholder="Deixe seu comentário">

                                              <button type="button" class="btn btn-sm btn-gray" onclick="cancelar(<?= $comentario->id ?>)">Cancelar</button>
                                              <button type="button" class="btn btn-sm btn-primary" onclick="btnResponder(<?= $comentario->id ?>)">Comentar</button>
                                          </form>
                                      </div>
                                  </div>

                                  <!-- RESPOSTAS -->
                                  <?php if ($respostas) : ?>
                                      <?php foreach ($respostas as $resp) : ?>
                                          <div class="comment mt-3 ml-5">
                                              <div class="row">
                                                  <div class="col-auto">
                                                      <div class="avatar">
                                                          <img src="<?= $resp->dataAluno()->foto ? url_octa("uploads/perfil/{$resp->dataAluno()->foto}") : 'https://static-00.iconduck.com/assets.00/avatar-icon-2048x2048-ilrgk6vk.png' ?>" width="40" height="40" alt="Avatar">
                                                      </div>
                                                  </div>
                                                  <div class="col ml-n2">
                                                      <div class="comment-body">
                                                          <div class="d-flex justify-content-between">
                                                              <h5 class="comment-title"><?= $resp->dataAluno()->nome ?></h5>
                                                              <div class="comment-time"><?= formataDataHora($resp->created_at) ?></div>
                                                          </div>
                                                          <div class="comment-text"><?= $resp->comentario ?></div>
                                                      </div>
                                                  </div>
                                              </div>
                                          </div>
                                      <?php endforeach; ?>
                                  <?php endif; ?>
                                  <!-- FIM RESPOSTAS -->
                              </div>
                          </div>
                      </div>
                  <?php endforeach; ?>

                  <a class="btn btn-white btn-block mt-4" href="#">Ver mais</a>
              </div>
          <?php else : ?>
              <p>Nenhum comentário ainda.</p>
          <?php endif; ?>


          </div>

          <!-- LATERAL -->
          <style>
            @media(max-width: 500px) {
              .m-none {
                display: none;
              }
            }
          </style>
          <div class="col-md-4 m-none">
            <!-- PAGINADOR VOLTAR E PRÓXIMO -->
            <div>

            <style>
              .btn {
                border-radius: 8px;
                padding: 8px 16px;
                font-weight: 600;
                transition: background-color 0.3s ease, transform 0.2s ease;
                display: inline-flex;
                align-items: center;
                gap: 6px;
                text-decoration: none;
              }

              .btn:hover {
                transform: translateY(-2px);
              }

              .btn-primary {
                background-color: rgb(217, 192, 0);
                border: 1px solid rgb(217, 192, 0);
                color: #000;
              }

              .btn-primary:hover {
                background-color: rgba(217, 192, 0, 0.9);
              }

              .btn-gray {
                background-color: #1f1f1f;
                border: 1px solid #333;
                color: #fff;
              }

              .btn-gray:hover {
                background-color: #333;
              }

              .pagination {
                display: flex;
                gap: 10px;
              }
            </style>

            <div class="col-auto d-flex pagination" style="float:right; margin-bottom: 10px">

              <?php if ($url_l != url("minhas-compras/viewaulas/" . $id)) : ?>
                <a class="btn btn-gray" href="<?= $url_l; ?>">
                  <i class="fas fa-chevron-left"></i> 
                  <span>Voltar aula</span>
                </a>
              <?php endif; ?>

              <?php if ($url_2 != url("minhas-compras/viewaulas/" . $id)) : ?>
                <a class="btn btn-primary" href="<?= $url_2; ?>">
                  <span>Próxima aula</span> 
                  <i class="fas fa-play"></i>
                </a>
              <?php endif; ?>

            </div>


              <div style="clear: both;"></div>
            </div>
            <!-- FIM PAGINADOR VOLTAR E PRÓXIMO -->

            <!-- AVALIAÇÃO DESKTOP -->
            <div class="sticky-top">
              <!-- <div class="row align-items-center no-gutters mb-h">
                    <div class="col">
                      <div class="progress ">
                        <div class="progress-bar" style="width: 44%"></div>
                      </div>
                    </div>

                    <div class="col-auto">
                      <small class="align-text-top text-upcase text-muted ml-2">
                        44%
                      </small>
                    </div>
                  </div> -->

              <style>
                .estrela {
                  color: #bbb;
                }

                .estrela:hover {
                  cursor: pointer;
                  color: #ff0
                }
              </style>

              <div class="card">
                <div class="card-body">
                  <div class="d-flex justify-content-between flex-wrap">
                    <h2>Avaliação</h2>
                    <i class="fa-solid fa-star estrela" id="star1" onmouseover="marcada(1, 'star')" onmouseout="normal(1, 'star')" onclick="avaliar(1)"></i>
                    <i class="fa-solid fa-star estrela" id="star2" onmouseover="marcada(2, 'star')" onmouseout="normal(2, 'star')" onclick="avaliar(2)"></i>
                    <i class="fa-solid fa-star estrela" id="star3" onmouseover="marcada(3, 'star')" onmouseout="normal(3, 'star')" onclick="avaliar(3)"></i>
                    <i class="fa-solid fa-star estrela" id="star4" onmouseover="marcada(4, 'star')" onmouseout="normal(4, 'star')" onclick="avaliar(4)"></i>
                    <i class="fa-solid fa-star estrela" id="star5" onmouseover="marcada(5, 'star')" onmouseout="normal(5, 'star')" onclick="avaliar(5)"></i>

                    <?php if (!$aulaConcluida) : ?>
                      <button type="button" id="btn-concluido-desk" class="btn btn-block btn-success btn-bordered">Concluído</button>
                    <?php endif ?>
                  </div>
                </div>
              </div>


            </div>
            <!-- FIM AVALIAÇÃO DESKTOP -->
            <!-- FIM SEÇÃO COMENTÁRIOS E AVALIAÇÃO -->
          </div>
          <!-- FIM LATERAL -->

        </div>
      </div>
      <!-- FIM INFORMAÇÕES AULA -->

    </div>

  </section>

</div>

<?= $v->start("scripts") ?>
<script>
  function normal(num, id = 'star') {
    for (i = 5; i > num; i--) {
      $("#" + id + i).css("color", "#bbb");
    }
  }

  function marcada(num, id = 'star') {
    for (i = 1; i <= num; i++) {
      $("#" + id + i).css("color", "#ff0");
      console.log(id);
    }
  }

  var avaliacao = $("#nota").val();
  marcada(avaliacao);
  marcada(avaliacao, 'starm');
  console.log(avaliacao);

  function avaliar(num, id = 'star') {
    for (i = 1; i <= num; i++) {
      $("#" + id + i).css("color", "#ff0");
    }

    $("#avaliacao").attr("value", num);
    var form = $("form[name=comentar]");
    console.log(form.serialize());

    $.ajax({
      url: "<?= $router->route("aulas.avaliar") ?>",
      type: "POST",
      data: form.serialize(),
      success: (callback) => {
        alert(callback);
        location.reload();
      }
    })
  }

  $("#btn-concluido-desk, #btn-concluido-mobile").click((e) => {
    e.preventDefault();
    var form = $("form[name=comentar]");
    concluido(form);
  });

  function concluido(form) {
    $.ajax({
      url: "<?= $router->route("aulas.concluir") ?>",
      type: "POST",
      data: form.serialize(),
      success: (callback) => {
        alert(callback);
      }
    })
  }

  $("#btn_comentar").click((e) => {
    e.preventDefault();
    var form = $("form[name=comentar]");
    var comentario = $("#comentario").val();
    if (comentario)
      salvar(form);
    else
      alert("sem comentarios")
  });

  function salvar(form) {
    $.ajax({
      url: "<?= $router->route("aulas.comentar") ?>",
      type: "POST",
      data: form.serialize(),
      success: (callback) => {
        alert(callback);
      }
    })
  }

  function responder(id) {
    $("#responder" + id).hide();
    $("#div_responder" + id).show();
  }

  function cancelar(id) {
    $("#responder" + id).show();
    $("#div_responder" + id).hide();
  }

  function btnResponder(id) {
    var form = $("form[name=responder" + id + "]");
    var comentario = $("#comentario_resposta" + id).val();
    if (comentario)
      salvar(form);
    else
      alert("sem comentarios")
  }
</script>
<?= $v->end() ?>