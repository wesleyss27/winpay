<script>
    // $(function () {
    //     $(window).scroll(function () {
    //         if ($(this).scrollTop() > 300) {
    //             $('#resumo').fadeIn();
    //         } else {
    //             $('#resumo').fadeOut();
    //         }
    //     });
    // });

    // TOAST DE NOTIFICAÇÃO

    <?php if (@$notificacoes->ativo == 1) : ?>
        var i = 1;
        var j = 1;
        var tempo1 = 5,
            tempo2 = 10,
            tempo;

        mostrar = function(i) {
            $("#frase-toast").html(dados[i][2]);
            $('.toast').toast('show');
        }

        esconder = function() {
            $('.toast').toast('hide');
        }

        var intervalo = setInterval(() => {
            // console.log(i);

            if (tempo1 == i) {
                // console.log(tempo1, tempo2, j);

                if (dados[j][0] && dados[j][1])
                    mostrar(j);

                tempo = tempo2;
                tempo1 += 15;
                tempo2 = tempo1 + 5;

                j++;
            }

            if (tempo == i) {
                esconder();

                if (j > 7) {
                    clearInterval(intervalo);
                }
            }
            i++;
        }, 1000);
    <?php endif; ?>
    // FIM TOAST DE NOTIFICAÇÃO
</script>