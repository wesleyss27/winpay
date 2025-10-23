<?php
$v->layout("_theme");

$imgCover = !empty($cursos->imagem)
    ? url("uploads/cursos/" . $cursos->imagem)
    : url("uploads/cursos/default.png");

$totalAulas = (int) $aulaObj->find("curso = {$cursos->id}")->count();
$registrosConcluidos = $aulaCl->find("aluno = {$_SESSION["id"]} AND curso = {$cursos->id}")->fetch(true);
$idsConcluidos = [];
if ($registrosConcluidos) {
    foreach ($registrosConcluidos as $registro) {
        $idsConcluidos[] = (int) $registro->aula;
    }
}
$contadorConcluidas = count($idsConcluidos);
$progress = $totalAulas > 0 ? round(($contadorConcluidas / $totalAulas) * 100) : 0;

$descricaoCurso = $cursos->descricao ? strip_tags($cursos->descricao) : "Pronto para mergulhar em um novo conteúdo?";
$descricaoCurso = mb_strimwidth($descricaoCurso, 0, 220, "…");

$semana = 0;
if (!empty($turma)) {
    $dias = floor((time() - strtotime($turma->created_at)) / 86400);
    $semana = $dias;
}

$modulosDisponiveis = [];
if ($modulos) {
    foreach ($modulos as $modulo) {
        $liberado = (
            $cursos->liberacao == 1 ||
            ($cursos->liberacao == 3 && $semana >= 7) ||
            empty($turma)
        );

        if ($liberado) {
            $modulosDisponiveis[] = $modulo;
        }
    }
}

$proximaAula = null;
if ($modulosDisponiveis) {
    foreach ($modulosDisponiveis as $modulo) {
        foreach ($aulas as $aula) {
            if ($aula->modulo != $modulo->id) {
                continue;
            }

            if (!in_array($aula->id, $idsConcluidos, true)) {
                $proximaAula = $aula;
                break 2;
            }
        }
    }
}

if (!$proximaAula && $aulas) {
    $proximaAula = $aulas[0];
}

$ctaUrl = $proximaAula ? url("minhas-compras/viewaulas/" . $proximaAula->id) : url("minhas-compras");
$certificadoDisponivel = ($totalAulas > 0 && $contadorConcluidas === $totalAulas);

?>

<style>
:root {
  --course-accent: #f4d03f;
  --course-accent-rgb: 244, 208, 63;
}

.lesson-card {
  display: flex;
  flex-direction: column;
  justify-content: space-between;
  border-radius: var(--wh-radius-md) !important;
  min-width: clamp(220px, 32vw, 260px);
  padding: 0;
  overflow: hidden;
  border: 1px solid rgba(244, 208, 63, 0.14);
  transition: transform 0.25s ease, border-color 0.25s ease, box-shadow 0.25s ease;
  background: linear-gradient(135deg, rgba(11, 15, 28, 0.75), rgba(5, 5, 8, 0.9));
  color: inherit;
  text-decoration: none;
}

.lesson-card:hover {
  transform: translateY(-8px);
  border-color: rgba(var(--course-accent-rgb), 0.6);
  box-shadow: 0 24px 48px rgba(0, 0, 0, 0.45);
}

.lesson-card.completed {
  border-color: rgba(var(--course-accent-rgb), 0.55);
}

.lesson-thumb {
  position: relative;
  width: 100%;
  aspect-ratio: 16 / 9;
  background: linear-gradient(135deg, rgba(244, 208, 63, 0.1), transparent);
  display: flex;
  align-items: center;
  justify-content: center;
  overflow: hidden;
}

.lesson-thumb img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  opacity: 0.7;
}

.lesson-card figcaption {
  padding: 1.1rem 1.4rem 1.3rem;
  display: flex;
  flex-direction: column;
  gap: 0.6rem;
}

.lesson-card h4 {
  margin: 0;
  font-size: 1.05rem;
  font-weight: 600;
}

.lesson-status {
  display: inline-flex;
  align-items: center;
  gap: 0.45rem;
  font-size: 0.72rem;
  letter-spacing: 0.12em;
  text-transform: uppercase;
  padding: 0.35rem 0.75rem;
  border-radius: 999px;
  background: rgba(148, 163, 184, 0.18);
  color: rgba(148, 163, 184, 0.82);
}

.lesson-status.completed {
  background: rgba(var(--course-accent-rgb), 0.22);
  color: var(--course-accent);
}

.lesson-status.upnext {
  background: rgba(var(--course-accent-rgb), 0.12);
  color: var(--course-accent);
}

