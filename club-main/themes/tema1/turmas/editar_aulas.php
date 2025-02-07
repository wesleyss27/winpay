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
            <h3 style="font-family:inter;padding-left: 15px;">Editar Aulas</h3>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item active">Editar Aulas</li>
            </ol>
          </div>
		  
		  
        </div>
      </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">
	 <form action="<?= $router->route("appcursos.cadastroeditaraulas"); ?>" id="form" method="post" enctype="multipart/form-data">
                        <div class="form-container">
                              <div class="row">
                        <fieldset>
                         
							 <div class="form-container">
								    <div class="finish">
							 <div class="row">
										<div class="col-lg-12 col-md-12 col-12 form-group">
											<div class="form-group">
												<label for="formGroupExampleInput">Name da Aula</label>
												<input type="text" class="form-control" id="formGroupExampleInput" placeholder="Aula 1" name="aulas_nome"required value="<?php if(!empty($aulass[0]->nome)) { echo $aulass[0]->nome; } ?>">
											 </div>
										</div>
										<?php if($modulos): ?>
										<div class="col-lg-12 col-md-12 col-12 form-group">
											<div class="inner-selct ">
											  <label for="inputState">Módulo</label>
											  <select id="inputState" class="form-control" name="modulo_sel" required>
												<option selected>Choose...</option>
												 <?php foreach($modulos as $modulo): ?>
													<option value="<?php echo $modulo->id; ?>" <?php if(!empty($aulass[0]->modulo)) { if($aulass[0]->modulo == $modulo->id){ echo "selected";} } ?>><?php echo $modulo->nome; ?></option>
												<?php  endforeach;  ?>
											  </select>
											</div>
										</div>
										<?php endif;  ?>
										<div class="col-lg-12 col-md-12 col-12 form-group">
											<div class="form-group">
												<label for="formGroupExampleInput">Video</label>
												<input type="url" class="form-control" id="formGroupExampleInput" placeholder="" name="link_video" value="<?php if(!empty($aulass[0]->link_video)) { echo $aulass[0]->link_video; } ?>">
											 </div>
										</div>
									</div>	
								</div>
							</div>
				
							<div class="col-lg-12 col-md-12 col-12 form-group">
										<div class="form-group">
											<label for="formGroupExampleInput">Conteúdo da aula</label>
										<textarea class="form-control" id="exampleFormControlTextarea1" rows="6" placeholder="Módulo 1" name="aulas_descricao"required><?php if(!empty($aulass[0]->descricao)) { echo $aulass[0]->descricao; } ?></textarea>
									</div>
							</div>
							<?php
							if(!empty($aulass))
								{
									?>
									<input type="hidden" name="aulas_id" value="<?php echo $aulass[0]->id; ?>">
									<input type="hidden" name="anexo1_img1" value="<?php echo $aulass[0]->arquivo_anexo1; ?>">
									<input type="hidden" name="anexo2_img2" value="<?php echo $aulass[0]->arquivo_anexo2; ?>">
									<input type="hidden" name="anexo3_img3" value="<?php echo $aulass[0]->arquivo_anexo3; ?>">
									
									<?php
								}
								?>
							<div class="col-lg-12 col-md-12 col-12 form-group">
								<div class="attachment">
									<label class="filelabel1">
										<i class="fa fa-paperclip">
										</i>
										<?php
										if(!empty($aulass[0]->arquivo_anexo1))
										{
										?>
										<span class="title1" style="color: rgb(32, 132, 64);"><?php echo $aulass[0]->arquivo_anexo1; ?></span>
										<?php
										}else{
										?>
										<span class="title1">
											Add File1
										</span>
										<?php
										}
										?>
										<input class="FileUpload1" id="FileInput1" name="arquivo_anexo1" type="file">
									</label>
								</div>
								<div class="attachment">
									<label class="filelabel2">
										<i class="fa fa-paperclip">
										</i>
										<?php
										if(!empty($aulass[0]->arquivo_anexo2))
										{
										?>
										<span class="title1" style="color: rgb(32, 132, 64);"><?php echo $aulass[0]->arquivo_anexo2; ?></span>
										<?php
										}else{
										?>
										<span class="title2">
											Add File2
										</span>
										<?php
										}
										?>
										<input class="FileUpload2" id="FileInput2" name="arquivo_anexo2" type="file">
									</label>
								</div>
								<div class="attachment">
									<label class="filelabel3">
										<i class="fa fa-paperclip">
										</i>
										<?php
										if(!empty($aulass[0]->arquivo_anexo3))
										{
										?>
										<span class="title1" style="color: rgb(32, 132, 64);"><?php echo $aulass[0]->arquivo_anexo3; ?></span>
										<?php
										}else{
										?>
										<span class="title3">
											Add File3
										</span>
										<?php
										}
										?>
										<input class="FileUpload3" id="FileInput3" name="arquivo_anexo3" type="file">
									</label>
								</div>
							</div>
							<div class="col-lg-12 col-md-12 col-12 form-group">
								<div class="attachment_links">
									<label for="formGroupExampleInput">Links</label>
									<input class="form-control"  name="links" type="text" placeholder="Links" value="<?php if(!empty($aulass[0]->link_video)) { echo $aulass[0]->link_video; } ?>"/>
								</div>
							</div>
							<input type="hidden" name="course_id" value="<?php echo $course_id; ?>">
                           
                         <input type="submit" name="submit-form" class="nxt_step submit_form_btn" value="Salvar" />
                        </fieldset>
                        </div>
                        </div>
                    </form>
    </section>
   
  </div>
