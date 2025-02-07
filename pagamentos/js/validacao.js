function limpaNum(id) {
    var x = document.getElementById(id);
    x.value = x.value.replace(/[^\d]+/g, '');
}
function allowOnlyNumbers(e) {
    var tecla = (window.event) ? event.keyCode : e.which;
    if ((tecla > 47 && tecla < 58)) return true;
    else {
        if (tecla == 8 || tecla == 0) return true;
        else return false;
    }
}

function check_form(form) {
    var inputs = document.getElementsByClassName(form);
    var len = inputs.length;
    var valid = true;
    for (var i = 0; i < len; i++) {
        if (!inputs[i].value) {
            valid = false;
        }
    }
    if (!valid) {
        alert('Por favor preencha todos os campos.');
        return false;
    }
    else {
        return true;
    }
}

function validarCPF(cpf, id, msg) {
    cpf = cpf.replace(/[^\d]+/g, '');

    // Elimina CPFs invalidos conhecidos	
    if (
        cpf.length != 11 ||
        cpf == "00000000000" ||
        cpf == "11111111111" ||
        cpf == "22222222222" ||
        cpf == "33333333333" ||
        cpf == "44444444444" ||
        cpf == "55555555555" ||
        cpf == "66666666666" ||
        cpf == "77777777777" ||
        cpf == "88888888888" ||
        cpf == "99999999999"
    ) {
        $(msg).html("CPF inválido ou incompleto!").css("color", "red");
        return false;
    } else {
        // Valida 1o digito	
        add = 0;
        for (i = 0; i < 9; i++)
            add += parseInt(cpf.charAt(i)) * (10 - i);

        rev = 11 - (add % 11);
        if (rev == 10 || rev == 11) {
            rev = 0;
        }

        if (rev != parseInt(cpf.charAt(9))) {
            $(id).focus();
            $(msg).show();
            $(msg).html("Preencha um CPF válido").css("color", "red");
        } else {
            $(msg).hide();
        }

        // Valida 2o digito	
        add = 0;
        for (i = 0; i < 10; i++) {
            add += parseInt(cpf.charAt(i)) * (11 - i);
        }

        rev = 11 - (add % 11);

        if (rev == 10 || rev == 11) {
            rev = 0;
        }

        if (rev != parseInt(cpf.charAt(10))) {
            $(id).focus();
            $(msg).show();
            $(msg).html("CPF Inválido").css("color", "red");
        } else {
            $(msg).hide();
        }
    }
}

function validarCNPJ(cnpj, id, msg) {

    cnpj = cnpj.replace(/[^\d]+/g, '');

    if (cnpj == '') return false;

    if (cnpj.length != 14)
        return false;

    // Elimina CNPJs invalidos conhecidos
    if (
        cnpj == "00000000000000" ||
        cnpj == "11111111111111" ||
        cnpj == "22222222222222" ||
        cnpj == "33333333333333" ||
        cnpj == "44444444444444" ||
        cnpj == "55555555555555" ||
        cnpj == "66666666666666" ||
        cnpj == "77777777777777" ||
        cnpj == "88888888888888" ||
        cnpj == "99999999999999"
    ) {
        $(msg).html("CNPJ inválido ou incompleto!").css("color", "red");
        return false;
    }

    // Valida DVs
    tamanho = cnpj.length - 2
    numeros = cnpj.substring(0, tamanho);
    digitos = cnpj.substring(tamanho);
    soma = 0;
    pos = tamanho - 7;
    for (i = tamanho; i >= 1; i--) {
        soma += numeros.charAt(tamanho - i) * pos--;
        if (pos < 2)
            pos = 9;
    }
    resultado = soma % 11 < 2 ? 0 : 11 - soma % 11;
    if (resultado != digitos.charAt(0)) {
        $(id).focus();
        $(msg).show();
        $(msg).html("Preencha um CNPJ válido").css("color", "red");
        return false;
    } else {
        $(msg).hide();
    }

    tamanho = tamanho + 1;
    numeros = cnpj.substring(0, tamanho);
    soma = 0;
    pos = tamanho - 7;
    for (i = tamanho; i >= 1; i--) {
        soma += numeros.charAt(tamanho - i) * pos--;
        if (pos < 2)
            pos = 9;
    }
    resultado = soma % 11 < 2 ? 0 : 11 - soma % 11;
    if (resultado != digitos.charAt(1)) {
        $(id).focus();
        $(msg).show();
        $(msg).html("CNPJ Inválido").css("color", "red");
        return false;
    } else {
        $(msg).hide();
    }

    return true;
}

