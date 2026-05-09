<?php

namespace Source\App\Monitoramento;

use League\Plates\Engine;
use Source\App\Sessao\AppSessao;
use Source\Models\Permissao\Section;
use Source\Models\Checkout\Checkout;
use CoffeeCode\DataLayer\Connect;

session_start();

class AppMonitoramento
{
    private $view;
    private $section;

    // Arquivo de log global (lido pelo admin para ver todos os produtores)
    const LOG_ADMIN = "winpay_monitor_0.jsonl";

    public function __construct($router)
    {
        $tema = @$_SESSION["tema"] ?: "tema1";
        $this->view = Engine::create(__DIR__ . "/../../../themes/$tema", "php");
        $this->view->addData(["router" => $router]);
        (new AppSessao())->verificaSeLogado();
        $this->section = new Section();
    }

    public function index($data)
    {
        if ($this->section->getTipo() != 1) {
            header("Location: " . url());
            exit;
        }

        // Admin vê TODOS os checkouts de todos os produtores
        $checkouts = (new Checkout())->find(
            null, null,
            "id, codigo, descricao, usuario"
        )->order("descricao ASC")->fetch(true);

        echo $this->view->render("monitoramento/index", [
            "title"     => "Monitoramento ao Vivo | " . SITE,
            "checkouts" => $checkouts ?: [],
            "userId"    => 0, // 0 = admin, sem filtro por produtor
        ]);
    }

    public function api($data)
    {
        header("Content-Type: application/json; charset=utf-8");
        header("Cache-Control: no-cache, no-store");
        $_SESSION["monitor_ping"] = time();

        $since          = $_GET["since"] ?? date("Y-m-d H:i:s", strtotime("-1 hour"));
        $checkoutFilter = !empty($_GET["checkout"]) ? trim($_GET["checkout"]) : null;

        try {
            $pdo     = Connect::getInstance();
            $eventos = [];
            $formas  = [1 => "cartao", 2 => "boleto", 3 => "pix"];
            $tipoMap = [1 => "cartao", 2 => "boleto", 3 => "pix"];

            // -- Visitas (log de arquivo global do admin) --
            foreach ($this->lerVisitas($since, $checkoutFilter) as $v) {
                $eventos[] = [
                    "tipo"            => "visita",
                    "checkout_codigo" => $v["checkout"]      ?? "",
                    "checkout_nome"   => $v["checkout_nome"] ?? ("Checkout " . ($v["checkout"] ?? "")),
                    "produto_nome"    => $v["produto_nome"]  ?? "",
                    "produtor_nome"   => $v["produtor_nome"] ?? "",
                    "valor"           => null,
                    "meio_pagamento"  => null,
                    "comprador"       => null,
                    "ip"              => $v["ip"] ?? "",
                    "ts"              => $v["ts"] ?? "",
                    "id"              => "v_" . md5(($v["ts"] ?? "") . ($v["ip"] ?? "") . ($v["checkout"] ?? "")),
                ];
            }

            $ckWhere = $checkoutFilter ? "AND c.codigo = :ck" : "";

            // -- Transações iniciadas (criadas desde $since, qualquer status) --
            $sqlIniciadas = "
                SELECT v.id, v.forma_pagamento, v.valor_bruto, v.url_checkout,
                       v.created_at, v.status_transacao,
                       c.descricao AS checkout_nome, c.codigo AS checkout_codigo,
                       p.nome AS produto_nome,
                       ud.nome AS comprador_nome,
                       u.nome AS produtor_nome
                FROM vendas v
                LEFT JOIN checkout c ON c.usuario = v.produtor AND LOCATE(c.codigo, v.url_checkout) > 0
                LEFT JOIN produto p ON p.id = v.produto
                LEFT JOIN usuario_dados ud ON ud.id = v.comprador
                LEFT JOIN usuario u ON u.id = v.produtor
                WHERE v.created_at > :since
                  AND v.status_transacao NOT IN (4, 8)
                  $ckWhere
                ORDER BY v.created_at DESC
                LIMIT 300
            ";

            $stmtI = $pdo->prepare($sqlIniciadas);
            $stmtI->bindValue(":since", $since);
            if ($checkoutFilter) $stmtI->bindValue(":ck", $checkoutFilter);
            $stmtI->execute();

            foreach ($stmtI->fetchAll(\PDO::FETCH_OBJ) as $t) {
                $fp   = (int)$t->forma_pagamento;
                $tipo = $tipoMap[$fp] ?? "tentativa";
                $eventos[] = [
                    "tipo"            => $tipo,
                    "checkout_codigo" => $t->checkout_codigo ?? "",
                    "checkout_nome"   => $t->checkout_nome   ?? "Checkout",
                    "produto_nome"    => $t->produto_nome    ?? "",
                    "produtor_nome"   => $t->produtor_nome   ?? "",
                    "valor"           => $t->valor_bruto,
                    "meio_pagamento"  => $formas[$fp] ?? "outro",
                    "comprador"       => $t->comprador_nome  ?? null,
                    "ip"              => "",
                    "ts"              => $t->created_at,
                    "id"              => "t_" . $t->id,
                ];
            }

            // -- Compras aprovadas: criadas OU atualizadas para status 4/8 desde $since --
            $sqlAprovadas = "
                SELECT v.id, v.forma_pagamento, v.valor_bruto, v.url_checkout,
                       v.created_at, v.updated_at, v.status_transacao,
                       c.descricao AS checkout_nome, c.codigo AS checkout_codigo,
                       p.nome AS produto_nome,
                       ud.nome AS comprador_nome,
                       u.nome AS produtor_nome
                FROM vendas v
                LEFT JOIN checkout c ON c.usuario = v.produtor AND LOCATE(c.codigo, v.url_checkout) > 0
                LEFT JOIN produto p ON p.id = v.produto
                LEFT JOIN usuario_dados ud ON ud.id = v.comprador
                LEFT JOIN usuario u ON u.id = v.produtor
                WHERE v.status_transacao IN (4, 8)
                  AND (v.created_at > :since OR v.updated_at > :since2)
                  $ckWhere
                ORDER BY v.updated_at DESC
                LIMIT 300
            ";

            $stmtA = $pdo->prepare($sqlAprovadas);
            $stmtA->bindValue(":since",  $since);
            $stmtA->bindValue(":since2", $since);
            if ($checkoutFilter) $stmtA->bindValue(":ck", $checkoutFilter);
            $stmtA->execute();

            foreach ($stmtA->fetchAll(\PDO::FETCH_OBJ) as $v) {
                $fp = (int)$v->forma_pagamento;
                $eventos[] = [
                    "tipo"            => "aprovada",
                    "checkout_codigo" => $v->checkout_codigo ?? "",
                    "checkout_nome"   => $v->checkout_nome   ?? "Checkout",
                    "produto_nome"    => $v->produto_nome    ?? "",
                    "produtor_nome"   => $v->produtor_nome   ?? "",
                    "valor"           => $v->valor_bruto,
                    "meio_pagamento"  => $formas[$fp] ?? "outro",
                    "comprador"       => $v->comprador_nome  ?? null,
                    "ip"              => "",
                    "ts"              => $v->updated_at ?? $v->created_at,
                    "id"              => "c_" . $v->id,
                ];
            }

            usort($eventos, fn($a, $b) => strcmp($b["ts"], $a["ts"]));

            echo json_encode(["eventos" => $eventos, "ts" => date("c"), "erro" => null]);

        } catch (\Exception $e) {
            echo json_encode(["eventos" => [], "ts" => date("c"), "erro" => $e->getMessage()]);
        }
    }

