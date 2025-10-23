<?php
$v->layout("_theme");
require __DIR__ . "/categorias.php";

$liberacao = [
  1 => "Imediatamente",
  2 => "A cada conclusão de módulo",
  3 => "Segundo módulo depois de 7 dias"
];
?>

<div class="content-wrapper win-page">
  <section class="content">
    <div class="container-fluid">
      <header class="win-page-header">
        <div class="win-page-title">
          <small>Painel de cursos</small>
          <h1><?= $title ?></h1>
        </div>
        <div class="win-page-actions">
          <a href="<?= url("cursos/novo") ?>" class="btn-win">
            <i class="fas fa-plus"></i>
            Novo curso
          </a>
        </div>
      </header>

      <?php if ($cursos) : ?>
        <script>
          var dados = [];
        </script>

        <section class="win-section">
          <div class="win-grid win-grid-2">
            <?php $i = 0;
            foreach ($cursos as $curso) : ?>
              <?php
              $alunosCount = $turmaAluno->find("usuario = '$curso->usuario' AND curso = $curso->id")->count();
              $aulasCount = $aula->find("curso = $curso->id")->count();
              $cover = $curso->imagem ? url("uploads/cursos/" . $curso->imagem) : url("uploads/cursos/default.png");
              $categoriaNome = $categorias[$curso->assunto] ?? "Categoria não informada";
              $produtoNome = $curso->dataProduto()->nome ?? "Produto não informado";
              $statusLabel = $curso->status == 1 ? "Aprovado" : "Reprovado";
              $statusClass = $curso->status == 1 ? "win-status win-status-success" : "win-status win-status-danger";
              ?>

              <article class="win-card">
                <figure>
                  <img src="<?= $cover; ?>" alt="<?= $curso->nome; ?>">
                </figure>

                <div class="win-card-body">
                  <div class="win-card-meta">
                    <span class="win-meta-chip">
                      <i class="fas fa-box-open"></i>
                      <?= $produtoNome; ?>
                    </span>
                    <span class="win-meta-chip">
                      <i class="fas fa-tags"></i>
                      <?= $categoriaNome; ?>
                    </span>
                  </div>

                  <h3><?= mb_strimwidth($curso->nome, 0, 60, "..."); ?></h3>

                  <?php if (!empty($curso->descricao)) : ?>
                    <p><?= mb_strimwidth(strip_tags($curso->descricao), 0, 140, "..."); ?></p>
                  <?php endif; ?>

                  <div class="win-card-meta">
                    <span><i class="fas fa-user-friends"></i> <?= $alunosCount; ?> alunos</span>
                    <span><i class="fas fa-video"></i> <?= $aulasCount; ?> aulas</span>
                    <?php if (!empty($curso->created_at)) : ?>
                      <span><i class="far fa-calendar-alt"></i> Criado em <?= date("d/m/Y", strtotime($curso->created_at)); ?></span>
                    <?php endif; ?>
                  </div>
                </div>

                <div class="win-card-footer">
                  <span class="<?= $statusClass; ?>" id="status-curso-badge-<?= $curso->id; ?>">
                    <i class="<?= $curso->status == 1 ? 'fas fa-check-circle' : 'fas fa-times-circle'; ?>"></i>
                    <?= $statusLabel; ?>
                  </span>
                  <div class="win-card-actions">
                    <a class="btn-win btn-win-ghost" href="<?= url("cursos/detalhe/" . $curso->id); ?>">
                      <i class="fas fa-arrow-right"></i>
                      Gerenciar
                    </a>
                    <button class="btn-win" data-toggle="modal" data-target="#curso-modal" onclick="editar(<?= $i; ?>)">
                      <i class="far fa-edit"></i>
                      Revisar
                    </button>
                  </div>
                </div>
              </article>

              <script>
                dados[<?= $i ?>] = {
                  id: <?= json_encode($curso->id); ?>,
                  produtor: <?= json_encode($curso->dataProdutor()->nome); ?>,
                  email: <?= json_encode($curso->dataProdutor()->email); ?>,
                  produto: <?= json_encode($curso->dataProduto()->nome); ?>,
                  nome: <?= json_encode($curso->nome); ?>,
                  categoria: <?= json_encode($categoriaNome); ?>,
                  formato: <?= json_encode($formato[$curso->formato] ?? "Formato não informado"); ?>,
                  liberacao: <?= json_encode($liberacao[$curso->liberacao] ?? "-"); ?>,
                  descricao: <?= json_encode($curso->descricao); ?>,
                  imagem: <?= json_encode($curso->imagem); ?>,
                  status: <?= json_encode($curso->status); ?>,
                  alunos: <?= json_encode($alunosCount); ?>,
                  aulas: <?= json_encode($aulasCount); ?>,
                  created_at: <?= json_encode($curso->created_at); ?>,
                  updated_at: <?= json_encode($curso->updated_at); ?>
                };
              </script>
              <?php $i++;
            endforeach; ?>
          </div>

          <div class="win-divider"></div>

          <div class="d-flex justify-content-center">
            <?php
            $link = URL_BASE . "/cursos/cursos-cadastrados/";
            $paginador->paginas($total, $pagina, $link);
            ?>
          </div>
        </section>
      <?php else : ?>
        <section class="win-section">
          <div class="win-empty-state">
            Nenhum curso cadastrado por aqui. Assim que um curso for publicado ele aparece nesta lista.
          </div>
        </section>
      <?php endif; ?>
    </div>
  </section>

  <a id="back-to-top" href="#" class="btn btn-primary back-to-top" role="button" aria-label="Scroll to top">
    <i class="fas fa-chevron-up"></i>
  </a>
