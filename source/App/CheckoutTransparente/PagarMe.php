<?php

namespace Source\App\CheckoutTransparente;

use Source\Models\Configuracoes\ConfiguracaoCheckout;
use Source\Models\CartaoCheckoutTransparente\Adquirente;
use Source\Models\Vendas\Venda;
use Source\App\Processos\AppProcessos;

class PagarMe
{
  private $view;
  private $endpoint;
  private $token;
  private $key;
  private $nome_fatura;
  private $endpoint_consulta;

  public function __construct()
  {
    // $configuracaoCheckout = (new ConfiguracaoCheckout())->findById(5);
    // $configuracao = (new ConfiguracaoCheckout())->findById(1);
    // if ($configuracao->sandbox == 1) {
    //     $this->endpoint = "https://api.pagar.me/1/";
    //     $this->token = $configuracaoCheckout->token_sandbox;
    //     $this->key = $configuracaoCheckout->chave_sandbox;
    // } else {
    //     $this->endpoint = "https://api.pagar.me/1/";
    //     $this->token = $configuracaoCheckout->token_producao;
    //     $this->key = $configuracaoCheckout->chave_producao;
    // }
  }

  public function dados($data)
  {
    // var_dump($data);
    $adquirente = (new Adquirente())->findById(@$data);

    if (@$adquirente) {
      $this->endpoint = "https://api.pagar.me/core/v5/";

      $this->token = @$adquirente->token;
      $this->key = @$adquirente->chave;
      $this->nome_fatura = @$adquirente->nome_fatura;
      // $this->email = $adquirente->email;
    }
  }

  public function executa($url, $metodo, $json = null)
  {
    $autorization = base64_encode($this->token . ":" . $this->key);
    // echo "Auth: " . $autorization;
    // exit;
    $curl = curl_init();

    curl_setopt_array($curl, array(
      CURLOPT_URL => "$url",
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => "$metodo",
      CURLOPT_POSTFIELDS => $json,
      CURLOPT_HTTPHEADER => array(
        "Authorization: Basic $autorization",
        'Content-Type: application/json'
      ),
    ));

    $response = curl_exec($curl);

    curl_close($curl);
    // echo $response;
    // exit;
    return json_decode($response);
  }

