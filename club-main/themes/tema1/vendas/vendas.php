<?php $v->layout("_theme"); ?>

<?= $v->start("links"); ?>
<link rel="stylesheet" href="<?= url("themes/tema1/dist/css/modal.css") ?>">
<?= $v->end(); ?>

<?php
$status_transacao = array(
    1 => "AGUARDANDO PAGAMENTO",
    2 => "EM ANÁLISE",
    3 => "PRÉ AUTORIZADA",
    4 => "PAGA",
    5 => "CANCELADA",
    6 => "DEVOLVIDA",
    7 => "CHARGEBACK",
    8 => "CONCLUÍDA",
    9 => "BLOQUEADA"
);

$statusT = array(
  1 => "text-warning",
  2 => "text-info",
  3 => "text-info",
  4 => "text-success",
  5 => "text-danger",
  6 => "text-danger",
  7 => "text-danger",
  8 => "text-success",
  9 => "text-danger"
);

if($_SESSION["tipo"] == 4){
    $produto = $produtos->find("status != 0 AND usuario = " . $_SESSION["id"])->order("nome ASC")->fetch(true);
    $produtor = $afiliacao->find("status != 0 AND afiliado = " . $_SESSION["id"])->order("nome ASC")->fetch(true);
} elseif ($_SESSION["tipo"] == 5){
    $produto = $afiliacao->find("status != 0 AND afiliado = " . $_SESSION["id"])->order("nome ASC")->fetch(true);
} else {
    $produto = $produtos->find("status != 0")->order("nome ASC")->fetch(true);
}

function dadosComprador($venda){ 
  echo "
  <b>{$venda->dataComprador()->nome} - CPF: </b> {$venda->dataComprador()->cpf_cnpj}<br>
  <b>Tel: </b> <a href='https://api.whatsapp.com/send?phone=55{$venda->dataComprador()->telefone}' title='Entrar em contato pelo whatsapp' target='_blank'>{$venda->dataComprador()->telefone} <i class='fab fa-whatsapp' style='color: green'></i></a> - 
  <b>email: </b><a href='mailto:{$venda->dataComprador()->email}'>{$venda->dataComprador()->email}</a><br>

  Rua {$venda->dataComprador()->rua_entrega}, nº {$venda->dataComprador()->numero_entrega} - {$venda->dataComprador()->complemento_entrega}<br>
  {$venda->dataComprador()->bairro_entrega}, {$venda->dataComprador()->cidade_entrega}, {$venda->dataComprador()->estado_entrega}<br>
  CEP: {$venda->dataComprador()->cep_entrega}";
} 

function dadosEndereco($venda){ 
  echo "
  <b>{$venda->dataEndereco()->nome} - CPF: </b> {$venda->dataEndereco()->cpf_cnpj}<br>
  <b>Tel: </b> <a href='https://api.whatsapp.com/send?phone=55{$venda->dataEndereco()->telefone}' title='Entrar em contato pelo whatsapp' target='_blank'>{$venda->dataEndereco()->telefone} <i class='fab fa-whatsapp' style='color: green'></i></a> - 
  <b>email: </b><a href='mailto:{$venda->dataEndereco()->email}'>{$venda->dataEndereco()->email}</a><br>

  Rua {$venda->dataEndereco()->rua}, nº {$venda->dataEndereco()->numero} - {$venda->dataEndereco()->complemento}<br>
  {$venda->dataEndereco()->bairro}, {$venda->dataEndereco()->cidade}, {$venda->dataEndereco()->estado}<br>
  CEP: {$venda->dataEndereco()->cep}";
} 

?>

<div class="ajax_load">
    <div class="ajax_load_box">
        <div class="ajax_load_box_circle"></div>
        <div class="ajax_load_box_title">Aguarde, carregando!</div>
    </div>
