<?php $v->layout("_theme"); ?>

<style>
body, .content-wrapper, .main-content, .container, .content {
  background-color: #000 !important;
  color: #fff;
  font-family: 'Poppins', sans-serif;
}

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

h2, h5, h4, h3 {
  color: rgb(217, 192, 0);
  font-weight: 600;
}

p {
  color: #aaa;
}

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

a {
  color: rgb(217, 192, 0);
}

a:hover {
  color: rgba(217, 192, 0, 0.8);
}

.modulos-area {
  background-color: #141414;
  border-radius: 12px;
  padding: 20px;
  box-shadow: 0 4px 12px rgba(217, 192, 0, 0.05);
  color: #fff;
}

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

.aula-item {
  background-color: #1e1e1e;
  border: 1px solid #333;
  border-radius: 8px;
  padding: 12px 18px;
  margin-bottom: 10px;
  color: #ccc;
  display: flex;
  align-items: center;
  transition: all 0.3s ease;
  cursor: pointer;
}

.aula-item:hover {
  background-color: #2a2a2a;
  color: rgb(217, 192, 0);
  border-color: rgb(217, 192, 0);
  transform: translateY(-2px);
}

.aula-item i {
  margin-right: 10px;
  color: rgb(217, 192, 0);
}

.aula-item span {
  flex: 1;
  font-weight: 500;
}

@media (max-width: 768px) {
  .sticky-top {
    margin-bottom: 20px;
  }
}
</style>

<div class="content-wrapper">
  <section class="content">
    <div class="main-content px-md-5">
      <div class="container pt-3 pt-md-5 pt-lg-6">
        <div class="row mx-lg-n4 mb-lg-5">

          <!-- Sidebar -->
          <div class="col-lg-4 px-lg-4">
            <div class="sticky-top mb-4 mb-md-0 py-md-5">
              <?php
              $img = !empty($cursos->imagem) ? url("uploads/cursos/" . $cursos->imagem) : url("uploads/cursos/default.png");
              ?>

              <img src="<?= $img; ?>"><br><br>

              <?php
              $aulasTotal = $aulaObj->find("curso = $cursos->id")->count();
              $aulasConcluidas = $aulaCl->find("aluno = " . $_SESSION["id"] . " AND curso = $cursos->id")->count();
              $porcent = $aulasTotal > 0 ? round($aulasConcluidas / $aulasTotal * 100) : 0;
              ?>

              <div class="progress" style="height: 20px;">
                <div class="progress-bar" role="progressbar" style="width: <?= $porcent ?>%;" aria-valuenow="<?= $porcent ?>" aria-valuemin="0" aria-valuemax="100">
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
              foreach ($modulos as $modulo) :
                $semana = $turma ? floor((strtotime(date("Y-m-d H:i:s")) - strtotime($turma->created_at)) / (60 * 60 * 24)) : 1;

                if (
                  $cursos->liberacao == 1 ||
                  ($cursos->liberacao == 3 && $semana >= 7) ||
                  is_null($turma)
                ) :
            ?>
                  <div class="mb-3">
                    <button class="accordion"><?= $modulo->nome; ?></button>
                    <div class="panel">
                      <?php
                      foreach ($aulas as $aula) :
                        if ($aula->modulo == $modulo->id) :
                      ?>
                          <div class="aula-item" onclick="window.location.href='<?= url('minhas-compras/viewaulas/' . $aula->id) ?>'"                          >
                            <i class="fa fa-play-circle"></i>
                            <span><?= $aula->nome ?></span>
                          </div>
                      <?php
                        endif;
                      endforeach;
                      ?>
                    </div>
                  </div>
            <?php
                endif;
              endforeach;
            else :
              echo "<p style='color: #888;'>Nenhum módulo encontrado.</p>";
            endif;
            ?>
          </div>

        </div>
      </div>
    </div>
  </section>
</div>

<?= $v->start("scripts") ?>
<script>
  document.querySelectorAll(".accordion").forEach((acc) => {
    acc.addEventListener("click", function() {
      this.classList.toggle("active");
      const panel = this.nextElementSibling;
      panel.style.display = (panel.style.display === "block") ? "none" : "block";
    });
  });
</script>
<?= $v->end() ?>
