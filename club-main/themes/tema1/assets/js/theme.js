$(document).ready(function(){
    //EVENTOS DO MENU
    $(".openNav").click(function(){//MOSTRA O MENU AO CLICAR
        $(".menu").toggle();
            
    });

    $(".closebtn").click(function(){//ESCONDE MENU AO CLICAR
        $(".menu").hide();
    });
    //FIM EVENTOS DO MENU
});