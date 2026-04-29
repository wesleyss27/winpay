<?php
$v->layout("_theme");

if ($_SESSION["tipo"] == 6) {
    header("Location: " . url("vendas"));
}
?>

<div class="content-wrapper win-page">
    <section class="content">
        <div class="container-fluid">
            <header class="win-page-header">
                <div class="win-page-title">
                    <small>Gestão de conteúdo</small>
                    <h1>Ofertas</h1>
                </div>
            </header>

            <?php $v->insert("cursos/menu", ['curso' => $curso]); ?>

            <?php if (!empty($_GET["msg"])) : ?>
                <div class="alert alert-warning"><?= htmlspecialchars($_GET["msg"], ENT_QUOTES, 'UTF-8'); ?></div>
            <?php endif; ?>

            <!-- FORMULÁRIO DE CADASTRO -->
            <section class="win-section">
                <div class="win-section-header">
                    <h2>Nova oferta</h2>
                    <span class="win-section-subtitle">
                        Escolha um produto seu para exibir durante as aulas deste curso.
                    </span>
                </div>

                <form id="form-oferta" action="<?= $router->route("ofertas.cadastro"); ?>" method="post" enctype="multipart/form-data" class="win-form">
                    <input type="hidden" name="oferta_id"    id="oferta_id"    value="">
                    <input type="hidden" name="curso_id"     value="<?= $curso->id; ?>">
                    <input type="hidden" name="imagem_atual" id="imagem_atual" value="">

                    <div class="win-form-grid">

                        <!-- SELECT PRODUTO -->
                        <div class="form-group">
                            <label for="sel-produto">Produto</label>
                            <select id="sel-produto" class="form-control" required>
                                <option value="">Selecione um produto...</option>
                                <?php foreach ($produtos as $p) : ?>
                                    <option value="<?= $p->id; ?>"><?= htmlspecialchars($p->nome, ENT_QUOTES, 'UTF-8'); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- SELECT PLANO -->
                        <div class="form-group">
                            <label for="sel-plano">Plano</label>
                            <select id="sel-plano" class="form-control" required disabled>
                                <option value="">Selecione um plano...</option>
                            </select>
                        </div>

                        <!-- SELECT CHECKOUT -->
                        <div class="form-group">
                            <label for="checkout_id">Checkout</label>
                            <select id="checkout_id" name="checkout_id" class="form-control" required disabled>
                                <option value="">Selecione um checkout...</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="nome">Título da oferta</label>
                            <input type="text" class="form-control" id="nome" name="nome" placeholder="Ex.: Aproveite 20% de desconto" required>
                        </div>

                        <div class="form-group">
                            <label for="valor">Valor de exibição (opcional)</label>
                            <input type="text" class="form-control" id="valor" name="valor" placeholder="Ex.: 97,00 — deixe vazio para não exibir preço">
                        </div>

                        <!-- OVERLAY NO PLAYER -->
                        <div class="form-group" style="grid-column: 1 / -1;">
                            <label style="font-size: 0.78rem; letter-spacing: 0.1em; text-transform: uppercase; color: var(--wh-text-soft);">
                                <i class="fas fa-play-circle" style="color: rgba(244,208,63,0.7); margin-right: 0.3rem;"></i>
                                Exibir como overlay no player (opcional)
                            </label>
                            <div style="display: grid; grid-template-columns: 1fr auto; gap: 0.75rem; align-items: end; margin-top: 0.4rem;">
                                <div class="form-group" style="margin: 0;">
                                    <label for="aula_id" style="font-size: 0.82rem;">Aula</label>
                                    <select id="aula_id" name="aula_id" class="form-control">
                                        <option value="">Não exibir no player</option>
                                        <?php foreach ($aulas as $a) : ?>
                                            <option value="<?= $a->id; ?>"><?= htmlspecialchars($a->nome, ENT_QUOTES, 'UTF-8'); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="form-group" style="margin: 0; min-width: 120px;">
                                    <label for="exibir_em" style="font-size: 0.82rem;">Momento (MM:SS)</label>
                                    <input type="text" class="form-control" id="exibir_em" name="exibir_em"
                                           placeholder="Ex.: 02:30"
                                           pattern="^\d{1,2}:\d{2}$"
                                           title="Formato MM:SS — ex.: 02:30">
                                </div>
                            </div>
                            <small style="color: var(--wh-text-soft); font-size: 0.78rem; margin-top: 0.3rem; display: block;">
                                Deixe em branco para exibir apenas na sidebar. Quando preenchido, um banner aparece sobre o vídeo no segundo configurado.
                            </small>
                        </div>
                    </div>

                    <div class="win-form-grid win-form-grid-2">
                        <div class="form-group">
                            <label>Imagem da oferta</label>
                            <div class="attachment">
                                <label class="filelabel image-filelabel" for="imageOfertaInput" data-default="Escolher imagem">
                                    <i class="fa fa-image"></i>
                                    <span class="title1">Escolher imagem</span>
                                </label>
                                <input type="file" accept="image/*" id="imageOfertaInput" class="FileUpload1" name="imagem">
                                <img src="" alt="" class="image-preview" style="display:none; border-radius: var(--wh-radius-sm); width: 120px; height: 120px; object-fit: cover; margin-top: 0.5rem;">
                                <span class="change-image" style="display:none; font-size: 0.8rem; color: var(--wh-text-soft); cursor: pointer; margin-top: 0.4rem;">Remover imagem</span>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="descricao">Descrição</label>
                            <textarea class="form-control" id="descricao" name="descricao" rows="5" placeholder="Descreva brevemente o que o aluno vai ganhar com esta oferta"></textarea>
                        </div>
                    </div>

                    <div class="win-form-actions">
                        <button type="button" id="btn-cancelar-edicao" class="btn-win btn-win-ghost" style="display:none;" onclick="cancelarEdicao()">Cancelar edição</button>
                        <button type="submit" id="btn-submit-oferta" class="btn-win">
                            Salvar oferta
                            <i class="fas fa-save"></i>
                        </button>
                    </div>
                </form>
            </section>

            <!-- LISTAGEM DE OFERTAS -->
            <section class="win-section">
                <div class="win-section-header">
                    <h2>Ofertas cadastradas</h2>
                </div>

                <?php if ($ofertas) : ?>
                    <div style="display:flex;flex-direction:column;gap:1rem;">
                        <?php foreach ($ofertas as $oferta) :
                            $imgSrc     = $oferta->imagem ? url("uploads/cursos/" . $oferta->imagem) : null;
                            $statusAtivo = $oferta->status == 1;
                            $info        = $oferta->checkout_info ?? null;
                        ?>
                            <div class="win-card" id="oferta-row-<?= $oferta->id; ?>" style="display:flex;align-items:center;gap:1rem;padding:1rem 1.2rem;">
                                <?php if ($imgSrc) : ?>
                                    <img src="<?= $imgSrc; ?>" alt="" style="width:72px;height:72px;object-fit:cover;border-radius:var(--wh-radius-sm);flex-shrink:0;">
                                <?php else : ?>
                                    <div style="width:72px;height:72px;background:rgba(244,208,63,0.08);border-radius:var(--wh-radius-sm);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                        <i class="fas fa-tag" style="color:rgba(244,208,63,0.4);font-size:1.4rem;"></i>
                                    </div>
                                <?php endif; ?>

                                <div style="flex:1;min-width:0;">
                                    <strong style="display:block;"><?= htmlspecialchars($oferta->nome, ENT_QUOTES, 'UTF-8'); ?></strong>
                                    <?php if ($info) : ?>
                                        <span style="font-size:0.8rem;color:var(--wh-text-soft);">
                                            <?= htmlspecialchars($info->produto_nome, ENT_QUOTES, 'UTF-8'); ?>
                                            › <?= htmlspecialchars($info->plano_nome, ENT_QUOTES, 'UTF-8'); ?>
                                            › Checkout <?= htmlspecialchars($info->codigo, ENT_QUOTES, 'UTF-8'); ?>
                                        </span>
                                    <?php endif; ?>
                                    <?php if ($oferta->valor) : ?>
                                        <span style="font-size:0.82rem;color:var(--wh-text-soft);display:block;">
                                            R$ <?= number_format((float)$oferta->valor, 2, ',', '.'); ?>
                                        </span>
                                    <?php endif; ?>
                                </div>

                                <div style="display:flex;align-items:center;gap:0.6rem;flex-shrink:0;">
                                    <span id="badge-status-<?= $oferta->id; ?>" class="win-pill"
                                          style="background:<?= $statusAtivo ? 'rgba(60,213,138,0.15)' : 'rgba(200,50,50,0.15)'; ?>;color:<?= $statusAtivo ? '#3cd58a' : '#e06060'; ?>;">
                                        <?= $statusAtivo ? 'Ativo' : 'Inativo'; ?>
                                    </span>
                                    <button type="button" class="btn btn-sm btn-outline-light" onclick="toggleStatus(<?= $oferta->id; ?>)">
                                        <i class="fas <?= $statusAtivo ? 'fa-eye-slash' : 'fa-eye'; ?>" id="icon-status-<?= $oferta->id; ?>"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-light"
                                            onclick="editarOferta(<?= $oferta->id; ?>, '<?= htmlspecialchars(addslashes($oferta->nome), ENT_QUOTES, 'UTF-8'); ?>', '<?= htmlspecialchars(addslashes($oferta->descricao ?? ''), ENT_QUOTES, 'UTF-8'); ?>', '<?= $oferta->valor ?? ''; ?>', <?= $oferta->checkout_id ?? 0; ?>, '<?= $oferta->imagem ?? ''; ?>', <?= $info->produto_id ?? 0; ?>, <?= $info->plano_id ?? 0; ?>, <?= $oferta->aula_id ?? 0; ?>, '<?= $oferta->exibir_em !== null ? gmdate('i:s', (int)$oferta->exibir_em) : ''; ?>')">
                                        <i class="far fa-edit"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-light" onclick="excluirOferta(<?= $oferta->id; ?>)">
                                        <i class="far fa-trash-alt"></i>
                                    </button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else : ?>
                    <div class="win-empty-state">
                        Nenhuma oferta cadastrada ainda. Crie uma acima para começar.
                    </div>
                <?php endif; ?>
            </section>
        </div>
    </section>