var cont = 0;
function validaCartao(cartao) {

    if (cartao.length < 14 || cartao.length > 16) {
        validaTitular(false, "numcart");
        disabled(false, "cart");
        pendencias(false, "pendenciasCart");
        // $("#numcart").focus();

        if (cont == 0) {
            cont = 1;
            $("#numcart").addClass('input-error')
            // alert("Cartão inválido. Verifique os digitos!");
        }
        return false;
    }
    else {
        let nCheck = 0, bEven = false;
        for (var n = cartao.length - 1; n >= 0; n--) {
            var cDigit = cartao.charAt(n), nDigit = parseInt(cDigit, 10);

            if (bEven && (nDigit *= 2) > 9) {
                nDigit -= 9;
            }
            nCheck += nDigit;
            bEven = !bEven;
        }
        if (!(nCheck % 10) == 0) {
            $("#numcart").addClass('input-error')

            // alert("Cartao inválido");
            validaTitular(false, "numcart");
            disabled(false, "cart");
            pendencias(false, "pendenciasCart");
            return false;
        }
        else {
            validaTitular(true, "numcart");
            disabled(true, "cart");
            pendencias(true, "pendenciasCart");
            bandeiraCartao(cartao)
        }
    }
}

function validaTitular(valor, id) {
    ok = "#" + id + "-ok";
    error = "#" + id + "-error";

    if (valor != "" && valor != false) {
        $(ok).css({
            "display": "block",
            "color": "green"
        });
        $(error).css({
            "display": "none"
        });
    }
    else {
        $(error).css({
            "display": "block",
            "color": "red"
        });
        $(ok).css({
            "display": "none"
        });
    }
}

function disabled(valor, id) {
    id = "#" + id;
    if (valor == false) {
        $(id).css({
            "pointer-events": "none",
            "cursor": "default",
            "background": "#ddd",
            "color": "#000"
        });
        $("#pendencias").css({
            "display": "block"
        });
    }
    else {
        $(id).css({
            "pointer-events": "auto",
            "cursor": "pointer",
            "background": "#28A745",
            "color": "#fff"
        });
        $("#pendencias").css({
            "display": "none"
        });
    }
}

function pendencias(valor, id) {
    id = "#" + id;
    if (valor == false) {
        $(id).css({
            "display": "block"
        });
    }
    else {
        $(id).css({
            "display": "none"
        });
    }
}

function bandeiraCartao(cartao) {
    bandeira = tgdeveloper.getCardFlag(cartao);
    var url_base = $("#url_base").val();
    $("#brand").attr("value", bandeira);

    url = "url(" + url_base + "/pagamentos/imgs/brands/" + bandeira + ".png) no-repeat right";

    $("#icone-bandeira").prop({
        "src": url_base + "/pagamentos/imgs/brands/" + bandeira + ".png",
    });

    bandeira = "#" + bandeira;

    $(".brand").css({
        "opacity": "0.5"
    });
    $("#numcart").css({
        "background": url,
    });

    $("#card-box").css({
        "background": "#7efcbd",
        "color": "#7efcbd",
    });

    console.log($("#card-box"));

    console.log("bandeira ", bandeira, url, url_base);
}

// FUNÇÃO PARA PEGAR BANDEIRA DO CARTÃO
var tgdeveloper = {

    /**
    * getCardFlag
    * Return card flag by number
    *
    * @param cardnumber
    */
    getCardFlag: function (cardnumber) {
        var cardnumber = cardnumber.replace(/[^0-9]+/g, '');

        var cards = {
            visa: /^4[0-9]{12}(?:[0-9]{3})/,
            mastercard: /^5[1-5][0-9]{14}/,
            diners: /^3(?:0[0-5]|[68][0-9])[0-9]{11}/,
            amex: /^3[47][0-9]{13}/,
            discover: /^6(?:011|5[0-9]{2})[0-9]{12}/,
            hipercard: /^(606282\d{10}(\d{3})?)|(3841\d{15})/,
            elo: /^((((636368)|(438935)|(504175)|(451416)|(636297))\d{0,10})|((5067)|(4576)|(4011))\d{0,12})/,
            jcb: /^(?:2131|1800|35\d{3})\d{11}/,
            aura: /^(5078\d{2})(\d{2})(\d{11})$/
        };

        for (var flag in cards) {
            if (cards[flag].test(cardnumber)) {
                return flag;
            }
        }

        return false;
    }

}