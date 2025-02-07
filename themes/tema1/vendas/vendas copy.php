<?php $v->layout("_theme"); ?>

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

if($_SESSION["tipo"] == 4){
    $produto = $produtos->find("status != 0 AND usuario = " . $_SESSION["id"])->fetch(true);
    $produtor = $afiliacao->find("status != 0 AND afiliado = " . $_SESSION["id"])->fetch(true);
} elseif ($_SESSION["tipo"] == 5){
    $produto = $afiliacao->find("status != 0 AND afiliado = " . $_SESSION["id"])->fetch(true);
} else {
    $produto = $produtos->find("status != 0")->fetch(true);
}

function dadosComprador($venda){ 
  echo "
  <h3>Dados do Comprador</h3>
  <b>{$venda->dataComprador()->nome} - CPF: </b> {$venda->dataComprador()->cpf_cnpj}<br>
  <b>Tel: </b> <a href='https://web.whatsapp.com/send?phone=55{$venda->dataComprador()->telefone}' title='Entrar em contato pelo whatsapp' target='_blank'>{$venda->dataComprador()->telefone}</a> - 
  <b>email: </b><a href='mailto:{$venda->dataComprador()->email}'>{$venda->dataComprador()->email}</a><br>

  Rua {$venda->dataComprador()->rua_entrega}, nº {$venda->dataComprador()->numero_entrega} - {$venda->dataComprador()->complemento_entrega}<br>
  {$venda->dataComprador()->bairro_entrega}, {$venda->dataComprador()->cidade_entrega}, {$venda->dataComprador()->estado_entrega}<br>
  CEP: {$venda->dataComprador()->cep_entrega}";
} 

?>

 <!-- Content Wrapper. Contains page content -->
 <div class="content-wrapper">
 <!-- Content Header (Page header) -->
 <section class="content-header">
 <!--<div class="container-fluid">--> 
 <div class="col-sm-6">
 <h4 style="font-family:inter">Vendas</h4>
 </div>
 <hr/>
 <!--</div>-->
 </section>
 <!-- Main content -->
 <section class="content">
 <!-- Default box -->
 <div class="card">
 <div class="card-body p-0">
 <table class="table table-striped projects" style="font-size:13px">
<thead>
<tr style="font-family:inter">
<th>Venda</th>
<th>Comprador</th>
<th>Produtor</th>
<th>Produto</th>
<th>Valor</th>
<th>Confirmado</th>
<th>Status</th>
<th>Data Transação</th>
<th style="width:10%">Ação</th>
</tr>
</thead>
   
<tbody>
<!-- select vendas -->
<tr style="font-family:inter">

<td>1</td>
<td>
<a>Sullivan Procopio Rodrigues</a>
</td>
<td><a>30</a></td>
<td><a>7.50%</a></td>
<td><a>15/01/1986</a></td>
<td><a>Sim</a></td>
<td><a>SIM</a></td>
<td><a>0</a></td>
<td>
<a class="btn btn-warning btn-sm" data-toggle="modal" data-target="#ideditar"><i class="fas fa-edit"></i></a>

</td>

</tr>

<!-- end select vendas -->
				  
   </tbody>
   </table>	  

<!-- modal mostrar vendas -->
    
	<div class="modal fade" id="ideditar">
    <div class="modal-dialog modal-xl">
    <div class="modal-content">

    <div class="modal-body" style="font-family:inter; font-size:13px">
    <div class="card card-warning">

    <section class="content" >
      <div class="container-fluid">
        <div class="row">
          <div class="col-12">
           
            <!-- Main content -->
            <div class="invoice p-3 mb-3" style="border:none">
              <!-- title row -->
              <div class="row" >
                <div class="col-12">
                  <h4>
                    Dados do Comprador: Ana Francisca dos Santos
                    <small class="float-right">Pedido nº: #0000 | <span data-dismiss="modal"  style="cursor:pointer"><i class="fa fa-close"></i>X Fechar</span>
					</small>
                  </h4>

                </div>
                <!-- /.col -->
              </div>
              <!-- info row -->
              <div class="row invoice-info">
                <div class="col-sm-6 invoice-col">
                
                  <address>
                    <br>
                    CPF: 50417746172<br>
                    TEL: 61996769076<br>
                    E-MAIL: aanafrancisca39@gmail.com<br>
                    
                  </address>
                </div>
                <!-- /.col -->

                <div class="col-sm-6 invoice-col">
               
                  <address>
                    <br>
                    RUA: Rua 50, nº 291 - Vila nova, São Sebastião, DF<br>
                    CEP: 71690153<br>
                    
                  
                  </address>
                </div>
                <!-- /.col -->
   
                <!-- /.col -->
              </div>
              <!-- /.row -->

