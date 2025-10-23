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

.course-view-page .container-fluid {
  display: flex;
  flex-direction: column;
  gap: clamp(2rem, 3vw, 3.5rem);
}

.course-view-page .win-hero {
  position: relative;
  display: block;
  padding: 0;
  min-height: clamp(260px, 42vw, 420px);
  border-radius: var(--course-radius-lg);
  overflow: hidden;
  background-color: #050508;
  box-shadow: 0 26px 60px rgba(0, 0, 0, 0.5);
  isolation: isolate;
}

.course-view-page .win-hero img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  display: block;
  transform: scale(1);
  transition: transform 0.6s ease;
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
  padding: clamp(1.4rem, 2.3vw, 2.8rem);
  display: flex;
  flex-direction: column;
  justify-content: flex-end;
  gap: 1.1rem;
  opacity: 0;
  transform: translateY(18%);
  transition: opacity 0.35s ease, transform 0.35s ease;
  pointer-events: none;
}

.course-view-page .hero-overlay > * {
  max-width: min(560px, 70%);
}

.course-view-page .hero-chip {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.35rem 0.85rem;
  border-radius: 999px;
  background: rgba(255, 255, 255, 0.14);
  color: rgba(255, 255, 255, 0.85);
  text-transform: uppercase;
  letter-spacing: 0.14em;
  font-size: 0.7rem;
}

.course-view-page .hero-overlay h2 {
  margin: 0;
  font-size: clamp(1.6rem, 3vw, 2.6rem);
  font-weight: 600;
}

.course-view-page .hero-overlay p {
  margin: 0;
  font-size: clamp(0.9rem, 1.3vw, 1rem);
  color: rgba(255, 255, 255, 0.8);
}

.course-view-page .hero-progress {
  position: relative;
  width: 100%;
  height: 6px;
  border-radius: 999px;
  background: rgba(255, 255, 255, 0.12);
  overflow: hidden;
}

.course-view-page .hero-progress-bar {
  position: absolute;
  inset: 0;
  width: var(--progress, 0%);
  border-radius: inherit;
  background: linear-gradient(90deg, var(--course-accent), rgba(var(--course-accent-rgb), 0.75));
}

.course-view-page .hero-progress-label {
  font-size: 0.85rem;
  color: rgba(255, 255, 255, 0.75);
}

.course-view-page .hero-actions {
  display: inline-flex;
  align-items: center;
  gap: 0.75rem;
}

.course-view-page .hero-actions .btn-win {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  border-radius: 999px;
  padding: 0.65rem 1.5rem;
  font-weight: 600;
  box-shadow: 0 16px 30px rgba(var(--course-accent-rgb), 0.2);
}

.course-view-page .hero-actions .btn-win {
  background: var(--course-accent);
  color: #050507;
}

.course-view-page .hero-actions .btn-win-ghost {
  border-color: rgba(255, 255, 255, 0.55);
  color: rgba(255, 255, 255, 0.78);
  background: transparent;
  box-shadow: none;
}

.course-view-page .win-hero:hover::after,
.course-view-page .win-hero:focus-within::after {
  opacity: 1;
}

.course-view-page .win-hero:hover img,
.course-view-page .win-hero:focus-within img {
  transform: scale(1.04);
}

.course-view-page .win-hero:hover .hero-overlay,
.course-view-page .win-hero:focus-within .hero-overlay {
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
  min-width: clamp(210px, 28vw, 260px);
  aspect-ratio: 16 / 9;
  border-radius: 20px;
  overflow: hidden;
  background: linear-gradient(135deg, rgba(8, 9, 20, 0.88), rgba(3, 4, 12, 0.9));
  color: inherit;
  text-decoration: none;
  box-shadow: 0 18px 38px rgba(0, 0, 0, 0.45);
  transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.lesson-card img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  filter: brightness(0.75);
  transform: scale(1);
  transition: transform 0.5s ease, filter 0.4s ease;
}

.lesson-card::before {
  content: "";
  position: absolute;
  inset: 0;
  background: linear-gradient(180deg, rgba(10, 12, 30, 0) 45%, rgba(5, 6, 12, 0.9) 100%);
  opacity: 0;
  transition: opacity 0.3s ease;
}

