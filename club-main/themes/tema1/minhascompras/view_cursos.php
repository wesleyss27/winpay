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
  --course-radius-lg: 28px;
}

.course-view-page {
  --hero-horizontal-pad: clamp(0.8rem, 4vw, 2.4rem);
}

.course-view-page .content {
  padding: clamp(0.8rem, 2.5vw, 1.6rem) var(--hero-horizontal-pad) clamp(2rem, 4vw, 2.8rem);
}

.course-view-page .container-fluid {
  display: flex;
  flex-direction: column;
  gap: clamp(2rem, 3vw, 3.5rem);
  padding: 0;
}

.course-view-page .container-fluid > .win-section,
.course-view-page .container-fluid > .win-empty-state,
.course-view-page .container-fluid > section:not(.win-hero) {
  padding-inline: var(--hero-horizontal-pad);
}

.course-view-page .win-slider {
  overflow-y: visible;
  gap: clamp(1rem, 2.2vw, 2rem);
  padding-bottom: clamp(2.4rem, 4vw, 3.2rem);
}

.course-view-page .win-hero {
  position: relative;
  display: block;
  width: calc(100% + (var(--hero-horizontal-pad) * 2));
  max-width: none;
  padding: 0;
  height: clamp(140px, 24vh, 260px);
  border-radius: var(--course-radius-lg);
  margin-left: calc(-1 * var(--hero-horizontal-pad));
  margin-right: calc(-1 * var(--hero-horizontal-pad));
  overflow: hidden;
  background-color: #050508;
  box-shadow: 0 26px 60px rgba(0, 0, 0, 0.5);
  isolation: isolate;
}

.course-view-page .win-hero img {
  width: 100%;
  height: 100%;
  object-fit: contain;
  object-position: center;
  display: block;
  background: radial-gradient(circle at 50% 50%, rgba(15, 19, 32, 0.68), rgba(5, 6, 12, 1));
  transform: scale(1);
  transition: transform 0.6s ease;
}

.course-view-page .win-hero > img,
.course-view-page .win-hero > .hero-overlay {
  position: absolute;
  inset: 0;
}

.course-view-page .win-hero > img {
  position: absolute;
}

.course-view-page .win-hero::after {
  content: "";
  position: absolute;
  inset: 0;
  background: linear-gradient(180deg, rgba(5, 5, 8, 0.05) 0%, rgba(5, 5, 8, 0.65) 70%, rgba(5, 5, 8, 0.92) 100%);
  opacity: 0;
  transition: opacity 0.4s ease;
}

.course-view-page .hero-overlay {
  position: absolute;
  inset: 0;
  padding: clamp(1.2rem, 2.1vw, 2.4rem);
  display: flex;
  align-items: flex-end;
  justify-content: center;
  background: linear-gradient(180deg, rgba(5, 5, 8, 0.05) 0%, rgba(5, 5, 8, 0.75) 100%);
  opacity: 0;
  transform: translateY(14%);
  transition: opacity 0.35s ease, transform 0.35s ease;
  pointer-events: none;
}

.course-view-page .hero-actions {
  display: inline-flex;
  align-items: center;
  gap: 0.75rem;
  opacity: 0;
  transform: translateY(18%);
  transition: opacity 0.3s ease, transform 0.3s ease;
  pointer-events: none;
}

.course-view-page .hero-actions .btn-win {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  border-radius: 999px;
  padding: 0.65rem 1.5rem;
  font-weight: 600;
  box-shadow: 0 16px 30px rgba(var(--course-accent-rgb), 0.2);
  background: var(--course-accent);
  color: #050507;
}

.course-view-page .win-hero:hover::after,
.course-view-page .win-hero:focus-within::after {
  opacity: 1;
}

.course-view-page .win-hero:hover img,
.course-view-page .win-hero:focus-within img {
  transform: scale(1);
}

.course-view-page .win-hero:hover .hero-overlay,
.course-view-page .win-hero:focus-within .hero-overlay {
  opacity: 1;
  transform: translateY(0);
  pointer-events: auto;
}

.course-view-page .win-hero:hover .hero-actions,
.course-view-page .win-hero:focus-within .hero-actions {
  opacity: 1;
  transform: translateY(0);
  pointer-events: auto;
}

@media (hover: none) {
  .course-view-page .win-hero::after {
    opacity: 1;
  }

  .course-view-page .hero-overlay {
    opacity: 1;
    transform: translateY(0);
    pointer-events: auto;
  }

  .course-view-page .hero-actions {
    opacity: 1;
    transform: translateY(0);
    pointer-events: auto;
  }
}

.course-view-page .win-section {
  gap: 1rem;
}

.course-view-page .win-section-header h2 {
  font-size: clamp(1.1rem, 2vw, 1.6rem);
}

