<?php 
    date_default_timezone_set("America/Sao_Paulo");
    //echo date("Y-m-d H:i:s", strtotime("+30 min"));

    $redirect = explode("/octapay", @$_GET["redirect"]);
    //echo $redirect[1];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title; ?></title>

    <link rel="icon" href="<?= url("themes/tema2/assets/imgs/logo.png"); ?>">

    <link rel="stylesheet" href="<?= url("themes/tema2/assets/css/bootstrap.css"); ?>">
    <link rel="stylesheet" href="<?= url("themes/tema2/assets/css/cores.css"); ?>">
    <link rel="stylesheet" href="<?= url("themes/tema2/assets/css/login.css?".time()); ?>">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <script src="<?= url("themes/tema2/assets/js/jquery.js"); ?>"></script>
    <script src="<?= url("themes/tema2/assets/js/bootstrap.js"); ?>"></script>
    <script src="<?= url("themes/tema2/assets/js/popper.min.js"); ?>"></script>


</head>
<body class="body">
  <div class="container-fluid">
  <div class="row no-gutter">
    <div class="d-none d-md-flex col-md-4 col-lg-6 bg-image"></div>
    <div class="col-md-8 col-lg-6">
      <div class="login d-flex align-items-center py-5">
        <div class="container">
          <div class="row">
            <div class="col-md-9 col-lg-8 mx-auto">


            <img class="logo" src="<?= url("themes/tema2/assets/imgs/logo.png"); ?>" alt="Logo-octapay" width=250 height=70>
            <br>
            <br>
            <h3 class="login-heading mb-4"><?= $title ?></h3>               
            
                  <form action="<?= $router->route("appusuarios.atualizaSenha"); ?>" method="post">
                    <input type="hidden" name="id" value="<?= @$data["id"] ?>">
                    <input type="hidden" name="codigo" value="<?= @$data["codigo"] ?>">
                    
                    <div class="form-label-group">
                        Código de recuperação: 
                       <input type="text" id='codigo_recuperacao' name="codigo_recuperacao" class="form-control input-lg" placeholder="Insira o código de recuperação">
                    </div>

                    <div class="form-label-group">
                        Nova Senha:
                       <input type="password" id='senha' name="senha" class="form-control input-lg" placeholder="Nova Senha">
                    </div>
                   
                    <div class="form-label-group">
                        Repetir Senha:
                        <input type="password" name="repetir_senha" id='repetir_senha' class="form-control input-lg" placeholder="Repetir Senha">
                    </div>
                                            
                    <button class="btn btn-lg btn-primary btn-block btn-login text-uppercase font-weight-bold mb-2" type="submit">Alterar</button>
                  
                    </form>
                  </form>
            </div>
        </div>

</div>
</body>
</html>