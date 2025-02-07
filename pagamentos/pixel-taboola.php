<?php
    $pixel = @$_GET["pixel"];
    $eventos = @$_GET["ev"];
?>

    <!-- Taboola Pixel Code -->
    <script type='text/javascript'>
    window._tfa = window._tfa || [];
    window._tfa.push({notify: 'event', name: 'page_view', id: <?= $pixel ?>});
    !function (t, f, a, x) {
            if (!document.getElementById(x)) {
                t.async = 1;t.src = a;t.id=x;f.parentNode.insertBefore(t, f);
            }
    }(document.createElement('script'),
    document.getElementsByTagName('script')[0],
    '//cdn.taboola.com/libtrc/unip/<?= $pixel ?>/tfa.js',
    'tb_tfa_script');
    </script>
    <!-- End of Taboola Pixel Code -->

    <!-- Taboola Pixel Code -->
    <?php if(@$eventos): ?>
    <script>
        _tfa.push({notify: 'event', name: '<?= $eventos ?>', id: <?= $pixel ?>});
    </script>
    <?php endif; ?>
    <!-- End of Taboola Pixel Code -->
