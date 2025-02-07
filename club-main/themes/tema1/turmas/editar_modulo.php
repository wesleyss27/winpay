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
            <h3 style="font-family:inter;padding-left: 15px;">Editar Modulas</h3>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="./">Início</a></li>
              <li class="breadcrumb-item active">Editar Modulas</li>
            </ol>
          </div>
		  
		  
        </div>
      </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">
	 <form action="<?= $router->route("appcursos.cadastreditoromodulo"); ?>" id="form" method="post" enctype="multipart/form-data">
             
                        <div class="form-container_box">
                             
                         <fieldset>
							 <div class="form-container">
								<p class="textt text-left">
									 <strong>Editar Módulo</strong><br>edite o nome, conteúdo e as configurações do seu módulo
							   </p>
							</div>
                            <div class="form-container">
								<div class="row">
										<div class="col-lg-12 col-md-12 col-12 form-group">
											<div class="edit_name">
												<label for="formGroupExampleInput">Name do Módulo</label>
												<input type="text" class="form-control" id="formGroupExampleInput" placeholder="Módulo 1" name="module_nome" required value="<?php if(!empty($moduloss[0]->nome)) { echo $moduloss[0]->nome; } ?>">
												
											 </div>
										</div>
									</div>
								<div class="row">
										<div class="col-lg-12 col-md-12 col-12 form-group">
											<div class="edit_textarea">
												<label for="formGroupExampleInput">Descrição do módulo</label>
												<textarea class="form-control" id="exampleFormControlTextarea1" rows="6" placeholder="Módulo 1"name="module_descricao" required><?php if(!empty($moduloss[0]->descricao)) { echo $moduloss[0]->descricao; } ?></textarea>
												
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
								<div class="form_next-prev px-3" >
									<input type="hidden" name="course_id" value="<?php echo $course_id; ?>">
									<input type="submit" name="next-step" class="nxt_step" value="Save" />
									</div>
                        </fieldset>
                       
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
