<?php
    date_default_timezone_set("America/Sao_Paulo");

    $redirect = explode("/club-redrocket", @$_GET["redirect"]);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title; ?></title>
    <meta name="robots" content="noindex, nofollow">

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- CSS -->
    <link rel="stylesheet" href="<?= url("themes/tema1/dist/css/redefinir-senha.css?".time()); ?>">

    <!-- JavaScript -->
    <script src="<?= url("themes/tema1/plugins/jquery/jquery.min.js"); ?>"></script>
    <script src="<?= url("themes/tema1/plugins/bootstrap/js/bootstrap.bundle.min.js"); ?>"></script>
</head>
<body>
    <div class="container-fluid">
        <div class="row no-gutter">
            <!-- Lado esquerdo - Decorativo -->
            <div class="d-none d-md-flex col-md-4 col-lg-6 bg-image">
                <div class="logo-decorativo">
                    <div class="texto-decorativo">
                        <h2>Redefinir Senha</h2>
                        <p>Digite o código de recuperação enviado para seu email e defina uma nova senha segura para sua conta.</p>
                    </div>
                </div>
            </div>

            <!-- Lado direito - Formulário -->
            <div class="col-md-8 col-lg-6">
                <div class="login">
                    <h3 class="login-heading"><?= $title ?></h3>

                    <form id="formRedefinirSenha" action="<?= $router->route("appusuarios.atualizaSenha"); ?>" method="post">
                        <input type="hidden" name="id" value="<?= @$data["id"] ?>">
                        <input type="hidden" name="codigo" value="<?= @$data["codigo"] ?>">

                        <div class="form-label-group">
                            <label for="codigo_recuperacao">Código de Recuperação</label>
                            <input type="text"
                                   id="codigo_recuperacao"
                                   name="codigo_recuperacao"
                                   class="form-control"
                                   placeholder="Digite o código enviado por email"
                                   required
                                   autocomplete="off">
                            <span class="error-message" id="error-codigo"></span>
                        </div>

                        <div class="form-label-group">
                            <label for="senha">Nova Senha</label>
                            <input type="password"
                                   id="senha"
                                   name="senha"
                                   class="form-control"
                                   placeholder="Digite sua nova senha"
                                   required
                                   minlength="6"
                                   autocomplete="new-password">
                            <span class="error-message" id="error-senha"></span>
                        </div>

                        <div class="form-label-group">
                            <label for="repetir_senha">Confirmar Nova Senha</label>
                            <input type="password"
                                   name="repetir_senha"
                                   id="repetir_senha"
                                   class="form-control"
                                   placeholder="Digite novamente sua nova senha"
                                   required
                                   autocomplete="new-password">
                            <span class="error-message" id="error-repetir"></span>
                        </div>

                        <button class="btn-login" type="submit" id="btnSubmit">
                            <span class="btn-text">Redefinir Senha</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            $('#codigo_recuperacao').on('input', function() { validateCodigo(); });
            $('#senha').on('input', function() { validateSenha(); validateRepetirSenha(); });
            $('#repetir_senha').on('input', function() { validateRepetirSenha(); });

            $('#formRedefinirSenha').on('submit', function(e) {
                e.preventDefault();
                if (validateForm()) submitForm();
            });

            function validateCodigo() {
                const codigo = $('#codigo_recuperacao').val().trim();
                const err = $('#error-codigo');
                if (codigo.length === 0) return showError(err, 'Código de recuperação é obrigatório');
                if (codigo.length < 6) return showError(err, 'Código deve ter pelo menos 6 caracteres');
                hideError(err); return true;
            }

            function validateSenha() {
                const senha = $('#senha').val();
                const err = $('#error-senha');
                if (senha.length === 0) return showError(err, 'Nova senha é obrigatória');
                if (senha.length < 6) return showError(err, 'Senha deve ter pelo menos 6 caracteres');
                hideError(err); return true;
            }

            function validateRepetirSenha() {
                const senha = $('#senha').val();
                const repetir = $('#repetir_senha').val();
                const err = $('#error-repetir');
                if (repetir.length === 0) return showError(err, 'Confirmação de senha é obrigatória');
                if (senha !== repetir) return showError(err, 'Senhas não coincidem');
                hideError(err); return true;
            }

            function validateForm() {
                return validateCodigo() && validateSenha() && validateRepetirSenha();
            }

            function showError(element, message) {
                element.text(message).show();
                element.parent().addClass('error').removeClass('success');
                return false;
            }

            function hideError(element) {
                element.hide();
                element.parent().removeClass('error').addClass('success');
                return true;
            }

            function submitForm() {
                const btn = $('#btnSubmit');
                btn.addClass('loading').prop('disabled', true);
                $('.btn-text').text('Processando...');
                setTimeout(function() { $('#formRedefinirSenha')[0].submit(); }, 1000);
            }

            $('.form-label-group input').on('focus', function() {
                $(this).parent().addClass('focused');
            }).on('blur', function() {
                $(this).parent().removeClass('focused');
            });
        });
    </script>
</body>
</html>
