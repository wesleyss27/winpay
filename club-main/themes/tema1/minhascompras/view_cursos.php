<?php $v->layout("_theme"); ?>

<style>
/* Estilo Geral */
body, .content-wrapper, .main-content, .container, .content {
  background-color: #000 !important;
  color: #fff;
  font-family: 'Poppins', sans-serif;
}

/* Card lateral (progresso, imagem, descrição) */
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

/* Títulos */
h2, h5, h4, h3 {
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

.btn-success, .btn-primary {
  background-color: rgb(217, 192, 0);
  border-color: rgb(217, 192, 0);
  color: #000;
}

.btn-success:hover, .btn-primary:hover {
  background-color: rgba(217, 192, 0, 0.9);
}

/* Links */
a {
  color: rgb(217, 192, 0);
}

a:hover {
  color: rgba(217, 192, 0, 0.8);
}

/* Área dos módulos */
.modulos-area {
  background-color: #141414;
  border-radius: 12px;
  padding: 20px;
  box-shadow: 0 4px 12px rgba(217, 192, 0, 0.05);
  color: #fff;
}

/* Corrige fundo branco de cards */
.modulos-area .card,
.modulos-area .card-body,
.modulos-area .list-group-item {
  background-color: #1e1e1e !important;
  color: #fff !important;
  border-color: #333 !important;
}

/* Accordion dos módulos */
.modulos-area .accordion {
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

.modulos-area .accordion:hover {
  background-color: #333;
}

.modulos-area .accordion.active {
  background-color: rgb(217, 192, 0);
  color: #000;
}

.modulos-area .panel {
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

<!-- Content Wrapper -->
<div class="content-wrapper">

  <section class="content" id="menu-content">
    <div class="main-content px-md-5">
      <div class="container pt-3 pt-md-5 pt-lg-6">
        <div class="row mx-lg-n4 mb-lg-5">

          <!-- Sidebar -->
          <div class="col-lg-4 px-lg-4">
            <div class="sticky-top mb-4 mb-md-0 py-md-5">
              <?php
              $img = !empty($cursos->imagem) 
                ? url("uploads/cursos/" . $cursos->imagem) 
                : url("uploads/cursos/default.png");
              ?>

              <img src="<?= $img; ?>">
              <br><br>

              <?php
              $aulasTotal = $aulaObj->find("curso = $cursos->id")->count();
              $aulasConcluidas = $aulaCl->find("aluno = " . $_SESSION["id"] . " AND curso = $cursos->id")->count();
              $porcent = $aulasTotal > 0 ? round($aulasConcluidas / $aulasTotal * 100) : 0;
              ?>

              <div class="progress" style="height: 20px;">
                <div class="progress-bar bg-success" role="progressbar" style="width: <?= $porcent ?>%;" aria-valuenow="<?= $porcent ?>" aria-valuemin="0" aria-valuemax="100">
                  <?= "$aulasConcluidas/$aulasTotal" ?>
                </div>
              </div>

              <?php if ($aulasConcluidas == $aulasTotal && $aulasTotal > 0) : ?>
                <br>
                <a href="<?= url("certificados/ver/$cursos->id") ?>" class="btn btn-success" target="_blank">Certificado</a>
                <br>
              <?php endif ?>
              <br>

              <h2><?= $title ?></h2>
              <p><?= $cursos->descricao; ?></p>

            </div>
          </div>

          <!-- Conteúdo dos módulos -->
          <div class="col-lg-8 px-lg-4 modulos-area">
            <?php
            if (!empty($modulos)) :
              foreach ($modulos as $modulos) :
                $semana = $turma 
                  ? floor((strtotime(date("Y-m-d H:i:s")) - strtotime($turma->created_at)) / (60 * 60 * 24)) 
                  : 1;

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

</div>
<!-- End Content Wrapper -->

<?= $v->start("scripts") ?>
<script>
  document.querySelectorAll(".accordion").forEach((acc) => {
    acc.addEventListener("click", function() {
      this.classList.toggle("active");
      const panel = this.nextElementSibling;
      panel.style.display = (panel.style.display === "block") ? "none" : "block";
    });
  });

  $(document).ready(function() {
    $(".course_action, .module_action, .aulas_action").click(function() {
      $(this).next('.action').toggle();
    });
  });

  function excluir_aula(id) {
    if (confirm("Você realmente deseja excluir esta aula?")) {
      $.post("<?= $router->route("appcursos.urlExcluirAula") ?>", { id: id })
        .done(function(callback) {
          if (callback == "1") location.reload();
          else alert("Erro ao excluir.");
        });
    }
  }

  function excluir_module(id) {
    if (confirm("A exclusão deste módulo também pode afetar outras aulas.")) {
      $.post("<?= $router->route("appcursos.urlExcluirModule") ?>", { id: id })
        .done(function(callback) {
          if (callback == "1") location.reload();
          else alert("Erro ao excluir.");
        });
    }
  }

  function excluir_cursos(id) {
    if (confirm("Tem certeza de que deseja excluir este curso? Pode afetar módulos e aulas.")) {
      $.post("<?= $router->route("appcursos.urlExcluirCursos") ?>", { id: id })
        .done(function(callback) {
          if (callback == "1") window.location.href = "<?= $router->route("appcursos.home") ?>";
          else alert("Erro ao excluir.");
        });
    }
  }
</script>
<?= $v->end() ?>
