  
  
  <footer class="main-footer">
    <a href="<?= url("termos-uso") ?>" target="_blank">Termos e Políticas</a>
    <span class="oculta"> &nbsp; &nbsp; <?= date("Y") ?> &copy; RedRocket - <b>Versão</b> 1.1 Beta</span>
    <span class="versao"><br> <?= date("Y") ?> &copy; RedRocket - <b>Versão</b> 1.1 Beta</span> 
  </footer>

  <!-- Control Sidebar -->
  <aside class="control-sidebar control-sidebar-dark">
    <!-- Control sidebar content goes here -->
  </aside>
  <!-- /.control-sidebar -->
</div>
<!-- ./wrapper -->
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

<!--script src="<?= url("themes/tema1/dist/js/pusher.js") ?>"></script-->
  
<script>
    var i = 0;
    $("#push-menu").click(() => {
      if (i == 0) {
        $("#logo-menu").attr({
          "src": "<?= url_octa("themes/imgs/logo-icon.png") ?>"
        }).css({
          "width": "35",
          "margin-left": "5px"
        });
        $("#icone-menu-bar").prop("class", "fas fa-chevron-right");
        i = 1;
      } else {
        $("#logo-menu").attr({
          "src": "<?= url_octa("themes/imgs/logo4.png") ?>"
        }).css({
          "width": "145",
          "margin-left": "-20px"
        });
        $("#icone-menu-bar").prop("class", "fas fa-chevron-left");
        i = 0;
      }

    });
  </script>