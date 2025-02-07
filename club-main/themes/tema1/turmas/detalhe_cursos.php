<?php $v->layout("_theme"); ?>

<!-- Content Wrapper. Contains page content -->

<div class="content-wrapper">

<!-- Content Header (Page header) -->
 	<section class="content" id="menu-content" >
	 <!-- .main-outer -->
		<div class="main-outer d-flex justify-content-between">
			<div class="top-head">
			<?php
				if(!empty($cursos->imagem)){
					$img = url("uploads/cursos/".$cursos->imagem);
				} else{
					$img = url("uploads/cursos/default.png");
				}
			?>
		
				<img src="<?= $img; ?>" alt="">
				<span><?= $title ?></span>
			</div>
			<div class="top-head-right">
				<div class="edit_action">
					<a href="javascript:void();" class="course_action"><i class="fas fa-ellipsis-v"></i></a>
					<div class="action" style="display:none;"><span><a href="../editarcursos?course_id=<?php echo $cursos->id; ?>">Edit</a></span> <span><a href="#" onclick="excluir_cursos(<?= $cursos->id; ?>)">Delete</a></span></div>
				</div>
			</div>
		</div>
		<!-- fim .main-outer -->

		<div class="main-button">
			<div class="button-left">
				<a href="<?= url("minhas-compras/viewcursos/".$cursos->id)?>"><i class="fa fa-eye" aria-hidden="true"></i>VISUALIZAR COMO ALUNO</a>
			</div>

			<div class="button-right">
				<a href="../editarmodulo/<?= $cursos->id; ?>"><i class="fa fa-plus" aria-hidden="true"></i>NOVO MODULO</a>
			</div>
		</div>
		
		<p><?= $cursos->descricao; ?></p>
	
		<!-- .row -->
		<div class="row">				
			<div class="col-md-12">
				<div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
				<?php
				if($modulos):
					$i=1;
					foreach($modulos as $modulos):
				?>
				<div class="panel panel-default">
					<div class="panel-heading" role="tab" id="heading<?= $i; ?>">
						<h4 class="panel-title">
							<a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapse<?= $i; ?>" aria-expanded="true" aria-controls="collapse<?= $i; ?>">
									<?=  $modulos->nome; ?>
							</a>
							<div class="edit_action" >
								<a href="javascript:void();" class="module_action"><i class="fas fa-ellipsis-v"></i></a>
								<div class="action" style="display:none;"><span><a href="../editarmodulo/<?php echo $cursos->id; ?>?module_id=<?php echo $modulos->id; ?>">Edit</a></span> <span><a href="#" onclick="excluir_module(<?= $modulos->id; ?>)">Delete</a></span></div>
							</div>
						</h4>
					</div>

					<?php
					$count = [];	
					if($aulas):
						foreach($aulas as $aula):
							if($aula->modulo == $modulos->id):
								$count[] = $modulos->id;
					?>

					<div id="collapse<?= $i; ?>" class="panel-collapse  collapse in" role="tabpanel" aria-labelledby="heading<?= $i; ?>">
						<div class="panel-body">
							<div class="accord_text">
								<div class="left_text">	
									<span class="play_icon mx-2"> <i class="far fa-play-circle icon_color"></i></span>
									<?= $aula->nome; ?>
								</div>
								<div class="right_text" >
									<div class="edit_action" >
										<a href="javascript:void();" class="aulas_action"><i class="fas fa-ellipsis-v"></i></a>
										
										<div class="action" style="display:none;">
											<span>
												<a href="../editaraulas/<?php echo $cursos->id; ?>?module_id=<?php echo $modulos->id; ?>&aulas_id=<?php echo $aula->id; ?>">Editar</a>
											</span> 
											<span>
												<a href="#" onclick="excluir_aula(<?= $aula->id; ?>)">Delete</a>
											</span>
										</div>
									</div>
								</div>											
							</div>						
						</div>
					</div>

					<?php
							endif;
						endforeach;
					else:
						echo "Nenhuma aula encontrada!!";
						
					endif;
					?>
				</div>
				<?php
					$i++;
					endforeach;
				endif;
				?>
				</div>
				<!------edited by yog---->
				
			</div>
			<!--- END COL -->		
		</div>
		<!--- fim .row -->			

		</div>
 	</section>
	<!-- END SECTION -->

</div>
   <?= $v->start("scripts") ?>
  <script>
var acc = document.getElementsByClassName("accordion");
var i;

for (i = 0; i < acc.length; i++) {
  acc[i].addEventListener("click", function() {
    this.classList.toggle("active");
    var panel = this.nextElementSibling;
    if (panel.style.display === "block") {
      panel.style.display = "none";
    } else {
      panel.style.display = "block";
    }
  });
}
$(document).ready(function () {
$(".course_action").click(function(){
			jQuery(this).next('.action').toggle();
			
		});
		
		$(".module_action").click(function(){
			jQuery(this).next('.action').toggle();
			
		});
		
		$(".aulas_action").click(function(){
			jQuery(this).next('.action').toggle();
			
		});
	});
	
function excluir_aula(id) {

    //console.log(id);
	if(confirm("Você realmente deseja excluir esta aulas?")){

        $.ajax({

            url: "<?=$router->route("appcursos.urlExcluirAula")?>",

            data: { id: id},



            type: "POST",

            beforeSend: function () {

                console.log("atualizando");

            },

            error: function() {

                console.log("O servidor não conseguiu processar o pedido");

            },

            success: function (callback) {

                console.log(callback);

                if(callback == "1"){

                    location.reload();

                } else {

                    console.log("fracasso");

                }

            }

        });

    }

}


function excluir_module(id) {

    //console.log(id);
	if(confirm("A exclusão deste módulo também pode afetar outras aulas")){

        $.ajax({

            url: "<?=$router->route("appcursos.urlExcluirModule")?>",

            data: { id: id},



            type: "POST",

            beforeSend: function () {

                console.log("atualizando");

            },

            error: function() {

                console.log("O servidor não conseguiu processar o pedido");

            },

            success: function (callback) {

                console.log(callback);

                if(callback == "1"){

                    location.reload();

                } else {

                    console.log("fracasso");

                }

            }

        });

    }

}


function excluir_cursos(id) {

    //console.log(id);
	if(confirm("Tem certeza de que deseja excluir este curso? Pode afetar módulos e aulas")){

        $.ajax({

            url: "<?=$router->route("appcursos.urlExcluirCursos")?>",

            data: { id: id},



            type: "POST",

            beforeSend: function () {

                console.log("atualizando");

            },

            error: function() {

                console.log("O servidor não conseguiu processar o pedido");

            },

            success: function (callback) {

                console.log(callback);

                if(callback == "1"){

                    window.location.href = "<?=$router->route("appcursos.home")?>";

                } else {

                    console.log("fracasso");

                }

            }

        });

    }

}

</script>

<?= $v->end() ?>