<?php 
    $v->layout("_theme"); 
?>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2" >
          <div class="col-sm-6" style="margin-bottom:10px">
            <h1 style="font-family:inter"><?= $title ?></h1>
          </div>
          <div class="col-sm-6" style="font-family:inter">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="painel.php">Início</a></li>
              <li class="breadcrumb-item active"><?= $title ?></li>
            </ol>
          </div>
        </div>
		
		
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        
        <button class="btn btn-outline-primary" data-toggle="modal" data-target="#modal-lg" style="font-family:inter" id="cadastro">Cadastrar <i class="fa fa-chevron-right"></i> </button>

        <div class="row">
         
        <?php if($turmas): ?>
        <?php foreach($turmas as $turma): ?>
          
          <!-- single prod -->
          <div class="col-md-3">
            
            <a href="<?= url("cursos/detalhe/".$turma->id)?>">
		  
              <div class="card card-widget widget-user-2 shadow-sm" style="border-radius:0">
             
                <div class="widget-user-header bg-white">
                  <div align="center">
                    <?php if($turma->imagem != ""): ?>

                    <img src="<?= url("uploads/cursos/".$turma->imagem); ?>" alt="<?= $turma->id; ?>" width="200px" height="200px"><br>
                    <?php else: ?>
                    <img src="<?= url("uploads/cursos/default.png"); ?>" alt="<?= $turma->id; ?>" width="200px" height="200px"><br>
                    <?php endif; ?>
                  </div>
                </div>

                <div class="card-footer p-0">
                  <ul class="nav flex-column" style="font-family:inter">
                    <br/>
                    <h5 align="center" style="font-family:inter; text-transform:uppercase; color:#333">
                      <strong><?= mb_strimwidth($turma->nome, 0, 20, "..."); ?></strong>
                    </h5>
                  </ul>
                </div>

              </div>
		        </a>
		      </div>
        <?php endforeach; ?>
        <!-- end single prod -->
		  
        </div>
        <!-- /.row -->
		    
        <?php
          //EXIBE A NAVEGAÇÃO DO PAGINADOR
          $link = URL_BASE."/turmas/p/";

          //VARIÁVEIS VÊM DO CONTROLADOR AppPedidos
          $paginador->paginas($total, $pagina, $link);
          
          else:
        ?>
            <h4>Nenhuma turma encontrada!</h4>
        <?php endif; ?>
      </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->

    <a id="back-to-top" href="#" class="btn btn-primary back-to-top" role="button" aria-label="Scroll to top">
      <i class="fas fa-chevron-up"></i>
    </a>
    
  </div>
  <!-- /.content-wrapper -->

  <!-- cadastar -->
  <div class="modal fade" id="modal-lg">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">

        <div class="modal-header">
          <h3 class="card-title"><i class="fa fa-info-circle"></i> Cadastrar URLS</h3>
          <button type="button" class="close" data-dismiss="modal"> &times;</button>
        </div>

        <form action="#" method="post" style="font-family:inter; font-size:12px"> 
          <div class="modal-body">
          
            <input type="hidden" name="id" id="id">

            <div class="row">
              <div class="col-md-6">
                  <label>Descrição*</label>
                  <input type="text" name="descricao" id='descricao' class="form-control input-lg" placeholder="Descrição">
              </div>

              <div class="col-md-6">
                  <label>Produto*</label>
                  <select name="produto" id="produto" class="form-control">
                    <?php if($cursos): ?>
                      <?php foreach($cursos as $curso): ?>
                        <option value="<?= $curso->id ?>"><?= $curso->nome ?></option>
                      <?php endforeach ?>
                    <?php endif ?>
                  </select>
              </div>
            </div>
              
          </div>

          <div class="modal-footer justify-content-between">
              <button type="button" class="btn btn-danger" data-dismiss="modal" id="cancelar">Cancelar</button>
              <button class="btn btn-primary" id="salvar">Salvar</button>
              <button class="btn btn-warning" id="editar">Editar</button>
          </div>
		    </form>
		
      </div>
    </div>
  </div>
  <!-- end cadastrar -->

<?= $v->start("scripts") ?>
  <script>
    $("#cadastro").click(() => {

        $(".modal-title").html("Cadastrar Urls");
        $("#salvar").css("display", "inline");
        $("#editar").css("display", "none");
        cancelar();
    });

    $("#salvar").click(function(e) {
        e.preventDefault();
        salvarEditar(1);
    });

    $("#editar").click(function(e) {
        e.preventDefault();
        salvarEditar(0);
    });

    $("#cancelar").click(function(e) {
        e.preventDefault();
        cancelar();
    });

    function salvarEditar(salvar) {
        if(salvar == 1) {
            destino = "<?= $router->route("produtos.urlCadastro"); ?>";
        } else {
            destino = "<?=$router->route("produtos.urlEditar")?>";
        }

        var descricao = $("#descricao").val(),
            url = $("#url").val();

        var form = $("form");

        if(descricao == "" || url == ""){

            alert("Preecha todos os dados necessários");

        } else {

            $.ajax({

                url: destino,
                data: form.serialize(),
                type: "POST",
                beforeSend: function () {
                    console.log("atualizando");
                },
                error: function() {
                    console.log("O servidor não conseguiu processar o pedido");
                },
                success: function (callback) {
                    console.log(destino);
                    console.log("callback ", callback);
                    alert(callback);
                    location.reload();
                }

            });
        }
    }

    function editar(i) {
        var id = dados[i]["id"];
        var url = dados[i]["url"];
        var descricao = dados[i]["descricao"];
        var privada = (dados[i]["privada"] == 1) ? true : false;
        var pv = (dados[i]["pv"] == 1) ? true : false;

        $(".modal-title").html("Editar Url");
        $("#salvar").css("display", "none");
        $("#editar").css("display", "inline");
        $("#id").attr({"value": id});
        $("#descricao").attr({"value": descricao});
        $("#privada").prop({"checked": privada});
        $("#pv").prop({"checked": pv});
    }

    function cancelar() {
        $("#salvar").css("display", "inline");
        $("#editar").css("display", "none");
        $("#id").attr({"value": ""});
        $("#url").attr({"value": ""});
        $("#descricao").attr({"value": ""});
        $("#privada").prop({"checked": false});
    }

    function excluir(id) {
        console.log(id);
        if(confirm("Tem certeza que deseja excluir essa url?")){
            $.ajax({
                url: "<?=$router->route("produtos.urlExcluir")?>",
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
                    if(callback == "Excluido com sucesso"){
                        console.log("sucesso");
                        alert(callback);
                        location.reload();
                    } else {
                        console.log("fracasso");
                        alert(callback);
                    }
                }
            });
        }
    }
  </script>
<?= $v->end() ?>