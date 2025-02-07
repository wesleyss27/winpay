  <audio id="audio">
    <source src="<?= url("themes/audios/caixa.mp3") ?>" type="audio/mp3">
  </audio>

  <footer class="main-footer">
    <?php
    $assunto = "Preciso de Ajuda";
    $para = "suporte.oficial@redrocketpay.com";
    $mensagem = "Email: {$_SESSION["nome"]} %0D".
                "ID: {$_SESSION["id"]} %0D".
                "Página: {$_SERVER["REQUEST_URI"]} %0D%0D".
                "Olá, sou " . $_SESSION["nome"] . " e gostaria de ajuda. Estou com o seguinte problema: ";
    ?>
    Dúvidas, problemas ou sugestões? <a href="mailto:<?= $para ?>?subject=<?= $assunto ?>&body=<?= $mensagem ?>">Entrar em contato</a> -
    <a href="<?= url("termos-uso") ?>" target="_blank">Termos e Políticas</a>
    <span class="oculta"> &nbsp; &nbsp; <?= date("Y") ?> &copy; WINPAY - <b>Versão</b> 1.1 Beta</span>
    <span class="versao"><br> <?= date("Y") ?> &copy; WINPAY - <b>Versão</b> 1.1 Beta</span>
  </footer>

  <!-- jQuery -->
  <script src="<?= url("themes/tema1/plugins/jquery/jquery.min.js") ?>"></script>
  <!-- jQuery UI 1.11.4 -->
  <script src="<?= url("themes/tema1/plugins/jquery-ui/jquery-ui.min.js") ?>"></script>
  <!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
  <script>
    $.widget.bridge('uibutton', $.ui.button)
  </script>
  <!-- Bootstrap 4 -->
  <script src="<?= url("themes/tema1/plugins/bootstrap/js/bootstrap.bundle.min.js") ?>"></script>
  <!-- ChartJS -->
  <script src="<?= url("themes/tema1/plugins/chart.js/Chart.min.js") ?>"></script>
  <!-- Sparkline -->
  <script src="<?= url("themes/tema1/plugins/sparklines/sparkline.js") ?>"></script>
  <!-- JQVMap -->
  <script src="<?= url("themes/tema1/plugins/jqvmap/jquery.vmap.min.js") ?>"></script>
  <script src="<?= url("themes/tema1/plugins/jqvmap/maps/jquery.vmap.usa.js") ?>"></script>
  <!-- jQuery Knob Chart -->
  <script src="<?= url("themes/tema1/plugins/jquery-knob/jquery.knob.min.js") ?>"></script>
  <!-- daterangepicker -->
  <script src="<?= url("themes/tema1/plugins/moment/moment.min.js") ?>"></script>
  <script src="<?= url("themes/tema1/plugins/daterangepicker/daterangepicker.js") ?>"></script>
  <!-- Tempusdominus Bootstrap 4 -->
  <script src="<?= url("themes/tema1/plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js") ?>"></script>
  <!-- Summernote -->
  <script src="<?= url("themes/tema1/plugins/summernote/summernote-bs4.min.js") ?>"></script>
  <!-- overlayScrollbars -->
  <script src="<?= url("themes/tema1/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js") ?>"></script>
  <!-- AdminLTE App -->
  <script src="<?= url("themes/tema1/dist/js/adminlte.js") ?>"></script>
  <!-- AdminLTE for demo purposes -->
  <script src="<?= url("themes/tema1/dist/js/demo.js") ?>"></script>
  <!-- AdminLTE dashboard demo (This is only for demo purposes) -->
  <script src="<?= url("themes/tema1/dist/js/pages/dashboard.js") ?>"></script>

  <script>
    var i = 0;
    $("#push-menu").click(() => {
      if (i == 0) {
        $("#logo-menu").attr({
          "src": "<?= url("themes/imgs/logos/logo-icon.png") ?>"
        }).css({
          "width": "35",
          "margin-left": "5px"
        });
        $("#icone-menu-bar").prop("class", "fas fa-chevron-right");
        i = 1;
      } else {
        $("#logo-menu").attr({
          "src": "<?= url("themes/imgs/logos/logo4.png") ?>"
        }).css({
          "width": "145",
          "margin-left": "-20px"
        });
        $("#icone-menu-bar").prop("class", "fas fa-chevron-left");
        i = 0;
      }

    });
  </script>

  <script src="<?= url("themes/tema1/dist/js/pusher.js") ?>"></script>

  <script>
    audio = document.getElementById('audio');

    function play() {
      audio.play();
    }

    $(".notifications").hide();

    // Enable pusher logging - don't include this in production
    // Pusher.logToConsole = true;

    /*
    var pusher = new Pusher('3947f07a6edc6d29a706', {
      cluster: 'us2'
    });

    var channel = pusher.subscribe('my-channel');
    var mensagem;
    var count = 0;
    
    channel.bind('my-event', function(data) {
      //alert(JSON.stringify(data.message));
      mensagem = JSON.stringify(data);
      mensagem = JSON.parse(mensagem);

      console.log(mensagem);
      
      if (mensagem.produtor == <?= $_SESSION["id"] ?>) {
        $("#msg").html(mensagem.message);
        $("#link_notificacao").attr("href", mensagem.link);
        $(".notifications").show();
        play();
        //alert(mensagem.message);
      }

      if (mensagem.afiliado && mensagem.afiliado == <?= $_SESSION["id"] ?>) {
        //alert(mensagem.message);
        $("#msg").html(mensagem.message);
        $("#link_notificacao").attr("href", mensagem.link);
        $(".notifications").show();
      }

      if (mensagem.coprodutor && mensagem.coprodutor == <?= $_SESSION["id"] ?>) {
        //alert(mensagem.message);
        $("#msg").html(mensagem.message);
        $("#link_notificacao").attr("href", mensagem.link);
        $(".notifications").show();
      }

      //load(<?= url("themes/tema1/inc/navbar_topo.php") ?>);
    });
    */
  </script>

  