<?php

/** 
 * CADA MÉTODO É UMA ROTA DO SISTEMA(HOME, CONTATO, BLOG, ETC...)
 *  
 **/

namespace Source\App\Notazz;

use League\Plates\Engine;
use Source\App\Sessao\AppSessao;
use Source\Helpers\Paginador;
use Source\Models\Notazz\IntegracaoNotazz;
use Source\Models\Notazz\NotaFiscal;
use Source\Models\Permissao\PermissaoModulo;
use Source\Models\Permissao\Section;
use Source\Models\Produtos\Produto;
use Source\Models\Vendas\Venda;

session_start();

class AppNotazz
{
    private $view;
    private $sessao;
    private $tema;
    private $section;

    public function __construct($router)
    {
        $this->tema = @$_SESSION["tema"] ? @$_SESSION["tema"] : "tema1";
        $this->view = Engine::create(__DIR__ . "/../../../themes/$this->tema", "php");
        $this->view->addData(["router" => $router]);

        //$this->sessao = (new AppSessao())->verificaSeLogado();
        $this->section = new Section();
    }

    public function notas($data)
    {
        $section = new Section();
        (new Section())->permissaoModulo("notas_fiscais");

        if (@$_SESSION["subusuario"])
            $permissao = (new PermissaoModulo())->find("usuario_permitido = " . $_SESSION["id"] . " AND modulo = 'notas_fiscais'")->fetch();

        $paginador = new Paginador();
        $pagina = @$data["page"];
        $offset = 10;

        $filtros = $this->filtros();

        if ($_SESSION["tipo"] == 1 || $_SESSION["tipo"] == 2 || $_SESSION["tipo"] == 3) {
            $notasCount = (new NotaFiscal())->find($filtros)->count();
            $paginas = ceil($notasCount / $offset);
            $notas = (new NotaFiscal())->find($filtros)->limit($offset)->offset($paginador->paginador($pagina, $offset))->fetch(true);
            $produtos = (new Produto())->find()->fetch(true);
        } else {
            if ($filtros)
                $filtros .= " AND ";

            $notasCount = (new NotaFiscal())->find($filtros . "usuario = " . $this->section->getId())->count();
            $paginas = ceil($notasCount / $offset);
            $notas = (new NotaFiscal())->find($filtros . "usuario = " . $this->section->getId())->limit($offset)->offset($paginador->paginador($pagina, $offset))->fetch(true);
            $produtos = (new Produto())->find("usuario = " . $this->section->getId())->fetch(true);
        }

        echo $this->view->render("nota-fiscal/notas", [
            "title" => "Notas Fiscais",
            "section" => @$section,
            "permissao" => @$permissao,
            "notas" => $notas,
            "produtos" => $produtos,
            "paginador" => $paginador,
            "total" => $paginas,
            "pagina" => @$data["page"] ? $data["page"] : 1,
        ]);
    }

    public function filtros()
    {
        $inicio = @$_GET["inicio"];
        $fim = @$_GET["fim"];
        $pedido = @$_GET["pedido"];
        $nota = @$_GET["nota"];
        $cod_operacao = @$_GET["cod_operacao"];
        $sql = array();

        if ($inicio != "" || $fim != "") {
            if ($inicio == "")
                $inicio = date("Y-m-d");

            if ($fim == "")
                $fim = date("Y-m-d");

            $sql[] = "data_hora BETWEEN '{$inicio} 00:00:00' AND '{$fim} 23:59:59'";
        }

        if ($pedido != "") {
            $sql[] = "venda = '{$pedido}'";
        }
        if ($nota != "") {
            $sql[] = "id = '{$nota}'";
        }
        if ($cod_operacao != "") {
            $sql[] = "cod_operacao = {$cod_operacao}";
        }

        $result = count($sql);

        for ($i = 0; $i < $result - 1; $i++) {
            $sql[$i] .= " AND";
        }

        return implode(" ", $sql);
    }