.lesson-card {
  position: relative;
  display: block;
  flex: 0 0 clamp(200px, 20vw, 260px);
  width: clamp(200px, 20vw, 260px);
  max-width: clamp(200px, 20vw, 260px);
  aspect-ratio: 2 / 3;
  border-radius: 24px;
  overflow: hidden;
  background: linear-gradient(135deg, rgba(8, 9, 20, 0.88), rgba(3, 4, 12, 0.97));
  color: inherit;
  text-decoration: none;
  box-shadow: 0 24px 46px rgba(0, 0, 0, 0.52);
  transition: transform 0.32s ease, box-shadow 0.32s ease, filter 0.32s ease;
  transform-origin: center;
}

.lesson-card img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  filter: brightness(0.94);
  transition: transform 0.45s ease, filter 0.35s ease;
}

.lesson-card::before {
  content: "";
  position: absolute;
  inset: 0;
  background: linear-gradient(180deg, rgba(8, 10, 20, 0.18) 0%, rgba(5, 6, 12, 0.48) 55%, rgba(5, 6, 12, 0.88) 100%);
  opacity: 0;
  transition: opacity 0.3s ease;
}

.lesson-card__overlay {
  position: absolute;
  inset: 0;
  padding: clamp(0.9rem, 2.2vw, 1.4rem);
  display: flex;
  align-items: flex-end;
  justify-content: center;
  background: linear-gradient(180deg, rgba(5, 6, 12, 0) 10%, rgba(5, 6, 12, 0.72) 100%);
  opacity: 0;
  transform: translateY(12%);
  transition: opacity 0.3s ease, transform 0.3s ease;
  pointer-events: none;
}

.lesson-card__button {
  display: inline-flex;
  align-items: center;
  gap: 0.55rem;
  padding: 0.65rem 1.4rem;
  border-radius: 999px;
  background: rgba(5, 6, 12, 0.85);
  color: rgba(255, 255, 255, 0.92);
  font-size: 0.72rem;
  font-weight: 600;
  letter-spacing: 0.14em;
  text-transform: uppercase;
  box-shadow: 0 20px 42px rgba(0, 0, 0, 0.55);
  pointer-events: none;
}

.lesson-card__button i {
  font-size: 0.9rem;
}

.lesson-card.completed .lesson-card__button {
  background: rgba(var(--course-accent-rgb), 0.2);
  color: var(--course-accent);
}

.lesson-card:hover,
.lesson-card:focus-visible {
  transform: scale(1.08) translateY(-6px);
  box-shadow: 0 36px 70px rgba(0, 0, 0, 0.6);
  z-index: 5;
}

.lesson-card:hover img,
.lesson-card:focus-visible img {
  transform: scale(1.05);
  filter: brightness(1);
}

.lesson-card:hover::before,
.lesson-card:focus-visible::before {
  opacity: 1;
}

.lesson-card:hover .lesson-card__overlay,
.lesson-card:focus-visible .lesson-card__overlay {
  opacity: 1;
  transform: translateY(0);
}

@media (hover: none) {
  .lesson-card {
    transform: none !important;
  }

  .lesson-card::before {
    opacity: 1;
  }

  .lesson-card__overlay {
    opacity: 1;
    transform: translateY(0);
  }
}

.lesson-card:focus-visible {
  outline: 2px solid rgba(var(--course-accent-rgb), 0.5);
  outline-offset: 3px;
}

@media (max-width: 575.98px) {
  .lesson-card {
    flex: 0 0 68vw;
    width: 68vw;
    max-width: 68vw;
  }
}
</style>

<div class="content-wrapper win-page course-view-page">
  <section class="content">
    <div class="container-fluid">

      <section class="win-hero" tabindex="0">
        <img src="<?= $imgCover; ?>" alt="<?= $cursos->nome; ?>">
        <div class="hero-overlay">
          <div class="hero-actions">
            <a href="<?= $ctaUrl; ?>" class="btn-win">
              <i class="fas fa-play"></i>
              Continuar aprendendo
            </a>
          </div>
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
                <?php
                  $aulaCover = !empty($aula->imagem)
                      ? url("uploads/cursos/" . $aula->imagem)
                      : $imgCover;
                  $lessonNameAttr = htmlspecialchars($aula->nome, ENT_QUOTES, 'UTF-8');
                  $buttonLabel = $completou ? 'Rever aula' : 'Assistir agora';
                  $buttonIcon = $completou ? 'fa-redo-alt' : 'fa-play';
                ?>
                <a href="<?= url("minhas-compras/viewaulas/" . $aula->id); ?>" class="lesson-card <?= $statusClass; ?>" aria-label="<?= $statusLabel; ?> - <?= $lessonNameAttr; ?>">
                  <img src="<?= $aulaCover; ?>" alt="<?= $aula->nome; ?>">
                  <div class="lesson-card__overlay">
                    <span class="lesson-card__button <?= $completou ? 'completed' : ''; ?>" aria-hidden="true">
                      <i class="fas <?= $buttonIcon; ?>"></i>
                      <?= $buttonLabel; ?>
                    </span>
                  </div>
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