</div>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <!--<div class="container-fluid">-->
	  
	     <div class="col-sm-6">
            <h4 style="font-family:inter"><?= $title ?></h4>
          </div>
       <hr/>
		 
		  <form action="#" style="font-family:inter; font-size:10px"> 
      <div class="callout callout-primary">
			  <div class="row">
                  <div class="col-12 col-md-3">
				     <h6 style="font-family:inter">Data do pedido:</h6>
                    <input type="date" name="inicio" id="inicio" class="form-control" style="cursor:pointer;" value="<?= @$_GET["inicio"]; ?>" >
                  </div>
                  <div class="col-12 col-md-3">
				            <h6 style="font-family:inter; color:#fff">|</h6>
                    <input type="date" name="fim" id="fim" class="form-control" style="cursor:pointer;" value="<?= @$_GET["fim"]; ?>" >
                  </div>
				  
				  <div class="col-12 col-md-3">
				  <h6 style="font-family:inter">Código da venda ou pedido:</h6>
            <input type="text" name="venda" class="form-control" placeholder="ex.: PAY-078VRHCE52BI ou 239" value="<?= @$_GET["venda"]; ?>">
          </div>
				  
				  <div class="col-12 col-md-3">
                 <h6 style="font-family:inter">Status da transação:</h6>
                 <select name="status" id="status" class="form-control">
                    <option value="">Status...</option>
                    <?php foreach($status_transacao as $cod => $status): ?>
                    <option value="<?= $cod ?>" <?php if(@$_GET["status"] == $cod) echo "selected"; echo "" ?>><?= $status ?></option>
                    <?php endforeach; ?>
                </select>
				  </div>
      </div>
    </div>
		
    <div class="callout callout-primary">
    <div class="row">
      <?php if($_SESSION["tipo"] != 6): ?>
      <div class="col-12 col-md-3">
        <h6 style="font-family:inter">Produto:</h6>
              
        <select name="produto" id="produto" class="form-control">
            <option value="">Produto...</option>
            <?php foreach($produto as $produto): ?>
            <option value="<?= $produto->id ?>" <?php if(@$_GET["produto"] == $produto->id) echo "selected"; echo "" ?>><?= $produto->nome ?></option>
            <?php endforeach; ?>
            <?php if(@$produtor): foreach($produtor as $produtor): ?>
            <option value="<?= $produtor->dataProduto()->id ?>" <?php if(@$_GET["produto"] == $produtor->dataProduto()->id) echo "selected"; echo "" ?>><?= $produtor->dataProduto()->nome ?></option>
            <?php endforeach; endif; ?>
        </select>
      </div>
      

    <div class="col-12 col-md-3">
      <h6 style="font-family:inter">Comprador:</h6>
      <input type="text" name="comprador" id="cpf" class="form-control" placeholder="(nome ou CPF/CNPJ ou email)" value="<?= @$_GET["comprador"]; ?>">
    </div>
    <?php endif; ?>

    <div class="col-12 col-md-3">
    <h6 style="font-family:inter">Forma de Pagamento:</h6>
              
    <select name="pagamento" id="pagamento" class="form-control">
        <option value="">Pagamento...</option>
        <option value="1" <?php if(@$_GET["pagamento"] == 1) echo "selected"; echo "" ?>>Cartão de crédito</option>
        <option value="2" <?php if(@$_GET["pagamento"] == 2) echo "selected"; echo "" ?>>Boleto</option>
    </select>
    </div>

    <div class="col-12 col-md-3">
      <h6 style="font-family:inter; color:#fff">|</h6>
      <button type="submit" name="" class="form-control form-control btn-primary"> <i class="fa fa-check"></i> Filtar</button>
    </div>
        
    </div>
  </div>