.hero-actions {
  display: flex;
  flex-wrap: wrap;
  gap: 0.75rem;
  margin-top: 1.2rem;
}

.hero-actions .btn-win {
  background: var(--course-accent);
  color: #050507;
}

.hero-actions .btn-win-ghost {
  border-color: var(--course-accent);
  color: var(--course-accent);
}

.win-progress-bar {
  background: linear-gradient(90deg, var(--course-accent), rgba(var(--course-accent-rgb), 0.65));
}

@media (max-width: 575.98px) {
  .lesson-card {
    min-width: 80vw;
  }
}
</style>

<div class="content-wrapper win-page">
  <section class="content">
    <div class="container-fluid">

      <section class="win-hero" style="background: linear-gradient(135deg, rgba(11, 15, 28, 0.88), rgba(5, 5, 8, 0.9)), url('<?= $imgCover; ?>') center/cover no-repeat;">
        <div class="win-page-title">
          <small>Curso adquirido</small>
          <h2><?= $cursos->nome; ?></h2>
        </div>
        <p><?= $descricaoCurso; ?></p>

        <div class="win-progress" style="margin-top: 1rem;">
          <div class="win-progress-bar" style="width: <?= $progress; ?>%;"></div>
        </div>
        <span class="win-section-subtitle" style="margin-top: 0.6rem;">
          <?= $contadorConcluidas; ?> de <?= $totalAulas; ?> aulas concluídas (<?= $progress; ?>%)
        </span>

        <div class="hero-actions">
          <a href="<?= $ctaUrl; ?>" class="btn-win">
            <i class="fas fa-play"></i>
            Continuar aprendendo
          </a>
          <?php if ($certificadoDisponivel) : ?>
            <a href="<?= url("certificados/ver/" . $cursos->id); ?>" class="btn-win btn-win-ghost" target="_blank">
              <i class="fas fa-award"></i>
              Certificado disponível
            </a>
          <?php endif; ?>
        </div>
      </section>

      <?php if ($modulosDisponiveis) : ?>
        <?php foreach ($modulosDisponiveis as $modulo) :
          $descricaoModulo = $modulo->descricao ? mb_strimwidth(strip_tags($modulo->descricao), 0, 140, "…") : "Avance para o próximo nível de conhecimento.";
        ?>
          <section class="win-section">
            <div class="win-section-header">
              <h2><?= $modulo->nome; ?></h2>
              <span class="win-section-subtitle"><?= $descricaoModulo; ?></span>
            </div>

            <div class="win-slider">
              <?php foreach ($aulas as $aula) :
                if ($aula->modulo != $modulo->id) {
                    continue;
                }

                $completou = in_array($aula->id, $idsConcluidos, true);
                $statusClass = $completou ? 'completed' : 'upnext';
                $statusLabel = $completou ? 'Concluída' : 'Assistir agora';
              ?>
                <a href="<?= url("minhas-compras/viewaulas/" . $aula->id); ?>" class="lesson-card <?= $statusClass; ?>">
                  <figure class="lesson-thumb">
                    <img src="<?= $imgCover; ?>" alt="<?= $aula->nome; ?>">
                  </figure>
                  <figcaption>
                    <span class="lesson-status <?= $statusClass; ?>">
                      <i class="fas <?= $completou ? 'fa-check-circle' : 'fa-play'; ?>"></i>
                      <?= $statusLabel; ?>
                    </span>
                    <h4><?= mb_strimwidth($aula->nome, 0, 52, "…"); ?></h4>
                    <?php if (!empty($aula->descricao)) : ?>
                      <p class="win-card-text"><?= mb_strimwidth(strip_tags($aula->descricao), 0, 120, "…"); ?></p>
                    <?php endif; ?>
                  </figcaption>
                </a>
              <?php endforeach; ?>
            </div>
          </section>
        <?php endforeach; ?>
      <?php else : ?>
        <section class="win-section">
          <div class="win-empty-state">
            Os módulos deste curso ainda não estão liberados para você. Volte mais tarde ou confira com o suporte.
          </div>
        </section>
      <?php endif; ?>
    </div>
  </section>
</div>

<?= $v->start("scripts") ?>
<script>
  document.querySelectorAll('.lesson-card').forEach(card => {
    card.addEventListener('keydown', (event) => {
      if (event.key === 'Enter' || event.key === ' ') {
        event.preventDefault();
        card.click();
      }
    });
  });
</script>
<?= $v->end() ?>