    public function integracao($data)
    {
        //$criar = $this->criarNFSE($data);
        $paginador = new Paginador();
        $pagina = @$data["page"];
        $offset = 10;

        $notasCount = (new IntegracaoNotazz())->find("usuario = " . $this->section->getId())->count();
        $paginas = ceil($notasCount / $offset);
        $notas = (new IntegracaoNotazz())->find("usuario = " . $this->section->getId())->limit($offset)->offset($paginador->paginador($pagina, $offset))->fetch(true);

        $produtos = (new Produto())->find("usuario = " . $this->section->getId())->fetch(true);

        /*$notaTeste = $this->criarNFETeste();
        $enviar = $this->send_data($notaTeste);

        var_dump($notaTeste, $enviar);*/

        echo $this->view->render("nota-fiscal/integracao", [
            "title" => "Integração Notazz",
            "notas" => $notas,
            "produtos" => $produtos,
            "paginador" => $paginador,
            "total" => $paginas,
            "pagina" => @$data["page"] ? $data["page"] : 1,
        ]);
    }

    public function consultar($data)
    {
        $consultar = $this->consultarCancelar($data);
        $enviar = $this->send_data($consultar);
        var_dump($enviar);
    }

    public function cadastrarIntegracao($data)
    {
        $integracao = new IntegracaoNotazz();

        $integracao->usuario             = $this->section->getId();
        $integracao->api_notazz          = $data["api_notazz"];
        $integracao->data_nota           = $data["data_nota"];
        $integracao->produto             = $data["produtonotazz"];
        $integracao->codigo_cnae         = $data["codigo_cnae"];
        $integracao->codigo_lc116        = $data["codigo_lc116"];
        $integracao->enviar_email_cliente = isset($data["enviar_email_cliente"]) ? (int)$data["enviar_email_cliente"] : 1;

        if ($integracao->save()) {
            echo "Salvo com sucesso";
        } else {
            echo $integracao->fail()->getMessage();
        }
    }

    public function editarIntegracao($data)
    {
        $integracao = (new IntegracaoNotazz())->findById($data["id"]);

        $integracao->api_notazz           = $data["api_notazz"];
        $integracao->data_nota            = $data["data_nota"];
        $integracao->produto              = $data["produto"];
        $integracao->codigo_cnae          = $data["codigo_cnae"];
        $integracao->codigo_lc116         = $data["codigo_lc116"];
        $integracao->enviar_email_cliente = isset($data["enviar_email_cliente"]) ? (int)$data["enviar_email_cliente"] : 1;

        if ($integracao->save()) {
            echo "Salvo com sucesso";
        } else {
            echo $integracao->fail()->getMessage();
        }
    }

    public function excluirIntegracao($data)
    {
        $data = filter_var_array($data, FILTER_SANITIZE_STRIPPED);

        $integracao = (new IntegracaoNotazz())->findById($data["id"]);
        if ($integracao->destroy()) {
            echo "Excluido com sucesso";
        } else {
            echo $integracao->fail()->getMessage();
        }
    }

    public function send_data($fields)
    {
        $fields = array("fields" => $fields);
        $fields_string = '';

        //url-ify the data for the POST
        foreach ($fields as $key => $value) {
            $fields_string .= $key . '=' . $value . '&';
        }

        rtrim($fields_string, '&');

        //open connection
        $ch = curl_init();

        //set the url, number of POST vars, POST data
        curl_setopt($ch, CURLOPT_URL, 'https://app.notazz.com/api');
        curl_setopt($ch, CURLOPT_POST, count($fields));
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

        //execute post
        $response = curl_exec($ch);

        //close connection
        curl_close($ch);

        //Convertendo json para array
        $pos = strpos($response, '{');

        return (json_decode(substr($response, $pos), true));
    }