</div>

<?= $v->start("scripts") ?>
<script>
    const ROUTE_EDITAR          = "<?= $router->route("ofertas.editar"); ?>";
    const ROUTE_STATUS          = "<?= $router->route("ofertas.status"); ?>";
    const ROUTE_EXCLUIR         = "<?= $router->route("ofertas.excluir"); ?>";
    const ROUTE_PLANOS          = "<?= $router->route("ofertas.planos"); ?>";
    const ROUTE_CHECKOUTS       = "<?= $router->route("ofertas.checkouts"); ?>";

    // Cascata: produto → plano
    $('#sel-produto').on('change', function () {
        const produtoId = $(this).val();
        $('#sel-plano').prop('disabled', true).html('<option value="">Carregando...</option>');
        $('#checkout_id').prop('disabled', true).html('<option value="">Selecione um checkout...</option>');

        if (!produtoId) return;

        $.post(ROUTE_PLANOS, { produto_id: produtoId }, function (planos) {
            let opts = '<option value="">Selecione um plano...</option>';
            planos.forEach(p => opts += `<option value="${p.id}">${p.nome} — R$ ${parseFloat(p.preco).toFixed(2).replace('.', ',')}</option>`);
            $('#sel-plano').html(opts).prop('disabled', false);
        }, 'json');
    });

    // Cascata: plano → checkout
    $('#sel-plano').on('change', function () {
        const planoId = $(this).val();
        $('#checkout_id').prop('disabled', true).html('<option value="">Carregando...</option>');

        if (!planoId) return;

        $.post(ROUTE_CHECKOUTS, { plano_id: planoId }, function (checkouts) {
            let opts = '<option value="">Selecione um checkout...</option>';
            checkouts.forEach(c => opts += `<option value="${c.id}">${c.descricao ? c.descricao + ' — ' : ''}${c.codigo}</option>`);
            $('#checkout_id').html(opts).prop('disabled', false);
        }, 'json');
    });

    // Preview imagem
    $('#imageOfertaInput').on('change', function () {
        if (this.files && this.files[0]) {
            const reader = new FileReader();
            reader.onload = e => $('.image-preview').attr('src', e.target.result).show();
            reader.readAsDataURL(this.files[0]);
            $('.change-image').show();
        }
    });

    $('.change-image').on('click', function () {
        $('#imageOfertaInput').val('');
        $('.image-preview').attr('src', '').hide();
        $(this).hide();
    });

    // Editar oferta
    function editarOferta(id, nome, descricao, valor, checkoutId, imagem, produtoId, planoId, aulaId, exibirEm) {
        $('#oferta_id').val(id);
        $('#imagem_atual').val(imagem);
        $('#nome').val(nome);
        $('#descricao').val(descricao);
        $('#valor').val(valor);
        $('#aula_id').val(aulaId || '');
        $('#exibir_em').val(exibirEm || '');

        if (imagem) {
            $('.image-preview').attr('src', "<?= url('uploads/cursos/'); ?>" + imagem).show();
            $('.change-image').show();
        } else {
            $('.image-preview').attr('src', '').hide();
            $('.change-image').hide();
        }

        $('#form-oferta').attr('action', ROUTE_EDITAR);
        $('#btn-submit-oferta').html('Atualizar oferta <i class="fas fa-save"></i>');
        $('#btn-cancelar-edicao').show();

        // Remonta a cascata produto → plano → checkout
        if (produtoId) {
            $('#sel-produto').val(produtoId);
            $('#sel-plano').prop('disabled', true).html('<option value="">Carregando...</option>');
            $('#checkout_id').prop('disabled', true).html('<option value="">Carregando...</option>');

            $.post(ROUTE_PLANOS, { produto_id: produtoId }, function (planos) {
                let opts = '<option value="">Selecione um plano...</option>';
                planos.forEach(p => opts += `<option value="${p.id}">${p.nome} — R$ ${parseFloat(p.preco).toFixed(2).replace('.', ',')}</option>`);
                $('#sel-plano').html(opts).prop('disabled', false).val(planoId);

                if (planoId) {
                    $.post(ROUTE_CHECKOUTS, { plano_id: planoId }, function (checkouts) {
                        let copts = '<option value="">Selecione um checkout...</option>';
                        checkouts.forEach(c => copts += `<option value="${c.id}">${c.descricao ? c.descricao + ' — ' : ''}${c.codigo}</option>`);
                        $('#checkout_id').html(copts).prop('disabled', false).val(checkoutId);
                    }, 'json');
                }
            }, 'json');
        } else {
            $('#checkout_id').html(`<option value="${checkoutId}" selected>Checkout ${checkoutId}</option>`).prop('disabled', false);
        }

        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    function cancelarEdicao() {
        $('#oferta_id').val('');
        $('#imagem_atual').val('');
        $('#aula_id').val('');
        $('#exibir_em').val('');
        $('#form-oferta')[0].reset();
        $('#form-oferta').attr('action', "<?= $router->route("ofertas.cadastro"); ?>");
        $('#btn-submit-oferta').html('Salvar oferta <i class="fas fa-save"></i>');
        $('#btn-cancelar-edicao').hide();
        $('.image-preview').attr('src', '').hide();
        $('.change-image').hide();
        $('#sel-plano').prop('disabled', true).html('<option value="">Selecione um plano...</option>');
        $('#checkout_id').prop('disabled', true).html('<option value="">Selecione um checkout...</option>');
    }

    function toggleStatus(id) {
        $.post(ROUTE_STATUS, { id: id }, function (r) {
            const ativo = r == '1';
            $('#badge-status-' + id).text(ativo ? 'Ativo' : 'Inativo')
                .css({ background: ativo ? 'rgba(60,213,138,0.15)' : 'rgba(200,50,50,0.15)', color: ativo ? '#3cd58a' : '#e06060' });
            $('#icon-status-' + id).toggleClass('fa-eye-slash', ativo).toggleClass('fa-eye', !ativo);
        });
    }

    function excluirOferta(id) {
        if (!confirm('Deseja excluir esta oferta?')) return;
        $.post(ROUTE_EXCLUIR, { id: id }, function (r) {
            if (r == '1') $('#oferta-row-' + id).fadeOut(300, function () { $(this).remove(); });
            else alert('Erro ao excluir.');
        });
    }
</script>
<?= $v->end() ?>
