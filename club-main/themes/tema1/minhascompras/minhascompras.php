<?php
$v->layout("_theme");
?>

<?php
$v->layout("_theme");
?>

<style>
body {
  background-color: #000;
}

.content-wrapper {
  background-color: #000;
}

.faixa {
  background-color: #000;
}

.wrapper {
  background-color: #000;
}

ul.miniaturas {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
  gap: 20px;
  list-style: none;
  padding: 0;
  margin: 0;
}



.card-produtos:hover {
  transform: scale(1.05);
  box-shadow: 0 8px 20px rgba(217, 192, 0, 0.4);
}

.card-img-produtos1 {
  width: 100%;
  height: 320px;
  object-fit: cover;
  display: block;
}

.card-body {
  position: absolute;
  bottom: 0;
  left: 0;
  right: 0;
  background: linear-gradient(to top, rgba(20, 20, 20, 0.95) 50%, rgba(20, 20, 20, 0) 100%);
  padding: 15px;
  color: #fff;
  opacity: 0;
  transition: opacity 0.3s ease;
}

.card-produtos:hover .card-body {
  opacity: 1;
}

.card-title {
  font-size: 1rem;
  margin-bottom: 5px;
  color: #fff;
}

.card-text {
  font-size: 0.85rem;
  color: #bbb;
  margin-bottom: 10px;
}

.btn {
  display: inline-block;
  padding: 8px 12px;
  background-color: rgb(217, 192, 0);
  color: black;
  border: none;
  border-radius: 5px;
  text-align: center;
  text-decoration: none;
  transition: background-color 0.3s ease;
  font-weight: bold;
}

.btn:hover {
  background-color: rgba(217, 192, 0, 0.8);
}

h4 {
  color: #fff;
  text-align: center;
  margin-top: 50px;
}


</style>


<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
  <section class="faixa">
    <div class="wrapper">
      <ul class="miniaturas">

        <?php if ($turmaaluno) : ?>
          <?php foreach ($turmaaluno as $cursos) : ?>
            <li class="card-produtos">
              <?php if ($cursos->dataCurso()->imagem != "") : ?>
                <img class="card-img-produtos1" src="<?= url("uploads/cursos/" . $cursos->dataCurso()->imagem); ?>" alt="Imagem do Curso">
              <?php else : ?>
                <img src="<?= url("uploads/cursos/default.png"); ?>" alt="Imagem do Curso" class="card-img-produtos1">
              <?php endif; ?>

              <div class="card-body">
                <h2 class="card-title"><?= mb_strimwidth($cursos->dataCurso()->nome, 0, 20, "..."); ?></h2>
                <p class="card-text"><?= mb_strimwidth($cursos->descricao, 0, 100, "..."); ?></p>
                <a href="<?= url("minhas-compras/viewcursos/" . $cursos->dataCurso()->id) ?>" class="btn">Acessar Curso</a>
              </div>
            </li>
          <?php endforeach; ?>

          <?php
          $link = URL_BASE . "/produtos/p/";
          $paginador->paginas($total, $pagina, $link);
          ?>

        <?php else : ?>
          <h4>Nenhum curso encontrado!</h4>
        <?php endif; ?>

      </ul>
    </div>
  </section>
</section>


  <!-- Main content -->

  <!-- /.content -->

  <a id="back-to-top" href="#" class="btn btn-primary back-to-top" role="button" aria-label="Scroll to top">
    <i class="fas fa-chevron-up"></i>
  </a>
</div>
<!-- /.content-wrapper -->