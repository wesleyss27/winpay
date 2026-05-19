<?php

require __DIR__ . "/vendor/autoload.php";

use CoffeeCode\Router\Router;

$router = new Router(WINTURB_URL);

// ── Login ──────────────────────────────────────────────────────────────────
$router->namespace("Source\App\Login");
$router->group(null);
$router->get("/login",  "AppLogin:index",  "login.index");
$router->post("/login", "AppLogin:entrar", "login.entrar");
$router->get("/logout", "AppLogin:sair",   "login.sair");

// ── Dashboard ─────────────────────────────────────────────────────────────
$router->namespace("Source\App\Dashboard");
$router->group(null);
$router->get("/",          "AppDashboard:index", "dashboard.index");
$router->get("/dashboard", "AppDashboard:index", "dashboard.home");

// ── Vídeos ────────────────────────────────────────────────────────────────
$router->namespace("Source\App\Videos");
$router->group("videos");
$router->get("/",                   "AppVideos:index",          "videos.index");
$router->post("/upload",            "AppVideos:upload",         "videos.upload");
$router->get("/editar/{id}",        "AppVideos:editar",         "videos.editar");
$router->post("/salvar",            "AppVideos:salvar",         "videos.salvar");
$router->post("/excluir",           "AppVideos:excluir",        "videos.excluir");
$router->get("/stream/{token}",     "AppVideos:stream",         "videos.stream");
$router->post("/progresso",         "AppVideos:salvarProgresso","videos.progresso");
$router->get("/progresso/{token}",  "AppVideos:getProgresso",   "videos.getProgresso");

// ── Anúncios (CRUD via AJAX) ───────────────────────────────────────────────
$router->namespace("Source\App\Anuncios");
$router->group("anuncios");
$router->post("/salvar",  "AppAnuncios:salvar",  "anuncios.salvar");
$router->post("/excluir", "AppAnuncios:excluir", "anuncios.excluir");

// ── Player público (embed + evento + stream — sem auth) ───────────────────
$router->namespace("Source\App\Player");
$router->group(null);
$router->get("/player/{token}",         "AppPlayer:embed",   "player.embed");
$router->post("/player/evento",         "AppPlayer:evento",  "player.evento");
$router->get("/player/stream/{token}",  "AppPlayer:stream",  "player.stream");

// ── Pastas / biblioteca ───────────────────────────────────────────────────
$router->namespace("Source\App\Pastas");
$router->group("pastas");
$router->post("/criar",   "AppPastas:criar",   "pastas.criar");
$router->post("/excluir", "AppPastas:excluir", "pastas.excluir");
$router->post("/mover",   "AppPastas:mover",   "pastas.mover");

// ── Métricas (autenticado) ────────────────────────────────────────────────
$router->namespace("Source\App\Metricas");
$router->group(null);
$router->get("/metricas/{id}", "AppMetricas:index", "metricas.index");

// ── Despachar ─────────────────────────────────────────────────────────────
$router->dispatch();

if ($router->error()) {
    http_response_code(404);
    exit("Página não encontrada.");
}
