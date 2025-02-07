<?php

namespace Source\Helpers;

class Paginador
{
    public function paginador($page, $offset)
    {
        if($page == null || $page == 1) {
            $page = 0;
        }
        else {
            $page *= $offset;
            $page -= $offset;
        }

        return $page;
    }

    public function paginas($total, $pagina, $link, $uri = null)
    {
        if($total > 1) {
            $max_links = 10;
            $links_laterais = ceil($max_links / 2);
 
            // variáveis para o loop
            $inicio = $pagina - $links_laterais;
            $limite = $pagina + $links_laterais;

            echo "<nav class='navegacao'>";
            if($pagina > 1){
                echo "<a class='inativo paginas' href='{$link}1?$uri'> << </a>";
                echo "<a class='inativo paginas' href='$link".($pagina - 1)."'> < </a>";
            }

            for($i = $inicio; $i <= $limite; $i++){
                if($pagina == $i){
                    $class = "ativo";
                }
                else {
                    $class = "inativo";
                }
                if ($i >= 1 && $i <= $total){
                    echo "<a class='$class paginas' href='$link$i?$uri'>$i</a>";
                }
            }
            if($pagina < $total){
                echo "<a class='inativo paginas' href='$link".($pagina + 1)."'> > </a>";
                echo "<a class='inativo paginas' href='$link$total?$uri'> >> </a>";
            }
            echo "</nav>";
        }
    }
}