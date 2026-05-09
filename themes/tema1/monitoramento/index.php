<?php $v->layout("_theme"); ?>

<style>
/* ===== MONITORING DASHBOARD STYLES ===== */
:root {
    --wp-yellow:   #FFDD00;
    --wp-yellow-d: #d4b800;
    --wp-yellow-g: rgba(255, 221, 0, 0.12);
    --wp-dark:     #111111;
    --wp-card:     #191919;
    --wp-card2:    #222222;
    --wp-border:   #2c2c2c;
    --wp-text:     #e8e8e8;
    --wp-muted:    #888888;
    --wp-green:    #22c55e;
    --wp-green-g:  rgba(34, 197, 94, 0.12);
    --wp-blue:     #3b82f6;
    --wp-blue-g:   rgba(59, 130, 246, 0.12);
    --wp-purple:   #a855f7;
    --wp-purple-g: rgba(168, 85, 247, 0.12);
    --wp-orange:   #f97316;
    --wp-orange-g: rgba(249, 115, 22, 0.12);
    --wp-red:      #ef4444;
}

.monitor-wrapper {
    background: var(--wp-dark);
    min-height: 100vh;
    color: var(--wp-text);
    font-family: 'Inter', sans-serif;
    padding: 0 0 40px 0;
}

/* ===== TOPBAR ===== */
.monitor-topbar {
    background: linear-gradient(135deg, #0d0d0d 0%, #1a1400 100%);
    border-bottom: 1px solid var(--wp-border);
    padding: 16px 24px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 12px;
    position: sticky;
    top: 0;
    z-index: 100;
    backdrop-filter: blur(10px);
}

.monitor-title {
    display: flex;
    align-items: center;
    gap: 12px;
}

.monitor-title h1 {
    font-size: 20px;
    font-weight: 700;
    color: #fff;
    margin: 0;
    letter-spacing: 0.5px;
}

.monitor-title h1 span {
    color: var(--wp-yellow);
}

.live-badge {
    display: flex;
    align-items: center;
    gap: 6px;
    background: rgba(34, 197, 94, 0.15);
    border: 1px solid rgba(34, 197, 94, 0.3);
    color: var(--wp-green);
    font-size: 11px;
    font-weight: 700;
    padding: 4px 10px;
    border-radius: 20px;
    letter-spacing: 1px;
    text-transform: uppercase;
}

.live-dot {
    width: 7px;
    height: 7px;
    background: var(--wp-green);
    border-radius: 50%;
    animation: pulse-live 1.5s ease-in-out infinite;
}

@keyframes pulse-live {
    0%, 100% { opacity: 1; transform: scale(1); box-shadow: 0 0 0 0 rgba(34,197,94,0.5); }
    50%       { opacity: 0.7; transform: scale(1.2); box-shadow: 0 0 0 6px rgba(34,197,94,0); }
}

.monitor-controls {
    display: flex;
    align-items: center;
    gap: 10px;
    flex-wrap: wrap;
}

.monitor-select {
    background: var(--wp-card2) !important;
    border: 1px solid var(--wp-border) !important;
    border-radius: 8px !important;
    color: var(--wp-text) !important;
    font-size: 13px !important;
    padding: 7px 12px !important;
    min-width: 200px;
    cursor: pointer;
    outline: none;
    transition: border-color .2s;
}

.monitor-select:focus {
    border-color: var(--wp-yellow) !important;
    box-shadow: 0 0 0 2px rgba(255,221,0,.15) !important;
}

.btn-monitor {
    display: flex;
    align-items: center;
    gap: 6px;
    padding: 7px 14px;
    border-radius: 8px;
    border: none;
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
    transition: all .2s;
}

.btn-monitor-primary {
    background: var(--wp-yellow);
    color: #111;
}

.btn-monitor-primary:hover {
    background: #ffe033;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(255,221,0,.3);
}

.btn-monitor-ghost {
    background: var(--wp-card2);
    color: var(--wp-text);
    border: 1px solid var(--wp-border);
}

.btn-monitor-ghost:hover {
    background: #2d2d2d;
    border-color: #444;
}

/* ===== STATS CARDS ===== */
.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
    gap: 16px;
    padding: 20px 24px 0;
}

.stat-card {
    background: var(--wp-card);
    border: 1px solid var(--wp-border);
    border-radius: 14px;
    padding: 20px;
    position: relative;
    overflow: hidden;
    transition: transform .2s, box-shadow .2s;
}

