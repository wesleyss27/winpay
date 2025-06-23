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

.card-produtos {
  background-color: #141414;
  border-radius: 10px;
  overflow: hidden;
  transition: transform 0.3s ease, box-shadow 0.3s ease;
  cursor: pointer;
  display: flex;
  flex-direction: column;
}

.card-produtos:hover {
  transform: translateY(-5px);
  box-shadow: 0 8px 20px rgba(217, 192, 0, 0.4);
}

.card-img-produtos1 {
  width: 100%;
  height: 200px;
  object-fit: cover;
  object-position: center;
  display: block;
  background-color: #000;
}

.card-footer {
  background-color: #1e1e1e;
  padding: 15px;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
  flex: 1;
}

.card-title {
  font-size: 1rem;
  margin-bottom: 10px;
  color: rgb(217, 192, 0);
  font-weight: 600;
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

<div class="content-wrapper">
  <section class="content-header">
    <section class="wrapper">
      <ul class="miniaturas">

        <?php if ($turmaaluno) : ?>
          <?php foreach ($turmaaluno as $cursos) : ?>
            <li class="card-produtos">
              
              <?php if ($cursos->dataCurso()->imagem != "") : ?>
                <img class="card-img-produtos1" src="<?= url("uploads/cursos/" . $cursos->dataCurso()->imagem); ?>" alt="Imagem do Curso">
              <?php else : ?>
                <img src="<?= url("uploads/cursos/default.png"); ?>" alt="Imagem do Curso" class="card-img-produtos1">
              <?php endif; ?>

              <div class="card-footer">
                <h2 class="card-title"><?= mb_strimwidth($cursos->dataCurso()->nome, 0, 40, "..."); ?></h2>
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
    </section>
  </section>

  <a id="back-to-top" href="#" class="btn btn-primary back-to-top" role="button" aria-label="Scroll to top">
    <i class="fas fa-chevron-up"></i>
  </a>
</div>
