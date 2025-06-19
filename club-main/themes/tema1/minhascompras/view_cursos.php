<?php $v->layout("_theme"); ?>

<style>
body, .content-wrapper, .main-content, .container, .content {
  background-color: #000 !important;
  color: #fff;
  font-family: 'Poppins', sans-serif;
}

/* Card lateral (box com img, descrição e progresso) */
.sticky-top {
  background-color: #141414;
  border-radius: 12px;
  padding: 20px;
  box-shadow: 0 4px 12px rgba(217, 192, 0, 0.05);
}

.sticky-top img {
  width: 100%;
  border-radius: 10px;
  margin-bottom: 15px;
}

h2, h5, h4 {
  color: rgb(217, 192, 0);
  font-weight: 600;
}

p {
  color: #aaa;
}

/* Progress Bar */
.progress {
  background-color: #333;
  border-radius: 20px;
  overflow: hidden;
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
  transition: all 0.3s ease;
}

.btn-success {
  background-color: rgb(217, 192, 0);
  border-color: rgb(217, 192, 0);
  color: #000;
}

.btn-success:hover {
  background-color: rgba(217, 192, 0, 0.9);
}

.btn-primary {
  background-color: rgb(217, 192, 0);
  border-color: rgb(217, 192, 0);
  color: #000;
}

.btn-primary:hover {
  background-color: rgba(217, 192, 0, 0.9);
}

a {
  color: rgb(217, 192, 0);
}

a:hover {
  color: rgba(217, 192, 0, 0.8);
}

/* Seção dos módulos */
.col-lg-8 {
  background-color: #141414;
  border-radius: 12px;
  padding: 20px;
}

/* Accordion */
.accordion {
  background-color: #1e1e1e;
  color: #fff;
  cursor: pointer;
  padding: 14px 18px;
  width: 100%;
  text-align: left;
  border: none;
  outline: none;
  transition: 0.3s;
  border-radius: 8px;
  margin-bottom: 10px;
}

.accordion:hover {
  background-color: #333;
}

.accordion.active {
  background-color: rgb(217, 192, 0);
  color: #000;
}

.panel {
  padding: 0 18px;
  display: none;
  background-color: #141414;
  overflow: hidden;
  border-radius: 8px;
  margin-bottom: 15px;
}

