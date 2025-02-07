<?php
$v->layout("_theme");
if ($_SESSION["tipo"] == 6) {
  header("Location: " . url("vendas"));
}
?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->

  <section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1 style="font-family:inter"><?= $title ?></h1>
        </div>
      </div>
      <hr>
    </div><!-- /.container-fluid -->
  </section>

  <!-- Main content -->
  <section class="content" style="padding: 0 20px">
    <form action="<?= $router->route("appcursos.cadastromodulo"); ?>" method="post" enctype="multipart/form-data">
      <ul id="progressbar">
        <li class="active" id="step1"><strong>CADASTRO DE CURSO</strong></li>
        <li class="active" id="step2"><strong>CADASTRO DE MÓDULO</strong></li>
        <li id="step3"><strong>CADASTRO DE AULA</strong></li>
      </ul>

      <div class="card card-body">
        <div class="form-group">
          <label for="formGroupExampleInput">Nome do Módulo</label>
          <input type="text" class="form-control" id="formGroupExampleInput" placeholder="Módulo 1" name="module_nome" required>
        </div>

        <div class="form-group">
          <label for="formGroupExampleInput">Descrição do Módulo</label>
          <textarea class="form-control" id="exampleFormControlTextarea1" rows="6" placeholder="Módulo 1" name="module_descricao" required></textarea>
        </div>

        <input type="hidden" name="course_id" value="<?php echo $course_id; ?>">
        <input type="submit" name="next-step" class="nxt_step" value="Próximo" />
        <a href="../novo?course_id=<?php echo $course_id; ?>" class="previous-step">Voltar</a>
      </div>
    </form>
  </section>

</div>

<?= $v->start("scripts") ?>
<script>
  $(document).ready(function() {
    var currentGfgStep, nextGfgStep, previousGfgStep;
    var opacity;
    var current = 1;
    var steps = $("fieldset").length;

    setProgressBar(current);

    $(".next-step").click(function() {

      currentGfgStep = $(this).parent();
      nextGfgStep = $(this).parent().next();

      $("#progressbar li").eq($("fieldset")
        .index(nextGfgStep)).addClass("active");

      nextGfgStep.show();
      currentGfgStep.animate({
        opacity: 0
      }, {
        step: function(now) {
          opacity = 1 - now;

          currentGfgStep.css({
            'display': 'none',
            'position': 'relative'
          });
          nextGfgStep.css({
            'opacity': opacity
          });
        },
        duration: 500
      });
      setProgressBar(++current);
    });

    $(".previous-step").click(function() {

      currentGfgStep = $(this).parent();
      previousGfgStep = $(this).parent().prev();

      $("#progressbar li").eq($("fieldset")
        .index(currentGfgStep)).removeClass("active");

      previousGfgStep.show();

      currentGfgStep.animate({
        opacity: 0
      }, {
        step: function(now) {
          opacity = 1 - now;

          currentGfgStep.css({
            'display': 'none',
            'position': 'relative'
          });
          previousGfgStep.css({
            'opacity': opacity
          });
        },
        duration: 500
      });
      setProgressBar(--current);
    });

    function setProgressBar(currentStep) {
      var percent = parseFloat(100 / steps) * current;
      percent = percent.toFixed();
      $(".progress-bar")
        .css("width", "60%")
    }

    $(".submit").click(function() {
      return false;
    })
  });
</script>
<?= $v->end() ?>