<style>
/* Footer */
.main-footer {
  background: linear-gradient(90deg, rgba(5, 5, 8, 0.97) 0%, rgba(9, 12, 18, 0.97) 60%, rgba(15, 18, 28, 0.97) 100%);
  color: rgba(148, 163, 184, 0.55);
  border-top: 1px solid rgba(255, 255, 255, 0.04);
  padding: 0.55rem 1.5rem;
  text-align: center;
  font-size: 0.72rem;
  letter-spacing: 0.08em;
  backdrop-filter: blur(16px);
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.35rem;
  position: fixed;
  bottom: 0;
  left: 0;
  right: 0;
  z-index: 1020;
  height: 36px;
}

.content-wrapper {
  padding-bottom: 50px;
}

.main-footer a {
  color: rgb(217, 192, 0);
  text-decoration: none;
  transition: color 0.2s ease;
}

.main-footer a:hover {
  text-decoration: underline;
  color: rgba(217, 192, 0, 0.82);
}

.main-footer b {
  color: rgb(217, 192, 0);
}

/* Control Sidebar */
.control-sidebar {
  background: rgba(5, 5, 8, 0.95) !important;
  color: #f9fafb;
}

.control-sidebar h5,
.control-sidebar h6 {
  color: rgb(217, 192, 0);
}

.control-sidebar a {
  color: rgb(217, 192, 0);
}

.control-sidebar a:hover {
  color: rgba(217, 192, 0, 0.82);
}

/* Texto oculto para responsividade */
span.oculta {
  display: none;
}

@media (min-width: 600px) {
  span.oculta {
    display: inline;
  }

  span.versao {
    display: none;
  }
}
</style>

  
  <footer class="main-footer">
    <!-- <a href="<?= url("termos-uso") ?>" target="_blank">Termos e Políticas</a> -->
    <span class="oculta"> &nbsp; &nbsp; <?= date("Y") ?> &copy; WinPay - <b>Versão</b> 1.1 Beta</span>
    <span class="versao"><br> <?= date("Y") ?> &copy; WinPay - <b>Versão</b> 1.1 Beta</span> 
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
  $(function () {
    var $body = $('body');
    var $icons = $('.menu-toggle-icon');

    function updateIcon() {
      if ($body.hasClass('sidebar-collapse')) {
        $icons.removeClass('fa-chevron-left').addClass('fa-bars');
      } else {
        $icons.removeClass('fa-bars').addClass('fa-chevron-left');
      }
    }

    $(document).on('collapsed.lte.pushmenu shown.lte.pushmenu', updateIcon);
    updateIcon();

    // Back to top (global + per-page instances)
    var $btt = $('#back-to-top-global, #back-to-top');
    $(window).on('scroll.btt', function () {
      $btt.toggleClass('visible', $(this).scrollTop() > 300);
    });
    $btt.on('click', function (e) {
      e.preventDefault();
      $('html, body').animate({ scrollTop: 0 }, 400);
    });
  });
</script>
