<?php

$categoria = array(
    1 => "Administração e Negócios",
    2 => "Animais de Estimação",
    3 => "Arquitetura e Engenharia",
    4 => "Artes e Música",
    5 => "Auto-ajuda e Desenvolvimento Pessoal",
    6 => "Automóveis",
    7 => "Blogs e Redes Sociais",
    8 => "Casa e Jardinagem",
    9 => "Culinária, Gastronomia, Receitas",
    10 => "Design e Templates PSD, PPT ou HTML",
    11 => "Edição de Áudio, Vídeo ou Imagens",
    12 => "Educacional, Cursos Técnicos e Profissionalizantes",
    13 => "Eletrodomésticos",
    14 => "Eletroportáteis",
    15 => "Entretenimento, Lazer e Diversão",
    16 => "Esportes e Fitness",
    17 => "Filmes e Cinema",
    18 => "Geral",
    19 => "Histórias em Quadrinhos",
    20 => "Idiomas",
    21 => "Informática",
    22 => "Internet Marketing",
    23 => "Investimentos e Finanças",
    24 => "Jogos de Cartas, Poker, Loterias",
    25 => "Jogos de Computador, Jogos Online",
    26 => "Jurídico",
    27 => "Literatura e Poesia",
    28 => "Marketing de Rede",
    29 => "Marketing e Comunicação",
    30 => "Meio Ambiente",
    31 => "Moda e vestuário",
    32 => "Moveis e decoração",
    33 => "Música, Bandas e Shows",
    34 => "Paquera, Sedução e Relacionamentos",
    35 => "Perfumaria",
    36 => "Pessoas com deficiência",
    37 => "Plugins, Widgets e Extensões",
    38 => "Produtividade e Organização Pessoal",
    39 => "Produtos infantis",
    40 => "Relatórios, Artigos e Pesquisas",
    41 => "Religião e Crenças",
    42 => "Romances, Dramas, Estórias e Contos",
    43 => "RPG e Jogos de Mesa",
    44 => "Saúde, Bem-estar e Beleza",
    45 => "Scripts",
    46 => "Segurança do Trabalho",
    47 => "Sexologia e Sexualidade",
    48 => "Snippets (Trechos de Vídeo)",
    49 => "Trader",
    50 => "Turismo"
);

echo "tipos<br>";
function tirarAcentos($string)
{
    return preg_replace(array('/[@\.\;\""]+/', "/(á|à|ã|â|ä)/", "/(Á|À|Ã|Â|Ä)/", "/(é|è|ê|ë)/", "/(É|È|Ê|Ë)/", "/(í|ì|î|ï)/", "/(Í|Ì|Î|Ï)/", "/(ó|ò|õ|ô|ö)/", "/(Ó|Ò|Õ|Ô|Ö)/", "/(ú|ù|û|ü)/", "/(Ú|Ù|Û|Ü)/", "/(ñ)/", "/(Ñ)/", "/(ç)/", "/(Ç)/"), explode(" ", "a a e e i i o o u u n n c c"), $string);
}

foreach ($categoria as $key => $value) {
    $chave = str_replace(",", "", $value);
    $chave = strtolower($chave);
    $chave = explode(" ", $chave);
    $chave = implode("-", $chave);
    $chave = tirarAcentos($chave);
    echo "$key => [\"$value\", \"$chave\"],<br>";
}
