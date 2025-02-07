<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Clientes | St4rt</title>

    <link rel="stylesheet" href="<?= url("themes/default/assets/css/bootstrap3.css"); ?>">
    <link rel="stylesheet" href="<?= url("themes/tema2/assets/css/login.css"); ?>">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
</head>
<body class="body">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-4 col-md-offset-4 alert centered">
                <h1>Octa Life</h1>
                <p>Acesse sua conta e acompanhe seus pedidos</p><br>

                <?= $v->insert("themes/tema2/fragmentos/msg-cadastrar-editar", []); ?>
                
                <form action="<?= $router->route("logar.user"); ?>" method="post">
                    <div class="input-group">
                        <span class="input-group-addon"><i class="material-icons">person</i></span>
                        <input type="text" name="email" id='email' class="form-control input-lg" placeholder="Email" autocomplete="off" autofocus>
                    </div>

                    <br>

                    <div class="input-group">
                        <span class="input-group-addon"><i class="material-icons">lock</i></span>
                        <input type="password" name="senha" id='senha' class="form-control input-lg" placeholder="Senha" autocomplete="off" autofocus>
                    </div>

                    <br>

                    <button class="btn btn-success input-lg btn-lg form-control">Entrar</button><br><br>

                    <div class="col-md-6"><a href="#">Esqueci a Senha </a></div>
                    <div class="col-md-6"><a href="#"> Solicitar Cadastro</a></div>
                    
                </form>
            </div>
        </div>
    </div>
</body>
</html>