    public function gerarNota($data)
    {
        $produto = (new Produto())->findById($data["produto"]);
        $data["valor_unitario"] = number_format($data["valor_total"] / $data["quantidade"], 2);

        $notazz = (new IntegracaoNotazz())->find("usuario = " . $data["produtor"])->fetch(true);

        if ($notazz) {
            foreach ($notazz as $notazz) {
                if (!$notazz->produto || $notazz->produto == $data["produto"]) {
                    $data["api_key"]              = $notazz->api_notazz;
                    $data["codigo_cnae"]          = @$notazz->codigo_cnae;
                    $data["codigo_lc116"]         = @$notazz->codigo_lc116;
                    $data["enviar_email_cliente"] = isset($notazz->enviar_email_cliente) ? (int)$notazz->enviar_email_cliente : 1;

                    if ($produto->tipo_produto == 8) {
                        $data["metodo"] = "create_nfe_55";
                        $nota = $this->criarNFE($data);
                    } else {
                        $data["metodo"] = "create_nfse";
                        $nota = $this->criarNFSE($data);
                    }

                    $enviar = $this->send_data($nota);

                    if ($enviar["codigoProcessamento"] == "000") {
                        $notaFiscal = new NotaFiscal();
                        $notaFiscal->id_nota = $enviar["id"];
                        $notaFiscal->status = 1;
                        $notaFiscal->venda = $data["id"];
                        $notaFiscal->usuario = $data["produtor"];

                        if ($notaFiscal->save()) {
                            $enviar["msg"] = "Salvo com sucesso";
                        } else {
                            $enviar["msg"] = "Erro ao salvar no banco de dados \n";
                            $enviar["msg"] .= $notaFiscal->fail()->getMessage();
                        }
                    }

                    echo json_encode($enviar);
                } else {
                    $enviar["motivo"] = "Nota não gerada para esse produto";
                    echo json_encode($enviar);
                }
            }
        } else {
            $enviar["motivo"] = "Você não possui nenhuma integração com o Notazz!\nCadastre uma para poder gerar a nota fiscal";
            echo json_encode($enviar);
        }
    }

    public function postbackNotazz($data)
    {
        var_dump($_POST);

        $arquivo = fopen('notazz-post.txt', 'a+');

        if ($arquivo == false)
            die('Não foi possível criar o arquivo.');

        $texto = "dados = {\n";

        $i = 0;
        foreach ($_POST as $chave => $valor) {
            @$texto .= '"' . $chave . '": "' . $valor . '",' . "\n";
            $i++;
        }

        $texto .= "}\n";
        $texto .= "#################################################";

        fwrite($arquivo, $texto . "\n\n");
        fclose($arquivo);

        $status = array(
            "Pendente" => 1,
            "Autorizada" => 2,
            "Cancelada" => 3,
            "Rejeitada" => 4,
            "Denegada" => 5
        );

        $id = @$_POST["id"];
        $numVenda = @$_POST["venda"];
        $notas = (new NotaFiscal())->find("id_nota = '" . $id . "'")->fetch();
        $pedido = (new Venda())->find("transacao = '" . $numVenda . "'")->fetch();

        if ($notas) {
            $nota = (new NotaFiscal())->findById($notas->id);
            $nota->status = $status[@$_POST["statusNota"]];
            $nota->venda = @$pedido->id;
            $nota->usuario = @$pedido->id_cliente;
            $nota->link_pdf = @$_POST["pdf"];
            $nota->link_xml = @$_POST["xml"];
            $nota->venda1 = @$_POST["venda"];
            $nota->chave = @$_POST["chave"];
            $nota->numero = @$_POST["numero"];
            $nota->codigo_verificacao = @$_POST["codigoVerificacao"];
            $nota->emissao = @$_POST["emissao"];
            $nota->motivo_status = @$_POST["motivoStatus"];

            if ($nota->link_xml) {
                $xml = @simplexml_load_file($nota->link_xml);
                $nota->cfop = @$xml->NFe->infNFe->det->prod->CFOP;
                $nota->serie = @$xml->NFe->infNFe->ide->serie;
                $nota->protocolo_autorizacao = @$xml->protNFe->infProt->nProt;
            }

            if ($nota->save()) {
                echo "Salvo com sucesso";
                echo @$pedido->id . " - " . @$pedido->id_cliente;
            } else {
                $arquivo = fopen('notazz-status.txt', 'a+');

                if ($arquivo == false)
                    die('Não foi possível criar o arquivo.');

                $text = "id=" . @$_POST["id"] . "\n";
                $text .= "#################################";

                fwrite($arquivo, $text . "\n\n");
                fclose($arquivo);
            }
        } else {
            $nota = new NotaFiscal();
            $nota->id_nota = @$_POST["id"];
            $nota->status = @$status[@$_POST["statusNota"]];
            $nota->numero = @$_POST["numero"];
            $nota->venda = @$pedido->id;
            $nota->venda1 = @$_POST["venda"];
            $nota->usuario = @$pedido->id_cliente;
            $nota->link_pdf = @$_POST["pdf"];
            $nota->link_xml = @$_POST["xml"];
            $nota->chave = @$_POST["chave"];
            $nota->codigo_verificacao = @$_POST["codigoVerificacao"];
            $nota->emissao = @$_POST["emissao"];
            $nota->motivo_status = @$_POST["motivoStatus"];

            if ($nota->link_xml) {
                $xml = @simplexml_load_file($nota->link_xml);
                $nota->cfop = @$xml->NFe->infNFe->det->prod->CFOP;
                $nota->serie = @$xml->NFe->infNFe->ide->serie;
                $nota->protocolo_autorizacao = @$xml->protNFe->infProt->nProt;
            }

            if ($nota->save()) {
                echo "Salvo com sucesso";
                echo @$pedido->id . " - " . @$pedido->id_cliente;
            } else {
                $arquivo = fopen('notazz-cadastro.txt', 'a+');

                if ($arquivo == false)
                    die('Não foi possível criar o arquivo.');

                $text = "id=" . @$_POST["id"] . "\n";
                $text .= $nota->fail()->getMessage();
                $text .= "#################################";

                fwrite($arquivo, $text . "\n\n");
                fclose($arquivo);
            }
        }
    }

