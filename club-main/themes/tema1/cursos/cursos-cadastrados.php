<?php
$v->layout("_theme");
$liberacao = array(
  1 => "Imediatamente",
  2 => "A cada conclusão de módulo",
  3 => "Segundo módulo depois de 7 dias"
);
?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <div class="container-fluid">
      <div class="row titulo-pagina">
        <div class="col-md-12">
          <h1 style="font-family:inter"><?= $title ?></h1>
        </div>
      </div>
      <hr>
    </div>
  </section>

  <!-- Main content -->
  <section class="content">
    <div class="container-fluid">

      <?php if ($cursos) : ?>

        <script>
          var dados = [];
        </script>

        <?php $i = 0;
        foreach ($cursos as $curso) : ?>
          <div class="card card-body">
            <div class="row">
              <div class="col">
                <?php if ($curso->imagem != "") : ?>
                  <img src="<?= url("uploads/cursos/" . $curso->imagem); ?>" alt="<?= $curso->nome; ?>" width="60px" height="60px" style="border-radius: 50%;"><br>
                <?php else : ?>
                  <img src="<?= url("uploads/cursos/default.png"); ?>" alt="<?= $curso->nome; ?>" width="60px" height="60px" style="border-radius: 50%;"><br>
                <?php endif; ?>

                <b><?= $curso->nome; ?></b>
              </div>

              <div class="col">
                <!-- < ?= @$curso->dataProdutor()->nome; ?><br>
                < ?= @$curso->dataProdutor()->email; ?> -->
              </div>

              <?php include "categorias.php"; ?>
              <div class="col">
                <i class="fas fa-boxes" style="color: #2c7be5;"></i> <?= $curso->dataProduto()->nome; ?><br>
                <i class="fas fa-tags" style="color: #2c7be5;"></i> <?= $categorias[$curso->assunto]; ?>
              </div>

              <div class="col">
                <?php $alunosCount = $turmaAluno->find("usuario = '$curso->usuario' AND curso = $curso->id")->count(); ?>
                <?php $aulasCount = $aula->find("curso = $curso->id")->count(); ?>
                <i class="fa-regular fa-heart" style="color: #EC0043"></i> <?= $alunosCount ?> alunos <br>
                <i class="fas fa-video"></i> <?= $aulasCount ?> aulas
              </div>

              <div class="col">
                <?php if ($curso->status == 1) : ?>
                  <i class='fas fa-check-circle' style="color: #39aa7e"></i> Aprovado
                <?php else : ?>
                  <i class='fa-solid fa-circle-xmark' style="color: #EC0043"></i> Reprovado
                <?php endif; ?>
              </div>

              <div class="col">
                <button class="btn btn-success btn-sm" data-target="#modal-lg" onclick="editar(<?= $i; ?>)" data-toggle="modal"><i class="far fa-edit"></i></button>
              </div>
            </div>
          </div>

          <script>
            dados[<?= $i ?>] = {
              id: "<?= $curso->id ?>",
              produtor: "<?= $curso->dataProdutor()->nome ?>",
              email: "<?= $curso->dataProdutor()->email ?>",
              produto: "<?= $curso->dataProduto()->nome ?>",
              nome: "<?= $curso->nome ?>",
              categoria: "<?= $categorias[$curso->assunto] ?>",
              formato: "<?= $formato[$curso->formato] ?>",
              liberacao: "<?= $liberacao[$curso->liberacao] ?>",
              descricao: "<?= $curso->descricao ?>",
              imagem: "<?= $curso->imagem ?>",
              status: "<?= $curso->status ?>",
              alunos: "<?= $alunosCount ?>",
              aulas: "<?= $aulasCount ?>",
              created_at: "<?= $curso->created_at ?>",
              updated_at: "<?= $curso->updated_at ?>",
            }
          </script>


          <!-- cadastar -->
          <div class="modal fade" id="modal-lg">
            <div class="modal-dialog modal-lg">
              <div class="modal-content">

                <div class="modal-header">
                  <h3 class="card-title"><i class="fa fa-info-circle"></i> Detalhes do curso</h3>
                  <button type="button" class="close" data-dismiss="modal"> &times;</button>
                </div>

                <form action="#" name="cadastro" method="post" style="font-family:inter; font-size:14px">
                  <input type="hidden" name="id" id="id">
                  <div class="modal-body" style="background: #f4f6f9">
                    <div class="card card-body">
                      <div class="row">
                        <div class="col-md-2">
                          <img src="" alt="<?= $curso->nome; ?>" width="100px" height="100px" style="border-radius: 10px;" id="imagem-curso" class="imagem-curso"><br>
                        </div>

                        <div class="col-md-10">
                          <h2 id="nome-curso"><?= $curso->nome; ?></h2>
                          <p id="descricao-curso"><?= $curso->descricao; ?></p>
                        </div>
                      </div>

                      <br>
                      <div class="row">
                        <div class="col-md-12">
                          <a class="btn btn-outline-primary" href="#" target="_blank" id="link-curso"><i class="fa fa-eye" aria-hidden="true"></i>VISUALIZAR COMO ALUNO</a>
                        </div>
                      </div>

                      <hr>
                      <div class="row">
                        <div class="col-md-12">
                          <i class="fas fa-user-lock" style="color: #2c7be5;"></i> <small>Produtor:</small><br>
                          <!-- <span id="produtor-curso">< ?= $curso->dataProdutor()->nome; ?></span><br>
                <span id="email-curso">< ?= $curso->dataProdutor()->email; ?></span> -->
                        </div>
                      </div>

                      <hr>
                      <div class="row">
                        <div class="col-md-6">
                          <small>Produto:</small><br>
                          <i class="fas fa-boxes" style="color: #2c7be5;"></i> <span id="produto-curso">
                            < ?=$curso->dataProduto()->nome; ?>
                          </span><br><br>

                          <small>Categoria:</small><br>
                          <i class="fas fa-tags" style="color: #2c7be5;"></i> <span id="categoria-curso"><?= $categorias[$curso->assunto]; ?></span><br><br>

                          <small>Formato:</small><br>
                          <i class="far fa-file-alt" style="color: #2c7be5;"></i> <span id="formato-curso"><?= $formato[$curso->formato]; ?></span>
                        </div>

                        <div class="col-md-6">
                          <small>Liberação </small><br>
                          <i class="fas fa-arrow-alt-circle-up" style="color: #2c7be5;"></i> <span id="liberacao-curso"><?= $liberacao[$curso->liberacao]; ?></span><br><br>

                          <?php if (@$curso) : ?>
                            <?php $alunosCount = $turmaAluno->find("usuario = $curso->usuario AND curso = $curso->id")->count(); ?>
                            <?php $aulasCount = $aula->find("curso = $curso->id")->count(); ?>
                            <i class="fa-regular fa-heart" style="color: #EC0043"></i> <span id="alunos-curso"><?= $alunosCount ?></span> alunos <br>
                            <i class="fas fa-video" style="color: #2c7be5;"></i> <span id="aulas-curso"><?= $aulasCount ?></span> aulas
                          <?php endif; ?>
                        </div>
                      </div>

                      <hr>
                      <div class="row">
                        <div class="col-md-12">
                          <span id="status-curso-icon"></span>
                          <small>Status:</small><br>

                          <select name="status-curso" id="status-curso" class="form-control col-md-3">
                            <option value="1">Aprovado</option>
                            <option value="0">Reprovado</option>
                          </select>
                        </div>
                      </div>

                    </div>

                  </div>

                  <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-danger" data-dismiss="modal" id="cancelar">Cancelar</button>
                    <button class="btn btn-primary" id="salvar-curso">Salvar</button>
                  </div>
                </form>

              </div>

            </div>

          </div>
          <!-- end cadastrar -->

        <?php
          $i++;
        endforeach;
        ?>

      <?php
        //EXIBE A NAVEGAÇÃO DO PAGINADOR
        $link = URL_BASE . "/cursos/cursos-cadastrados/";

        //VARIÁVEIS VÊM DO CONTROLADOR AppPedidos
        $paginador->paginas($total, $pagina, $link);

      else :
      ?>
        <h4>Nenhum cursos encontrado!</h4>
      <?php endif; ?>

    </div><!-- /.container-fluid -->
  </section>
  <!-- /.content -->

  <a id="back-to-top" href="#" class="btn btn-primary back-to-top" role="button" aria-label="Scroll to top">
    <i class="fas fa-chevron-up"></i>
  </a>
