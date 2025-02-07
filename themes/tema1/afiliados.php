<?php 
    $v->layout("_theme"); 

    if($_SESSION["tipo"] != 1 && $_SESSION["tipo"] != 2){
        header("Location: " . URL_BASE);
    }

    $ativo = array(
        0 => "NÃO",
        1 => "SIM"
    );

    $grupo = array(
        1 => "Iniciante",
        2 => "Bronze",
        3 => "Prata",
        4 => "Ouro"
    );

    $cpf = array(
        1 => "CPF",
        2 => "CNPJ"
    );
?>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h4 style="font-family:inter">Afiliados</h4>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="./">Início</a></li>
              <li class="breadcrumb-item">Afiliados</li>
         
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">

      <!-- Default box -->
      <div class="card">
   
        <div class="card-body p-0" style="overflow: auto">
        <?php if($afiliado): ?>
            <script> var dados = []; </script>
          <table class="table table-striped projects" style="font-size:13px">
            <thead>
              <tr style="font-family:inter">
                <th scope="col">#</th>
                <th scope="col">Nome</th>
                <th scope="col">Dias para Saque</th>
                <th scope="col">Taxa de Saque</th>
                <th scope="col">Desde</th>
                <th scope="col">Grupo</th>
                <th scope="col">Ativo</th>
                <th scope="col">Vendas</th>
                <th scope="col">Ação</th>
              </tr>
            </thead>
     

                <tbody>
                  <?php 
                      $i = 1; 
                      foreach($afiliado as $afiliado):
                      
                      $venda = $vendas->find("afiliado = $afiliado->id")->count();
                  ?>
                  <tr style="font-family:inter">
                    <th scope="row"><?= $afiliado->id; ?></th>
                    <td><?= $afiliado->nome . "<br>" . $afiliado->email . "<br>" . $afiliado->login; ?></td>
                    <td align="center"><?= $afiliado->tempo_saque; ?></td>
                    <td align="center"><?= number_format($afiliado->taxa_saque, 2, ",", "."); ?>%</td>
                    <td><?= formataData($afiliado->created_at); ?></td>
                    <td align="center"><?= $grupo[$afiliado->grupo]; ?></td>
                    <td align="center"><?= $ativo[$afiliado->ativo]; ?></td>
                    <td align="center"><?= $venda; ?></td>
                    <td>
                      <button class="btn btn-warning btn-sm" data-target="#ideditar" onclick="editar(<?= $i; ?>)" data-toggle="modal"><i class="fas fa-edit"></i></button>
                      <?php if($afiliado->ativo == 1): ?>
                          <button class="btn btn-danger btn-sm" onclick="ativo(<?= $afiliado->id; ?>, 0)"><i class="fas fa-ban"></i></button>
                      <?php else: ?>
                          <button class="btn btn-success btn-sm" onclick="ativo(<?= $afiliado->id; ?>, 1)"><i class="fas fa-check"></i></button>
                      <?php endif; ?>
                    </td>

                  </tr>
				  
                <?php $dados = $afiliadoDados->find("usuario = $afiliado->id")->fetch(); ?>
                    <script>
                    console.log(<?= $i ?>);
                        dados[<?= $i ?>] = {
                            id : "<?= $afiliado->id ?>",
                            email : "<?= $afiliado->email ?>",
                            nome : "<?= $afiliado->nome ?>",
                            tempo_saque : "<?= $afiliado->tempo_saque ?>",
                            taxa_saque : "<?= $afiliado->taxa_saque ?>",
                            observacao : "<?= $afiliado->observacao ?>",
                            ativo : "<?= $afiliado->ativo ?>",
                            grupo : "<?= $afiliado->grupo ?>",

                            tipo_doc: "<?= @$cpf[$dados->tipo_doc] ?>",
                            cpf: "<?= @$dados->cpf_cnpj ?>",
                            
                            email: "<?= @$dados->email ?>",
                            telefone: "<?= @$dados->telefone ?>",

                            cidade: "<?= @$dados->cidade ?>",
                            estado: "<?= @$dados->estado ?>",
                        }
                    </script>
                <?php $i++; endforeach; ?>
                
              </tbody>
          </table>

          <?php
        //EXIBE A NAVEGAÇÃO DO PAGINADOR
        $link = URL_BASE."/afiliados/";

        //VARIÁVEIS VÊM DO CONTROLADOR AppProdutor
        $paginador->paginas($total, $pagina, $link);
        
        else:
        ?>
        <h4>Não existem afiliados cadastrados!</h4>
        <?php
        endif;
        ?>
		  
	  <!-- cadastar -->
	  <!-- end cadastrar -->
	  
	   <!-- editar -->
		   <div class="modal fade" id="ideditar">
        <div class="modal-dialog modal-lg">
          <div class="modal-content">

            <div class="modal-header">
              <h3 class="card-title"><i class="fa fa-info-circle"></i> Opções para <span id="nome_produtor"></span></h3>
              <button type="button" class="close" data-dismiss="modal"> &times;</button>
            </div>

            <form action="#" method="post" style="font-family:inter; font-size:12px"> 
            <div class="modal-body">
			  
               <input type="hidden" name="id" id="id">
			  
                <div class="row">
                  <div class="col-md-6">
                      <b>Tipo Documento: </b> <span id="tipo_doc"></span><br>
                      <b>CPF/CNPJ: </b> <span id="cpf"></span><br>
                      <b>Email: </b> <span id="email"></span><br>
                  </div>

                  <div class="col-md-6">
                      <b>Telefone: </b> <span id="telefone"></span><br>
                      <b>Cidade: </b> <span id="cidade"></span> - <span id="estado"></span><br>
                  </div>
              </div>
              
              <br>
              
              <div class="row">
                  <div class="col-md-6">
                      Tempo de saque:
                      <input type="text" name="tempo_saque" id='tempo_saque' class="form-control input-lg" placeholder="Tempo de saque">
                  </div>

                  <div class="col-md-6">
                      Taxa de saque:
                      <input type="text" name="taxa_saque" id='taxa_saque' class="form-control input-lg" placeholder="Taxa de saque">
                  </div>
              </div>

              <br>

              Observação:
              <textarea name="observacao" id='observacao' class="form-control input-lg" placeholder="Observação"></textarea>

              <br>

              <div class="row">
                  <div class="col-md-6">
                      Status:
                      <select name="ativo" id="ativo" class="form-control input-lg" required="required">
                          <option disabled selected>Ativo *</option>
                          <option value="1">Ativo</option>
                          <option value="0">Inativo</option>
                      </select>
                  </div>

                  <div class="col-md-6">
                      Grupo:
                      <select name="grupo" id="grupo" class="form-control input-lg" required="required">
                          <option disabled selected>Grupo *</option>
                          <option value="1">Iniciante</option>
                          <option value="2">Bronze</option>
                          <option value="3">Prata</option>
                          <option value="4">Outro</option>
                      </select>
                  </div>
              </div>
            </div>
            <div class="modal-footer justify-content-between">
              <button type="button" class="btn btn-danger" data-dismiss="modal" id="cancelar">Cancelar</button>
              <button class="btn btn-primary" id="salvar">Salvar</button>
            </div>
				</form>
          </div>
      
        </div>

      </div>
	  <!-- end editar -->
		  
        </div>
    
      </div>
      

    </section>
   
  </div>

  <?= $v->start("scripts"); ?>
  <script>
    $("#taxa_saque").blur(function(e) {
        var valor = $("#taxa_saque").val();
        $("#taxa_saque").val(valor.replace(",", "."));
    });

    function editar(i) {
        $('form').each (function(){
            this.reset();
        });
        var id = dados[i]["id"];
        var email = dados[i]["email"];
        var nome = dados[i]["nome"];
        var tempo_saque = dados[i]["tempo_saque"];
        var taxa_saque = dados[i]["taxa_saque"];
        var observacao = dados[i]["observacao"];
        var ativo = dados[i]["ativo"];
        var grupo = dados[i]["grupo"];

        console.log(id, email, nome, tempo_saque, taxa_saque, observacao, ativo, dados[i]["cpf"]);


        $("#nome_produtor").html(nome);
        $("#id").attr({"value": id});
        $("#email").attr({"value": email});
        $("#nome").attr({"value": nome});
        $("#tempo_saque").attr({"value": tempo_saque});
        $("#taxa_saque").attr({"value": taxa_saque});
        $("#observacao").attr({"value": observacao});
        $("#ativo option[value=" + ativo + "]").attr({"selected": true});
        $("#grupo option[value=" + grupo + "]").attr({"selected": true});

        $("#tipo_doc").html(dados[i]["tipo_doc"]);
        $("#cpf").html(dados[i]["cpf"]);
        $("#email").html(dados[i]["email"]);
        $("#telefone").html(dados[i]["telefone"]);
        $("#cidade").html(dados[i]["cidade"]);
        $("#estado").html(dados[i]["estado"]);
    }

    $("#salvar").click(function(e) {
        e.preventDefault();
        salvarEditar(1);
    });
    
    function salvarEditar(salvar) {
        if(salvar == 1) {
            url = "<?= $router->route("afiliado.editar"); ?>";
        } else {
            url = "<?=$router->route("appusuarios.editar")?>";
        }

        var form = $("form");
        $.ajax({

            url: url,
            data: form.serialize(),
            type: "POST",
            beforeSend: function () {
                console.log("atualizando");
            },
            error: function() {
                console.log("O servidor não conseguiu processar o pedido");
            },
            success: function (callback) {
                console.log("callback ", callback);
                alert(callback);
                if(callback == "Atualizado Com Sucesso!"){
                    $('form').each (function(){
                        this.reset();
                    });
                    location.reload();
                }
            }

        });
    }

    function ativo(id, ativo) {
        $.ajax({
            url: "<?= $router->route("afiliado.inativar"); ?>",
            data: {id: id, ativo: ativo},
            type: "POST",
            beforeSend: function () {
                console.log("atualizando");
            },
            error: function() {
                console.log("O servidor não conseguiu processar o pedido");
            },
            success: function (callback) {
                console.log("callback ", callback);
                alert(callback);
                if(callback == "Atualizado com sucesso"){
                    location.reload();
                }
            }

        });
    }
</script>
<?= $v->end(); ?>