.stat-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 24px rgba(0,0,0,.4);
}

.stat-card::before {
    content: '';
    position: absolute;
    top: 0; left: 0; right: 0;
    height: 3px;
    border-radius: 14px 14px 0 0;
}

.stat-card.visits::before   { background: linear-gradient(90deg, var(--wp-blue), #60a5fa); }
.stat-card.pix::before      { background: linear-gradient(90deg, var(--wp-yellow), #fbbf24); }
.stat-card.cartao::before   { background: linear-gradient(90deg, var(--wp-purple), #c084fc); }
.stat-card.approved::before { background: linear-gradient(90deg, var(--wp-green), #4ade80); }

.stat-icon {
    width: 40px;
    height: 40px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    margin-bottom: 12px;
}

.stat-card.visits  .stat-icon { background: var(--wp-blue-g);   color: var(--wp-blue);   }
.stat-card.pix     .stat-icon { background: var(--wp-yellow-g); color: var(--wp-yellow); }
.stat-card.cartao  .stat-icon { background: var(--wp-purple-g); color: var(--wp-purple); }
.stat-card.approved .stat-icon { background: var(--wp-green-g); color: var(--wp-green);  }

.stat-value {
    font-size: 36px;
    font-weight: 800;
    line-height: 1;
    margin-bottom: 6px;
    font-variant-numeric: tabular-nums;
}

.stat-card.visits   .stat-value { color: var(--wp-blue);   }
.stat-card.pix      .stat-value { color: var(--wp-yellow); }
.stat-card.cartao   .stat-value { color: var(--wp-purple); }
.stat-card.approved .stat-value { color: var(--wp-green);  }

.stat-label {
    font-size: 12px;
    color: var(--wp-muted);
    font-weight: 500;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.stat-sub {
    font-size: 11px;
    color: var(--wp-muted);
    margin-top: 8px;
}

/* ===== MAIN CONTENT GRID ===== */
.monitor-content {
    display: grid;
    grid-template-columns: 1fr 380px;
    gap: 16px;
    padding: 16px 24px 0;
}

@media (max-width: 1100px) {
    .monitor-content { grid-template-columns: 1fr; }
}

/* ===== SECTION CARD ===== */
.section-card {
    background: var(--wp-card);
    border: 1px solid var(--wp-border);
    border-radius: 14px;
    overflow: hidden;
}

.section-header {
    padding: 14px 18px;
    border-bottom: 1px solid var(--wp-border);
    display: flex;
    align-items: center;
    justify-content: space-between;
    background: rgba(0,0,0,.2);
}

.section-title {
    font-size: 13px;
    font-weight: 700;
    color: var(--wp-text);
    text-transform: uppercase;
    letter-spacing: 0.8px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.section-badge {
    background: var(--wp-card2);
    border: 1px solid var(--wp-border);
    color: var(--wp-muted);
    font-size: 11px;
    padding: 2px 8px;
    border-radius: 20px;
    font-weight: 600;
}

/* ===== EVENT FEED ===== */
.event-feed {
    max-height: 580px;
    overflow-y: auto;
    scrollbar-width: thin;
    scrollbar-color: var(--wp-border) transparent;
}

.event-feed::-webkit-scrollbar { width: 5px; }
.event-feed::-webkit-scrollbar-thumb { background: var(--wp-border); border-radius: 4px; }

.event-item {
    display: flex;
    align-items: flex-start;
    gap: 12px;
    padding: 13px 18px;
    border-bottom: 1px solid rgba(44,44,44,.5);
    transition: background .15s;
    position: relative;
    animation: slideIn .3s ease;
}

@keyframes slideIn {
    from { opacity: 0; transform: translateY(-8px); }
    to   { opacity: 1; transform: translateY(0); }
}

.event-item:last-child { border-bottom: none; }

.event-item:hover { background: rgba(255,255,255,.02); }

.event-item.new-flash {
    animation: newFlash .6s ease;
}

@keyframes newFlash {
    0%   { background: rgba(255,221,0,.08); }
    100% { background: transparent; }
}

.event-type-badge {
    flex-shrink: 0;
    width: 36px;
    height: 36px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 16px;
    font-weight: 700;
}

.badge-visita  { background: var(--wp-blue-g);   color: var(--wp-blue);   border: 1px solid rgba(59,130,246,.2);  }
.badge-pix     { background: var(--wp-yellow-g); color: var(--wp-yellow); border: 1px solid rgba(255,221,0,.2);   }
.badge-cartao  { background: var(--wp-purple-g); color: var(--wp-purple); border: 1px solid rgba(168,85,247,.2);  }
.badge-boleto  { background: var(--wp-orange-g); color: var(--wp-orange); border: 1px solid rgba(249,115,22,.2);  }
.badge-aprovada { background: var(--wp-green-g); color: var(--wp-green);  border: 1px solid rgba(34,197,94,.2);   }

.event-body { flex: 1; min-width: 0; }

.event-top {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 8px;
    margin-bottom: 4px;
}

.event-type-label {
    font-size: 10px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.8px;
    padding: 2px 7px;
    border-radius: 4px;
}

.label-visita   { color: var(--wp-blue);   background: var(--wp-blue-g);   }
.label-pix      { color: var(--wp-yellow); background: var(--wp-yellow-g); }
.label-cartao   { color: var(--wp-purple); background: var(--wp-purple-g); }
.label-boleto   { color: var(--wp-orange); background: var(--wp-orange-g); }
.label-aprovada { color: var(--wp-green);  background: var(--wp-green-g);  }

.event-time {
    font-size: 11px;
    color: var(--wp-muted);
    flex-shrink: 0;
    font-variant-numeric: tabular-nums;
}

.event-checkout {
    font-size: 13px;
    font-weight: 600;
    color: var(--wp-text);
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    margin-bottom: 2px;
}

.event-meta {
    display: flex;
    align-items: center;
    gap: 10px;
    flex-wrap: wrap;
}

.event-product {
    font-size: 11px;
    color: var(--wp-muted);
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    max-width: 200px;
}

.event-value {
    font-size: 12px;
    font-weight: 700;
    color: var(--wp-green);
    font-variant-numeric: tabular-nums;
}

.event-ip {
    font-size: 10px;
    color: #555;
    font-family: 'SF Mono', 'Cascadia Code', monospace;
}

/* Empty state */
.feed-empty {
    padding: 60px 24px;
    text-align: center;
}

.feed-empty .feed-empty-icon {
    font-size: 48px;
    margin-bottom: 12px;
    opacity: 0.3;
}

.feed-empty p {
    color: var(--wp-muted);
    font-size: 14px;
    margin: 0;
}

/* ===== CHECKOUT TABLE (right panel) ===== */
.checkout-table {
    max-height: 580px;
    overflow-y: auto;
    scrollbar-width: thin;
    scrollbar-color: var(--wp-border) transparent;
}

.checkout-table::-webkit-scrollbar { width: 5px; }
.checkout-table::-webkit-scrollbar-thumb { background: var(--wp-border); border-radius: 4px; }

.ck-row {
    padding: 12px 16px;
    border-bottom: 1px solid rgba(44,44,44,.5);
    transition: background .15s;
}

.ck-row:hover { background: rgba(255,255,255,.025); }
.ck-row:last-child { border-bottom: none; }

.ck-name {
    font-size: 13px;
    font-weight: 600;
    color: var(--wp-text);
    margin-bottom: 8px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.ck-stats {
    display: flex;
    gap: 6px;
    flex-wrap: wrap;
}

.ck-stat-pill {
    display: flex;
    align-items: center;
    gap: 4px;
    padding: 3px 8px;
    border-radius: 6px;
    font-size: 11px;
    font-weight: 600;
    font-variant-numeric: tabular-nums;
}

.pill-visita  { background: var(--wp-blue-g);   color: var(--wp-blue);   }
.pill-pix     { background: var(--wp-yellow-g); color: var(--wp-yellow); }
.pill-cartao  { background: var(--wp-purple-g); color: var(--wp-purple); }
.pill-venda   { background: var(--wp-green-g);  color: var(--wp-green);  }
.pill-conv    { background: rgba(249,115,22,.1); color: var(--wp-orange); }

.ck-conv-bar {
    margin-top: 8px;
    height: 3px;
    background: var(--wp-card2);
    border-radius: 2px;
    overflow: hidden;
}

.ck-conv-fill {
    height: 100%;
    background: linear-gradient(90deg, var(--wp-yellow), #fbbf24);
    border-radius: 2px;
    transition: width .5s ease;
}

/* ===== FILTER TABS ===== */
.filter-tabs {
    display: flex;
    gap: 6px;
    padding: 12px 24px 0;
    flex-wrap: wrap;
}

.filter-tab {
    padding: 5px 14px;
    border-radius: 20px;
    border: 1px solid var(--wp-border);
    background: var(--wp-card);
    color: var(--wp-muted);
    font-size: 12px;
    font-weight: 600;
    cursor: pointer;
    transition: all .2s;
    display: flex;
    align-items: center;
    gap: 5px;
}

.filter-tab:hover {
    border-color: var(--wp-yellow-d);
    color: var(--wp-yellow);
    background: var(--wp-yellow-g);
}

.filter-tab.active {
    background: var(--wp-yellow);
    border-color: var(--wp-yellow);
    color: #111;
}

/* ===== SESSION INFO ===== */
.session-info {
    display: flex;
    align-items: center;
    gap: 16px;
    padding: 10px 24px;
    margin-top: 16px;
    background: rgba(255,221,0,.04);
    border-top: 1px solid rgba(255,221,0,.08);
    border-bottom: 1px solid rgba(255,221,0,.08);
    font-size: 12px;
    color: var(--wp-muted);
}

.session-info strong { color: var(--wp-yellow); }

/* ===== SCROLLBAR GLOBAL ===== */
.content-wrapper { background: var(--wp-dark) !important; }
</style>

<div class="content-wrapper monitor-wrapper">

    <!-- TOPBAR -->
    <div class="monitor-topbar">
        <div class="monitor-title">
            <h1>WIN<span>PAY</span> &mdash; Monitoramento</h1>
            <div class="live-badge">
                <div class="live-dot"></div>
                AO VIVO
            </div>
        </div>

        <div class="monitor-controls">
            <!-- Filtro de checkout -->
            <select id="filterCheckout" class="monitor-select">
                <option value="">Todos os Checkouts</option>
                <?php foreach ($checkouts as $ck): ?>
                    <option value="<?= htmlspecialchars($ck->codigo) ?>">
                        <?= htmlspecialchars($ck->descricao ?: "Checkout #" . $ck->codigo) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <!-- Intervalo de polling -->
            <select id="pollingInterval" class="monitor-select" style="min-width: 130px;">
                <option value="2000">Atualizar: 2s</option>
                <option value="5000" selected>Atualizar: 5s</option>
                <option value="10000">Atualizar: 10s</option>
                <option value="30000">Atualizar: 30s</option>
            </select>

            <button class="btn-monitor btn-monitor-ghost" onclick="clearFeed()" title="Limpar feed">
                <span class="material-symbols-rounded" style="font-size:16px;">delete_sweep</span>
                Limpar
            </button>

            <button class="btn-monitor btn-monitor-primary" onclick="pauseToggle()" id="btnPause">
                <span class="material-symbols-rounded" style="font-size:16px;" id="pauseIcon">pause</span>
                <span id="pauseLabel">Pausar</span>
            </button>
        </div>
    </div>

    <!-- SESSION INFO BAR -->
    <div class="session-info">
        <span>Monitorando desde: <strong id="sessionStart">--:--:--</strong></span>
        <span>Última atualização: <strong id="lastUpdate">aguardando...</strong></span>
        <span>Eventos capturados: <strong id="totalEvents">0</strong></span>
        <span id="statusMsg" style="margin-left:auto; color: var(--wp-green); font-weight:600;"></span>
    </div>

    <!-- FILTER TABS -->
    <div class="filter-tabs">
        <button class="filter-tab active" data-type="todos" onclick="setTypeFilter('todos', this)">
            <span class="material-symbols-rounded" style="font-size:14px;">list</span> Todos
        </button>
        <button class="filter-tab" data-type="visita" onclick="setTypeFilter('visita', this)">
            <span class="material-symbols-rounded" style="font-size:14px;">visibility</span> Visitas
        </button>
        <button class="filter-tab" data-type="pix" onclick="setTypeFilter('pix', this)">
            <span class="material-symbols-rounded" style="font-size:14px;">qr_code_2</span> PIX / QR Code
        </button>
        <button class="filter-tab" data-type="cartao" onclick="setTypeFilter('cartao', this)">
            <span class="material-symbols-rounded" style="font-size:14px;">credit_card</span> Cartão
        </button>
        <button class="filter-tab" data-type="boleto" onclick="setTypeFilter('boleto', this)">
            <span class="material-symbols-rounded" style="font-size:14px;">receipt_long</span> Boleto
        </button>
        <button class="filter-tab" data-type="aprovada" onclick="setTypeFilter('aprovada', this)">
            <span class="material-symbols-rounded" style="font-size:14px;">check_circle</span> Aprovadas
        </button>
    </div>

    <!-- STATS CARDS -->
    <div class="stats-grid">
        <div class="stat-card visits">
            <div class="stat-icon">
                <span class="material-symbols-rounded">visibility</span>
            </div>
            <div class="stat-value" id="statVisitas">0</div>
            <div class="stat-label">Visitas ao Checkout</div>
            <div class="stat-sub" id="statVisitasSub">&nbsp;</div>
        </div>
        <div class="stat-card pix">
            <div class="stat-icon">
                <span class="material-symbols-rounded">qr_code_2</span>
            </div>
            <div class="stat-value" id="statPix">0</div>
            <div class="stat-label">PIX / QR Code Gerado</div>
            <div class="stat-sub" id="statPixSub">&nbsp;</div>
        </div>
        <div class="stat-card cartao">
            <div class="stat-icon">
                <span class="material-symbols-rounded">credit_card</span>
            </div>
            <div class="stat-value" id="statCartao">0</div>
            <div class="stat-label">Tentativas Cartão</div>
            <div class="stat-sub" id="statCartaoSub">&nbsp;</div>
        </div>
        <div class="stat-card approved">
            <div class="stat-icon">
                <span class="material-symbols-rounded">check_circle</span>
            </div>
            <div class="stat-value" id="statAprovadas">0</div>
            <div class="stat-label">Compras Aprovadas</div>
            <div class="stat-sub" id="statAprovadasSub">&nbsp;</div>
        </div>
    </div>

    <!-- MAIN CONTENT: Feed + Checkout Summary -->
    <div class="monitor-content">

        <!-- EVENT FEED -->
        <div class="section-card">
            <div class="section-header">
                <div class="section-title">
                    <span class="material-symbols-rounded" style="font-size:16px; color:var(--wp-yellow);">bolt</span>
                    Feed de Eventos
                </div>
                <span class="section-badge" id="feedCount">0 eventos</span>
            </div>
            <div class="event-feed" id="eventFeed">
                <div class="feed-empty" id="feedEmpty">
                    <div class="feed-empty-icon">
                        <span class="material-symbols-rounded" style="font-size:48px;">radar</span>
                    </div>
                    <p>Aguardando eventos...<br>
                    <small style="color:#555; font-size:11px;">Os eventos aparecerão aqui em tempo real.</small></p>
                </div>
            </div>
        </div>

        <!-- CHECKOUT SUMMARY PANEL -->
        <div class="section-card">
            <div class="section-header">
                <div class="section-title">
                    <span class="material-symbols-rounded" style="font-size:16px; color:var(--wp-yellow);">store</span>
                    Por Checkout
                </div>
                <span class="section-badge" id="ckCount">0</span>
            </div>
            <div class="checkout-table" id="checkoutTable">
                <div class="feed-empty">
                    <div class="feed-empty-icon">
                        <span class="material-symbols-rounded" style="font-size:48px;">storefront</span>
                    </div>
                    <p>Sem atividade ainda.</p>
                </div>
            </div>
        </div>

    </div><!-- /monitor-content -->

</div><!-- /monitor-wrapper -->

<?php $v->start("scripts"); ?>
<script>
(function () {
    'use strict';

    // ── CONFIG ──────────────────────────────────────────────────────────
    const apiBase     = '<?= url("monitoramento/api") ?>';
    const sessionStart = new Date();
    let   sinceTs     = formatTs(sessionStart);
    let   typeFilter  = 'todos';
    let   paused      = false;
    let   timer       = null;
    let   allEvents   = [];  // {id, tipo, checkout_codigo, checkout_nome, produto_nome, valor, meio_pagamento, comprador, ip, ts}
    let   seenIds     = new Set();

    // ── INIT ─────────────────────────────────────────────────────────────
    document.getElementById('sessionStart').textContent = sessionStart.toLocaleTimeString('pt-BR');
    startPolling();

    // ── POLLING ──────────────────────────────────────────────────────────
    function startPolling() {
        clearTimeout(timer);
        fetchEvents();
    }

    async function fetchEvents() {
        if (paused) return scheduleNext();
        try {
            const checkoutFilter = document.getElementById('filterCheckout').value;
            const url = new URL(apiBase);
            url.searchParams.set('since', sinceTs);
            if (checkoutFilter) url.searchParams.set('checkout', checkoutFilter);

            const resp = await fetch(url, { cache: 'no-store' });
            if (!resp.ok) throw new Error('HTTP ' + resp.status);
            const data = await resp.json();

            if (data.erro) {
                console.error('API erro:', data.erro);
                document.getElementById('statusMsg').textContent = '⚠ Erro SQL: ' + data.erro;
                return scheduleNext();
            }

            processEvents(data.eventos || []);
            document.getElementById('lastUpdate').textContent = new Date().toLocaleTimeString('pt-BR');
            document.getElementById('statusMsg').textContent  = '';

            // Move since forward a bit before next poll to avoid re-fetching same second
            sinceTs = formatTs(new Date(Date.now() - 2000));

        } catch (e) {
            document.getElementById('statusMsg').textContent = '⚠ Erro ao buscar eventos';
            console.error(e);
        }
        scheduleNext();
    }

    function scheduleNext() {
        const interval = parseInt(document.getElementById('pollingInterval').value, 10) || 5000;
        timer = setTimeout(fetchEvents, interval);
    }

    // ── PROCESS ──────────────────────────────────────────────────────────
    function processEvents(eventos) {
        let newCount = 0;
        for (const ev of eventos) {
            if (seenIds.has(ev.id)) continue;
            seenIds.add(ev.id);
            allEvents.unshift(ev);
            newCount++;
        }
        if (newCount > 0) {
            renderFeed();
            renderCheckoutTable();
            updateStats();
        }
        document.getElementById('totalEvents').textContent = allEvents.length;
    }

    // ── RENDER FEED ───────────────────────────────────────────────────────
    function renderFeed() {
        const feed    = document.getElementById('eventFeed');
        const empty   = document.getElementById('feedEmpty');
        const visible = allEvents.filter(ev => typeFilter === 'todos' || ev.tipo === typeFilter);

        document.getElementById('feedCount').textContent = visible.length + ' evento' + (visible.length !== 1 ? 's' : '');

        if (visible.length === 0) {
            feed.innerHTML = '';
            feed.appendChild(emptyPlaceholder('Nenhum evento para este filtro.'));
            return;
        }

        // Rebuild all (simple approach — list is bounded by session)
        const fragment = document.createDocumentFragment();
        for (const ev of visible) {
            fragment.appendChild(buildEventRow(ev));
        }
        feed.innerHTML = '';
        feed.appendChild(fragment);

        // Flash newest items
        const rows = feed.querySelectorAll('.event-item');
        if (rows.length > 0) rows[0].classList.add('new-flash');
    }

    function buildEventRow(ev) {
        const div = document.createElement('div');
        div.className = 'event-item';
        div.dataset.id = ev.id;

        const icon  = tipoIcon(ev.tipo);
        const label = tipoLabel(ev.tipo);
        const valor = ev.valor ? 'R$ ' + parseFloat(ev.valor).toLocaleString('pt-BR', {minimumFractionDigits:2}) : '';

        div.innerHTML = `
            <div class="event-type-badge badge-${ev.tipo}">${icon}</div>
            <div class="event-body">
                <div class="event-top">
                    <span class="event-type-label label-${ev.tipo}">${label}</span>
                    <span class="event-time">${relTime(ev.ts)}</span>
                </div>
                <div class="event-checkout">${esc(ev.checkout_nome || ev.checkout_codigo || '—')}</div>
                <div class="event-meta">
                    ${ev.produto_nome ? `<span class="event-product">${esc(ev.produto_nome)}</span>` : ''}
                    ${valor ? `<span class="event-value">${valor}</span>` : ''}
                    ${ev.comprador ? `<span class="event-ip">👤 ${esc(ev.comprador)}</span>` : ''}
                    ${ev.produtor_nome ? `<span class="event-ip" style="color:#666;">🏪 ${esc(ev.produtor_nome)}</span>` : ''}
                    ${ev.ip ? `<span class="event-ip">${esc(ev.ip)}</span>` : ''}
                </div>
            </div>
        `;
        return div;
    }

    // ── RENDER CHECKOUT TABLE ─────────────────────────────────────────────
    function renderCheckoutTable() {
        const container = document.getElementById('checkoutTable');

        // Aggregate per checkout
        const map = {};
        for (const ev of allEvents) {
            const key = ev.checkout_codigo || '_sem_checkout';
            if (!map[key]) {
                map[key] = { nome: ev.checkout_nome || ev.checkout_codigo || 'Sem checkout', produtor: ev.produtor_nome || '', visitas:0, pix:0, cartao:0, boleto:0, vendas:0, receita:0 };
            }
            const m = map[key];
            if (ev.tipo === 'visita')   m.visitas++;
            if (ev.tipo === 'pix')      m.pix++;
            if (ev.tipo === 'cartao')   m.cartao++;
            if (ev.tipo === 'boleto')   m.boleto++;
            if (ev.tipo === 'aprovada') { m.vendas++; m.receita += parseFloat(ev.valor || 0); }
        }

        const entries = Object.values(map);
        document.getElementById('ckCount').textContent = entries.length;

        if (entries.length === 0) {
            container.innerHTML = `<div class="feed-empty"><div class="feed-empty-icon"><span class="material-symbols-rounded" style="font-size:48px;">storefront</span></div><p>Sem atividade ainda.</p></div>`;
            return;
        }

        // Sort by total activity desc
        entries.sort((a, b) => (b.visitas + b.pix + b.cartao + b.boleto + b.vendas) - (a.visitas + a.pix + a.cartao + a.boleto + a.vendas));

        let html = '';
        for (const m of entries) {
            const tentativas = m.pix + m.cartao + m.boleto;
            const conv = tentativas > 0 ? Math.round((m.vendas / tentativas) * 100) : 0;
            const receita = m.receita > 0 ? 'R$ ' + m.receita.toLocaleString('pt-BR',{minimumFractionDigits:2}) : '';

            html += `
            <div class="ck-row">
                <div class="ck-name" title="${esc(m.nome)}">${esc(m.nome)}</div>
                ${m.produtor ? `<div style="font-size:11px; color:#666; margin-bottom:6px; margin-top:-4px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">🏪 ${esc(m.produtor)}</div>` : ''}
                <div class="ck-stats">
                    <span class="ck-stat-pill pill-visita" title="Visitas">
                        <span class="material-symbols-rounded" style="font-size:12px;">visibility</span> ${m.visitas}
                    </span>
                    <span class="ck-stat-pill pill-pix" title="PIX">
                        <span class="material-symbols-rounded" style="font-size:12px;">qr_code_2</span> ${m.pix}
                    </span>
                    <span class="ck-stat-pill pill-cartao" title="Cartão">
                        <span class="material-symbols-rounded" style="font-size:12px;">credit_card</span> ${m.cartao}
                    </span>
                    <span class="ck-stat-pill pill-venda" title="Aprovadas">
                        <span class="material-symbols-rounded" style="font-size:12px;">check_circle</span> ${m.vendas}
                        ${receita ? `<span style="margin-left:2px; font-size:10px;">${receita}</span>` : ''}
                    </span>
                    ${conv > 0 ? `<span class="ck-stat-pill pill-conv" title="Conversão">${conv}%</span>` : ''}
                </div>
                <div class="ck-conv-bar"><div class="ck-conv-fill" style="width:${Math.min(conv,100)}%"></div></div>
            </div>`;
        }
        container.innerHTML = html;
    }

    // ── STATS ─────────────────────────────────────────────────────────────
    function updateStats() {
        const visitas  = allEvents.filter(e => e.tipo === 'visita').length;
        const pix      = allEvents.filter(e => e.tipo === 'pix').length;
        const cartao   = allEvents.filter(e => e.tipo === 'cartao').length;
        const boleto   = allEvents.filter(e => e.tipo === 'boleto').length;
        const aprovadas = allEvents.filter(e => e.tipo === 'aprovada').length;
        const tentativas = pix + cartao + boleto;
        const conv = tentativas > 0 ? Math.round(aprovadas / tentativas * 100) : 0;
        const receita = allEvents.filter(e => e.tipo === 'aprovada').reduce((s,e) => s + parseFloat(e.valor||0), 0);

        animCount('statVisitas',  visitas);
        animCount('statPix',      pix);
        animCount('statCartao',   cartao);
        animCount('statAprovadas', aprovadas);

        document.getElementById('statVisitasSub').textContent  = visitas  > 0 ? 'desta sessão' : ' ';
        document.getElementById('statPixSub').textContent       = boleto > 0 ? `+${boleto} boleto(s)` : ' ';
        document.getElementById('statCartaoSub').textContent    = tentativas > 0 ? `${tentativas} tentativas total` : ' ';
        document.getElementById('statAprovadasSub').textContent = receita > 0
            ? 'R$ ' + receita.toLocaleString('pt-BR', {minimumFractionDigits:2})
            : (conv > 0 ? `Conv: ${conv}%` : ' ');
    }

    function animCount(id, target) {
        const el   = document.getElementById(id);
        const from = parseInt(el.textContent) || 0;
        if (from === target) return;
        const step = target > from ? 1 : -1;
        const steps = Math.abs(target - from);
        const delay = Math.max(10, Math.min(50, 500 / steps));
        let cur = from;
        const iv = setInterval(() => {
            cur += step;
            el.textContent = cur;
            if (cur === target) clearInterval(iv);
        }, delay);
    }

    // ── CONTROLS ──────────────────────────────────────────────────────────
    window.pauseToggle = function () {
        paused = !paused;
        document.getElementById('pauseIcon').textContent  = paused ? 'play_arrow' : 'pause';
        document.getElementById('pauseLabel').textContent = paused ? 'Retomar' : 'Pausar';
        if (!paused) startPolling();
    };

    window.clearFeed = function () {
        if (!confirm('Limpar todos os eventos desta sessão?')) return;
        allEvents = [];
        seenIds.clear();
        sinceTs = formatTs(new Date());
        renderFeed();
        renderCheckoutTable();
        updateStats();
        document.getElementById('totalEvents').textContent = '0';
    };

    window.setTypeFilter = function (type, btn) {
        typeFilter = type;
        document.querySelectorAll('.filter-tab').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        renderFeed();
    };

    // Re-poll immediately when checkout filter changes
    document.getElementById('filterCheckout').addEventListener('change', () => {
        clearTimeout(timer);
        fetchEvents();
    });

    document.getElementById('pollingInterval').addEventListener('change', () => {
        clearTimeout(timer);
        scheduleNext();
    });

    // ── HELPERS ───────────────────────────────────────────────────────────
    function tipoIcon(tipo) {
        const icons = {
            visita:   '<span class="material-symbols-rounded" style="font-size:18px;">visibility</span>',
            pix:      '<span class="material-symbols-rounded" style="font-size:18px;">qr_code_2</span>',
            cartao:   '<span class="material-symbols-rounded" style="font-size:18px;">credit_card</span>',
            boleto:   '<span class="material-symbols-rounded" style="font-size:18px;">receipt_long</span>',
            aprovada: '<span class="material-symbols-rounded" style="font-size:18px;">check_circle</span>',
        };
        return icons[tipo] || '<span class="material-symbols-rounded" style="font-size:18px;">bolt</span>';
    }

    function tipoLabel(tipo) {
        const labels = { visita:'Visita', pix:'PIX / QR Code', cartao:'Cartão', boleto:'Boleto', aprovada:'Aprovada ✓' };
        return labels[tipo] || tipo;
    }

    function relTime(ts) {
        if (!ts) return '';
        const diff = Math.floor((Date.now() - new Date(ts).getTime()) / 1000);
        if (diff < 5)   return 'agora';
        if (diff < 60)  return `há ${diff}s`;
        if (diff < 3600) return `há ${Math.floor(diff/60)}min`;
        return new Date(ts).toLocaleTimeString('pt-BR', {hour:'2-digit', minute:'2-digit'});
    }

    function formatTs(d) {
        // MySQL-compatible timestamp: YYYY-MM-DD HH:MM:SS
        const pad = n => String(n).padStart(2, '0');
        return `${d.getFullYear()}-${pad(d.getMonth()+1)}-${pad(d.getDate())} ${pad(d.getHours())}:${pad(d.getMinutes())}:${pad(d.getSeconds())}`;
    }

    function esc(str) {
        return String(str).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
    }

    function emptyPlaceholder(msg) {
        const d = document.createElement('div');
        d.className = 'feed-empty';
        d.innerHTML = `<div class="feed-empty-icon"><span class="material-symbols-rounded" style="font-size:48px;">radar</span></div><p>${msg}</p>`;
        return d;
    }

    // Refresh relative times every 30s
    setInterval(() => {
        document.querySelectorAll('.event-time').forEach(el => {
            const ts = el.closest('.event-item')?.dataset?.ts;
            if (ts) el.textContent = relTime(ts);
        });
    }, 30000);

})();
</script>
<?php $v->end(); ?>