</form>
	   
	   
      <!--</div>-->
    </section>

    <!-- Main content -->
   
   <section class="content">

      <!-- Default box -->
      <?php if($vendas): ?>
        <?php if($_SESSION["tipo"] != 6): ?>
      <div class="row" style="margin: 0 0 20px 0">
          <div class="col-md-3 center">
            <div class="row">
              <div class="col-md-10 bg-white card-venda alert" style="border-bottom: 3px solid #3aaa3a">
                Total de vendas: <br>
                <h4><?= $count; ?></h4>
              </div>

              <div class="col-md-2"></div>
            </div>
          </div>

          <div class="col-md-3 center">
            <div class="row">
              <div class="col-md-1"></div>

              <div class="col-md-10 bg-white card-venda alert" style="border-bottom: 3px solid #3aaa3a">
                Total faturado*: <br>
                <h4><?= @$somaVendas->soma ? "R$ " . number_format($somaVendas->soma, 2, ",", ".") : "-"; ?></h4>
              </div>

              <div class="col-md-1"></div>
            </div>
          </div>

          <div class="col-md-3 center">
            <div class="row">
              <div class="col-md-1"></div>

              <div class="col-md-10 bg-white card-venda alert" style="border-bottom: 3px solid #3aaa3a">
                Total cartão: <span class="badge bg-warning"><?= number_format($vendasCartao / $count * 100, 2, ",", "."); ?>%</span><br>
                <h4><?= $vendasCartao ?></h4>
              </div>

              <div class="col-md-1"></div>
            </div>
          </div>

          <div class="col-md-3 center">
            <div class="row">
              <div class="col-md-2"></div>

              <div class="col-md-10 bg-white card-venda alert" style="border-bottom: 3px solid #3aaa3a">
                Total boletos: <span class="badge bg-warning"><?= number_format($vendasBoleto / $count * 100, 2, ",", "."); ?>%</span><br>
                <h4><?= $vendasBoleto ?></h4>
              </div>

            </div>
          </div>

          * valor da venda menos a taxa RedRocket

      </div>
      <?php endif; ?>

      <style>
        .legenda {
          width: 25px;
          height: 25px;
          margin: 0 10px;
          border-radius: 50%;
        }
      </style>
      <div class="card card-body callout">
        <div class="row">
          <span class="legenda bg-warning"></span> AGUARDANDO PAGAMENTO 
          <span class="legenda bg-info"></span> EM ANÁLISE 
          <span class="legenda bg-success"></span> PAGA / COMPLETA 
          <span class="legenda bg-danger"></span> CANCELADA / DEVOLVIDA / ESTORNADA
        </div>
      </div>
      
      <div class="card">
   
        <div class="card-body p-0" style="overflow: auto">
          <table class="table table-striped projects" style="font-size:13px">
            <thead>
              <tr style="font-family:inter">
                <th scope="col">Venda</th>
                <th scope="col">Comprador</th>
                <?php if($_SESSION["tipo"] == 6): ?>
                  <th scope="col">Vendedor</th>
                <?php else: ?>
                <th scope="col">Produtor</th>
                <?php endif; ?>
                <th scope="col">Produto</th>
                <th scope="col">R$</th>
                <th scope="col">Status</th>
                <th scope="col">Data Transação</th>
                <th scope="col">Ação</th>
              </tr>
            </thead>

            <tbody>
            <?php     
              $forma_pagamento = array(
                  1 => "CARTÃO DE CRÉDITO",
                  2 => "BOLETO",
                  3 => "PIX"
              );

              $i = 1;
              foreach($vendas as $venda): 
                  /**
                   * BUSCA INFORMAÇÕES DA AFILIAÇÃO, PARA VERIFICAR SE PODE VER DADOS DO COMPRADOR
                   */
                  $afiliado = $afiliacao->find("produto = $venda->produto AND afiliado = " . $_SESSION["id"])->fetch();

                  if(@$coprodutor)
                    $coproducao = @$coprodutor->find("co_produtor = " . $_SESSION["id"] . " and produto = $venda->produto")->fetch();

                  if(@$coproducao){
                    if (@$coproducao->gerente == 0) {
                      if ($coproducao->tipo_comissao == 0) {
                          $valorComissao = $venda->valor_liquido * ($coproducao->comissao / 100);
                      } else {
                          $valorComissao = $coproducao->comissao;
                      }
                    } else {
                        if ($valor->afiliado) {
                            if ($coproducao->tipo_comissao_afiliados == 0) {
                                $valorComissao = $venda->valor_liquido * ($coproducao->comissao_afiliados / 100);
                            } else {
                                $valorComissao = $coproducao->comissao_afiliados;
                            }
                        } else {
                            if ($coproducao->tipo_comissao == 0) {
                                $valorComissao = $venda->valor_liquido * ($coproducao->comissao / 100);
                            } else {
                                $valorComissao = $coproducao->comissao;
                            }
                        }
                    }
                  } else {
                    $valorComissao = $venda->valor_produto - @$venda->valor_desconto;
                  }
            ?>

              <tr style="font-family:inter">
                <td>
                  <small>#<?= $venda->id; ?></small> <br>
                  <small>#<?= $venda->transacao; ?></small>
                </td>

                <?php if($_SESSION["tipo"] == 1 || $_SESSION["tipo"] == 2 || $_SESSION["tipo"] == 3 || $_SESSION["tipo"] == 6): ?>
                    <td>
                      <?= $venda->endereco ? $venda->dataEndereco()->nome : $venda->dataComprador()->nome ?><br>
                      <?= $venda->endereco ? $venda->dataEndereco()->email : $venda->dataComprador()->email ?><br>
                      <?= $venda->endereco ? $venda->dataEndereco()->telefone : $venda->dataComprador()->telefone ?> <a href='https://api.whatsapp.com/send?phone=55<?= $venda->endereco ? $venda->dataEndereco()->telefone : $venda->dataComprador()->telefone ?>' title='Entrar em contato pelo whatsapp' target='_blank'><i class="fab fa-whatsapp" style="color: green"></i></a><br>
                    </td>
                <?php elseif($_SESSION["tipo"] == 4 || ($_SESSION["tipo"] == 5 && @$afiliado->liberar_comprador == 1)): ?>
                      <td>
                        <?= $venda->endereco ? $venda->dataEndereco()->nome : $venda->dataComprador()->nome ?><br>
                        <?= $venda->endereco ? $venda->dataEndereco()->email : $venda->dataComprador()->email ?><br>
                        <?= $venda->endereco ? $venda->dataEndereco()->telefone : $venda->dataComprador()->telefone ?> <a href='https://api.whatsapp.com/send?phone=55<?= $venda->endereco ? $venda->dataEndereco()->telefone : $venda->dataComprador()->telefone ?>' title='Entrar em contato pelo whatsapp' target='_blank'><i class="fab fa-whatsapp" style="color: green"></i></a><br>
                      </td>
                <?php else: ?>
                    <td>Dados ocultado pelo produtor</td>
                <?php endif; ?>

                <td>
                  <?= $venda->dataProdutor()->nome ?><br>
                  <?= $venda->dataProdutor()->email ?>
                </td>

                <?php if($venda->plano): ?>
                  <td><a href="<?= url("produtos/detalhe/$venda->produto") ?>"><?= $venda->dataPlano()->nome ?></a></td>
                <?php else: ?>
                  <td><a href="<?= url("produtos/detalhe/$venda->produto") ?>"><?= $venda->dataProduto()->nome ?></a></td>
                <?php endif ?>
                
                <td>R$ <?= number_format($valorComissao, 2, ",", "."); ?></td>
                <td>
                  <?php if($venda->forma_pagamento == 1): ?>
                    <h4><i class="fa fa-credit-card <?= $statusT[$venda->status_transacao] ?>" title="<?= $status_transacao[$venda->status_transacao] ?>"></i></h4>
                  <?php elseif($venda->forma_pagamento == 2): ?>
                    <h4><i class="fa fa-barcode <?= $statusT[$venda->status_transacao] ?>" title="<?= $status_transacao[$venda->status_transacao] ?>"></i></h4>
                    <?php elseif($venda->forma_pagamento == 3): ?>
                    <img src="<?= url("themes/tema1/images/pix-{$statusT[$venda->status_transacao]}.png") ?>" title="<?= $status_transacao[$venda->status_transacao] ?>" width="20px"></img>
                  <?php endif ?>
                </td>
                <td><?php 
                  if($venda->status_transacao != 1 && $venda->status_transacao != 2){
                    echo formataDataHora($venda->data_finalizada);
                  } else { echo formataDataHora($venda->created_at); } ?>
                </td>

                <td><a class="btn btn-warning btn-sm" data-toggle="modal" data-target="#myModal<?= $venda->id; ?>"><i class="fas fa-edit"></i></a></td>
                <!--td><a class="modalx-slideUp_open" href="#modalx-slideUp<?= $venda->id; ?>"><i class="fas fa-edit"></i></a></td-->
                
              </tr>
                
              <!--?= $v->insert("vendas/modal-info", [
                  "venda" => $venda, 
                  "pagina" => $pagina, 
                  "status_transacao" => $status_transacao, 
                  "forma_pagamento" => $forma_pagamento, 
                  "i" => $i, 
                  "afiliado" => $afiliado,
                  //"dadosComprador" => dadosComprador($venda)
              ]); ?-->

              <form action="" method="post" id="form<?= $i ?>">
                <input type="hidden" name="vencimento" id="vencimento<?= $i ?>">
                <input type="hidden" name="id" id="idV<?= $i ?>" value="<?= $venda->id ?>">
                <input type="hidden" name="transacao" id="transacao<?= $i ?>" value="<?= $venda->transacao ?>">
                <input type="hidden" name="produto" id="produto<?= $i ?>" value="<?= $venda->produto ?>">
                <input type="hidden" name="produtor" id="produtor<?= $i ?>" value="<?= $venda->produtor ?>">
                <input type="hidden" name="nome" id="nome<?= $i ?>" value="<?= $venda->dataComprador()->nome ?>">
                <input type="hidden" name="email" id="email<?= $i ?>" value="<?= $venda->dataComprador()->email ?>">
                <input type="hidden" name="cpf" id="cpf<?= $i ?>" value="<?= $venda->dataComprador()->cpf_cnpj ?>">

                <?php 
                  $tel = @$venda->dataComprador()->telefone;
                  $ddd = substr($tel, 0, 2);
                  $telefone = substr($tel, 2, 12);

                  $data = mt_rand(strtotime("1970-01-01"), strtotime("2000-01-01"));
                  $date = new DateTime();
                  $date->setTimestamp($data);
                  $nascimento = $date->format('Y-m-d');
                ?>

                <input type="hidden" name="nasc" id="nasc<?= $i ?>" value="<?= $nascimento ?>">
                <input type="hidden" name="ddd" id="ddd<?= $i ?>" value="<?= $ddd ?>">
                <input type="hidden" name="telefone" id="telefone<?= $i ?>" value="<?= $telefone ?>">
                <input type="hidden" name="rua" id="rua<?= $i ?>" value="<?= $venda->dataComprador()->rua_entrega ?>">
                <input type="hidden" name="numero" id="numero<?= $i ?>" value="<?= $venda->dataComprador()->numero_entrega ?>">
                <input type="hidden" name="complemento" id="complemento<?= $i ?>" value="<?= $venda->dataComprador()->complemento_entrega ?>">
                <input type="hidden" name="bairro" id="bairro<?= $i ?>" value="<?= $venda->dataComprador()->bairro_entrega ?>">
                <input type="hidden" name="cidade" id="cidade<?= $i ?>" value="<?= $venda->dataComprador()->cidade_entrega ?>">
                <input type="hidden" name="estado" id="estado<?= $i ?>" value="<?= $venda->dataComprador()->estado_entrega ?>">
                <input type="hidden" name="pais" id="pais<?= $i ?>" value="<?= $venda->dataComprador()->pais_entrega ?>">
                <input type="hidden" name="cep" id="cep<?= $i ?>" value="<?= $venda->dataComprador()->cep_entrega ?>">
              
                <?php if($venda->plano): ?>
                  <input type="hidden" name="nomekit" id="nomekit<?= $i ?>" value="<?= $venda->dataPlano()->nome ?>">
                  <input type="hidden" name="codigo_produto" id="codigo_produto<?= $i ?>" value="<?= $venda->dataPlano()->codigo ?>">
                  <input type="hidden" name="quantidade" id="quantidade<?= $i ?>" value="<?= $venda->dataPlano()->quantidade ?>">
                <?php else: ?>
                  <input type="hidden" name="nomekit" id="nomekit<?= $i ?>" value="<?= $venda->dataProduto()->nome ?>">
                  <input type="hidden" name="codigo_produto" id="codigo_produto<?= $i ?>" value="<?= $venda->dataProduto()->codigo ?>">
                  <input type="hidden" name="quantidade" id="quantidade<?= $i ?>" value="1">
                <?php endif ?>

                <input type="hidden" name="email_suporte" id="email_suporte<?= $i ?>" value="<?= $venda->dataProduto()->email_suporte ?>">
                <input type="hidden" name="valor_venda" id="valor_venda<?= $i ?>" value="<?= number_format($venda->valor_produto - $venda->valor_desconto, 2) ?>">
                <input type="hidden" name="valor_total" id="valor_total<?= $i ?>" value="<?= $venda->valor_bruto ?>">
                
              </form>

              <?php include "modal-info.php"?>
              <?php $i++; endforeach; ?>
            </tbody>
          </table>

            <?php
                else:
            ?>
            <h4>Não existem pedidos cadastrados!</h4>
            <?php
            endif;
            ?>
   
        </div>
      </div>
    </section>
  </div>

  <div>
    <?php
      //EXIBE A NAVEGAÇÃO DO PAGINADOR
      $uri = explode("?", $_SERVER["REQUEST_URI"], 2);
      $link = URL_BASE."/vendas/";

      //VARIÁVEIS VÊM DO CONTROLADOR AppPedidos
      $paginador->paginas($total, $pagina, $link, @$uri[1]);

      $url = explode("?", $_SERVER["REQUEST_URI"]);
      if(@$url[1] != ""){
          $conc = "&";
      }
      else{
          $conc = "";
      }
    ?>
  </div>
 