.lesson-card__chip {
  position: absolute;
  top: 1rem;
  left: 1rem;
  display: inline-flex;
  align-items: center;
  gap: 0.4rem;
  padding: 0.35rem 0.85rem;
  border-radius: 999px;
  font-size: 0.65rem;
  letter-spacing: 0.12em;
  text-transform: uppercase;
  background: rgba(255, 255, 255, 0.14);
  color: rgba(255, 255, 255, 0.85);
  transition: opacity 0.25s ease;
}

.lesson-card.completed .lesson-card__chip {
  background: rgba(var(--course-accent-rgb), 0.18);
  color: var(--course-accent);
}

.lesson-card__overlay {
  position: absolute;
  inset: 0;
  padding: 1.2rem;
  display: flex;
  flex-direction: column;
  justify-content: flex-end;
  gap: 0.6rem;
  opacity: 0;
  transform: translateY(14%);
  transition: opacity 0.3s ease, transform 0.3s ease;
  pointer-events: none;
}

.lesson-card__status {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  font-size: 0.7rem;
  letter-spacing: 0.16em;
  text-transform: uppercase;
  font-weight: 600;
  color: rgba(255, 255, 255, 0.75);
}

.lesson-card.completed .lesson-card__status {
  color: var(--course-accent);
}

.lesson-card__title {
  margin: 0;
  font-size: 1rem;
  font-weight: 600;
  color: #ffffff;
}

.lesson-card__description {
  margin: 0;
  font-size: 0.85rem;
  color: rgba(255, 255, 255, 0.68);
  line-height: 1.4;
}

.lesson-card:hover,
.lesson-card:focus-visible {
  transform: translateY(-10px);
  box-shadow: 0 28px 55px rgba(0, 0, 0, 0.55);
}

.lesson-card:hover img,
.lesson-card:focus-visible img {
  transform: scale(1.06);
  filter: brightness(1);
}

.lesson-card:hover::before,
.lesson-card:focus-visible::before {
  opacity: 1;
}

.lesson-card:hover .lesson-card__overlay,
.lesson-card:focus-visible .lesson-card__overlay,
.lesson-card:focus-within .lesson-card__overlay {
  opacity: 1;
  transform: translateY(0);
  pointer-events: auto;
}

.lesson-card:hover .lesson-card__chip,
.lesson-card:focus-visible .lesson-card__chip {
  opacity: 0;
}

@media (hover: none) {
  .lesson-card::before {
    opacity: 1;
  }

  .lesson-card__overlay {
    opacity: 1;
    transform: translateY(0);
    pointer-events: auto;
  }

  .lesson-card__chip {
    opacity: 0;
  }
}

.lesson-card:focus-visible {
  outline: 2px solid rgba(var(--course-accent-rgb), 0.5);
  outline-offset: 3px;
}

@media (max-width: 575.98px) {
  .lesson-card {
    min-width: 80vw;
  }
}
</style>

<div class="content-wrapper win-page course-view-page">
  <section class="content">
    <div class="container-fluid">

      <section class="win-hero" tabindex="0">
        <img src="<?= $imgCover; ?>" alt="<?= $cursos->nome; ?>">
        <div class="hero-overlay">
          <span class="hero-chip">
            <i class="fas fa-layer-group"></i>
            Curso adquirido
          </span>
          <h2><?= $cursos->nome; ?></h2>
          <p><?= $descricaoCurso; ?></p>
          <div class="hero-progress" style="--progress: <?= $progress; ?>%;">
            <div class="hero-progress-bar"></div>
          </div>
          <span class="hero-progress-label">
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
                  <img src="<?= $imgCover; ?>" alt="<?= $aula->nome; ?>">
                  <span class="lesson-card__chip">
                    <i class="fas <?= $completou ? 'fa-check' : 'fa-play'; ?>"></i>
                    <?= $statusLabel; ?>
                  </span>
                  <div class="lesson-card__overlay">
                    <span class="lesson-card__status">
                      <i class="fas <?= $completou ? 'fa-check-circle' : 'fa-play-circle'; ?>"></i>
                      <?= $statusLabel; ?>
                    </span>
                    <h4 class="lesson-card__title"><?= mb_strimwidth($aula->nome, 0, 60, "…"); ?></h4>
                    <?php if (!empty($aula->descricao)) : ?>
                      <p class="lesson-card__description"><?= mb_strimwidth(strip_tags($aula->descricao), 0, 140, "…"); ?></p>
                    <?php endif; ?>
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
