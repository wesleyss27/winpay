<?php
$v->layout("_theme");
if ($_SESSION["tipo"] == 6) {
    header("Location: " . url("vendas"));
}
?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->

    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 style="font-family:inter;padding-left: 15px;">Editar Aulas</h1>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">
        <style>
            .btn-light {
                background: #fff;
                border: 1px solid #ddd;
            }

            .mn a {
                padding: 0 15px;
                font-weight: 600;
            }
        </style>
        <div class="card card-body mn">
            <a href="<?= url("cursos/detalhe/" . @$curso->id) ?>" class="txt-azul-dark"><i class="fa-solid fa-book-open"></i> Conteúdo</a>
            <a href="<?= url("turmas/alunos/" . @$curso->id) ?>" class="txt-azul-dark"><i class="fa-solid fa-users"></i> Alunos</a>
            <a href="#" class="txt-azul-dark"><i class="fa-regular fa-comments"></i> Comentários</a>
            <a href="<?= url("cursos/editarcursos?course_id=" . @$curso->id) ?>" class="txt-azul-dark"><i class="fa-solid fa-gear"></i> Configurações</a>
            <a href="../editarmodulo/<?= @$curso->id ?>" class="txt-azul-dark"><i class="fa fa-plus" aria-hidden="true"></i> Novo Módulo</a>
        </div>

        <div class="card card-body">
            <form action="<?= $router->route("appcursos.cadastroeditaraulas"); ?>" id="form" method="post" enctype="multipart/form-data">

                <div class="form-container" style="padding-top: 0;">
                    <div class="row">
                        <!-- LADO ESQUERDO -->
                        <div class="col-md-8 col-12 ">
                            <div class="form-group">
                                <label>Nome da Aula</label>
                                <input type="text" class="form-control" id="formGroupExampleInput" placeholder="Aula 1" name="aulas_nome" required value="<?= @$aulass[0]->nome; ?>">
                            </div>

                            <?php if ($modulos) : ?>
                                <div class="form-group">
                                    <div class="inner-selct ">
                                        <label for="inputState">Módulo</label>
                                        <select id="inputState" class="form-control" name="modulo_sel" required>
                                            <option selected>Choose...</option>
                                            <?php foreach ($modulos as $modulo) : ?>
                                                <option value="<?php echo $modulo->id; ?>" <?= (@$aulass[0]->modulo == $modulo->id) ? "selected" : "" ?>><?= $modulo->nome; ?></option>
                                            <?php endforeach;  ?>
                                        </select>
                                    </div>
                                </div>
                            <?php endif;  ?>

                            <p>Coloque o link do vídeo ou o script do player</p>

                            <div class="form-group">
                                <label for="formGroupExampleInput">Video</label>
                                <input type="url" class="form-control" id="formGroupExampleInput" placeholder="" name="link_video" value="<?= @$aulass[0]->link_video; ?>">
                            </div>

                            <div class="form-group">
                                <label for="formGroupExampleInput">Script do player (opcional)</label>
                                <textarea class="form-control" rows="5" name="script_player"><?= @base64_decode(@$aulass[0]->script_player); ?></textarea>
                            </div>
                        </div>
                        <!-- FIM LADO ESQUERDO -->

                        <!-- LADO DIREITO -->
                        <div class="col-lg-4 col-md-4 col-12 right-area" id="right-col_form">
                            <label for="exampleFormControlTextarea1">Imagem do curso</label>
                            <div class="image-col">
                                <div class="input-group mb-3">
                                    <div class="custom-file">
                                        <?php if (!empty($_GET['aulas_id'])) {  ?>
                                            <?php if ($aulass[0]->imagem != "") { ?>
                                                <input type="file" accept="image/*" id="imageInput" class="FileUpload1" name="imagem">
                                                <label for="imageInput" class="filelabel" style="display:none;"><i class="far fa-image"></i> Choose image</label>
                                                <img src="<?= url("uploads/cursos/" . $aulass[0]->imagem); ?>" class="image-preview">
                                                <span class="change-image">Escolha uma imagem Diferente</span>
                                            <?php } else {

                                            ?>
                                                <input type="file" accept="image/*" id="imageInput" class="FileUpload1" name="imagem">
                                                <label for="imageInput" class="filelabel"><i class="far fa-image"></i> Escolha uma imagem</label>
                                                <img src="" class="image-preview">
                                                <span class="change-image">Escolha uma imagem Diferente</span>

                                            <?php }
                                        } else { ?>

                                            <input type="file" accept="image/*" id="imageInput" class="FileUpload1" name="imagem">
                                            <label for="imageInput" class="filelabel"><i class="far fa-image"></i> Escolha uma imagem</label>
                                            <img src="" class="image-preview">
                                            <span class="change-image">Escolha uma imagem Diferente</span>

                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- FIM LADO DIREITO -->

                    </div>


                    <div class="col-lg-12 col-md-12 col-12 form-group">
                        <div class="form-group">
                            <label for="formGroupExampleInput">Conteúdo da aula</label>
                            <textarea class="form-control" id="exampleFormControlTextarea1" rows="6" placeholder="Módulo 1" name="aulas_descricao" required><?= @$aulass[0]->descricao; ?></textarea>
                        </div>
                    </div>
                    <?php
                    if (!empty($aulass)) {
                    ?>
                        <input type="hidden" name="aulas_id" value="<?php echo @$aulass[0]->id; ?>">
                        <input type="hidden" name="anexo1_img1" value="<?php echo @$aulass[0]->arquivo_anexo1; ?>">
                        <input type="hidden" name="anexo2_img2" value="<?php echo @$aulass[0]->arquivo_anexo2; ?>">
                        <input type="hidden" name="anexo3_img3" value="<?php echo @$aulass[0]->arquivo_anexo3; ?>">

                    <?php
                    }
                    ?>
                    <div class="col-lg-12 col-md-12 col-12 form-group">
                        Tamanho máximo para arquivos: 10mb <br>
                        Arquivos suportados: png, jpg, jpeg, pdf, zip, rar, webm, mp4, mpg
                        <div class="attachment">
                            <label class="filelabel1">
                                <i class="fa fa-paperclip">
                                </i>
                                <?php
                                if (!empty(@$aulass[0]->arquivo_anexo1)) {
                                ?>
                                    <span class="title1" style="color: rgb(32, 132, 64);"><?= @$aulass[0]->arquivo_anexo1; ?></span>
                                <?php
                                } else {
                                ?>
                                    <span class="title1">
                                        Add File1
                                    </span>
                                <?php
                                }
                                ?>
                                <input class="FileUpload1" id="FileInput1" name="arquivo_anexo1" type="file">
                            </label>
                        </div>
                        <div class="attachment">
                            <label class="filelabel2">
                                <i class="fa fa-paperclip">
                                </i>
                                <?php
                                if (!empty(@$aulass[0]->arquivo_anexo2)) {
                                ?>
                                    <span class="title1" style="color: rgb(32, 132, 64);"><?= @$aulass[0]->arquivo_anexo2; ?></span>
                                <?php
                                } else {
                                ?>
                                    <span class="title2">
                                        Add File2
                                    </span>
                                <?php
                                }
                                ?>
                                <input class="FileUpload2" id="FileInput2" name="arquivo_anexo2" type="file">
                            </label>
                        </div>
                        <div class="attachment">
                            <label class="filelabel3">
                                <i class="fa fa-paperclip">
                                </i>
                                <?php
                                if (!empty(@$aulass[0]->arquivo_anexo3)) {
                                ?>
                                    <span class="title1" style="color: rgb(32, 132, 64);"><?= @$aulass[0]->arquivo_anexo3; ?></span>
                                <?php
                                } else {
                                ?>
                                    <span class="title3">
                                        Add File3
                                    </span>
                                <?php
                                }
                                ?>
                                <input class="FileUpload3" id="FileInput3" name="arquivo_anexo3" type="file">
                            </label>
                        </div>
                    </div>
                    <div class="col-lg-12 col-md-12 col-12 form-group">
                        <div class="attachment_links">
                            <label for="formGroupExampleInput">Links</label>
                            <input class="form-control" name="links" type="text" placeholder="Links" value="<?= @$aulass[0]->link_externo; ?>" />
                        </div>
                    </div>
                    <input type="hidden" name="course_id" value="<?php echo @$course_id; ?>">

                    <input type="submit" name="submit-form" class="nxt_step submit_form_btn" value="Salvar" />
                </div>
            </form>
        </div>
    </section>

</div>
<style>
    section.content {
        padding-top: 0px !important;
    }

    .content-header {
        padding-bottom: 0px;
    }
</style>

<?= $v->start("scripts") ?>
<script>
    $(document).ready(function() {
        var currentGfgStep, nextGfgStep, previousGfgStep;
        var opacity;
        var current = 1;
        var steps = $("fieldset").length;

        setProgressBar(current);

        $(".next-step").click(function() {

            currentGfgStep = $(this).parent();
            nextGfgStep = $(this).parent().next();

            $("#progressbar li").eq($("fieldset")
                .index(nextGfgStep)).addClass("active");

            nextGfgStep.show();
            currentGfgStep.animate({
                opacity: 0
            }, {
                step: function(now) {
                    opacity = 1 - now;

                    currentGfgStep.css({
                        'display': 'none',
                        'position': 'relative'
                    });
                    nextGfgStep.css({
                        'opacity': opacity
                    });
                },
                duration: 500
            });
            setProgressBar(++current);
        });

        $(".previous-step").click(function() {

            currentGfgStep = $(this).parent();
            previousGfgStep = $(this).parent().prev();

            $("#progressbar li").eq($("fieldset")
                .index(currentGfgStep)).removeClass("active");

            previousGfgStep.show();

            currentGfgStep.animate({
                opacity: 0
            }, {
                step: function(now) {
                    opacity = 1 - now;

                    currentGfgStep.css({
                        'display': 'none',
                        'position': 'relative'
                    });
                    previousGfgStep.css({
                        'opacity': opacity
                    });
                },
                duration: 500
            });
            setProgressBar(--current);
        });

        function setProgressBar(currentStep) {
            var percent = parseFloat(100 / steps) * current;
            percent = percent.toFixed();
            $(".progress-bar")
                .css("width", "100%")
        }

        $(".submit").click(function() {
            return false;
        })
    });

    $("#FileInput1").on('change', function(e) {
        var labelVal = $(".title1").text();
        var oldfileName = $(this).val();
        fileName = e.target.value.split('\\').pop();

        if (oldfileName == fileName) {
            return false;
        }
        var extension = fileName.split('.').pop();

        if ($.inArray(extension, ['jpg', 'jpeg', 'png']) >= 0) {
            $(".filelabel1 i").removeClass().addClass('fa fa-file-image-o');
            $(".filelabel1 i, .filelabel1 .title1").css({
                'color': '#208440'
            });
            $(".filelabel1").css({
                'border': ' 2px solid #208440'
            });
        } else if (extension == 'pdf') {
            $(".filelabel1 i").removeClass().addClass('fa fa-file-pdf-o');
            $(".filelabel1 i, .filelabel1 .title1").css({
                'color': 'red'
            });
            $(".filelabel1").css({
                'border': ' 2px solid red'
            });

        } else if (extension == 'doc' || extension == 'docx') {
            $(".filelabel1 i").removeClass().addClass('fa fa-file-word-o');
            $(".filelabel1 i, .filelabel1 .title1").css({
                'color': '#2388df'
            });
            $(".filelabel1").css({
                'border': ' 2px solid #2388df'
            });
        } else {
            $(".filelabel1 i").removeClass().addClass('fa fa-file-o');
            $(".filelabel1 i, .filelabel1 .title1").css({
                'color': 'black'
            });
            $(".filelabel1").css({
                'border': ' 2px solid black'
            });
        }

        if (fileName) {
            if (fileName.length > 10) {
                $(".filelabel1 .title1").text(fileName.slice(0, 4) + '...' + extension);
            } else {
                $(".filelabel1 .title1").text(fileName);
            }
        } else {
            $(".filelabel1 .title1").text(labelVal);
        }
    });

    $("#FileInput2").on('change', function(e) {
        var labelVal = $(".title2").text();
        var oldfileName = $(this).val();
        fileName = e.target.value.split('\\').pop();

        if (oldfileName == fileName) {
            return false;
        }
        var extension = fileName.split('.').pop();

        if ($.inArray(extension, ['jpg', 'jpeg', 'png']) >= 0) {
            $(".filelabel2 i").removeClass().addClass('fa fa-file-image-o');
            $(".filelabel2 i, .filelabel2 .title2").css({
                'color': '#208440'
            });
            $(".filelabel2").css({
                'border': ' 2px solid #208440'
            });
        } else if (extension == 'pdf') {
            $(".filelabel2 i").removeClass().addClass('fa fa-file-pdf-o');
            $(".filelabel2 i, .filelabel2 .title2").css({
                'color': 'red'
            });
            $(".filelabel2").css({
                'border': ' 2px solid red'
            });

        } else if (extension == 'doc' || extension == 'docx') {
            $(".filelabel2 i").removeClass().addClass('fa fa-file-word-o');
            $(".filelabel2 i, .filelabel2 .title2").css({
                'color': '#2388df'
            });
            $(".filelabel2").css({
                'border': ' 2px solid #2388df'
            });
        } else {
            $(".filelabel2 i").removeClass().addClass('fa fa-file-o');
            $(".filelabel2 i, .filelabel2 .title2").css({
                'color': 'black'
            });
            $(".filelabel2").css({
                'border': ' 2px solid black'
            });
        }

        if (fileName) {
            if (fileName.length > 10) {
                $(".filelabel2 .title2").text(fileName.slice(0, 4) + '...' + extension);
            } else {
                $(".filelabel2 .title2").text(fileName);
            }
        } else {
            $(".filelabel2 .title2").text(labelVal);
        }
    });

    $("#FileInput3").on('change', function(e) {
        var labelVal = $(".title3").text();
        var oldfileName = $(this).val();
        fileName = e.target.value.split('\\').pop();

        if (oldfileName == fileName) {
            return false;
        }
        var extension = fileName.split('.').pop();

        if ($.inArray(extension, ['jpg', 'jpeg', 'png']) >= 0) {
            $(".filelabel3 i").removeClass().addClass('fa fa-file-image-o');
            $(".filelabel3 i, .filelabel3 .title3").css({
                'color': '#208440'
            });
            $(".filelabel3").css({
                'border': ' 2px solid #208440'
            });
        } else if (extension == 'pdf') {
            $(".filelabel3 i").removeClass().addClass('fa fa-file-pdf-o');
            $(".filelabel3 i, .filelabel3 .title3").css({
                'color': 'red'
            });
            $(".filelabel3").css({
                'border': ' 2px solid red'
            });

        } else if (extension == 'doc' || extension == 'docx') {
            $(".filelabel3 i").removeClass().addClass('fa fa-file-word-o');
            $(".filelabel3 i, .filelabel3 .title3").css({
                'color': '#2388df'
            });
            $(".filelabel3").css({
                'border': ' 2px solid #2388df'
            });
        } else {
            $(".filelabel3 i").removeClass().addClass('fa fa-file-o');
            $(".filelabel3 i, .filelabel3 .title3").css({
                'color': 'black'
            });
            $(".filelabel3").css({
                'border': ' 2px solid black'
            });
        }

        if (fileName) {
            if (fileName.length > 10) {
                $(".filelabel3 .title3").text(fileName.slice(0, 4) + '...' + extension);
            } else {
                $(".filelabel3 .title3").text(fileName);
            }
        } else {
            $(".filelabel3 .title3").text(labelVal);
        }
    });

    $('#imageInput').on('change', function() {
        $input = $(this);
        if ($input.val().length > 0) {
            fileReader = new FileReader();
            fileReader.onload = function(data) {
                $('.image-preview').attr('src', data.target.result);
            }
            fileReader.readAsDataURL($input.prop('files')[0]);
            $('.filelabel').css('display', 'none');
            $('.image-preview').css('display', 'block');
            $('.change-image').css('display', 'block');
        }
    });

    $('.change-image').on('click', function() {
        $control = $(this);
        $('#imageInput').val('');
        $preview = $('.image-preview');
        $preview.attr('src', '');
        $preview.css('display', 'none');
        $control.css('display', 'none');
        $('.filelabel').css('display', 'block');
    });
</script>
<script src="https://cdn.jsdelivr.net/npm/tinymce@6.8.3/tinymce.min.js" referrerpolicy="origin"></script>
<script>
  // Inicializa o TinyMCE
  tinymce.init({
    selector: '#exampleFormControlTextarea1',
    plugins: 'lists link image code table',
    toolbar: 'undo redo | bold italic underline | alignleft aligncenter alignright alignjustify | bullist numlist | link image | code',
    menubar: false,
    height: 300
  });

  // 🔥 Validação do conteúdo TinyMCE antes do submit
  $('form').on('submit', function(e) {
    tinymce.triggerSave(); // 🔸 Isso garante que o conteúdo vá para o textarea
    const content = tinymce.get('exampleFormControlTextarea1').getContent({format: 'text'}).trim();
    if (content === '') {
      alert('O conteúdo da aula é obrigatório.');
      e.preventDefault();
    }
  });
</script>

<?= $v->end() ?>