</div>
<!-- /.content-wrapper -->

<?= $v->start("scripts") ?>
<script>
  function editar(i) {
    var id = dados[i]["id"];
    var produtor = dados[i]["produtor"];
    var email = dados[i]["email"];
    var produto = dados[i]["produto"];
    var nome = dados[i]["nome"];
    var categoria = dados[i]["categoria"];
    var formato = dados[i]["formato"];
    var liberacao = dados[i]["liberacao"];
    var descricao = dados[i]["descricao"];
    var imagem = dados[i]["imagem"];
    var status = dados[i]["status"];
    var alunos = dados[i]["alunos"];
    var aulas = dados[i]["aulas"];
    var created_at = dados[i]["created_at"];
    var updated_at = dados[i]["updated_at"];

    $("#id").attr("value", id);
    $("#produtor-curso").html(produtor);
    $("#email-curso").html(email);
    $("#produto-curso").html(produto);
    $("#nome-curso").html(nome);
    $("#categoria-curso").html(categoria);
    $("#formato-curso").html(formato);
    $("#liberacao-curso").html(liberacao);
    $("#descricao-curso").html(descricao);
    $("#imagem-curso").prop("src", "<?= url("uploads/cursos/"); ?>" + imagem);
    $("#alunos-curso").html(alunos);
    $("#aulas-curso").html(aulas);
    $("#link-curso").prop("href", "<?= url("minhas-compras/viewcursos/") ?>" + id)

    $("#status-curso option[value=" + status + "]").prop("selected", true);

    if (status == 1) {
      $("#status-curso-icon").html("<i class='fas fa-check-circle'></i>").css("color", "#39aa7e");
    } else {
      $("#status-curso-icon").html("<i class='fa-solid fa-circle-xmark'></i>").css("color", "#EC0043");
    }
  }

  $("#salvar-curso").click((e) => {
    e.preventDefault();
    salvar();
  })

  function salvar() {
    var form = $("form");

    $.ajax({

      url: "<?= $router->route("cursos.status") ?>",
      data: form.serialize(),
      type: "POST",
      beforeSend: function() {
        console.log("atualizando");
      },
      error: function() {
        console.log("O servidor não conseguiu processar o pedido");
      },
      success: function(callback) {
        console.log("callback ", callback);
        alert(callback);
        location.reload();
      }
    });
  }
</script>
<?= $v->end() ?>