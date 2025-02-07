<?php 
$v->layout("_theme"); 
if($_SESSION["tipo"] == 6){
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
            <h3 style="font-family:inter">Cadastro de modulo</h3>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item">Início</li>
			   <li class="breadcrumb-item active">Cadastro de curso</li>
              <li class="breadcrumb-item active"><a href="">Cadastro de modulo</a></li>
            </ol>
          </div>
		  
		  
        </div>
      </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content" style="padding:20px">
	 <form action="<?= $router->route("appcursos.cadastromodulo"); ?>" id="form" method="post" enctype="multipart/form-data">
                        <ul id="progressbar">
                            <li class="active" id="step1">
                                <strong>Inicio</strong>
                            </li>
                            <li class="active" id="step2"><strong>Cadastro de curso</strong></li>
                            <li class="active" id="step3"><strong>Cadastro do módulo</strong></li>
                            <li id="step4"><strong>Cadastro de Aule</strong></li>
                        </ul>
                        <!----<div class="progress">
                            <div class="progress-bar"></div>
                        </div> <br>--->
                        <div class="form-container_box">
							<div class="form-container bg_theme pl-0">
									<div class="modulo_title">
										<p class="textt modulo_tit text-left">
											 <strong>Editar Módulo</strong><br>edite o nome, conteúdo e as configurações do seu módulo
									   </p>
								   </div>
							  </div>
                         <fieldset>
							 
                             <div class="form-container">
								<div class="row">
										<div class="col-lg-12 col-md-12 col-12 form-group">
											<div class="form-group">
												<label for="formGroupExampleInput">Name do Módulo</label>
												<input type="text" class="form-control" id="formGroupExampleInput" placeholder="Módulo 1" name="module_nome" required value="<?php if(!empty($moduloss[0]->nome)) { echo $moduloss[0]->nome; } ?>">
												
											 </div>
										</div>
									</div>
							</div>
							
							<div class="hrs-break" style="    background: #f4f6f9;   float: left;margin-bottom: 20px;  width: 100%; height: 20px;"></div>
									 <div class="form-container">
										<div class="row">
												<div class="col-lg-12 col-md-12 col-12 form-group">
													<div class="form-group">
														<label for="formGroupExampleInput">Descrição do módulo</label>
														<textarea class="form-control" id="exampleFormControlTextarea1" rows="6" placeholder="Módulo 1"name="module_descricao" required><?php if(!empty($moduloss[0]->descricao)) { echo $moduloss[0]->descricao; } ?></textarea>
														<!--<span>8/6000</span>-->
													 </div>
												</div>
											</div>	
									</div>
								<?php
							if(!empty($moduloss))
								{
									?>
								<input type="hidden" name="module_id" value="<?php echo $moduloss[0]->id; ?>">
									
									<?php
								}
								?>
								<input type="hidden" name="course_id" value="<?php echo $course_id; ?>">
                            <input type="submit" name="next-step" 
                                class="nxt_step" value="Next" />
								<a href="../novo?course_id=<?php echo $course_id;?>" class="previous-step">Back</a>
                        </fieldset>
                       </div>
                    </form>
    </section>
   
  </div>

 <?= $v->start("scripts") ?>
   <script>
    $(document).ready(function () {
    var currentGfgStep, nextGfgStep, previousGfgStep;
    var opacity;
    var current = 1;
    var steps = $("fieldset").length;
  
    setProgressBar(current);
  
    $(".next-step").click(function () {
  
        currentGfgStep = $(this).parent();
        nextGfgStep = $(this).parent().next();
  
        $("#progressbar li").eq($("fieldset")
            .index(nextGfgStep)).addClass("active");
  
        nextGfgStep.show();
        currentGfgStep.animate({ opacity: 0 }, {
            step: function (now) {
                opacity = 1 - now;
  
                currentGfgStep.css({
                    'display': 'none',
                    'position': 'relative'
                });
                nextGfgStep.css({ 'opacity': opacity });
            },
            duration: 500
        });
        setProgressBar(++current);
    });
  
    $(".previous-step").click(function () {
  
        currentGfgStep = $(this).parent();
        previousGfgStep = $(this).parent().prev();
  
        $("#progressbar li").eq($("fieldset")
            .index(currentGfgStep)).removeClass("active");
  
        previousGfgStep.show();
  
        currentGfgStep.animate({ opacity: 0 }, {
            step: function (now) {
                opacity = 1 - now;
  
                currentGfgStep.css({
                    'display': 'none',
                    'position': 'relative'
                });
                previousGfgStep.css({ 'opacity': opacity });
            },
            duration: 500
        });
        setProgressBar(--current);
    });
  
    function setProgressBar(currentStep) {
        var percent = parseFloat(100 / steps) * current;
        percent = percent.toFixed();
        $(".progress-bar")
            .css("width",  "60%")
    }
  
    $(".submit").click(function () {
        return false;
    })
});
</script>
<?= $v->end() ?>