  public function compraCartao($data)
  {

    $this->dados(@$data["adquirente"]);

    if (strlen(trim(@$data["cpf"])) == 11)
      $type = "individual";
    else
      $type = "company";

    $ano_val = substr(@$data["ano-val"], -2);
    $numero_cartao = preg_replace('/\D+/', '', (string) ($data["numcart"] ?? ""));
    $cvv = preg_replace('/\D+/', '', (string) ($data["cvv"] ?? ""));

    $dados["url"] = $this->endpoint . "/orders";

    $dados["json"] = '{
          "items": [
              {
                "amount": ' . @$data["preco"] . ',
                "description": "' . @$data["nomekit"] . '",
                "quantity": 1,
                "code": "' . @$data["produto"] . '"
              }
          ],
          "customer": {
            "name": "' . @$data["nome"] . '",
            "document": "' . @$data["cpf"] . '",
            "email": "' . @$data["email"] . '",
            "type": "' . @$type . '",
            "phones": {
              "mobile_phone": {
                "country_code": "55",
                "area_code": "' . @$data["ddd"] . '",
                "number": "' . @$data["telefone"] . '"
              }
            }
          },
          "shipping": {
              "recipient_name": "' . @$data["nome"] . '",
              "description": "' . @$data["nomekit"] . '",
              "recipient_phone": "' . @$data["telefone"] . '",
              "address": {
                  "line_1": "' . @$data["numero"] . ', ' . @$data["rua"] . ', ' . @$data["bairro"] . '",
                  "zip_code": "' . @$data["cep"] . '",
                  "city": "' . @$data["cidade"] . '",
                  "state": "' . @$data["estado"] . '",
                  "country": "BR"    
              }
          },
          "payments": [
              {
                  "payment_method": "credit_card",
                  "credit_card": {
                      "recurrence": false,
                      "installments": ' . @$data["parcela"] . ',
                      "statement_descriptor": "' . $this->nome_fatura . '",
                      "card": {
                          "number": "' . $numero_cartao . '",
                          "holder_name": "' . @$data["titular"] . '",
                          "exp_month": "' . @$data["mes-val"] . '",
                          "exp_year": "' . @$ano_val . '",
                          "cvv": "' . $cvv . '",
                          "billing_address": {
                            "line_1": "' . @$data["numero"] . ', ' . @$data["rua"] . ', ' . @$data["bairro"] . '",
                            "zip_code": "' . @$data["cep"] . '",
                            "city": "' . @$data["cidade"] . '",
                            "state": "' . @$data["estado"] . '",
                            "country": "BR"                 
                          }
                      }
                  }
              }
          ]
      }';
    // var_dump($dados["json"]);
    // exit;
    // return $dados;
    return $this->executa($dados["url"], "POST", $dados["json"]);
  }

  public function compraBoleto($data)
  {
    $this->dados(@$data["adquirente"]);

    if (strlen(trim(@$data["cpf"])) == 11)
      $type = "individual";
    else
      $type = "company";

    $dados["url"] = $this->endpoint . "/orders";

    $dados["json"] = '{
          "items": [
              {
                "amount": ' . @$data["preco"] . ',
                "description": "' . @$data["nomekit"] . '",
                "quantity": 1,
                "code": "' . @$data["produto"] . '"
              }
          ],
          "customer": {
            "name": "' . @$data["nome"] . '",
            "email": "' . @$data["email"] . '",
            "document": "' . @$data["cpf"] . '",
            "type": "' . @$type . '",
            "phones": {
              "mobile_phone": {
                "country_code": "55",
                "area_code": "' . @$data["ddd"] . '",
                "number": "' . @$data["telefone"] . '"
              }
            }
          },
          "payments": [
              {
                  "payment_method": "boleto",
                  "boleto": {
                      "instructions": "Pagar até o vencimento",
                      "due_at": "' . @$data["vencimento"] . '"
                  }
              }
          ]
      }';

    // var_dump($dados["json"]);
    // exit;
    // return $dados;
    return $this->executa($dados["url"], "POST", $dados["json"]);
  }

  public function compraPix($data)
  {
    $this->dados(@$data["adquirente"]);

    if (strlen(trim(@$data["cpf"])) == 11)
      $type = "individual";
    else
      $type = "company";

    $dados["url"] = $this->endpoint . "/orders";

    $dados["json"] = '{
          "items": [
              {
                "amount": ' . @$data["preco"] . ',
                "description": "' . @$data["nomekit"] . '",
                "quantity": 1,
                "code": "' . @$data["produto"] . '"
              }
          ],
          "customer": {
            "name": "' . @$data["nome"] . '",
            "email": "' . @$data["email"] . '",
            "document": "' . @$data["cpf"] . '",
            "type": "' . @$type . '",
            "phones": {
              "mobile_phone": {
                "country_code": "55",
                "area_code": "' . @$data["ddd"] . '",
                "number": "' . @$data["telefone"] . '"
              }
            }
          },
          "payments": [
              {
                  "payment_method": "pix",
                  "pix": {
                      "expires_in": "1800"
                  }
              }
          ]
      }';

    // var_dump($dados["json"]);
    // exit;
    // return $dados;
    return $this->executa($dados["url"], "POST", $dados["json"]);
  }

  // Reembolso Cartão - Pix - Não é possível fazer reembolso de boleto
  public function reembolso($data)
  {
    $this->dados(@$data["adquirente"]);

    $dados["url"] = $this->endpoint . "charges/" . $data["transacao"];
    $dados["json"] = "";

    return $this->executa($dados["url"], "DELETE", $dados["json"]);
  }



  // public function assinatura($data)
  // {
  //   $dados["url"] = $this->endpoint . "/assinaturas/v1/customers?new_vault=true";
  //   $dados["json"] = '{
  //           "code": "assinatura02",
  //           "amount": "9990",
  //           "payment_method": "CREDIT_CARD",
  //           "plan": {
  //               "code": "plan102"
  //           },
  //           "customer": {
  //               "code": "cliente01"
  //           }
  //       }';

  //   return $this->executa($dados["url"], "POST");
  // }


  public function consultaPedidos($data)
  {

    $this->endpoint_consulta = "https://api.pagar.me/core/v5/charges/";
    date_default_timezone_set("America/Recife");

    echo "
      <form method='get'>
      <h4>**************** CONSULTAS PAGARME ****************</h4>
      Data inicial: 
      <input type='date' name='inicio'><br>
      
      Data final:
      <input type='date' name='fim'><br>
      
          <input type='submit' value='Enviar'>
          </form>
          ";
    echo "Data da consulta: <b>" . date("d/m/Y H:i:s") . "</b><br><br>";

    $status_transacaoP = array(
      "pending" => 1,
      "paid" => 4,
      "canceled" => 5,
      "failed" => 5,
      "refunded" => 6
    );

    if (@$_GET["inicio"] && @$_GET["fim"]) {
      $vendas = (new Venda())->find("meio_pagamento like '%pagarme%' AND nit_pix <> '' AND created_at BETWEEN '" . @$_GET["inicio"] . " 00:00:00' AND '" . @$_GET["fim"] . " 23:59:59'")->fetch(true);
    } else {
      $vendas = (new Venda())->find("meio_pagamento like '%pagarme%' AND nit_pix <> '' AND created_at BETWEEN '" . date("Y-m-d H:i:s", strtotime("-1 week")) . "' AND '" . date("Y-m-d H:i:s") . "'")->fetch(true);
    }
    // echo "<pre>";
    // exit;
    if (@$vendas) {
      foreach ($vendas as $venda) {
        $adquirente = (new Adquirente())->find("usuario = {$venda->produtor} and adquirente like '%pagarme%'")->fetch();
        $url = $this->endpoint_consulta . @$venda->nit_pix;
        $this->key = @$adquirente->chave;
        $this->token = @$adquirente->token;
        $transacao = $this->executa($url, "GET");


        echo "<pre>";
        echo "********************************************************************************************************<br>";
        // echo "url: " . $url . "\n";
        // echo "\n <b>KEY:</b> " . $this->key . "\n";
        // echo "<b>TOKEN:</b> " . $this->token . "\n";
        // echo "<b>URL:</b> $url<br>";
        echo "<b>Venda:</b> $venda->id<br>";
        echo "<b>NIT PIX:</b> $venda->nit_pix<br>";
        echo "<b>Status Transacao RedRocket:</b> $venda->status_transacao<br>";
        echo "<b>Status Transacao Na PagarMe: </b>" . @$transacao->status . "<br>";
        // echo "<b>Data: </b>" . @$venda->created_at . "<br>";
        echo "<b>Status Transacao Na PagarMe: </b>" . $status_transacaoP[@$transacao->status];
        $status_transacao = $status_transacaoP[@$transacao->status];
        // exit;
        // $status_transacao = 1;

        if ((@$venda->status_transacao != @$status_transacao) && @$status_transacao != "") {
          $venda->status_transacao = @$status_transacao;

          if (@$transacao->updated_at)
            $venda->data_ultimo_evento = @$transacao->updated_at;

          if ($venda->status_transacao == 4)
            $venda->data_finalizada = date("Y-m-d H:i:s");

          if (@$venda->status_transacao == 5)
            $venda->motivo = @$transacao->last_transaction->gateway_response->errors[0]->message;

          //         echo "data finalizada: " . $venda->data_finalizada . "<br><br>";
          if (@$venda->save()) {
            echo "<br> atualizado com sucesso <br>";
          } else {
            echo "<br>" . $venda->fail()->getMessage() . "<br>";
          }

          $data["status_transacao"] = $status_transacao;
          $data["venda"] = $venda->id;
          (new AppProcessos(null))->processos($data);
        } else {
          echo "<br><br> Não foi necessario atualizar <br>";
        }
      }
    }
  }
}