    public function criarNFSETeste()
    {
        $fields = json_encode(array(
            "API_KEY" => "wYyYDO0EzYjlDOmdjNwYmNzYjZyMGNklDZ4IzNlFDOjpHd14ke5QVMuNmM2gDNxM2Y5gjZ3YDMmZzM2YmMjRDZ5QGOycTZxgzYnZ",
            "METHOD" => "create_nfse", // create_nfse - update_nfse
            "DESTINATION_NAME" => "Pedro Teste",
            "DESTINATION_TAXID" => "12719252654",
            "DESTINATION_TAXTYPE" => "F", // F = Física, J = Jurídica, E = Estrangeiro
            "DESTINATION_STREET" => "São Bartolomeu",
            "DESTINATION_NUMBER" => "330",
            "DESTINATION_COMPLEMENT" => "",
            "DESTINATION_DISTRICT" => "Vila Mariana",
            "DESTINATION_CITY" => "Governador Valadares",
            "DESTINATION_UF" => "MG",
            "DESTINATION_ZIPCODE" => "35012140",
            "DESTINATION_PHONE" => "33999999999",
            "DESTINATION_EMAIL" => "pedro@octa.life",
            "DESTINATION_EMAIL_SEND" => "pedro@octa.life",
            "DOCUMENT_BASEVALUE" => "1.00",
            "DOCUMENT_DESCRIPTION" => "Nota de Teste",
            "DOCUMENT_COMPETENCE" => date("Y-m-d"),
            "DOCUMENT_CNAE" => "7112000",
            "SERVICE_LIST_LC116" => "0703",
            "EXTERNAL_ID" => rand(),
            "SALE_ID" => "127652",
            "DOCUMENT_ISSUE_DATE" => date("Y-m-d H:i:s"),
            "REQUEST_ORIGIN" => "RedRocket",
            "REQUEST_RETURN" => url("notas/postback")
        ));

        return $fields;
    }

