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
    <a href="<?= url("cursos/detalhe/$curso->id") ?>" class="txt-azul-dark"><i class="fa-solid fa-book-open"></i> Conteúdo</a>
    <a href="<?= url("turmas/alunos/$curso->id") ?>" class="txt-azul-dark"><i class="fa-solid fa-users"></i> Alunos</a>
    <a href="<?= url("cursos/comentarios/$curso->id") ?>" class="txt-azul-dark"><i class="fa-regular fa-comments"></i> Comentários</a>
    <a href="<?= url("cursos/editarcursos?course_id=$curso->id") ?>" class="txt-azul-dark"><i class="fa-solid fa-gear"></i> Configurações</a>
    <a href="<?= url("cursos/editarmodulo/$curso->id") ?>" class="txt-azul-dark"><i class="fa fa-plus" aria-hidden="true"></i> Novo Módulo</a>
    <a href="<?= url("certificados/$curso->id") ?>" class="txt-azul-dark"><i class="fas fa-graduation-cap" aria-hidden="true"></i> Certificado</a>
</div>