/* Responsivo */
@media (max-width: 768px) {
  .sticky-top {
    margin-bottom: 20px;
  }
}
</style>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">

  <!-- Content Header (Page header) -->
  <section class="content" id="menu-content">

    <div class="main-content px-md-5">
      <div class="container pt-3 pt-md-5 pt-lg-6">
        <div class="row mx-lg-n4 mb-lg-5">
          <!-- .col-lg-4 .px-lg-4 -->
          <div class="col-lg-4 px-lg-4">
            <!-- .sticky -->
            <div class="sticky-top mb-4 mb-md-0 py-md-5">
              <?php
              if (!empty($cursos->imagem)) :
                $img = url("uploads/cursos/" . $cursos->imagem);
              else :
                $img =  url("uploads/cursos/default.png");
              endif;
              ?>

              <img src="<?= $img; ?>" style="border-radius: 10px;"> <br><br>
              <?php
              $aulasTotal = $aulaObj->find("curso = $cursos->id")->count();
              $aulasConcluidas = $aulaCl->find("aluno = " . $_SESSION["id"] . " AND curso = $cursos->id")->count();

              $porcent = round($aulasConcluidas / $aulasTotal * 100);
              ?>
              <div class="progress" style="height: 20px;">
                <div class="progress-bar bg-success" role="progressbar" style="width: <?= $porcent ?>%;" aria-valuenow="<?= $porcent ?>" aria-valuemin="0" aria-valuemax="100"><?= "$aulasConcluidas/$aulasTotal" ?></div>
              </div>

              <?php if ($aulasConcluidas == $aulasTotal) : ?>
                <br>
                <a href="<?= url("certificados/ver/$cursos->id") ?>" class="btn btn-success" target="_blank">Certificado</a>
                <br>
              <?php endif ?>
              <br>
              <h2><?= $title ?></h2>
              <!-- <h5 class="h5 text-muted mb-0">
                    6h 56m •
                  60 conteúdos
                </h5> -->

              <p class="text-muted mt-3 mb-4"><?= $cursos->descricao; ?></p>

              <!-- <div class="row align-items-center no-gutters my-4">
                  <div class="col">
                    <div class="progress"><div class="progress-bar" style="width: 44%"></div></div>
                  </div>
                  <div class="col-auto">
                    <small class="align-text-top text-upcase text-muted ml-2">
                      44%
                    </small>
                  </div>
                </div> -->

              <!-- <a class="btn btn-lg btn-bordered btn-block btn-outline-primary" href="#">Continuar de onde parei</a> -->

            </div>
            <!-- fim .sticky -->
          </div>
          <!-- fim .col-lg-4 .px-lg-4 -->

          <div class="col-lg-8 px-lg-4">
            <?php
            if (!empty($modulos)) :
              $i = 1;
              // echo $modulosCount;
              foreach ($modulos as $modulos) :
                // echo $turma->created_at;
                if ($turma) {
                  $semana = (strtotime(date("Y-m-d H:i:s")) - strtotime($turma->created_at));
                  $semana = floor($semana / (60 * 60 * 24));
                } else {
                  $semana = 1;
                }

                if ($cursos->liberacao == 1) {
                  $v->insert("minhascompras/ver-modulos", ["modulos" => $modulos, "aulas" => $aulas]);
                } elseif ($cursos->liberacao == 3) {
                  if ($semana < 7) {
                    $v->insert("minhascompras/ver-modulos", ["modulos" => $modulos, "aulas" => $aulas]);
                    break;
                  } else {
                    $v->insert("minhascompras/ver-modulos", ["modulos" => $modulos, "aulas" => $aulas]);
                  }
                }

              endforeach;
            endif;
            ?>

          </div>
        </div>

      </div>
    </div>

  </section>
  <!-- fim .content -->
</div>
<!-- fim .content-wraper -->

<?= $v->start("scripts") ?>
<script>
  var acc = document.getElementsByClassName("accordion");
  var i;

  for (i = 0; i < acc.length; i++) {
    acc[i].addEventListener("click", function() {
      this.classList.toggle("active");
      var panel = this.nextElementSibling;
      if (panel.style.display === "block") {
        panel.style.display = "none";
      } else {
        panel.style.display = "block";
      }
    });
  }
  $(document).ready(function() {
    $(".course_action").click(function() {
      jQuery(this).next('.action').toggle();

    });

    $(".module_action").click(function() {
      jQuery(this).next('.action').toggle();

    });

    $(".aulas_action").click(function() {
      jQuery(this).next('.action').toggle();

    });
  });

  function excluir_aula(id) {

    //console.log(id);
    if (confirm("Você realmente deseja excluir esta aulas?")) {

      $.ajax({

        url: "<?= $router->route("appcursos.urlExcluirAula") ?>",

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

          if (callback == "1") {

            location.reload();

          } else {

            console.log("fracasso");

          }

        }

      });

    }

  }


  function excluir_module(id) {

    //console.log(id);
    if (confirm("A exclusão deste módulo também pode afetar outras aulas")) {

      $.ajax({

        url: "<?= $router->route("appcursos.urlExcluirModule") ?>",

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

          if (callback == "1") {

            location.reload();

          } else {

            console.log("fracasso");

          }

        }

      });

    }

  }


  function excluir_cursos(id) {

    //console.log(id);
    if (confirm("Tem certeza de que deseja excluir este curso? Pode afetar módulos e aulas")) {

      $.ajax({

        url: "<?= $router->route("appcursos.urlExcluirCursos") ?>",

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

          if (callback == "1") {

            window.location.href = "<?= $router->route("appcursos.home") ?>";

          } else {

            console.log("fracasso");

          }

        }

      });

    }

  }
</script>

<?= $v->end() ?>