    public function criarNFETeste()
    {
        $fields = json_encode(array(
            "API_KEY" => "wYyYDO0EzYjlDOmdjNwYmNzYjZyMGNklDZ4IzNlFDOjpHd14ke5QVMuNmM2gDNxM2Y5gjZ3YDMmZzM2YmMjRDZ5QGOycTZxgzYnZ",
            "METHOD" => "create_nfe_55",
            "DESTINATION_NAME" => "Pedro Teste",
            "DESTINATION_TAXID" => "12719252654",
            "DESTINATION_TAXTYPE" => "F",
            "DESTINATION_STREET" => "Rua São Bartolomeu",
            "DESTINATION_NUMBER" => "3",
            "DESTINATION_COMPLEMENT" => "",
            "DESTINATION_DISTRICT" => "Vila Mariana",
            "DESTINATION_CITY" => "Governador Valadares",
            "DESTINATION_UF" => "MG",
            "DESTINATION_ZIPCODE" => "35012140",
            "DESTINATION_PHONE" => "",
            "DESTINATION_EMAIL" => "",
            "DOCUMENT_BASEVALUE" => "5.00",
            "DOCUMENT_CNAE" => "8599604",
            "SERVICE_LIST_LC116" => "0802",
            "DOCUMENT_PRODUCT" => array(
                "1" => array(
                    "DOCUMENT_PRODUCT_COD" => "205990641936",
                    "DOCUMENT_PRODUCT_NAME" => "Produto Teste Pedro",
                    "DOCUMENT_PRODUCT_QTD" => 5,
                    "DOCUMENT_PRODUCT_UNITARY_VALUE" => 1.00
                )
            ),
            "EXTERNAL_ID" => rand(),
            "SALE_ID" => "321",
            "DOCUMENT_ISSUE_DATE" => "2021-01-13",
            "REQUEST_ORIGIN" => "RedRocket",
            "REQUEST_RETURN" => url("notas/postback")
        ));

        return $fields;
    }

    public function criarNFSE($data)
    {
        $fields = array(
            "API_KEY" => $data["api_key"],
            "METHOD" => $data["metodo"], // create_nfse - update_nfse
            "DESTINATION_NAME" => $data["nome"],
            "DESTINATION_TAXID" => $data["cpf"],
            "DESTINATION_TAXTYPE" => "F",
            "DESTINATION_STREET" => $data["rua"],
            "DESTINATION_NUMBER" => $data["numero"],
            "DESTINATION_COMPLEMENT" => $data["complemento"],
            "DESTINATION_DISTRICT" => $data["bairro"],
            "DESTINATION_CITY" => $data["cidade"],
            "DESTINATION_UF" => $data["estado"],
            "DESTINATION_ZIPCODE" => $data["cep"],
            "DESTINATION_PHONE" => $data["ddd"] . $data["telefone"],
            "DESTINATION_EMAIL" => $data["email"],
            "DESTINATION_EMAIL_SEND" => (@$data["enviar_email_cliente"] == 1) ? $data["email"] : "",
            "DOCUMENT_BASEVALUE" => $data["valor_venda"],
            "DOCUMENT_DESCRIPTION" => $data["nomekit"],
            "DOCUMENT_COMPETENCE" => date("Y-m-d"),

            "EXTERNAL_ID" => $data["transacao"],
            "SALE_ID" => "127654",
            "DOCUMENT_ISSUE_DATE" => date("Y-m-d H:i:s"),
            "REQUEST_ORIGIN" => "RedRocket",
            "REQUEST_RETURN" => url("notas/postback")
        );

        if (@$data["codigo_cnae"])
            $fields["DOCUMENT_CNAE"] = $data["codigo_cnae"];

        if (@$data["codigo_lc116"])
            $fields["SERVICE_LIST_LC116"] = $data["codigo_lc116"];

        $fields = json_encode($fields);
        return $fields;
    }

