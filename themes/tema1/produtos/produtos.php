<?php
$v->layout("_theme");

$status = [
  0 => "Rascunho",
  1 => "Em Analise",
  2 => "Aprovado",
  3 => "Reprovado",
  4 => "Bloqueado",
  5 => "Excluido",
  6 => "Inativado",
];

$statusTone = [
  0 => 'status-draft',
  1 => 'status-review',
  2 => 'status-approved',
  3 => 'status-reproved',
  4 => 'status-blocked',
  5 => 'status-deleted',
  6 => 'status-inactive',
];

include __DIR__ . "/tipo.php";

if ($_SESSION["tipo"] == 6) {
  header("Location: " . url("vendas"));
}

$groupedProdutos = [];
if ($produtos) {
  foreach ($produtos as $produto) {
    $categoriaNome = $categoria[$produto->categoria] ?? "Sem categoria";
    $groupedProdutos[$categoriaNome][] = $produto;
  }
  ksort($groupedProdutos);
}
?>

<style>
  .catalog-page {
    background: #000;
    color: #f5f5f5;
    font-family: 'Inter', sans-serif;
    min-height: 100vh;
    padding-bottom: 4rem;
  }

  .catalog-page a {
    color: inherit;
  }

  .catalog-header {
    border: none;
    margin-bottom: 0;
    padding: 2.5rem 0 1rem;
  }

  .catalog-hero {
    display: grid;
    gap: 1.25rem;
    padding: 0 1.5rem;
  }

  .catalog-hero__kicker {
    text-transform: uppercase;
    letter-spacing: 0.3rem;
    font-size: 0.75rem;
    color: rgba(255, 255, 255, 0.6);
  }

  .catalog-hero__title {
    font-size: 2.6rem;
    font-weight: 700;
    margin: 0;
    color: #ffffff;
  }

  .catalog-hero__subtitle {
    max-width: 640px;
    color: rgba(255, 255, 255, 0.72);
    font-size: 0.95rem;
  }

  .catalog-breadcrumb {
    display: flex;
    gap: 0.75rem;
    align-items: center;
    font-size: 0.82rem;
    color: rgba(255, 255, 255, 0.55);
  }

  .catalog-breadcrumb span {
    opacity: 0.55;
  }

  .catalog-filters-wrap {
    padding: 0 1.5rem 1.5rem;
  }

  .catalog-filters {
    backdrop-filter: blur(18px);
    background: rgba(17, 17, 20, 0.65);
    border: 1px solid rgba(217, 192, 0, 0.12);
    border-radius: 18px;
    padding: 1.5rem;
    display: grid;
    gap: 1.25rem;
  }

  .catalog-fields {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(190px, 1fr));
    gap: 1rem;
  }

  .catalog-field label {
    font-size: 0.75rem;
    text-transform: uppercase;
    letter-spacing: 0.08rem;
    color: rgba(255, 255, 255, 0.55);
    margin-bottom: 0.35rem;
    display: block;
  }

  .catalog-field input,
  .catalog-field select {
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid rgba(255, 255, 255, 0.12);
    color: #f5f5f5;
    border-radius: 12px;
    padding: 0.65rem 0.8rem;
    width: 100%;
    font-size: 0.92rem;
    appearance: none;
    -webkit-appearance: none;
    -moz-appearance: none;
    position: relative;
    z-index: 1;
  }

  .catalog-field select {
    padding-right: 2.1rem;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='8' viewBox='0 0 12 8'%3E%3Cpath fill='%23d9c000' d='M6 8 0 0h12z'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 0.85rem center;
    background-size: 12px 8px;
    color: #f5f5f5;
  }

  .catalog-field select option {
    background: #111;
    color: #f5f5f5;
  }

  .catalog-field input::placeholder {
    color: rgba(255, 255, 255, 0.45);
  }

  .catalog-actions {
    display: flex;
    gap: 0.75rem;
    align-items: flex-end;
    flex-wrap: wrap;
  }

  .catalog-button {
    border: none;
    border-radius: 12px;
    padding: 0.7rem 1.2rem;
    font-weight: 600;
    font-size: 0.9rem;
    letter-spacing: 0.05rem;
    text-transform: uppercase;
    background: linear-gradient(135deg, #d9c000, #f4d03f);
    color: #111;
    cursor: pointer;
    transition: filter 0.25s ease;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
  }

  .catalog-button:hover {
    filter: brightness(1.08);
  }

  .catalog-button.secondary {
    background: transparent;
    color: #f5f5f5;
    border: 1px solid rgba(217, 192, 0, 0.35);
    padding: 0.7rem 1.1rem;
  }

  .catalog-content {
    padding: 0 1.5rem 1.5rem;
  }

  .catalog-row {
    position: relative;
    display: grid;
    gap: 1rem;
  }

  .catalog-row + .catalog-row {
    margin-top: 2.5rem;
  }

  .catalog-row__head {
    display: flex;
    justify-content: space-between;
    align-items: center;
  }

  .catalog-row__title {
    font-size: 1.4rem;
    font-weight: 600;
    margin: 0;
  }

  .catalog-slider {
    display: grid;
    gap: 1.5rem;
    grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
    padding: 0.5rem 0 0;
    overflow: visible;
  }

  @media (min-width: 1200px) {
    .catalog-slider {
      grid-template-columns: repeat(5, minmax(0, 1fr));
    }
  }

  .catalog-item {
    position: relative;
    scroll-snap-align: start;
    border-radius: 18px;
    overflow: hidden;
    background: linear-gradient(145deg, rgba(255, 255, 255, 0.05), rgba(255, 255, 255, 0.01));
    border: 1px solid rgba(255, 255, 255, 0.08);
    min-height: 320px;
    cursor: pointer;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
  }

  .catalog-item:hover {
    transform: translateY(-8px);
    box-shadow: 0 28px 40px rgba(0, 0, 0, 0.35);
  }

  .catalog-item__poster {
    position: relative;
    aspect-ratio: 3 / 4;
    overflow: hidden;
  }

  .catalog-item__poster img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.4s ease;
  }

  .catalog-item:hover .catalog-item__poster img {
    transform: scale(1.06);
  }

  .catalog-status {
    position: absolute;
    top: 12px;
    right: 12px;
    padding: 0.35rem 0.65rem;
    border-radius: 999px;
    font-size: 0.68rem;
    text-transform: uppercase;
    letter-spacing: 0.08rem;
    font-weight: 600;
    background: rgba(14, 14, 18, 0.78);
    border: 1px solid rgba(255, 255, 255, 0.15);
    color: #f5f5f5;
    backdrop-filter: blur(6px);
  }

  .status-approved { background: rgba(60, 180, 75, 0.85); color: #0b1c0b; }
  .status-review { background: rgba(59, 102, 255, 0.85); color: #0b1029; }
  .status-draft { background: rgba(88, 110, 140, 0.78); color: #0d121b; }
  .status-reproved,
  .status-blocked,
  .status-deleted { background: rgba(249, 87, 84, 0.85); color: #270606; }
  .status-inactive { background: rgba(244, 208, 63, 0.85); color: #302205; }

  .catalog-category {
    position: absolute;
    bottom: 14px;
    left: 14px;
    padding: 0.35rem 0.7rem;
    border-radius: 999px;
    background: rgba(17, 17, 20, 0.75);
    font-size: 0.72rem;
    letter-spacing: 0.05rem;
    text-transform: uppercase;
    border: 1px solid rgba(255, 255, 255, 0.12);
  }

  .catalog-item__details {
    padding: 1rem 1.2rem 1.25rem;
    display: grid;
    gap: 0.6rem;
  }

  .catalog-item__title {
    font-size: 1rem;
    font-weight: 600;
    margin: 0;
    color: #fdfdfd;
    text-transform: uppercase;
    letter-spacing: 0.05rem;
  }

  .catalog-item__meta {
    font-size: 0.78rem;
    color: rgba(255, 255, 255, 0.62);
    display: flex;
    gap: 0.6rem;
    align-items: center;
  }

  .catalog-item__meta span {
    display: inline-flex;
    align-items: center;
    gap: 0.3rem;
  }

  .catalog-empty {
    padding: 3rem 0;
    text-align: center;
    color: rgba(255, 255, 255, 0.72);
  }

  .catalog-pagination {
    margin-top: 2rem;
  }

  @media (max-width: 991px) {
    .catalog-slider {
      grid-auto-columns: minmax(200px, 65%);
    }
    .catalog-hero__title {
      font-size: 2.1rem;
    }
  }

  @media (max-width: 768px) {
    .catalog-filters {
      padding: 1.25rem;
    }
    .catalog-slider {
      grid-auto-columns: minmax(180px, 75%);
    }
  }
</style>

<div class="content-wrapper catalog-page">
  <section class="content-header catalog-header">
    <?= $section->getTipoSecaoLayout() ?>

	    <div class="catalog-hero">
	      <div>
	        <span class="catalog-hero__kicker">Catalogo</span>
	        <h1 class="catalog-hero__title">Produtos</h1>
	        <p class="catalog-hero__subtitle">Descubra, gerencie e acompanhe os produtos que impulsionam o seu negocio.</p>
	      </div>
	      <div class="catalog-breadcrumb">
	        <a href="painel.php">Inicio</a>
        <span>></span>
        <span>Produtos</span>
      </div>
    </div>
  </section>

  <section class="catalog-filters-wrap">
    <form action="<?= $router->route("produtos.home"); ?>" class="catalog-filters">
      <div class="catalog-fields">
        <div class="catalog-field">
          <label for="nome">Pesquisar</label>
          <input type="text" name="nome" id="nome" placeholder="Nome do produto" value="<?= @$_GET["venda"]; ?>">
        </div>

        <div class="catalog-field">
          <label for="categoria">Categoria</label>
          <select name="categoria" id="categoria">
            <option value="" selected>Selecionar...</option>
            <?php $i = 1; foreach ($categoria as $categorias) : ?>
              <option value="<?= $i ?>"><?= $categorias ?></option>
            <?php $i++; endforeach ?>
          </select>
        </div>

        <div class="catalog-field">
          <label for="status">Status</label>
          <select name="status" id="status">
            <option value="" selected>Selecionar...</option>
            <option value="0">Rascunho</option>
            <option value="1">Em analise</option>
            <option value="2">Aprovado</option>
            <option value="3">Reprovado</option>
            <option value="4">Bloqueado</option>
          </select>
        </div>

        <div class="catalog-field">
          <label for="usuario">Produtor</label>
          <select name="usuario" id="usuario">
            <option value="" selected>Selecionar...</option>
            <?php if (@$produtores) : ?>
              <?php foreach (@$produtores as $produtor) : ?>
                <option value="<?= $produtor->id ?>"><?= $produtor->nome ?></option>
              <?php endforeach; ?>
            <?php endif; ?>
          </select>
        </div>

        <div class="catalog-field">
          <label for="tipo">Tipo</label>
          <select name="tipo" id="tipo">
            <option value="" selected>Selecionar...</option>
            <?php $i = 1; foreach ($tipoProduto as $tipo) : ?>
              <option value="<?= $i ?>"><?= $tipo ?></option>
            <?php $i++; endforeach ?>
          </select>
        </div>
      </div>

      <div class="catalog-actions">
        <button type="submit" class="catalog-button">
          <span class="material-symbols-rounded" style="font-variation-settings:'FILL' 1;">search</span>
          Filtrar
        </button>

        <?php if (@$shopify) : ?>
          <button type="button" id="sinc_shopify" class="catalog-button secondary">
            <span class="material-symbols-rounded">sync</span>
            Sincronizar Shopify
          </button>
        <?php endif; ?>
      </div>
    </form>
  </section>

  <section class="content catalog-content">
    <?php if ($produtos && $groupedProdutos) : ?>
      <?php foreach ($groupedProdutos as $categoriaNome => $lista) : ?>
        <div class="catalog-row">
          <header class="catalog-row__head">
            <h2 class="catalog-row__title"><?= $categoriaNome ?></h2>
          </header>

          <div class="catalog-slider">
            <?php foreach ($lista as $produto) : ?>
              <?php
                $foto = url("uploads/produtos/default.jpg");
                if ($produto->foto != "" && $produto->shopify == 0) {
                  $foto = url("uploads/produtos/" . $produto->foto);
                } elseif ($produto->foto != "" && $produto->shopify == 1) {
                  $foto = $produto->foto;
                }
                $statusClass = $statusTone[$produto->status] ?? 'status-draft';
                $tipoNome = $tipoProduto[$produto->tipo] ?? 'Produto';
              ?>
              <article class="catalog-item" onclick="redirecionar('<?= url('produtos/detalhe/' . $produto->id) ?>')">
                <div class="catalog-item__poster">
                  <img src="<?= $foto ?>" alt="<?= $produto->nome; ?>">
                  <span class="catalog-status <?= $statusClass ?>"><?= $status[$produto->status] ?? 'Acoes' ?></span>
                  <span class="catalog-category"><?= mb_strimwidth($tipoNome, 0, 22, '...'); ?></span>
                </div>
                <div class="catalog-item__details">
                  <h3 class="catalog-item__title"><?= mb_strimwidth($produto->nome, 0, 28, '...'); ?></h3>
                  <div class="catalog-item__meta">
                    <span><span class="material-symbols-rounded" style="font-size: 1rem;">sell</span>#<?= str_pad($produto->id, 4, '0', STR_PAD_LEFT); ?></span>
                    <span><span class="material-symbols-rounded" style="font-size: 1rem;">inventory_2</span><?= $tipoNome ?></span>
                  </div>
                </div>
              </article>
            <?php endforeach; ?>
          </div>
        </div>
      <?php endforeach; ?>

      <div class="catalog-pagination">
        <?php
          echo "<br>";
          $link = URL_BASE . "/produtos/p/";
          $paginador->paginas($total, $pagina, $link);
          echo "<br>";
        ?>
      </div>
    <?php else : ?>
      <div class="catalog-empty">
        <h4>Nao encontramos produtos com os filtros selecionados.</h4>
        <p>Experimente ajustar os filtros ou criar um novo produto para preencher seu catalogo.</p>
      </div>
    <?php endif; ?>
  </section>
</div>

<?php $v->start("scripts"); ?>
<script>
  function redirecionar(url) {
    location.href = url;
  }

  $("#sinc_shopify").click((e) => {
    e.preventDefault();
    var url = "<?= $router->route("shopify.produtos"); ?>";
    $.ajax({
      url: url,
      type: "GET",
      beforeSend: function () {
        console.log("Sincronizando produtos Shopify...");
      },
      error: function () {
        console.log("O servidor nao conseguiu processar o pedido");
      },
      success: function () {
        alert("Produtos sincronizados com sucesso!");
        location.reload();
      }
    });
  });
</script>
<?php $v->end() ?>
