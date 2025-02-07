<?php 
    $v->layout("_theme"); 

    if($_SESSION["tipo"] != 1){
        header("Location: " . URL_BASE);
    }

    date_default_timezone_set("America/Sao_Paulo");
    //echo date("Y-m-d H:i:s", strtotime("+30 min"));

?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- HEADER PAGE -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h3 style="font-family:inter"><?= $title ?></h3>
                </div>
                
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="./">Início</a></li>
                        <li class="breadcrumb-item active">Notas</li>
                    </ol>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>
    <!-- FIM HEADER PAGE -->

    <!-- Main content -->
    <section class="content">
        <!-- Default box -->
        <div class="card">

            <!-- CARD BODY -->
            <div class="card-body" style="overflow-x: auto">
            <?= $v->insert("fragmentos/msg-cadastrar-editar", []); ?>
            <form action="<?= $router->route("logar.master"); ?>" method="post">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-label-group">
                            <input type="text" id='email' name="email" class="form-control input-lg" placeholder="Email ou login" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <button class="btn btn-primary btn-block btn-login text-uppercase font-weight-bold mb-2" type="submit">Entrar</button>
                    </div>
                </div>
            </form>
            </div>
            <!-- FIM CARD BODY -->
    
        </div>
    </section>
</div>

<script>

    $("#salvar").click(function(e) {
        e.preventDefault();
        var email = $("#email2").val();
        console.log(email);
        salvar("<?=$router->route("appusuarios.esqueceuSenha")?>");
    });

    function salvar(url) {
        var form = $("form");
        $.ajax({

            url: url,
            data: form.serialize(),
            type: "POST",
            success: function (callback) {

                if(callback == "Email enviado com sucesso."){
                    alert("Verifique o email informado para redefinir sua senha");
                    location.reload();
                } else {
                    alert(callback);
                }
            }

        });
    };

</script>
</div>
</body>
</html>