    // Chamado pelo AppCheckout quando alguém abre uma página de checkout
    public static function logVisita(
        string $checkoutCodigo,
        int    $produtorId,
        string $checkoutNome  = "",
        string $produtoNome   = "",
        string $produtorNome  = ""
    ): void {
        if (!$produtorId || !$checkoutCodigo) return;

        $ip = !empty($_SERVER["HTTP_X_FORWARDED_FOR"])
            ? trim(explode(",", $_SERVER["HTTP_X_FORWARDED_FOR"])[0])
            : ($_SERVER["REMOTE_ADDR"] ?? "");

        $entry = json_encode([
            "checkout"      => $checkoutCodigo,
            "checkout_nome" => $checkoutNome,
            "produto_nome"  => $produtoNome,
            "produtor_nome" => $produtorNome,
            "usuario"       => $produtorId,
            "ip"            => $ip,
            "ts"            => date("Y-m-d H:i:s"),
        ], JSON_UNESCAPED_UNICODE);

        $dir = sys_get_temp_dir();

        // Log global (admin lê este)
        @file_put_contents($dir . "/" . self::LOG_ADMIN, $entry . "\n", FILE_APPEND | LOCK_EX);
        // Log do produtor (caso precise filtrar por produtor no futuro)
        @file_put_contents($dir . "/winpay_monitor_{$produtorId}.jsonl", $entry . "\n", FILE_APPEND | LOCK_EX);
    }

    private function lerVisitas(string $since, ?string $checkoutFilter): array
    {
        $logFile = sys_get_temp_dir() . "/" . self::LOG_ADMIN;
        if (!file_exists($logFile)) return [];

        $linhas = @file($logFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        if (!$linhas) return [];

        $visitas = [];
        foreach (array_reverse($linhas) as $linha) {
            $v = json_decode($linha, true);
            if (!$v || ($v["ts"] ?? "") <= $since) continue;
            if ($checkoutFilter && ($v["checkout"] ?? "") !== $checkoutFilter) continue;
            $visitas[] = $v;
            if (count($visitas) >= 300) break;
        }

        return $visitas;
    }
}
