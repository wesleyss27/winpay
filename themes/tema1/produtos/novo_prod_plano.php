<?php

$v->layout("_theme");

if ($produto->usuario != $section->getId()) {
  header("Location: " . url("produtos/detalhe/") . $produto->id);
}

$i = 1;

$status = array(
  1 => "EM ANÁLISE",
  2 => "APROVADO",
  3 => "REPROVADO",
  4 => "BLOQUEADO"
);
$ativo = array(
  1 => "SIM",
  0 => "NÃO"
);
if ($_SESSION["tipo"] == 6) {
  header("Location: " . url("vendas"));
}
?>

<div class="content-wrapper">
  <!-- Content Wrapper. Contains page content -->
  <section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-10">
          <h4 style="font-family:inter"><?= $title ?></h4>
        </div>
        <div class="col-sm-2">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="<?= url() ?>">Início</a></li>
            <li class="breadcrumb-item active">Novo Plano</li>
          </ol>
        </div>
      </div>
      <div class="row">
        <div class="col">
          <ul id="progressbar">
            <li class="active" id="step1"><strong>Cadastrar Produto</strong></li>
            <li class="active" id="step2"><strong>Cadastrar Plano</strong></li>
            <li id="step3"><strong>Cadastrar Checkout</strong></li>
          </ul>
        </div>
      </div>
    </div><!-- /.container-fluid -->
  </section>

  <style>
    /* Modernização local - mantém a paleta WinPay (amarelo #FFDD00) */
    :root {
      --winpay-primary: #FFDD00;
      --winpay-primary-700: #D4B800;
      --winpay-bg-elevated: #0b0b0b;
      --winpay-bg-surface: #101010;
      --winpay-border: rgba(255, 255, 255, 0.08);
      --winpay-text: #f2f2f2;
      --winpay-text-dim: #bdbdbd;
      --winpay-focus: rgba(255, 221, 0, 0.35);
      --winpay-shadow: 0 10px 30px rgba(0,0,0,0.35);
    }

    /* Breadcrumb (existente) deixa mais elegante sem mudar markup */
    .breadcrumb {
      background: transparent;
      margin: 0;
      padding: 0;
    }

    /* Cards */
    .card {
      background: linear-gradient(135deg, var(--winpay-bg-surface) 0%, #060606 100%);
      border: 1px solid var(--winpay-border);
      border-radius: 14px;
      box-shadow: var(--winpay-shadow);
      overflow: hidden;
    }
    .card > .card-body { padding: 18px; }

    /* Inputs */
    .form-control {
      background: #111;
      border: 1px solid rgba(255,255,255,0.08);
      color: var(--winpay-text);
      border-radius: 10px;
      height: 43px;
      transition: border-color .2s ease, box-shadow .2s ease, background .2s ease;
    }
    .form-control::placeholder { color: #8c8c8c; }
    .form-control:focus {
      background: #141414;
      color: #fff;
      border-color: rgba(255, 221, 0, 0.45);
      box-shadow: 0 0 0 3px var(--winpay-focus);
    }

    label { color: var(--winpay-text); font-weight: 500; }
    small { color: var(--winpay-text-dim); }

    /* Callouts */
    .callout.callout-primary {
      background: linear-gradient(145deg, rgba(255, 221, 0, 0.08), rgba(255, 221, 0, 0.02));
      border-left: 4px solid var(--winpay-primary);
      border-radius: 12px;
      border: 1px solid rgba(255, 221, 0, 0.2);
      color: var(--winpay-text);
    }

    /* Botões */
    .btn.btn-primary {
      background: linear-gradient(135deg, var(--winpay-primary) 0%, #ffe866 100%);
      color: #1a1a1a;
      border: none;
      border-radius: 10px;
      padding: 10px 18px;
      font-weight: 600;
      transition: transform .15s ease, box-shadow .2s ease, filter .2s ease;
    }
    .btn.btn-primary:hover {
      transform: translateY(-1px);
      box-shadow: 0 8px 22px rgba(255, 221, 0, 0.25);
      filter: brightness(1.02);
    }
    .btn.btn-outline-primary {
      border: 1px solid var(--winpay-primary);
      color: var(--winpay-primary);
      border-radius: 10px;
      background: transparent;
    }
    .btn.btn-outline-primary:hover {
      background: rgba(255, 221, 0, 0.1);
      color: var(--winpay-primary);
      box-shadow: 0 6px 18px rgba(255,221,0,0.18);
    }

    /* Progressbar existente (#progressbar) */
    #progressbar {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 10px;
      counter-reset: step;
      padding: 0;
      margin: 10px 0 0;
      list-style: none;
    }
    #progressbar li {
      position: relative;
      text-align: center;
      color: var(--winpay-text-dim);
      font-weight: 600;
      background: linear-gradient(145deg, #0f0f0f, #0a0a0a);
      border: 1px solid var(--winpay-border);
      padding: 12px 8px;
      border-radius: 12px;
      transition: all .25s ease;
    }
    #progressbar li::before {
      counter-increment: step;
      content: counter(step);
      width: 28px;
      height: 28px;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      border-radius: 50%;
      margin-right: 8px;
      background: rgba(255, 221, 0, 0.15);
      color: var(--winpay-primary-700);
      border: 1px solid rgba(255,221,0,0.25);
      box-shadow: inset 0 0 10px rgba(255,221,0,0.08);
    }
    #progressbar li.active {
      color: var(--winpay-primary);
      border-color: rgba(255,221,0,0.35);
      box-shadow: 0 8px 26px rgba(255,221,0,0.12);
    }
    #progressbar li.active::before {
      background: var(--winpay-primary);
      color: #1a1a1a;
      border-color: var(--winpay-primary);
    }

    /* Checkbox rascunho */
    .rascunho.alert {
      background: linear-gradient(145deg, rgba(255, 193, 7, 0.15), rgba(255, 193, 7, 0.05));
      border: 1px solid rgba(255, 193, 7, 0.3);
      border-radius: 12px;
      color: var(--winpay-text);
    }

    /* Seções de upload */
    input[type="file"].form-control { background: #111; }

    /* Ajuda (botão flutuante) */
    .btn-ajuda {
      border-radius: 999px;
      background: linear-gradient(135deg, var(--winpay-primary) 0%, #ffe866 100%);
      color: #151515;
      border: none;
      box-shadow: 0 10px 24px rgba(255,221,0,0.28);
      font-weight: 700;
    }
    .ajuda {
      background: linear-gradient(135deg, #0f0f0f, #0a0a0a);
      border: 1px solid var(--winpay-border);
      border-radius: 14px;
      color: var(--winpay-text);
      box-shadow: var(--winpay-shadow);
    }
    .ajuda-titulo { color: var(--winpay-primary); }
    .mensagens a { color: var(--winpay-primary); }
  </style>

  <!-- Main content -->
  <section class="content">
    <?= $v->insert("fragmentos/msg-cadastrar-editar", []); ?>
    <!-- Default box -->

    <!-- cadastar -->
    <form action="<?= $router->route("produtos.planosCadastro") ?>" method="post" style="font-family:inter; text-transform:uppercase; font-size:11px" name="plano" enctype="multipart/form-data">

      <input type="hidden" name="status" value="<?= $produto->status ?>">
      <input type="hidden" name="produto" value="<?= $produto->id ?>">
      <input type="hidden" name="plano-guiado" value="1">

      <div class="card card-body">
        <div class="row">
          <div class="col-md-4">
            <label>Nome do plano: *</label>
            <input type="text" name="nome" id='nome' class="form-control input-lg" placeholder="Nome">
          </div>

          <div class="col-md-4">
            <label>Preço: * <small>mínimo: R$ 5,00</small></label>
            <input type="text" name="preco" id='preco' class="form-control input-lg" placeholder="Preço *" required>
          </div>

          <div class="col-md-4">
            <label>Quantidade: </label>
            <input type="text" name="quantidade" id='quantidade' class="form-control input-lg" placeholder="Quantidade *">
            <small>Quantidade de itens que compõe esse plano</small>
          </div>
        </div>

        <hr>

        <div class="row">
          <div class="col-md-4">
            <label>Máximo de parcelas:</label>
            <input type="number" max="12" min="1" name="parcelas" id="parcelas" class="form-control">
            <small>O máximo de parcelas que deseja disponibilizar no checkout. Não tem relação com assumir juros do cartão</small>
          </div>

          <div class="col-md-4">
            <label>Assumir juros do parcelamento:</label>
            <select name="assumir_juros" id="assumir_juros" class="form-control">
              <option value="0">Não</option>
              <option value="1">Sim</option>
            </select>
            <small>Caso deseje oferecer parcelamento sem juros. O produtor assume os juros do parcelamento</small>
          </div>

          <div class="col-md-4" id="parcelamento">
            <label>Simulação Parcelamento</label>

            <select name="parcelamento1" id="parcelamento1" class="form-control newform3">
              <?php for ($parcela = 1; $parcela <= 12; $parcela++) : ?>
                <option value="<?= $parcela ?>" id="parcelas<?= $parcela ?>"></option>
              <?php endfor ?>
            </select>
          </div>
        </div>
      </div>

      <hr>
      <div class="card card-body">
        <span><label>Breve Descrição:</label> <small>Não inserir quebra de linha</small></span>
        <input type="text" name="descricao" id="descricao" class="form-control">
        <small>máximo 200 carcteres</small>
      </div>

      <hr>
      <div class="card card-body">
        <!-- COMISSÃO AFILIADO -->
        <?php if (@$produto->tipo_cobranca != 2) : ?>
          <div class="row">
            <!-- <small><b>*Atenção! Caso o plano esteja ativo para o televendas e foi definido uma comissao personalizada para o afiliado no televendas, será priorizado a comissão que está definido no televendas!</b></small> -->
            <div class="col-md-6">
              <br>
              <b>Habilitar comissão personalizada para o afiliado?*</b>
              <br><br>
              <select name="habilitar_comissao_afiliado" id="habilitar_comissao" class="form-control">
                <option value="0" <?= (@$plano->habilitar_comissao_afiliado == 0) ? 'selected' : '' ?>>Não</option>
                <option value="1" <?= (@$plano->habilitar_comissao_afiliado == 1) ? 'selected' : '' ?>>Sim</option>
              </select>
            </div>
            <div class="mostrar_comissao" id="mostrar_comissao" style="margin-left: 5px">
              <br>
              <div class="row">
                <label><b>Comissão Afiliado:</b></label>
              </div>

              <div class="row" style="margin-top: 2%;">
                <div class="col-md-3">
                  <select name="tipo_comissao1" id="tipo_comissao1" class="form-control">
                    <option value="0" <?= (@$plano->tipo_comissao_afiliado == 0) ? 'selected' : ''  ?>>%</option>
                    <option value="1" <?= (@$plano->tipo_comissao_afiliado == 1) ? 'selected' : ''  ?>>R$</option>
                  </select>
                </div>

                <div class="col-md-3">
                  <input type="text" name="comissao1" id="comissao1" class="form-control" value="<?= (@$plano->comissao_afiliado != "") ? @$plano->comissao_afiliado : ''  ?>" placeholder="0,00">
                  <br>

                </div>
              </div>
            </div>
          </div>
          <!-- FIM COMISSÃO AFILIADO -->
          <hr>
        <?php endif; ?>

        <!-- COMISSÃO COPRODUTORES -->
        <?php if ($coproducao) : $i = 0; ?>
          <div class="row">
            <div class="col-md-4">
              <label>Habilitar comissão personalizada de coprodutores cadastrados</label>

              <select name="habilitar_comissao_coprodutores" id="habilitar_comissao_coprodutores" class="form-control">
                <option value="0">Não</option>
                <option value="1">Sim</option>
              </select>
              <small>Para habilitar é necessário ter coprodutores cadastrados na aba coprodutores e os mesmos tenham aceitado a coprodução</small>
            </div>

            <div class="col-md-8 mostrar_comissao_coprodutor">

              <?php foreach ($coproducao as $coprodutor) : ?>
                <div class="row">
                  <div class="col-md-6">
                    <b>Coprodutor:<input type="hidden" class="form-control" name="coprodutores[<?= $i ?>]" id="coprodutores<?= $coprodutor->co_produtor ?>" readonly></b>
                    <b>Coprodutor:<input type="text" class="form-control" name="coprodutor<?= $coprodutor->co_produtor ?>" id="coprodutor<?= $coprodutor->co_produtor ?>" readonly></b>
                  </div>

                  <div class="col-md-2">
                    <br>
                    <select name="tipo_comissao_coprodutor[<?= $i ?>]" id="tipo_comissao_coprodutor<?= $coprodutor->co_produtor ?>" class="form-control">
                      <option value="0">%</option>
                      <option value="1">R$</option>
                    </select>
                  </div>

                  <div class="col-md-4"><br><input type="text" name="comissao_coprodutor[<?= $i ?>]" id="comissao_coprodutor<?= $coprodutor->co_produtor ?>" class="form-control"></div>
                </div>
                <br>
              <?php $i++;
              endforeach; ?>
            </div>
          </div>
        <?php endif; ?>
      </div>

      <?php if ($produto->tipo_cobranca == 2) : ?>
        <hr>
        <div class="card card-body">
          <div class="row">
            <div class="col-md-4">
              <label>Periodicidade:</label>
              <select name="periodo" id="periodo" class="form-control">
                <option value="Semanal">Semanal</option>
                <option value="Mensal" selected>Mensal</option>
                <option value="Bimestral">Bimestral</option>
                <option value="Trimestral">Trimestral</option>
                <option value="Semestral">Semestral</option>
                <option value="Anual">Anual</option>
                <option value="Trienal">Trienal</option>
              </select>
            </div>

            <div class="col-md-4">
              <label>Máximo de recorrência: </label>
              <input type="text" name="maximo_recorrencia" id='maximo_recorrencia' class="form-control input-lg" placeholder="">
              <small>Deixe em branco para a assinatura ser cobrada por tempo inderteminado</small>
            </div>

            <div class="col-md-4">
              <label>Primeira parcela:</label>
              <select name="primeira_parcela" id="primeira_parcela" class="form-control">
                <option value="igual">Igual as demais</option>
                <option value="gratis">Grátis</option>
                <option value="preco">Preço diferenciado</option>
                <option value="carencia">Carência</option>
              </select>
            </div>

            <div class="col-md-4">
              <br>
              <div class="preco1">
                <label>Preço primeira parcela: *</label>

                <div class="input-group mb-3">
                  <div class="input-group-prepend">
                    <span class="input-group-text" id="basic-addon1">R$</span>
                  </div>
                  <input type="text" name="preco_parcela" id='preco_parcela' class="form-control input-lg">
                </div>
              </div>

              <div class="carencia1">
                <label>Tempo de carência: *</label>

                <div class="input-group mb-3">
                  <input type="text" name="carencia" id='carencia' class="form-control input-lg">
                  <div class="input-group-append">
                    <span class="input-group-text" id="basic-addon1">dias</span>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-md-4">
              <label>Comissão Afiliado: (%)</label>
              <input type="text" name="comissao_afiliado_assinatura" id="comissao_afiliado_assinatura" class="form-control">
              <small>Comissão para afiliado a partir da segunda cobrança. Coloque 0 (zero) caso não queira que o afiliado receba comissão</small>
            </div>
            <div class="col-md-4"></div>
            <div class="col-md-4"></div>
          </div>
        </div>
      <?php endif ?>

      <hr>
      <div class="card card-body">
        <div class="row">
          <div class="col-md-6">

            <label>Imagem do plano:</label> 200px X 200px, max.: 2mb
            <input type="file" name="foto" id="foto" class="form-control"><br>

            <input type="hidden" class="form-control" id="foto_atual" name="foto_atual">
            <input type="hidden" class="form-control" id="ft_atual" name="ft_atual">

            <button id="excluir-foto" class="btn btn-link text-danger" title="Remover imagem atual">&times;</button>
            <img src="" alt="" id="imagem_foto_atual" width="100%">

            <br>
          </div>
        </div>
      </div>

      <hr>
      <div class="card card-body">
        <div class="row">
          <div class="col-md-4">
            <label>Plano privado:</label>
            <select name="privado" id="privado" class="form-control">
              <option value="0">Não</option>
              <option value="1">Sim</option>
            </select>
          </div>

          <div class="col-md-4">
            <label>Ativar Plano:</label>
            <select name="ativo" id="ativo" class="form-control">
              <option value="0">Não</option>
              <option value="1" selected>Sim</option>
            </select>
          </div>

          <div class="col-md-4">
            <label>Habilitar para ecommerce</label>
            <select name="habilitar_ecommerce" id="habilitar_ecommerce" class="form-control">
              <option value="0">Não</option>
              <option value="1">Sim</option>
            </select>
          </div>

        </div>
      </div>

      <div class="card card-body">
        <div style="float:right">
          <button class="btn btn-finalizar-tarde" style="float:left" id="finish-later">Finalizar mais tarde <i class="far fa-clock"></i></button>
          &nbsp;
          
          <?php if (!@$_SESSION["subusuario"] || $permissao->adicionar == 1) : ?>
            <button type="submit" class="btn btn-primary" style="float:right" id="next-step">Continuar <i class="fas fa-arrow-right"></i></button>
          <?php endif ?>
        </div>
      </div>
    </form>
    <!-- end cadastrar -->
  </section>

  <!-- INICIO BOTÃO AJUDA E SUAS FUNCIONALIDADES -->
  <button class="btn btn-primary btn-ajuda" id="btn-ajuda">
    <i class="far fa-question-circle"></i> <span class="desktop">Ajuda</span>
  </button>

  <?php
  $assunto = "Preciso de Ajuda";
  $para = "suporte.oficial@redrocketpay.com";
  $mensagem = "Email: {$_SESSION["email"]} %0D" .
    "ID: {$_SESSION["id"]} %0D" .
    "Página: {$_SERVER["REQUEST_URI"]} %0D%0D" .
    "Olá, sou " . $_SESSION["nome"] . " e gostaria de ajuda. Estou com o seguinte problema: ";

  $mensagem_whats = "Email: {$_SESSION["email"]} %0D" .
    "ID: {$_SESSION["id"]} %0D" .
    "Página: {$_SERVER["REQUEST_URI"]} %0D%0D" .
    "Olá, sou " . $_SESSION["nome"] . " e gostaria de ajuda.";
  ?>

  <div class="ajuda">
    <div class="ajuda-titulo">
      Assistente
      <button type="button" class="close" id="ajuda-fechar"> &times;</button>
    </div>

    <div class="ajuda-corpo">
      <div class="mensagens">Olá <?= $_SESSION["nome_abreviado"] ?>, tudo bem? Como posso te ajudar?</div>
      <div class="mensagens"><a href="https://octapay.com.br/tutoriais/cadastro-de-planos/" target="_blank">Dúvidas sobre o cadastro de plano</a></div>
      <div class="mensagens"><a href="mailto:<?= $para ?>?subject=<?= $assunto ?>&body=<?= $mensagem ?>" target="_blank">Entrar em contato por email</a></div>
      <div class="mensagens"><a href="https://api.whatsapp.com/send?phone=5533998131343&text=<?= $mensagem_whats ?>" target="_blank">Entrar em contato pelo Whatsapp</a></div>
    </div>
  </div>
  <!-- FIM BOTÃO AJUDA E SUAS FUNCIONALIDADES -->

  <?= $v->start("scripts") ?>
  <script>
    // INICIO BOTÃO DE AJUDA
    $("#btn-ajuda").click(() => {
      $(".ajuda").toggle("slow");
    });

    $("#ajuda-fechar").click(() => {
      $(".ajuda").hide("slow");
    });
    // FIM BOTÃO DE AJUDA
    // function juros() {
    //   if ($("#assumir_juros").val() == 1) {

    //     $("#parcelamento").show();

    //     var capital = parseFloat($("#preco").val());
    //     // var capital = 150;
    //     var i = 0.0299;
    //     var valor = 0;
    //     var total = 0;

    //     for (n = 1; n <= 12; n++) {
    //       if (n == 1) {
    //         juros = capital;
    //       } else if (n == 2) {
    //         valor = capital + (capital * 0.0519);
    //         valor = valor / n;
    //         juros = valor;
    //       } else {
    //         t = 1 + i;
    //         exp = Math.pow(t, (-n));
    //         juros = (i / (1 - exp)) * capital;
    //       }

    //       total = juros * n;
    //       total = total.toLocaleString("pt-br", {
    //         style: 'currency',
    //         currency: 'BRL'
    //       });
    //       juros = juros.toLocaleString("pt-br", {
    //         style: 'currency',
    //         currency: 'BRL'
    //       });

    //       console.log(juros);
    //       $("#parcelas" + n).html(n + "X de " + juros + " = " + total);
    //     }
    //   } else {
    //     $("#parcelamento").hide();
    //   }
    // }

    // $("#parcelamento").hide();

    // $("#assumir_juros,#preco").change(() => {
    //   juros();
    // });

    $("#preco").blur(function(e) {
      var valor = $("#preco").val();
      $("#preco").val(valor.replace(",", "."));

      if (valor < 5) {
        $("#preco").focus();
        alert("Valor do produto abaixo do mínimo permitido. Valor mínimo R$5,00");
      }
    });

    function juros() {
      if ($("#assumir_juros").val() == 1) {

        $("#parcelamento").show();

        var capital = parseFloat($("#preco").val());
        // var capital = 150;
        var i = 0.0299;
        var valor = 0;
        var total = 0;

        for (n = 1; n <= 12; n++) {
          if (n == 1) {
            juros = capital;
          } else if (n == 2) {
            valor = capital + (capital * 0.0519);
            valor = valor / n;
            juros = valor;
          } else {
            t = 1 + i;
            exp = Math.pow(t, (-n));
            juros = (i / (1 - exp)) * capital;
          }

          total = juros * n;
          total = total.toLocaleString("pt-br", {
            style: 'currency',
            currency: 'BRL'
          });
          juros = juros.toLocaleString("pt-br", {
            style: 'currency',
            currency: 'BRL'
          });

          console.log(juros);
          $("#parcelas" + n).html(n + "X de " + juros + " = " + total);
        }
      } else {
        $("#parcelamento").hide();
      }
    }

    $("#parcelamento").hide();

    $("#assumir_juros, #preco").change(() => {
      // alert("preco");
      if ($("#assumir_juros").val() == 1) {
        $("#parcelamento").show();
        juros();
      } else {
        $("#parcelamento").hide();
      }
    });

    $("#parcelas").blur(function(e) {
      var valor = $("#parcelas").val();

      if (valor > 12) {
        $("#parcelas").prop("value", 12);
      }

      if (valor < 1) {
        $("#parcelas").prop("value", 1);
      }

      var val = $("#preco").val();

      if ((val / valor) < 5) {
        var minimo = val / 5;
        alert("O valor mínimo da parcela precisa ser de R$ 5,00. Para isso você precisa colocar o máximo de parcelas em " + minimo + " vezes");
      }
    });

    $("#comissao1").blur(function(e) {
      var valor = $("#comissao1").val();
      $("#comissao1").val(valor.replace(",", "."));
    });

    $(".preco1").hide();
    $(".carencia1").hide();
    $("#primeira_parcela").change(() => {
      var tipo = $("#primeira_parcela").val();

      if (tipo == "preco") {
        $(".preco1").show();
        $(".carencia1").hide();
      } else if (tipo == "carencia") {
        $(".preco1").hide();
        $(".carencia1").show();
      } else {
        $(".preco1").hide();
        $(".carencia1").hide();
      }
    });

    $("#excluir-foto").hide();
    $(".mostrar_comissao_coprodutor").css("display", "none");

    // INICIO SESSAO COMISSAO AFILIADO
    $(".mostrar_comissao").css("display", "none");
    $("#habilitar_comissao").change((e) => {
      var hab_comissao = $("#habilitar_comissao").val();
      console.log(hab_comissao);
      if (hab_comissao == 1) {
        $(".mostrar_comissao").css("display", "inline");
        // televendas = 0;
      } else {
        $(".mostrar_comissao").css("display", "none");
        // televendas = 1;
      }
    });

    $("#habilitar_comissao_coprodutores").change((e) => {
      var hab_comissao = $("#habilitar_comissao_coprodutores").val();
      console.log(hab_comissao);
      if (hab_comissao == 1) {
        $(".mostrar_comissao_coprodutor").css("display", "inline");
        hab_comissao = 0;
      } else {
        $(".mostrar_comissao_coprodutor").css("display", "none");
        hab_comissao = 1;
      }
    });
    // FIM SESSAO COMISSAO AFILIADO

    $("#excluir-foto").click((e) => {
      e.preventDefault();
      $("#foto_atual").attr("value", "");
      $("#imagem_foto_atual").hide("slow");
      $("#excluir-foto").hide("slow");
    });
  </script>
  <?= $v->end() ?>