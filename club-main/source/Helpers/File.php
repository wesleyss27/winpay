<?php

namespace Source\Helpers;

class File
{
    public function upload($files, $pasta, $tamanho = 10)
    {

        //var_dump($files);
        $files["size"] = @$files["size"] / 1024 / 1024;
        // echo " - " . $files["size"];

        $extensao = strtolower(pathinfo(@$files["name"], PATHINFO_EXTENSION));
        echo $extensao;
        $nome = explode(".", @$files["name"]);

        if (
            @$extensao != "png" &&
            @$extensao != "jpg" &&
            @$extensao != "jpeg" &&
            @$extensao != "pdf" &&
            @$extensao != "zip" &&
            @$extensao != "rar" &&
            @$extensao != "webm" &&
            @$extensao != "mp4" &&
            @$extensao != "mpg"
        ) {
            return false;
            exit;
        }

        if (@$files["size"] > $tamanho) {
            return false;
            exit;
        } elseif (isset($files['name']) && @$files["error"] == 0) {
            $arquivo_tmp = @$files['tmp_name'];
            $nome = @$files['name'];

            // $extensao = strrchr($nome, '.');
            // $extensao = strtolower($extensao);

            $novoNome = md5(microtime()) . "." . $extensao;
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
