<?php 
$v->layout("_theme"); 

?>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h3 style="font-family:inter;padding-left: 15px;">Editar Coursos</h3>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item active">Editar Coursos</li>
            </ol>
          </div>
		  
		  
        </div>
      </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">
	 <form action="<?= $router->route("appcursos.editarcadastro"); ?>" id="form" method="post" enctype="multipart/form-data" style="font-family:inter; text-transform:uppercase; font-size:11px">
                        <div class="form-container_box">
                              	<fieldset>
									   
										<strong>O registro começa aqui, vá passo a passo</strong>
										<input type="button" name="next-step" 
											class="next-step nxt_step" value="Next Step" />
									
								</fieldset>
								<fieldset>
									<div class="form-container">
										<div class="row">  
											<!-----left---->
											<div class="col-lg-8 col-md-8 col-12 left-area">
												<div class="row">
													<div class="col-lg-12 col-md-12 col-12 form-group">
														<div class="form-group">
															<label for="formGroupExampleInput">Name do curso*</label>
															<input type="text" class="form-control" id="formGroupExampleInput" name="nome" placeholder="name" required value="<?php if(!empty($cursoss[0]->nome)) { echo $cursoss[0]->nome; } ?>">
														 </div>
													</div>
												</div>
												 <?php if($produtos): ?>
											  
												<div class="row">
													<div class="col-lg-12 col-md-12 col-12 form-group">
														<div class="inner-selct ">
														  <label for="inputState">Prdutos</label>
														  <select id="inputState" class="form-control" name="produto" required>
															 <?php foreach($produtos as $produto): ?>
															<option value="<?php echo $produto->id; ?>" <?php if(!empty($cursoss[0]->produto)) { if($cursoss[0]->produto == $produto->id){ echo "selected";} } ?>><?php echo $produto->nome; ?></option>
															<?php  endforeach;  ?>
														  </select>
														</div>
													</div>
												</div>
												<?php endif;  ?>
												<div class="row">
													<div class="col-lg-6 form-group">
														<div class="inner-selct ">
														  <label for="inputState">Assunto*</label>
														  <select id="inputState" class="form-control" name="assunto" required>
															<option <?php if(!empty($cursoss[0]->assunto)) { if($cursoss[0]->assunto == 2){ echo "selected";} } ?>>2</option>
															<option <?php if(!empty($cursoss[0]->assunto)) { if($cursoss[0]->assunto == 3){ echo "selected";} } ?>>3</option>
															<option <?php if(!empty($cursoss[0]->assunto)) { if($cursoss[0]->assunto == 4){ echo "selected";} } ?>>4</option>
															<option <?php if(!empty($cursoss[0]->assunto)) { if($cursoss[0]->assunto == 5){ echo "selected";} } ?>>5</option>
														  </select>
														</div>
													</div>
													
													<div class="col-lg-6 form-group">
														<div class="inner-selct ">
														  <label for="inputState">Formato*</label>
														  <select id="inputState3" class="form-control" name="formato" required>
															<option <?php if(!empty($cursoss[0]->formato)) { if($cursoss[0]->formato == 2){ echo "selected";} } ?>>2</option>
															<option <?php if(!empty($cursoss[0]->formato)) { if($cursoss[0]->formato == 3){ echo "selected";} } ?>>3</option>
															<option <?php if(!empty($cursoss[0]->formato)) { if($cursoss[0]->formato == 4){ echo "selected";} } ?>>4</option>
															<option <?php if(!empty($cursoss[0]->formato)) { if($cursoss[0]->formato == 5){ echo "selected";} } ?>>5</option>
														  </select>
														</div>
													</div>
												</div>
												
											</div><!------left main col ends----->
										<div class="col-lg-4 col-md-4 col-12 right-area" id="right-col_form">
											<label for="exampleFormControlTextarea1">Imagem do curso</label>
												<div class="image-col">
													<div class="input-group mb-3">
													  
													  <div class="custom-file">
															<?php if(!empty($_GET['course_id'])) :  ?>
															 <?php if($cursoss[0]->imagem != ""){ ?>
																<input type="file" accept="image/*" id="imageInput" class="FileUpload1" name="imagem">
																<label for="imageInput" class="filelabel" style="display:none;"><i class="far fa-image"></i> Choose image</label>
																<img src="<?= url("uploads/cursos/".$cursoss[0]->imagem); ?>" class="image-preview">
																<span class="change-image">Choose different image</span>
															 <?php }
															else { ?>
																<input type="file" accept="image/*" id="imageInput" class="FileUpload1" name="imagem">
																<label for="imageInput" class="filelabel"><i class="far fa-image"></i> Choose image</label>
																<img src="" class="image-preview">
																<span class="change-image">Choose different image</span>
															<?php
															}															endif; ?>

														</div>
														
													</div>
												</div>
												
												
											</div>
										</div>
									</div><!------form containe div ends--->
												<?php
									if(!empty($cursoss))
										{
											?>
											<input type="hidden" name="course_id" value="<?php echo $cursoss[0]->id; ?>">
											<input type="hidden" name="course_img" value="<?php echo $cursoss[0]->imagem; ?>">
											
											<?php
										}
										?>
								  <div class="hrs-break" style="    background: #f4f6f9;   float: left;margin-bottom: 20px;  width: 100%; height: 20px;"></div>
								   <div class="form-container">
										<div class="row">
											<div class="form-group col-lg-12 text-areas">
												<label for="exampleFormControlTextarea1">Describe da curso</label>
												<textarea class="form-control" id="exampleFormControlTextarea1" rows="8" name="descricao" required><?php if(!empty($cursoss[0]->descricao)) { echo $cursoss[0]->descricao; } ?>"</textarea>
												
											</div>
											<!--<span class="text-right text-format" id="textarea_feedback">0/6000</span>-->
										</div>
								   </div>
									<div class="form_next-prev px-3">
										<input type="submit" name="next-step" class="nxt_step" value="Save" />
									</div>
								</fieldset>
                      
                        </div>
                    </form>
  

      

    </section>
   
  </div>
  <?php
  
  if(!empty($_GET['course_id']))
  {
	?>
<style>

.progress-bar {
    width: 40% !important;
}
fieldset:nth-child(2) {
  display: block !important;
}

fieldset:nth-child(1) {
  display: none ;
}
</style>

<?php	
  
  }	
  ?>
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

	
$('#imageInput').on('change', function() {
	$input = $(this);
	if($input.val().length > 0) {
		fileReader = new FileReader();
		fileReader.onload = function (data) {
		$('.image-preview').attr('src', data.target.result);
		}
		fileReader.readAsDataURL($input.prop('files')[0]);
		$('.filelabel').css('display', 'none');
		$('.image-preview').css('display', 'block');
		$('.change-image').css('display', 'block');
	}
});
						
$('.change-image').on('click', function() {
	$control = $(this);			
	$('#imageInput').val('');	
	$preview = $('.image-preview');
	$preview.attr('src', '');
	$preview.css('display', 'none');
	$control.css('display', 'none');
	$('.filelabel').css('display', 'block');
});
});

</script>
<?= $v->end() ?>