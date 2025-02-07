<?php

namespace Source\Helpers;

class File
{
    public function upload2($files, $pasta, $tamanho = 2)
    {
        var_dump($files);

        $url = "http://localhost/arquivos/arquivos.php"; // e.g. http://localhost/myuploader/upload.php // request URL
        $filename = $files['name'];
        $filedata = $files['tmp_name'];
        $filesize = $files['size'];
        if ($filedata != '') {
            $headers = array("Content-Type:multipart/form-data"); // cURL headers for file uploading
            $postfields = array("filedata" => "@$filedata", "filename" => $filename);
            $ch = curl_init();
            $options = array(
                CURLOPT_URL => $url,
                CURLOPT_HEADER => true,
                CURLOPT_POST => 1,
                CURLOPT_HTTPHEADER => $headers,
                CURLOPT_POSTFIELDS => $postfields,
                CURLOPT_INFILESIZE => $filesize,
                CURLOPT_RETURNTRANSFER => true
            ); // cURL options
            curl_setopt_array($ch, $options);
            $respose = curl_exec($ch);
            echo $respose . "\n";
            if (!curl_errno($ch)) {
                $info = curl_getinfo($ch);
                // var_dump($info);
                if ($info['http_code'] == 200)
                    $errmsg = "File uploaded successfully";
            } else {
                $errmsg = curl_error($ch);
            }
            curl_close($ch);
        } else {
            $errmsg = "Please select the file";
        }

        echo $errmsg;
    }
    public function upload($files, $pasta, $tamanho = 2)
    {
        $files["size"] = @$files["size"] / 1024 / 1024;
        // echo " - " . $files["size"];

        $nome = explode(".", @$files["name"]);
        @$nome[1] = strtolower(end($nome));

        if (@$nome[1] != "png" && @$nome[1] != "jpg" && @$nome[1] != "jpeg" && @$nome[1] != "pdf" && @$nome[1] != "zip" && @$nome[1] != "rar" && @$nome[1] != "gif") {
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

            $novoNome = md5(microtime()) . $extensao;
            $destino = $pasta . $novoNome;

            if (@move_uploaded_file($arquivo_tmp, $destino)) {
                return $novoNome;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }
}
