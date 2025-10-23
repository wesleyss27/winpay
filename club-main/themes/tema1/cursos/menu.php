<div class="win-section">
    <nav class="win-tabs">
        <a href="<?= url("cursos/detalhe/$curso->id") ?>">
            <i class="fa-solid fa-book-open"></i> Conteúdo
        </a>
        <a href="<?= url("turmas/alunos/$curso->id") ?>">
            <i class="fa-solid fa-users"></i> Alunos
        </a>
        <a href="<?= url("cursos/comentarios/$curso->id") ?>">
            <i class="fa-regular fa-comments"></i> Comentários
        </a>
        <a href="<?= url("cursos/editarcursos?course_id=$curso->id") ?>">
            <i class="fa-solid fa-gear"></i> Configurações
        </a>
        <a href="<?= url("cursos/editarmodulo/$curso->id") ?>">
            <i class="fa fa-plus" aria-hidden="true"></i> Novo Módulo
        </a>
        <a href="<?= url("certificados/$curso->id") ?>">
            <i class="fas fa-graduation-cap" aria-hidden="true"></i> Certificado
        </a>
    </nav>
</div>
