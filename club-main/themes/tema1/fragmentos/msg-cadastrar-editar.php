<?php  if(@$msg): //MENSAGEM DO CADASTRO ?>
    <div class="col-md-12 <?= $classCss; ?> alert">
        <?= $msg; ?>
    </div>
<?php elseif(@$_GET["msg"]): ?>
    <div class="col-md-12 <?= $_GET["classCss"]; ?> alert">
        <?= $_GET["msg"]; ?>
    </div>
<?php endif; ?>