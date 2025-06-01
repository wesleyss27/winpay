<?php
if ($_SESSION["tipo"] != 1) {
    header("Location: " . url());
}
?>


<?php $v->layout("_theme"); ?>


<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h4 style="font-family: inter;"><?= $title ?></h4>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= url() ?>">Início</a></li>
                        <li class="breadcrumb-item active">Notificação</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <div class="dashboard-container">
        <form action="<?= url('/processos/notificar-todos') ?>" method="post" class="form-modern">
            <h4>Notificar Todos</h4>

            <div class="form-group">
                <label>Título *</label>
                <input type="text" name="titulo" class="form-control" required>
            </div>

            <div class="form-group">
                <label>Mensagem *</label>
                <textarea name="mensagem" class="form-control" rows="4" required></textarea>
            </div>

            <div class="form-group">
                <label>Imagem (URL - opcional)</label>
                <input type="url" name="imagem" class="form-control" placeholder="https://exemplo.com/imagem.jpg">
            </div>

            <div class="form-group">
                <label>Link (URL ao clicar na notificação - opcional)</label>
                <input type="url" name="url" class="form-control" placeholder="https://seusite.com/pagina">
            </div>
            <button type="submit" class="btn btn-primary btn-lg mt-3">Enviar Notificação</button>
        </form>
    </div>
</div>

<style>
    .form-modern h4 { margin-top: 20px; color: #FFD700; font-weight: bold; }
    .form-modern label { margin-top: 10px; color: #fff; font-family: 'Inter', sans-serif; }
    .form-modern .form-control { background: #1E1E1E; color: #fff; border: 1px solid #333; border-radius: 8px; padding: 10px; }
    .form-modern .form-control:focus { border-color: #FFD700; outline: none; box-shadow: 0 0 5px rgba(255, 215, 0, 0.5); }
    .btn-primary { background-color: #FFD700; color: #121212; border: none; padding: 10px 20px; border-radius: 8px; font-weight: bold; }
    .btn-primary:hover { background-color: #e6c200; }
</style>
