<?php
$v->layout("_theme");

require __DIR__ . "/categorias.php";

$imagemAtual = $curso->imagem ? url("uploads/cursos/" . $curso->imagem) : "";
?>

<div class="content-wrapper win-page">
    <section class="content">
        <div class="container-fluid">
            <header class="win-page-header">
                <div class="win-page-title">
                    <small>Gestão de conteúdo</small>
                    <h1>Editar curso</h1>
                </div>
                <div class="win-page-actions">
                    <a href="<?= url("cursos/detalhe/" . $curso->id) ?>" class="btn-win btn-win-ghost">
                        <i class="fas fa-arrow-left"></i>
                        Voltar para o curso
                    </a>
                </div>
            </header>

            <?php $v->insert("cursos/menu", ['curso' => $curso]); ?>

            <section class="win-section">
                <form action="<?= $router->route("appcursos.editarcadastro"); ?>" id="form" method="post" enctype="multipart/form-data" class="win-form">
                    <div class="win-form-grid">
                        <div class="form-group">
                            <label for="nome">Nome do curso *</label>
                            <input type="text" class="form-control" id="nome" name="nome" placeholder="Nome" required value="<?= $curso->nome; ?>">
                        </div>

                        <?php if ($produtos) : ?>
                            <div class="form-group">
                                <label for="produto">Produto associado *</label>
                                <select id="produto" class="form-control" name="produto" required>
                                    <?php foreach ($produtos as $produto) : ?>
                                        <option value="<?= $produto->id; ?>" <?= ($curso->produto == $produto->id) ? 'selected' : '' ?>><?= $produto->nome; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        <?php endif; ?>

                        <div class="form-group">
                            <label for="assunto">Categoria *</label>
                            <select id="assunto" class="form-control" name="assunto" required>
                                <option value="" disabled>Selecione...</option>
                                <?php foreach ($categorias as $id => $categoria) : ?>
                                    <option value="<?= $id; ?>" <?= ($curso->assunto == (int)$id) ? 'selected' : '' ?>><?= $categoria; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="formato">Formato *</label>
                            <select id="formato" class="form-control" name="formato" required>
                                <option value="" disabled>Selecione...</option>
                                <?php foreach ($formato as $id => $label) : ?>
                                    <option value="<?= $id; ?>" <?= ($curso->formato == (int)$id) ? 'selected' : '' ?>><?= $label; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="liberacao">Liberação dos módulos</label>
                            <select name="liberacao" id="liberacao" class="form-control">
                                <option value="">Selecionar...</option>
                                <option value="1" <?= ($curso->liberacao == 1) ? 'selected' : '' ?>>Liberar imediatamente</option>
                                <option value="3" <?= ($curso->liberacao == 3) ? 'selected' : '' ?>>Segundo módulo depois de 7 dias</option>
                            </select>
                        </div>
                    </div>

                    <div class="win-divider"></div>

                    <div class="win-form-grid win-form-grid-2">
                        <div class="form-group">
                            <label for="imageInput">Imagem do curso</label>
                            <small class="win-form-hint" style="display:block;margin-top:0.35rem;color:var(--wh-text-soft);">Ideal: <strong>900×1600px</strong> (proporção 9:16, portrait) — usada nos cards de módulos e aulas.</small>
                            <div class="attachment">
                                <label class="filelabel image-filelabel" for="imageInput" data-default="<?= $curso->imagem ?: 'Escolher imagem'; ?>">
                                    <i class="fa fa-image" style="<?= $curso->imagem ? 'color: var(--wh-accent);' : '' ?>"></i>
                                    <span class="title1" style="<?= $curso->imagem ? 'color: var(--wh-accent);' : '' ?>"><?= $curso->imagem ?: 'Escolher imagem'; ?></span>
                                </label>
                                <input type="file" accept="image/*" id="imageInput" class="FileUpload1" name="imagem">
                                <img src="<?= $imagemAtual; ?>" alt="Pré-visualização" class="image-preview" style="<?= $imagemAtual ? '' : 'display:none;' ?> border-radius: var(--wh-radius-sm); width: 140px; height: 140px; object-fit: cover;">
                                <span class="change-image" style="<?= $imagemAtual ? '' : 'display:none;' ?> font-size: 0.8rem; color: var(--wh-text-soft); cursor: pointer;">Remover imagem</span>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="descricao">Descrição do curso</label>
                            <textarea class="form-control" id="descricao" name="descricao" rows="8" required><?= $curso->descricao; ?></textarea>
                        </div>
                    </div>

                    <div class="win-divider"></div>

                    <!-- PERSONALIZAÇÃO DA ÁREA DE MEMBROS -->
                    <div class="win-section-header" style="margin-bottom:1rem;">
                        <h2 style="font-size:1rem;">Personalização da área de membros</h2>
                        <span class="win-section-subtitle">Customize o que o aluno vê ao acessar o curso.</span>
                    </div>

                    <div class="win-form-grid win-form-grid-2">
                        <div class="form-group">
                            <label for="bannerInput">Banner da área de membros</label>
                            <small class="win-form-hint" style="display:block;margin-top:0.35rem;color:var(--wh-text-soft);">Ideal: <strong>1920×480px</strong> (proporção ~4:1, landscape) — exibido no topo da área de membros e em Meus Cursos.</small>
                            <?php $bannerAtual = !empty($curso->banner) ? url("uploads/cursos/" . $curso->banner) : ""; ?>
                            <div class="attachment">
                                <label class="filelabel banner-filelabel" for="bannerInput" data-default="<?= $curso->banner ?: 'Escolher banner'; ?>">
                                    <i class="fa fa-panorama" style="<?= $curso->banner ? 'color: var(--wh-accent);' : '' ?>"></i>
                                    <span class="title-banner" style="<?= $curso->banner ? 'color: var(--wh-accent);' : '' ?>"><?= $curso->banner ?: 'Escolher banner'; ?></span>
                                </label>
                                <input type="file" accept="image/*" id="bannerInput" class="FileUpload1" name="banner">
                                <img src="<?= $bannerAtual; ?>" alt="Pré-visualização banner" class="banner-preview" style="<?= $bannerAtual ? '' : 'display:none;' ?> border-radius: var(--wh-radius-sm); width: 100%; max-height: 100px; object-fit: cover; margin-top:0.5rem;">
                                <span class="change-banner" style="<?= $bannerAtual ? '' : 'display:none;' ?> font-size: 0.8rem; color: var(--wh-text-soft); cursor: pointer;">Remover banner</span>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="banner_titulo">Título do banner</label>
                            <small class="win-form-hint" style="display:block;margin-top:0.35rem;color:var(--wh-text-soft);">Título em destaque exibido sobre o banner. Deixe vazio para não exibir.</small>
                            <input type="text" class="form-control" id="banner_titulo" name="banner_titulo" placeholder="Ex.: Área de Membros Exclusiva" value="<?= htmlspecialchars($curso->banner_titulo ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                        </div>

                        <div class="form-group">
                            <label for="boas_vindas">Texto do banner</label>
                            <small class="win-form-hint" style="display:block;margin-top:0.35rem;color:var(--wh-text-soft);">Parágrafo exibido abaixo do título no banner. Deixe vazio para não exibir.</small>
                            <textarea class="form-control" id="boas_vindas" name="boas_vindas" rows="5" placeholder="Ex.: Olá! Seja bem-vindo ao curso. Aproveite cada módulo ao máximo."><?= htmlspecialchars($curso->boas_vindas ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>
                        </div>

                        <div class="form-group">
                            <label>Posição da imagem no banner</label>
                            <small class="win-form-hint" style="display:block;margin-top:.35rem;color:var(--wh-text-soft);">Clique no ponto que define qual parte da imagem fica em destaque.</small>

                            <?php $posAtual = $curso->banner_posicao ?? 'center center'; ?>
                            <input type="hidden" name="banner_posicao" id="bannerPosicao" value="<?= htmlspecialchars($posAtual, ENT_QUOTES) ?>">

                            <div style="display:flex;gap:1.5rem;align-items:flex-start;margin-top:.75rem;flex-wrap:wrap;">
                                <!-- Grid 3×3 -->
                                <div id="posGrid" style="
                                    display:grid;grid-template-columns:repeat(3,1fr);
                                    gap:5px;width:108px;flex-shrink:0;
                                ">
                                <?php
                                $positions = [
                                    ['top left',    'top center',    'top right'],
                                    ['center left', 'center center', 'center right'],
                                    ['bottom left', 'bottom center', 'bottom right'],
                                ];
                                foreach ($positions as $row): foreach ($row as $pos): ?>
                                    <button type="button"
                                        data-pos="<?= $pos ?>"
                                        class="pos-btn <?= $posAtual === $pos ? 'active' : '' ?>"
                                        title="<?= $pos ?>"
                                        style="
                                            width:32px;height:32px;border-radius:6px;
                                            border:2px solid rgba(255,255,255,.12);
                                            background:rgba(255,255,255,.05);
                                            display:flex;align-items:center;justify-content:center;
                                            cursor:pointer;transition:all .15s;
                                        ">
                                        <span style="width:8px;height:8px;border-radius:50%;background:rgba(255,255,255,.3);display:block;"></span>
                                    </button>
                                <?php endforeach; endforeach; ?>
                                </div>

                                <!-- Preview ao vivo -->
                                <div style="flex:1;min-width:180px;">
                                    <div id="bannerPosPreview" style="
                                        width:100%;height:90px;border-radius:8px;
                                        overflow:hidden;position:relative;
                                        background:#111;
                                        border:1px solid rgba(255,255,255,.1);
                                    ">
                                        <img id="bannerPosImg"
                                             src="<?= $bannerAtual ?>"
                                             alt=""
                                             style="
                                                width:100%;height:100%;
                                                object-fit:cover;
                                                object-position:<?= $posAtual ?>;
                                                display:<?= $bannerAtual ? 'block' : 'none' ?>;
                                             ">
                                        <div id="bannerPosEmpty" style="
                                            position:absolute;inset:0;
                                            display:<?= $bannerAtual ? 'none' : 'flex' ?>;
                                            align-items:center;justify-content:center;
                                            font-size:.7rem;color:rgba(255,255,255,.3);
                                        ">Faça upload do banner para visualizar</div>
                                    </div>
                                    <div id="bannerPosLabel" style="font-size:.68rem;color:rgba(255,255,255,.4);margin-top:.4rem;">
                                        Posição: <strong style="color:var(--wh-accent)"><?= $posAtual ?></strong>
                                    </div>
                                </div>
                            </div>

                            <style>
                            .pos-btn.active {
                                border-color: var(--wh-accent) !important;
                                background: rgba(244,208,63,.15) !important;
                            }
                            .pos-btn.active span { background: var(--wh-accent) !important; }
                            .pos-btn:hover { border-color: rgba(255,255,255,.4) !important; }
                            </style>
                        </div>
                    </div>

                    <!-- Título nas aulas -->
                    <div class="win-divider"></div>
                    <div class="win-section-header" style="margin-bottom:.8rem;">
                        <div>
                            <h2 style="font-size:1rem;">Exibição dos cards de aula</h2>
                            <span class="win-section-subtitle">Controla o que o aluno vê nos cards dentro do curso.</span>
                        </div>
                    </div>
                    <div class="form-group" style="display:flex;align-items:center;gap:1rem;">
                        <label class="win-switch" style="margin:0;cursor:pointer;display:flex;align-items:center;gap:.75rem;">
                            <input type="checkbox" name="mostrar_titulo_aulas" value="1" id="mostrar_titulo_aulas"
                                   <?= ($curso->mostrar_titulo_aulas ?? 1) ? 'checked' : '' ?>
                                   style="display:none;">
                            <span class="win-switch-track"></span>
                            <span style="font-size:.82rem;letter-spacing:.1em;text-transform:uppercase;color:rgba(148,163,184,.8);">Mostrar título das aulas nos cards</span>
                        </label>
                    </div>

                    <!-- TEMA VISUAL DA PÁGINA DE MÓDULOS -->
                    <div class="win-divider"></div>
                    <div class="win-section-header" style="margin-bottom:1rem;">
                        <div>
                            <h2 style="font-size:1rem;">Tema visual da página de módulos</h2>
                            <span class="win-section-subtitle">Define cores, logo e título da página que o aluno vê ao entrar no curso.</span>
                        </div>
                    </div>

                    <div class="win-form-grid win-form-grid-2" style="margin-bottom:1rem;">
                        <!-- Logo / Imagem da marca -->
                        <div class="form-group">
                            <label for="temaLogoInput">Logo / imagem da marca</label>
                            <small class="win-form-hint" style="display:block;margin-top:.35rem;color:var(--wh-text-soft);">Exibida em destaque no topo da página. Recomendado: quadrada, mín. 200×200px.</small>
                            <?php $temaLogoAtual = !empty($curso->tema_logo) ? url("uploads/cursos/" . $curso->tema_logo) : ""; ?>
                            <div class="attachment">
                                <label class="filelabel tema-logo-filelabel" for="temaLogoInput" data-default="<?= $curso->tema_logo ?: 'Escolher logo'; ?>">
                                    <i class="fa fa-image" style="<?= $curso->tema_logo ? 'color: var(--wh-accent);' : '' ?>"></i>
                                    <span class="title-tema-logo" style="<?= $curso->tema_logo ? 'color: var(--wh-accent);' : '' ?>"><?= $curso->tema_logo ?: 'Escolher logo'; ?></span>
                                </label>
                                <input type="file" accept="image/*" id="temaLogoInput" class="FileUpload1" name="tema_logo">
                                <img src="<?= $temaLogoAtual; ?>" alt="Logo atual" class="tema-logo-preview" style="<?= $temaLogoAtual ? '' : 'display:none;' ?> border-radius: 50%; width: 80px; height: 80px; object-fit: cover; margin-top:.5rem;">
                                <span class="change-tema-logo" style="<?= $temaLogoAtual ? '' : 'display:none;' ?> font-size:.8rem; color:var(--wh-text-soft); cursor:pointer;">Remover logo</span>
                            </div>
                        </div>

                        <!-- Título alternativo -->
                        <div class="form-group">
                            <label for="tema_titulo_exibicao">Título alternativo na página de módulos</label>
                            <small class="win-form-hint" style="display:block;margin-top:.35rem;color:var(--wh-text-soft);">Deixe vazio para usar o nome do curso.</small>
                            <input type="text" class="form-control" id="tema_titulo_exibicao" name="tema_titulo_exibicao"
                                   placeholder="Ex.: Academia do Tráfego"
                                   value="<?= htmlspecialchars($curso->tema_titulo_exibicao ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                        </div>
                    </div>

                    <!-- Cores -->
                    <div class="win-form-grid" style="grid-template-columns:repeat(3,1fr);gap:1rem;margin-bottom:1rem;">
                        <div class="form-group">
                            <label for="tema_cor_primaria">Cor de destaque</label>
                            <small class="win-form-hint" style="display:block;margin-top:.3rem;color:var(--wh-text-soft);">Badges, barras de progresso, ícones.</small>
                            <div style="display:flex;align-items:center;gap:.6rem;margin-top:.5rem;">
                                <input type="color" id="tema_cor_primaria" name="tema_cor_primaria"
                                       value="<?= htmlspecialchars($curso->tema_cor_primaria ?? '#f4d03f', ENT_QUOTES, 'UTF-8') ?>"
                                       style="width:44px;height:44px;border-radius:8px;border:1px solid rgba(255,255,255,.15);cursor:pointer;padding:2px;background:transparent;">
                                <input type="text" name="tema_cor_primaria_hex"
                                       value="<?= htmlspecialchars($curso->tema_cor_primaria ?? '#f4d03f', ENT_QUOTES, 'UTF-8') ?>"
                                       class="form-control" style="flex:1;font-family:monospace;font-size:.82rem;"
                                       maxlength="7" placeholder="#f4d03f">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="tema_cor_fundo">Cor de fundo</label>
                            <small class="win-form-hint" style="display:block;margin-top:.3rem;color:var(--wh-text-soft);">Fundo geral da página.</small>
                            <div style="display:flex;align-items:center;gap:.6rem;margin-top:.5rem;">
                                <input type="color" id="tema_cor_fundo" name="tema_cor_fundo"
                                       value="<?= htmlspecialchars($curso->tema_cor_fundo ?? '#0f1118', ENT_QUOTES, 'UTF-8') ?>"
                                       style="width:44px;height:44px;border-radius:8px;border:1px solid rgba(255,255,255,.15);cursor:pointer;padding:2px;background:transparent;">
                                <input type="text" name="tema_cor_fundo_hex"
                                       value="<?= htmlspecialchars($curso->tema_cor_fundo ?? '#0f1118', ENT_QUOTES, 'UTF-8') ?>"
                                       class="form-control" style="flex:1;font-family:monospace;font-size:.82rem;"
                                       maxlength="7" placeholder="#0f1118">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="tema_cor_texto">Cor do texto</label>
                            <small class="win-form-hint" style="display:block;margin-top:.3rem;color:var(--wh-text-soft);">Texto principal dos cards.</small>
                            <div style="display:flex;align-items:center;gap:.6rem;margin-top:.5rem;">
                                <input type="color" id="tema_cor_texto" name="tema_cor_texto"
                                       value="<?= htmlspecialchars($curso->tema_cor_texto ?? '#ffffff', ENT_QUOTES, 'UTF-8') ?>"
                                       style="width:44px;height:44px;border-radius:8px;border:1px solid rgba(255,255,255,.15);cursor:pointer;padding:2px;background:transparent;">
                                <input type="text" name="tema_cor_texto_hex"
                                       value="<?= htmlspecialchars($curso->tema_cor_texto ?? '#ffffff', ENT_QUOTES, 'UTF-8') ?>"
                                       class="form-control" style="flex:1;font-family:monospace;font-size:.82rem;"
                                       maxlength="7" placeholder="#ffffff">
                            </div>
                        </div>
                    </div>

                    <!-- Preview live do tema -->
                    <div id="tema-preview-wrap" style="margin-bottom:1.5rem;">
                        <label style="font-size:.78rem;letter-spacing:.08em;text-transform:uppercase;color:rgba(148,163,184,.7);margin-bottom:.5rem;display:block;">Pré-visualização</label>
                        <div id="tema-preview" style="
                            border-radius:12px; padding:1.2rem 1.4rem;
                            display:flex; align-items:center; gap:1rem;
                            border:1px solid rgba(255,255,255,.1);
                            transition:background .3s;
                        ">
                            <div id="tp-logo" style="width:48px;height:48px;border-radius:50%;background:rgba(255,255,255,.1);display:flex;align-items:center;justify-content:center;font-size:1.4rem;flex-shrink:0;">
                                🎓
                            </div>
                            <div>
                                <div id="tp-title" style="font-size:1rem;font-weight:700;margin-bottom:.25rem;">Nome do curso</div>
                                <div style="display:flex;gap:.5rem;align-items:center;">
                                    <div id="tp-badge" style="font-size:.6rem;font-weight:800;padding:.15rem .5rem;border-radius:999px;text-transform:uppercase;letter-spacing:.08em;color:#000;">Módulo 1</div>
                                    <div id="tp-bar-bg" style="flex:1;height:4px;border-radius:999px;background:rgba(255,255,255,.1);overflow:hidden;min-width:80px;">
                                        <div id="tp-bar" style="height:100%;border-radius:999px;width:40%;transition:background .3s;"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Exibir título -->
                    <div class="form-group" style="display:flex;align-items:center;gap:1rem;">
                        <label class="win-switch" style="margin:0;cursor:pointer;display:flex;align-items:center;gap:.75rem;">
                            <input type="checkbox" name="tema_exibir_titulo" value="1" id="tema_exibir_titulo"
                                   <?= ($curso->tema_exibir_titulo ?? 1) ? 'checked' : '' ?>
                                   style="display:none;">
                            <span class="win-switch-track"></span>
                            <span style="font-size:.82rem;letter-spacing:.1em;text-transform:uppercase;color:rgba(148,163,184,.8);">Exibir título do curso na página de módulos</span>
                        </label>
                    </div>

                    <input type="hidden" name="tema_logo_atual" value="<?= htmlspecialchars($curso->tema_logo ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                    <input type="hidden" name="course_id" value="<?= $curso->id; ?>">
                    <input type="hidden" name="course_img" value="<?= $curso->imagem; ?>">
                    <input type="hidden" name="banner_atual" value="<?= htmlspecialchars($curso->banner ?? '', ENT_QUOTES, 'UTF-8'); ?>">

                    <div class="win-form-actions">
                        <a href="<?= url("cursos/detalhe/" . $curso->id) ?>" class="btn-win btn-win-ghost">Cancelar</a>
                        <button type="submit" name="next-step" class="btn-win">
                            Salvar alterações
                            <i class="fas fa-save"></i>
                        </button>
                    </div>
                </form>
            </section>
        </div>
    </section>
</div>

<?= $v->start("links") ?>
<style>
.win-switch-track {
    display: inline-block;
    width: 44px; height: 24px;
    background: rgba(148,163,184,.2);
    border-radius: 999px;
    border: 1px solid rgba(255,255,255,.1);
    position: relative;
    transition: background .25s ease, border-color .25s ease;
    flex-shrink: 0;
}
.win-switch-track::after {
    content: '';
    position: absolute;
    top: 2px; left: 2px;
    width: 18px; height: 18px;
    border-radius: 50%;
    background: rgba(148,163,184,.6);
    transition: transform .25s ease, background .25s ease;
}
input:checked + .win-switch-track {
    background: rgba(244,208,63,.25);
    border-color: rgba(244,208,63,.4);
}
input:checked + .win-switch-track::after {
    transform: translateX(20px);
    background: #f4d03f;
}
</style>
<?= $v->end() ?>

<?= $v->start("scripts") ?>
<script>
    $(function() {
        $('#imageInput').on('change', function() {
            const input = this;
            const $label = $('.image-filelabel');
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    $('.image-preview').attr('src', e.target.result).show();
                    $('.change-image').show();
                };
                reader.readAsDataURL(input.files[0]);
                $label.find('.title1').text(input.files[0].name);
                $label.css('border-color', 'var(--wh-accent)');
                $label.find('i').css('color', 'var(--wh-accent)');
            }
        });

        $('.change-image').on('click', function() {
            $('#imageInput').val('');
            $('.image-preview').attr('src', '').hide();
            $(this).hide();
            const $label = $('.image-filelabel');
            const defaultText = $label.data('default') || 'Escolher imagem';
            $label.find('.title1').text(defaultText);
            $label.css('border-color', '');
            $label.find('i').css('color', '');
        });

        $('#bannerInput').on('change', function() {
            if (this.files && this.files[0]) {
                const reader = new FileReader();
                reader.onload = e => {
                    $('.banner-preview').attr('src', e.target.result).show();
                    $('.change-banner').show();
                    // Atualiza preview de posição também
                    $('#bannerPosImg').attr('src', e.target.result).show();
                    $('#bannerPosEmpty').hide();
                };
                reader.readAsDataURL(this.files[0]);
                $('.banner-filelabel .title-banner').text(this.files[0].name).css('color', 'var(--wh-accent)');
                $('.banner-filelabel i').css('color', 'var(--wh-accent)');
            }
        });

        $('.change-banner').on('click', function() {
            $('#bannerInput').val('');
            $('input[name=banner_atual]').val('');
            $('.banner-preview').attr('src', '').hide();
            $(this).hide();
            $('.banner-filelabel .title-banner').text('Escolher banner').css('color', '');
            $('.banner-filelabel i').css('color', '');
            $('#bannerPosImg').hide();
            $('#bannerPosEmpty').show();
        });

        // Grid de posição do banner
        $('#posGrid .pos-btn').on('click', function() {
            const pos = $(this).data('pos');
            $('#posGrid .pos-btn').removeClass('active');
            $(this).addClass('active');
            $('#bannerPosicao').val(pos);
            $('#bannerPosImg').css('object-position', pos);
            $('#bannerPosLabel strong').text(pos);
        });

        // Logo do tema
        $('#temaLogoInput').on('change', function() {
            if (this.files && this.files[0]) {
                const reader = new FileReader();
                reader.onload = e => {
                    $('.tema-logo-preview').attr('src', e.target.result).show();
                    $('.change-tema-logo').show();
                    $('#tp-logo').html('<img src="'+e.target.result+'" style="width:100%;height:100%;object-fit:cover;border-radius:50%;">');
                };
                reader.readAsDataURL(this.files[0]);
                $('.tema-logo-filelabel .title-tema-logo').text(this.files[0].name).css('color', 'var(--wh-accent)');
                $('.tema-logo-filelabel i').css('color', 'var(--wh-accent)');
            }
        });

        $('.change-tema-logo').on('click', function() {
            $('#temaLogoInput').val('');
            $('input[name=tema_logo_atual]').val('');
            $('.tema-logo-preview').attr('src', '').hide();
            $(this).hide();
            $('.tema-logo-filelabel .title-tema-logo').text('Escolher logo').css('color', '');
            $('.tema-logo-filelabel i').css('color', '');
            $('#tp-logo').html('🎓');
        });

        // Sincroniza color picker <-> input texto e atualiza preview
        function syncColor(pickerId, hexName) {
            const picker = $('#' + pickerId);
            const hex    = $('input[name="' + hexName + '"]');
            picker.on('input change', function() {
                hex.val(this.value);
                updatePreview();
            });
            hex.on('input', function() {
                if (/^#[0-9a-fA-F]{6}$/.test(this.value)) {
                    picker.val(this.value);
                    updatePreview();
                }
            });
        }

        syncColor('tema_cor_primaria', 'tema_cor_primaria_hex');
        syncColor('tema_cor_fundo',    'tema_cor_fundo_hex');
        syncColor('tema_cor_texto',    'tema_cor_texto_hex');

        // Antes de enviar o form, copia os hex para os campos reais
        $('#form').on('submit', function() {
            $('#tema_cor_primaria').val($('input[name="tema_cor_primaria_hex"]').val());
            $('#tema_cor_fundo').val($('input[name="tema_cor_fundo_hex"]').val());
            $('#tema_cor_texto').val($('input[name="tema_cor_texto_hex"]').val());
        });

        function updatePreview() {
            const bg      = $('input[name="tema_cor_fundo_hex"]').val()    || '#0f1118';
            const accent  = $('input[name="tema_cor_primaria_hex"]').val() || '#f4d03f';
            const texto   = $('input[name="tema_cor_texto_hex"]').val()    || '#ffffff';
            $('#tema-preview').css('background', bg);
            $('#tp-title').css('color', texto);
            $('#tp-badge').css('background', accent);
            $('#tp-bar').css('background', accent);
        }
        updatePreview();

        // Título alternativo reflete no preview
        $('#tema_titulo_exibicao').on('input', function() {
            const v = $(this).val() || $('#nome').val() || 'Nome do curso';
            $('#tp-title').text(v);
        });
        $('#nome').on('input', function() {
            if (!$('#tema_titulo_exibicao').val()) $('#tp-title').text($(this).val() || 'Nome do curso');
        });
    });
</script>
<?= $v->end() ?>
