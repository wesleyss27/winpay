<?php 
    $v->layout("_theme");
    if($_SESSION["tipo"] != 1){
        header("Location: " . URL_BASE);
    }
?>
    <style>
        .center {
            text-align: center
        }
    </style>

    <!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2" >
          <div class="col-sm-6" style="margin-bottom:10px">
            <h1 style="font-family:inter"><?= $title ?></h1>
          </div>
          <div class="col-sm-6" style="font-family:inter">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="<?= url() ?>">Início</a></li>
              <li class="breadcrumb-item active">Pixel</li>
            </ol>
          </div>
        </div>
              
    <div class="container">
        <!-- <label>Remessa ou pedido</label> 
        <input type="text" class="form-control" id="remessa" name="remessa"><br>

        <button class="btn btn-primary" id="btn_email">Buscar Emails</button> -->


        <form action="#" method="post">
            <label>Email</label> 
            <input type="text" class="form-control" name="email" id="email"><br>

            <label>Nome</label>
            <input type="text" class="form-control" name="nome" id="nome"><br>

            <label>Assunto</label>
            <input type="text" class="form-control" name="assunto"><br>

            <label>Mensagem</label>
            <pre>
            <textarea name="mensagem" class="form-control" id="" cols="30" rows="10"></textarea><br>
            </pre>

            <input type="submit" class="btn btn-primary" value="Enviar" name="enviar">
            <br>
            <input type="submit" value="Validar" name="validar" id="validar">
        </form>
    </div>

    <script
			  src="https://code.jquery.com/jquery-3.5.1.min.js"
			  integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0="
              crossorigin="anonymous"></script>
              
    <script>
        $("#btn_email").click( function(e) {

            e.preventDefault();
            var remessa = $("#remessa").val();

            $.ajax({
                url: "<?=url("encomendas/busca-email") ?>",
                data: {remessa: remessa},
                type: "POST",
                beforeSend: function () {
                    console.log("testando");
                },
                success: function (callback) {
                    var dados = JSON.parse(callback);
                    console.log(dados.nome);
                    $("#email").attr("value", dados.email);
                }
            });
        });

        $("#validar").click( function(e) {

            e.preventDefault();
            var email = $("#email").val();

            $.ajax({
                url: "<?=url("themes/tema1/ferramentas/validar-email.php") ?>",
                data: {email: email},
                type: "POST",
                beforeSend: function () {
                    console.log("testando");
                },
                success: function (callback) {
                    alert(callback);
                }
            });
        });
    </script>

<?php
use Source\Helpers\Emails;
if(isset($_POST["enviar"])){
    /*echo "<pre>";
    var_dump($_POST);
    echo "</pre>";*/

    $data["assunto"] = $_POST["assunto"];
    $data["mensagem"] = "<pre> {$_POST["mensagem"]} </pre>";
    $data["nome"] = @$_POST["nome"];

    $email = explode(",", $_POST["email"]);
    //var_dump($email);

    foreach($email as $email){
        $data["email"] = $email;
        echo "<pre>";
        $envia = (new Emails())->enviaSendGrid($data);
        echo $envia . " | " . $data["email"];

        echo "</pre>";
    }
    //$envia = (new MailHelper())->enviaPHPMAILER($_POST);
}

?>

</div>
    
      </div>
      
    </div>
    </section>
   
  </div>