<?= $v->start("scripts"); ?>
<script src="<?= url("themes/tema1/dist/js/modal.js") ?>"></script>
<script>

$(document).ready(function () {
    
  <?php foreach ($vendas as $venda): ?>
    $('#modalx-slideUp<?= $venda->id ?>').popup({
        pagecontainer: '.container',
        transition: 'all 0.5s',
        background: true,
        color: '#000',
        opacity: '0.5',
    });
    <?php endforeach ?>
});

function load(action, tempo = 1000) {
    var load_div = $(".ajax_load");
    if(action === "open") {
        load_div.fadeIn().css("display", "flex");
        setTimeout(() => {
            load_div.fadeOut();
        }, tempo);
    }
    else {
        load_div.fadeOut();
    }
}

  function vencimento(i) {
    $("#vencimento" + i).val($("#data_venc" + i).val());
  }

  function editar(i) {
    load("open", 3000);
    //alert('clicou ' + i);
    var form = $("#form" + i);

    $.ajax({
      url: "<?= $router->route("checkout.novoBoleto") ?>",
      method: "POST",
      data: form.serialize(),
      success: function(callback) {
        console.log(callback);
        location.reload();
      }
    })
  }

  function notaFiscal(i) {
    load("open", 3000);
    //alert('clicou ' + i);
    var form = $("#form" + i);

    $.ajax({
      url: "<?= $router->route("notazz.gerar") ?>",
      method: "POST",
      data: form.serialize(),
      success: function(callback) {
        console.log(callback);
        var dados = JSON.parse(callback);
        if(dados.statusProcessamento == "sucesso"){
          alert("Nota fiscal gerada com sucesso!\nID da nota fiscal " + dados.id + "\n" + dados.msg);
        } else {
          alert("Erro ao gerar nota fiscal\n" + dados.motivo);
        }
        //location.reload();
      }
    })
  }

  function motivo(i) {
    load("open", 3000);
    //alert('clicou ' + i);
    var motivo = $("#motivo" + i).val();
    console.log(i);

    $.ajax({
      url: "<?= $router->route("vendas.motivo") ?>",
      method: "POST",
      data: {id: i, motivo: motivo},
      success: function(callback) {
        console.log(callback);
        
        //location.reload();
      }
    })
  }
  
  function postback(venda, produtor, produto) {
    load("open", 3000);

    $.ajax({
      url: "<?= $router->route("postback.retorno") ?>",
      method: "POST",
      data: {venda: venda, produtor: produtor, produto: produto},
      success: function(callback) {
        console.log(callback);
        alert(callback);
        
        //location.reload();
      }
    })
  }

  function bling(venda, produtor, produto) {
    load("open", 3000);

    $.ajax({
      url: "<?= $router->route("bling.criarPedido") ?>",
      method: "POST",
      data: {venda: venda, usuario: produtor, produto: produto},
      success: function(callback) {
        console.log(callback);
        alert(callback);
        
        //location.reload();
      }
    })
  }
</script>
<?= $v->end(); ?>