    public function criarNFE($data)
    {
        $fields = array(
            "API_KEY" => $data["api_key"],
            "METHOD" => $data["metodo"], // create_nfe_55 - update_nfe_55

            "DESTINATION_NAME" => $data["nome"],
            "DESTINATION_TAXID" => $data["cpf"],
            "DESTINATION_TAXTYPE" => "F",
            "DESTINATION_STREET" => $data["rua"],
            "DESTINATION_NUMBER" => $data["numero"],
            "DESTINATION_COMPLEMENT" => $data["complemento"],
            "DESTINATION_DISTRICT" => $data["bairro"],
            "DESTINATION_CITY" => $data["cidade"],
            "DESTINATION_UF" => $data["estado"],
            "DESTINATION_ZIPCODE" => $data["cep"],
            "DESTINATION_PHONE" => $data["ddd"] . $data["telefone"],
            "DESTINATION_EMAIL" => $data["email"],
            "DESTINATION_EMAIL_SEND" => (@$data["enviar_email_cliente"] == 1) ? $data["email"] : "",
            "DOCUMENT_BASEVALUE" => $data["valor_total"],
            "DOCUMENT_DESCRIPTION" => $data["nomekit"],
            "DOCUMENT_COMPETENCE" => date("Y-m-d"),

            "EXTERNAL_ID" => $data["transacao"],
            "SALE_ID" => "127654",
            "DOCUMENT_ISSUE_DATE" => date("Y-m-d H:i:s"),
            "REQUEST_ORIGIN" => "RedRocket",
            "DOCUMENT_PRODUCT" => array(
                "1" => array(
                    "DOCUMENT_PRODUCT_COD" => $data["codigo_produto"],
                    "DOCUMENT_PRODUCT_NAME" => $data["nomekit"],
                    "DOCUMENT_PRODUCT_QTD" => $data["quantidade"],
                    "DOCUMENT_PRODUCT_UNITARY_VALUE" => $data["valor_unitario"],
                )
            ),
            "REQUEST_RETURN" => url("notas/postback")
        );

        if (@$data["codigo_cnae"])
            $fields["DOCUMENT_CNAE"] = $data["codigo_cnae"];

        if (@$data["codigo_lc116"])
            $fields["SERVICE_LIST_LC116"] = $data["codigo_lc116"];

        $fields = json_encode($fields);
        return $fields;
    }

    public function consultarCancelar($data)
    {
        /** MÉTODOS
         * 
         * consult_nfse - CONSULTAR NFSE
         * cancel_nfse - CANCELAR NFSE DEPOIS DE AUTORIZADA
         * delete_nfse - EXCLUIR NFSE ANTES DE AUTORIZADA OU CANCELADA
         * unlink_nfse_external_id - DESVINCULAR EXTERNAL_ID
         * 
         * consult_nfe_55 - CONSULTAR NFE
         * cancel_nfe_55 - CANCELAR NFE DEPOIS DE AUTORIZADA
         * delete_nfe_55 - EXCLUIR NFE ANTES DE AUTORIZADA OU CANCELADA
         * unlink_nfe_external_id - DESVINCULAR EXTERNAL_ID
         * 
         * cancel_logistics - CANCELAR LOGÍSTICA
         */

        $fields = json_encode(
            array(
                "API_KEY" => $data["api_key"],
                "METHOD" => $data["metodo"],
                "DOCUMENT_ID" => $data["id_documento"],
                "EXTERNAL_ID" => $data["codigo"]
            )
        );

        return $fields;
    }

    public function consultarTodas($data)
    {
        $fields = json_encode(array(
            "API_KEY" => $data["api_key"],
            "METHOD" => $data["metodo"], // consult_all_nfse - consult_all_nfe_55
            "FILTER" => array(
                "INITIAL_DATE" => $data["data_inicio"],
                "FINAL_DATE" => $data["data_fim"],
                "STATUS" => @$data["status"],
                "NUMBER" => @$data["numero"],
                "pagina" => @$data["page"] ? $data["page"] : 1,
                "DATA_TYPE" => @$data["tipo_data"] // geracao, transmissao, competencia, ultimaAlteracao
            )
        ));

        return $fields;
    }

    public function anexarArquivo($data)
    {
        /** MÉTODOS
         * 
         * nfe_attachment - ANEXAR ARQUIVO
         * 
         */

        $fields = json_encode(
            array(
                "API_KEY" => $data["api_key"],
                "METHOD" => $data["metodo"],
                "DOCUMENT_ID" => $data["id_documento"], // ID do documento que será removido              
                "EXTERNAL_ID" => $data["codigo"], // ID externo do documento que será removido
                "DOCUMENT_ATTACHMENT" => $data["link_documento"] // Link do PDF que será anexado
            )
        );

        return $fields;
    }
}