</div>

<div class="modal fade win-modal" id="curso-modal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="fa fa-info-circle"></i> Detalhes do curso</h5>
        <button type="button" class="close text-light" data-dismiss="modal" aria-label="Fechar">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <form action="#" name="cadastro" method="post" class="win-form">
        <input type="hidden" name="id" id="id">
        <div class="modal-body">
          <div class="win-card">
            <div class="d-flex flex-column flex-md-row align-items-start win-flex-gap">
              <img src="" alt="Imagem do curso" class="win-thumb-square" id="imagem-curso">
              <div class="d-flex flex-column win-flex-gap-sm">
                <h3 id="nome-curso" class="mb-0"></h3>
                <p id="descricao-curso" class="mb-0"></p>
                <div class="win-chip-group">
                  <span class="win-chip" id="categoria-curso"></span>
                  <span class="win-chip" id="formato-curso"></span>
                  <span class="win-chip" id="liberacao-curso"></span>
                </div>
              </div>
            </div>

            <div class="win-divider"></div>

            <div class="win-card-meta flex-column flex-md-row win-flex-gap-sm">
              <span><i class="fas fa-user-lock"></i> Produtor <strong id="produtor-curso"></strong></span>
              <span><i class="far fa-envelope"></i> <span id="email-curso"></span></span>
            </div>

            <div class="win-divider"></div>

            <div class="win-mini-grid">
              <div class="win-mini-card">
                <span>Produto</span>
                <strong id="produto-curso"></strong>
              </div>
              <div class="win-mini-card">
                <span>Alunos</span>
                <strong><span id="alunos-curso">0</span></strong>
              </div>
              <div class="win-mini-card">
                <span>Aulas</span>
                <strong><span id="aulas-curso">0</span></strong>
              </div>
              <div class="win-mini-card">
                <span>Atualizado</span>
                <strong id="curso-atualizado">-</strong>
              </div>
            </div>

            <div class="win-divider"></div>

            <a class="btn-win align-self-start" href="#" target="_blank" id="link-curso">
              <i class="fa fa-eye"></i>
              Visualizar como aluno
            </a>

            <div class="win-divider"></div>

            <div class="d-flex flex-column win-flex-gap-sm">
              <div class="d-flex align-items-center win-flex-gap-sm">
                <span id="status-curso-icon" class="win-status win-status-warning"></span>
                <div class="flex-grow-1">
                  <label class="mb-2">Status</label>
                  <select name="status-curso" id="status-curso" class="form-control">
                    <option value="1">Aprovado</option>
                    <option value="0">Reprovado</option>
                  </select>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal" id="cancelar">Cancelar</button>
          <button class="btn-win" id="salvar-curso" type="submit">
            <i class="fas fa-save"></i>
            Salvar alterações
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

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
    $("#descricao-curso").html(descricao || "");
    $("#imagem-curso").prop("src", imagem ? "<?= url("uploads/cursos/"); ?>" + imagem : "<?= url("uploads/cursos/default.png"); ?>");
    $("#alunos-curso").html(alunos);
    $("#aulas-curso").html(aulas);
    $("#curso-atualizado").html(updated_at ? updated_at : "-");
    $("#link-curso").prop("href", "<?= url("minhas-compras/viewcursos/") ?>" + id);

    $("#status-curso option[value=" + status + "]").prop("selected", true);

    if (status == 1) {
      $("#status-curso-icon").attr("class", "win-status win-status-success").html("<i class='fas fa-check-circle'></i> Aprovado");
    } else {
      $("#status-curso-icon").attr("class", "win-status win-status-danger").html("<i class='fas fa-times-circle'></i> Reprovado");
    }
  }

  $("#salvar-curso").on("click", function(e) {
    e.preventDefault();
    salvar();
  });

  function salvar() {
    var form = $("form[name='cadastro']");

    $.ajax({
      url: "<?= $router->route("cursos.status") ?>",
      data: form.serialize(),
      type: "POST",
      error: function() {
        console.log("O servidor não conseguiu processar o pedido");
      },
      success: function(callback) {
        alert(callback);
        location.reload();
      }
    });
  }
</script>
<?= $v->end() ?>