<style>


section.content {
    padding-top: 0px !important;
}
.content-header {
    padding-bottom: 0px;
}
</style>

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
            .css("width",  "100%")
    }
  
    $(".submit").click(function () {
        return false;
    })
});

$("#FileInput1").on('change',function (e) {
            var labelVal = $(".title1").text();
            var oldfileName = $(this).val();
                fileName = e.target.value.split( '\\' ).pop();

                if (oldfileName == fileName) {return false;}
                var extension = fileName.split('.').pop();

            if ($.inArray(extension,['jpg','jpeg','png']) >= 0) {
                $(".filelabel1 i").removeClass().addClass('fa fa-file-image-o');
                $(".filelabel1 i, .filelabel1 .title1").css({'color':'#208440'});
                $(".filelabel1").css({'border':' 2px solid #208440'});
            }
            else if(extension == 'pdf'){
                $(".filelabel1 i").removeClass().addClass('fa fa-file-pdf-o');
                $(".filelabel1 i, .filelabel1 .title1").css({'color':'red'});
                $(".filelabel1").css({'border':' 2px solid red'});

            }
  else if(extension == 'doc' || extension == 'docx'){
            $(".filelabel1 i").removeClass().addClass('fa fa-file-word-o');
            $(".filelabel1 i, .filelabel1 .title1").css({'color':'#2388df'});
            $(".filelabel1").css({'border':' 2px solid #2388df'});
        }
            else{
                $(".filelabel1 i").removeClass().addClass('fa fa-file-o');
                $(".filelabel1 i, .filelabel1 .title1").css({'color':'black'});
                $(".filelabel1").css({'border':' 2px solid black'});
            }

            if(fileName ){
                if (fileName.length > 10){
                    $(".filelabel1 .title1").text(fileName.slice(0,4)+'...'+extension);
                }
                else{
                    $(".filelabel1 .title1").text(fileName);
                }
            }
            else{
                $(".filelabel1 .title1").text(labelVal);
            }
        });
		
		$("#FileInput2").on('change',function (e) {
            var labelVal = $(".title2").text();
            var oldfileName = $(this).val();
                fileName = e.target.value.split( '\\' ).pop();

                if (oldfileName == fileName) {return false;}
                var extension = fileName.split('.').pop();

            if ($.inArray(extension,['jpg','jpeg','png']) >= 0) {
                $(".filelabel2 i").removeClass().addClass('fa fa-file-image-o');
                $(".filelabel2 i, .filelabel2 .title2").css({'color':'#208440'});
                $(".filelabel2").css({'border':' 2px solid #208440'});
            }
            else if(extension == 'pdf'){
                $(".filelabel2 i").removeClass().addClass('fa fa-file-pdf-o');
                $(".filelabel2 i, .filelabel2 .title2").css({'color':'red'});
                $(".filelabel2").css({'border':' 2px solid red'});

            }
  else if(extension == 'doc' || extension == 'docx'){
            $(".filelabel2 i").removeClass().addClass('fa fa-file-word-o');
            $(".filelabel2 i, .filelabel2 .title2").css({'color':'#2388df'});
            $(".filelabel2").css({'border':' 2px solid #2388df'});
        }
            else{
                $(".filelabel2 i").removeClass().addClass('fa fa-file-o');
                $(".filelabel2 i, .filelabel2 .title2").css({'color':'black'});
                $(".filelabel2").css({'border':' 2px solid black'});
            }

            if(fileName ){
                if (fileName.length > 10){
                    $(".filelabel2 .title2").text(fileName.slice(0,4)+'...'+extension);
                }
                else{
                    $(".filelabel2 .title2").text(fileName);
                }
            }
            else{
                $(".filelabel2 .title2").text(labelVal);
            }
        });
		
		$("#FileInput3").on('change',function (e) {
            var labelVal = $(".title3").text();
            var oldfileName = $(this).val();
                fileName = e.target.value.split( '\\' ).pop();

                if (oldfileName == fileName) {return false;}
                var extension = fileName.split('.').pop();

            if ($.inArray(extension,['jpg','jpeg','png']) >= 0) {
                $(".filelabel3 i").removeClass().addClass('fa fa-file-image-o');
                $(".filelabel3 i, .filelabel3 .title3").css({'color':'#208440'});
                $(".filelabel3").css({'border':' 2px solid #208440'});
            }
            else if(extension == 'pdf'){
                $(".filelabel3 i").removeClass().addClass('fa fa-file-pdf-o');
                $(".filelabel3 i, .filelabel3 .title3").css({'color':'red'});
                $(".filelabel3").css({'border':' 2px solid red'});

            }
  else if(extension == 'doc' || extension == 'docx'){
            $(".filelabel3 i").removeClass().addClass('fa fa-file-word-o');
            $(".filelabel3 i, .filelabel3 .title3").css({'color':'#2388df'});
            $(".filelabel3").css({'border':' 2px solid #2388df'});
        }
            else{
                $(".filelabel3 i").removeClass().addClass('fa fa-file-o');
                $(".filelabel3 i, .filelabel3 .title3").css({'color':'black'});
                $(".filelabel3").css({'border':' 2px solid black'});
            }

            if(fileName ){
                if (fileName.length > 10){
                    $(".filelabel3 .title3").text(fileName.slice(0,4)+'...'+extension);
                }
                else{
                    $(".filelabel3 .title3").text(fileName);
                }
            }
            else{
                $(".filelabel3 .title3").text(labelVal);
            }
        });
</script>
<?= $v->end() ?>
