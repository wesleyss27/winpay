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
  grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
  gap: 20px;
  list-style: none;
  padding: 0;
  margin: 0;
}

.card-produtos {
  background-color: #1e1e1e;
  border-radius: 12px;
  overflow: hidden;
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.7);
  transition: transform 0.3s ease, box-shadow 0.3s ease;
  display: flex;
  flex-direction: column;
}

.card-produtos:hover {
  transform: scale(1.05);
  box-shadow: 0 8px 20px rgba(217, 192, 0, 0.4);
}

.card-img-produtos1 {
  width: 100%;
  height: 200px;
  object-fit: cover;
  display: block;
}

.card-body {
  background-color: #141414;
  padding: 15px;
  color: #fff;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
}

.card-title {
  font-size: 1rem;
  margin-bottom: 8px;
  color: rgb(217, 192, 0);
  font-weight: bold;
}

.card-text {
  font-size: 0.85rem;
  color: #bbb;
  margin-bottom: 12px;
  min-height: 40px;
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
                  <h2 class="card-title"><?= mb_strimwidth($cursos->dataCurso()->nome, 0, 30, "..."); ?></h2>
                  <p class="card-text"><?= mb_strimwidth($cursos->descricao, 0, 90, "..."); ?></p>
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

  <a id="back-to-top" href="#" class="btn btn-primary back-to-top" role="button" aria-label="Scroll to top">
    <i class="fas fa-chevron-up"></i>
  </a>
</div>
