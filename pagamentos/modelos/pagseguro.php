<?php

$url = "https://ws.sandbox.pagseguro.uol.com.br/v2/sessions";

$credenciais = array(
    "email" => "fhamonteiro@hotmail.com",
    "token" => "749E4412AFE645D48C23CEF0B11C2733"
);
$curl = curl_init($url);
curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($credenciais));
$resultado = curl_exec($curl);
curl_close($curl);
$session = simplexml_load_string($resultado)->id;

?>

<!-- MODO SANDBOX -->
<script type="text/javascript" src="https://stc.sandbox.pagseguro.uol.com.br/pagseguro/api/v2/checkout/pagseguro.directpayment.js"></script>

<!-- MODO PRODUÇÃO -->
<!-- <script type="text/javascript" src="https://stc.pagseguro.uol.com.br/pagseguro/api/v2/checkout/pagseguro.directpayment.js"></script> -->

<script type="text/javascript">
    // Obtém parr
    PagSeguroDirectPayment.setSessionId('<?php $session ?>');


    // Gera um identificador com os dados do comprador baseado em uma sessão determinada
    PagSeguroDirectPayment.onSenderHashReady(function(response) {
        if (response.status == 'error') {
            console.log(response.message);
            return false;
        }
        var hash = response.senderHash; //Hash estará disponível nesta variável.
    });

    //Metodo necessario para o meio de pagamento cartão de crédito
    // Verifica se o cartão é internacional
    $("#creditCardNumber").keyup(function() {
        if ($("#creditCardNumber").val().length >= 6) {
            PagSeguroDirectPayment.getBrand({
                cardBin: $("#creditCardNumber").val().substring(0, 6),
                success: function(response) {
                    console.log(response);
                    $("#creditCardBrand").val(response['brand']['name']);
                    $("#creditCardCvv").attr('size', response['brand']['cvvSize']);
                },
                error: function(response) {
                    console.log(response);
                }
            })
        };
    })

    //Gera Token do cartão de credito
	$("#generateCreditCardToken").click(function(){
		var param = {
			cardNumber: $("#creditCardNumber").val(),
			cvv: $("#creditCardCvv").val(),
			expirationMonth: $("#creditCardExpMonth").val(),
			expirationYear: $("#creditCardExpYear").val(),
			success: function(response) {
				console.log("TOKEN CREDITCARD:", response);
				$("#creditCardToken").val(response['card']['token']);
			},
			error: function(response) {
				console.log(response);
				printError(response);
			}
		}
			//parâmetro opcional para qualquer chamada
			if($("#creditCardBrand").val() != '') {
				param.brand = $("#creditCardBrand").val();
			}
			PagSeguroDirectPayment.createCardToken(param);
	})
</script>