<br/>
				
			
  <div class="row">
  <div class="col-4">
                
  <div align="center"><img src="images/produto.png" style="width:80%;"></div>
                
                  </div>
				
				
				
				<div class="col-8">
                  <p class="lead">Informações do Produto:</p>



                  <div class="table-responsive">
                    <table class="table">
					

                     					 <tr>
                        <th>Produto:</th>
                        <td>12348 - Phytophen Caps</td>
                      </tr>
					 <tr>
                        <th>Plano:</th>
                        <td>29902 - 2 Potes Phytophen Caps</td>
                      </tr>
					  
					   <tr>
                        <th>Preço:</th>
                        <td>R$ 197,00</td>
                      </tr>
                      
					   <tr>
                        <th>Itens por plano:</th>
                        <td>2</td>
                      </tr>
					  
                   </table>
                  </div>
				  
			<p class="lead">Pagamento:</p>
	<div class="table-responsive">
                    <table class="table">				  




<tr>             				  				  
<th>Data do Pedido:</th>
<td>02/09/2020 17:45:50</td>
</tr>

<tr>
<th>Data Finalizada: </th>
<td>04/09/2020 13:18:36</td>
</tr>

<tr>
<th>Status do Pagamento:</th>
<td>PAGA</td>
</tr>

<tr>
<th>Valor bruto:</th>
<td>R$ 208.89</td>
</tr>

<tr>
<th>Parcelas: </th>
<td>3</td>
</tr>

<tr>
<th>Meio de Pagamento: </th>
<td>Moip</td>
</tr>

<tr>
<th>Forma de Pagamento: </th>
<td>CARTÃO DE CRÉDITO</td>
</tr>
					  
   </table>
                  </div>

</div>
<!-- /.col -->
</div>
<!-- /.row -->
				  
                </div>
			
                </div>
				
				<br/>
				<br/>
			
                <!-- /.col -->
              </div>
              <!-- /.row -->

              <div class="row" style="padding:20px">
                <!-- accepted payments column -->
				
				<div class="col-6">
                  <p class="lead">Taxas e Comissões:</p>



                  <div class="table-responsive">
                    <table class="table">
                      <tr>
                        <th style="width:50%">Produtor:</th>
                        <td>dgoncalvesgv@gmail.com</td>
                      </tr>
                      <tr>
                        <th>Total da venda:</th>
                        <td>R$ 208,89</td>
                      </tr>
                      <tr>
                        <th>Valor do produto:</th>
                        <td>R$ 197,00</td>
                      </tr>
                      <tr>
                        <th>Juros do cartão:</th>
                        <td>R$ 11,89</td>
                      </tr>
					  
					  
					   <tr>
                        <th>Valor do frete:</th>
                        <td>R$ 0,00</td>
                      </tr>
					  
					     <tr>
                        <th>Valor de desconto:</th>
                        <td>R$ 0,00</td>
                      </tr>
					  
					  <tr>
                        <th>Taxa redrocket:</th>
                        <td>R$ 0,00 (R$ -1,00 + R$ 1,00)</td>
                      </tr>
					  
					  					  <tr>
                        <th>Valor liquido:</th>
                        <td>R$ 197,00</td>
                      </tr>
					  
					  		  <tr>
                        <th>Parcelas:</th>
                        <td>3</td>
                      </tr>
					  
					  
					  
                    </table>
                  </div>
                </div>
				
				
						<div class="col-6">
                  <p class="lead">Rastreio:</p>

                  <div class="table-responsive">
                    <table class="table">
                      <tr>
                        <th>Link do Boleto:</th>
                        <td>--</td>
                      </tr>
                      <tr>
                        <th>Url Checkout:</th>
                        <td><a href="redrocketpay.com/redrocket/checkout/840BE07B" target="_blank" rel="nofollow">redrocketpay.com/redrocket/checkout/840BE07B</a></td>
                      </tr>
                     
					 <tr>
                        <th>SRC:</th>
                        <td>--</td>
                      </tr>
             
			           <tr>
                        <th>UTM_SOURCE:</th>
                        <td>--</td>
                      </tr>
			 
			 
			   <tr>
                        <th>UTM_CAMPAIGN:</th>
                        <td>--</td>
                      </tr>
					  
					    <tr>
                        <th>UTM_MEDIUM:</th>
                        <td>--</td>
                      </tr>
					  
					    <tr>
                        <th>UTM_CONTENT:</th>
                        <td>--</td>
                      </tr>
			 

                    </table>
                  </div>
                </div>
               
              </div>
   
            </div>
            
          </div>
        </div>
      </div>
    </section>
   
      </div>

       </div>
   
<!-- end modal mostrar vendas -->
	  
    </div>
      </div>
       </div>
    
      </div>
     
    </section>
   
  </div>
 
  <footer class="main-footer">
   </body>
<?php include_once "inc/footer.php";?>