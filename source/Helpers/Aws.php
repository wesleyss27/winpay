<?php

namespace Source\Helpers;

require 'vendor/autoload.php';

use Aws\S3\S3Client;

class Aws
{
    public function upload($files, $pasta, $tamanho = 2)
    {
        $dados["files"] = serialize($files);
        $dados["pasta"] = $pasta;
        $dados["tamanho"] = $tamanho;
        $this->logs($dados, "uploads");

        //var_dump($files);
        $files["size"] = @$files["size"] / 8192 / 8192;
        //echo " - " . $files["size"];

        $nome = explode(".", @$files["name"]);
        @$nome[1] = strtolower(@$nome[1]);
        if (@$nome[1] != "png" && @$nome[1] != "jpg" && @$nome[1] != "jpeg" && @$nome[1] != "pdf" && @$nome[1] != "zip" && @$nome[1] != "rar") {
            return false;
            exit;
        }

        if (@$files["size"] > $tamanho) {
            return false;
            exit;
        } elseif (isset($files['name']) && @$files["error"] == 0) {
            $arquivo_tmp = @$files['tmp_name'];
            $nome = @$files['name'];

            $extensao = strrchr($nome, '.');
            $extensao = strtolower($extensao);

            $s3Client =  new S3Client([
                'region' => 'us-east-2',
                'version' => 'latest',
                'credentials' => [
                    'key' => "AKIA6MRCQUPLI5CU7PM7",
                    'secret' => "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",
                ]
            ]);

            $result = $s3Client->putObject([
                'Bucket' => 'octapay-aws',
                'Key' => $pasta . $files["name"],
                'Body' => 'Devnote tutorial!',
                'SourceFile' => $arquivo_tmp,
                'ACL'        => 'public-read'
                //'SourceFile' => 'd:\test.png' //use you want to upload a file from a local computer location.
            ]);

            $dado["result"] = serialize($result);
            $this->logs($dado, "uploads");
            return $pasta . $files["name"];
        } else {
            return false;
        }
    }

    public function delete($files, $pasta)
    {
        $dados["files"] = serialize($files);
        $dados["pasta"] = $pasta;

        $nome = explode(".", @$files["name"]);

        if (isset($files['name']) && @$files["error"] == 0) {
            $arquivo_tmp = @$files['tmp_name'];
            $nome = @$files['name'];

            $extensao = strrchr($nome, '.');
            $extensao = strtolower($extensao);

            $s3Client =  new S3Client([
                'region' => 'us-east-2',
                'version' => 'latest',
                'credentials' => [
                    'key' => "AKIA6MRCQUPLI5CU7PM7",
                    'secret' => "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",
                ]
            ]);

            $result = $s3Client->putObject([
                'Bucket' => 'octapay-aws',
                'Key' => $pasta . $files["name"],
                'Body' => 'Devnote tutorial!',
                'SourceFile' => $arquivo_tmp,
                'ACL'        => 'public-read'
                //'SourceFile' => 'd:\test.png' //use you want to upload a file from a local computer location.
            ]);

            $dado["result"] = serialize($result);
            $this->logs($dado, "uploads");
            return $pasta . $files["name"];
        } else {
            return false;
        }
    }

    public function logs($data, $arq)
    {
        // var_dump($data);
        $arquivo = fopen(DIRETORIO . "logs/$arq.txt", 'a+');

        if ($arquivo == false)
            die('Não foi possível criar o arquivo.');

        $texto = "{\n";
        $texto .= ' "data": "' . date("d/m/Y H:i:s") . '",' . "\n";
        if ($data) {
            foreach ($data as $chave => $valor) {
                if ($chave == "numcart" || $chave == "cvv" || $chave == "ano-val") {
                    $valor = "-";
                }
                if ($chave != "codigo_order_bump")
                    $texto .= ' "' . $chave . '": "' . $valor . '",' . "\n";
            }
        } else {
            $texto .= "Erro dos dados";
        }
        $texto .= "}\n\n";
        $texto .= "*************************************************************";

        fwrite($arquivo, $texto . "\n\n");
        fclose($arquivo);
    }
}
