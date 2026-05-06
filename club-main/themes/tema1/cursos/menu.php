<div class="win-section" style="padding:0.6rem 1rem">
    <nav class="win-tabs" id="curso-tabs" style="flex-wrap:wrap;border-radius:16px;">
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
            <i class="fa fa-plus"></i> Novo Módulo
        </a>
        <a href="<?= url("certificados/$curso->id") ?>">
            <i class="fas fa-graduation-cap"></i> Certificado
        </a>
        <a href="<?= url("ofertas/$curso->id") ?>">
            <i class="fas fa-tag"></i> Ofertas
        </a>
    </nav>
</div>
<script>
(function(){
    var cur = window.location.href.split('?')[0];
    document.querySelectorAll('#curso-tabs a').forEach(function(a){
        if(a.href.split('?')[0] === cur) a.classList.add('active');
    });
})();
</script>
