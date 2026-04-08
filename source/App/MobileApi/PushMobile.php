<?php

namespace Source\App\MobileApi;

/**
 * PushMobile — Gerencia tokens Expo e envio de push notifications para o app mobile.
 *
 * Tabela SQLite `mobile_push`:
 *   user_id    TEXT PRIMARY KEY
 *   expo_token TEXT
 *   pref_pix   INTEGER DEFAULT 1
 *   pref_sale  INTEGER DEFAULT 1
 *   pref_cb    INTEGER DEFAULT 0
 *   pref_payout INTEGER DEFAULT 1
 *   pref_recap  INTEGER DEFAULT 0
 *   updated_at  DATETIME DEFAULT CURRENT_TIMESTAMP
 */
class PushMobile
{
    private const DB_PATH = __DIR__ . "/../../../db.sqlite";

    // ── Banco ───────────────────────────────────────────────────────────────

    private static function pdo(): \PDO
    {
        $pdo = new \PDO("sqlite:" . self::DB_PATH);
        $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

        $pdo->exec("CREATE TABLE IF NOT EXISTS mobile_push (
            user_id      TEXT    PRIMARY KEY,
            expo_token   TEXT,
            pref_pix     INTEGER DEFAULT 1,
            pref_pix_gen INTEGER DEFAULT 0,
            pref_sale    INTEGER DEFAULT 1,
            pref_cb      INTEGER DEFAULT 0,
            pref_payout  INTEGER DEFAULT 1,
            pref_recap   INTEGER DEFAULT 0,
            updated_at   DATETIME DEFAULT CURRENT_TIMESTAMP
        )");
        // migração: adiciona coluna se ainda não existe
        try { $pdo->exec("ALTER TABLE mobile_push ADD COLUMN pref_pix_gen INTEGER DEFAULT 0"); } catch (\Throwable $e) {}

        return $pdo;
    }

    // ── Token ────────────────────────────────────────────────────────────────

    public static function registrarToken(int $userId, string $expoToken): void
    {
        $pdo  = self::pdo();
        $stmt = $pdo->prepare("
            INSERT INTO mobile_push (user_id, expo_token, updated_at)
            VALUES (:uid, :token, CURRENT_TIMESTAMP)
            ON CONFLICT(user_id) DO UPDATE SET
                expo_token = excluded.expo_token,
                updated_at = CURRENT_TIMESTAMP
        ");
        $stmt->execute([":uid" => $userId, ":token" => $expoToken]);
    }

    // ── Preferências ─────────────────────────────────────────────────────────

    public static function buscarPreferencias(int $userId): array
    {
        $pdo  = self::pdo();
        $stmt = $pdo->prepare("SELECT pref_pix, pref_pix_gen, pref_sale, pref_cb, pref_payout, pref_recap FROM mobile_push WHERE user_id = :uid");
        $stmt->execute([":uid" => $userId]);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);

        return $row ? [
            "pix"     => (bool) $row["pref_pix"],
            "pix_gen" => (bool) $row["pref_pix_gen"],
            "sale"    => (bool) $row["pref_sale"],
            "cb"      => (bool) $row["pref_cb"],
            "payout"  => (bool) $row["pref_payout"],
            "recap"   => (bool) $row["pref_recap"],
        ] : [
            "pix" => true, "pix_gen" => false, "sale" => true, "cb" => false, "payout" => true, "recap" => false,
        ];
    }

    public static function salvarPreferencias(int $userId, array $prefs): void
    {
        $pdo  = self::pdo();
        $stmt = $pdo->prepare("
            INSERT INTO mobile_push (user_id, pref_pix, pref_pix_gen, pref_sale, pref_cb, pref_payout, pref_recap, updated_at)
            VALUES (:uid, :pix, :pix_gen, :sale, :cb, :payout, :recap, CURRENT_TIMESTAMP)
            ON CONFLICT(user_id) DO UPDATE SET
                pref_pix     = excluded.pref_pix,
                pref_pix_gen = excluded.pref_pix_gen,
                pref_sale    = excluded.pref_sale,
                pref_cb      = excluded.pref_cb,
                pref_payout  = excluded.pref_payout,
                pref_recap   = excluded.pref_recap,
                updated_at   = CURRENT_TIMESTAMP
        ");
        $stmt->execute([
            ":uid"     => $userId,
            ":pix"     => (int) ($prefs["pix"]     ?? true),
            ":pix_gen" => (int) ($prefs["pix_gen"] ?? false),
            ":sale"    => (int) ($prefs["sale"]    ?? true),
            ":cb"      => (int) ($prefs["cb"]      ?? false),
            ":payout"  => (int) ($prefs["payout"]  ?? true),
            ":recap"   => (int) ($prefs["recap"]   ?? false),
        ]);
    }

    // ── Envio ────────────────────────────────────────────────────────────────

    /**
     * Envia push para um usuário se ele tiver o token registrado e a preferência habilitada.
     *
     * @param int    $userId  ID do usuário (produtor/afiliado)
     * @param string $titulo  Título da notificação
     * @param string $corpo   Corpo da notificação
     * @param string $tipo    Chave da preferência: pix | sale | cb | payout | recap
     * @param array  $extra   Dados extras enviados no payload (opcional)
     */
    public static function enviar(int $userId, string $titulo, string $corpo, string $tipo, array $extra = []): void
    {
        try {
            $pdo  = self::pdo();
            $stmt = $pdo->prepare("SELECT expo_token, pref_pix, pref_pix_gen, pref_sale, pref_cb, pref_payout, pref_recap FROM mobile_push WHERE user_id = :uid");
            $stmt->execute([":uid" => $userId]);
            $row = $stmt->fetch(\PDO::FETCH_ASSOC);

            if (!$row || empty($row["expo_token"])) {
                return; // usuário sem token registrado
            }

            // Verifica preferência
            $prefMap = ["pix" => "pref_pix", "pix_gen" => "pref_pix_gen", "sale" => "pref_sale", "cb" => "pref_cb", "payout" => "pref_payout", "recap" => "pref_recap"];
            $prefCol = $prefMap[$tipo] ?? null;

            if ($prefCol && isset($row[$prefCol]) && !(int) $row[$prefCol]) {
                return; // usuário desativou este tipo
            }

            self::sendExpo($row["expo_token"], $titulo, $corpo, array_merge(["type" => $tipo], $extra));
        } catch (\Throwable $e) {
            error_log("[PushMobile] Erro ao enviar push para user {$userId}: " . $e->getMessage());
        }
    }

    /**
     * Envia push para múltiplos usuários (ex.: resumo diário).
     */
    public static function enviarParaTodos(string $titulo, string $corpo, string $tipo, array $extra = []): void
    {
        try {
            $pdo  = self::pdo();
            $prefCol = ["pix" => "pref_pix", "sale" => "pref_sale", "cb" => "pref_cb", "payout" => "pref_payout", "recap" => "pref_recap"][$tipo] ?? "pref_sale";

            $stmt = $pdo->query("SELECT expo_token FROM mobile_push WHERE expo_token IS NOT NULL AND {$prefCol} = 1");
            $tokens = $stmt->fetchAll(\PDO::FETCH_COLUMN);

            if (!$tokens) {
                return;
            }

            foreach ($tokens as $token) {
                self::sendExpo($token, $titulo, $corpo, array_merge(["type" => $tipo], $extra));
            }
        } catch (\Throwable $e) {
            error_log("[PushMobile] Erro ao enviar push broadcast: " . $e->getMessage());
        }
    }

    // ── FCM (Firebase Cloud Messaging) ──────────────────────────────────────
    // Usa o mesmo token FCM nativo obtido via getDevicePushTokenAsync() no app.
    // A chave do servidor FCM fica em configuracao_checkout id=4 -> chave_producao.

    private static function fcmServerKey(): string
    {
        try {
            // Mesmo campo usado pelo pushGoogle() existente no sistema
            require_once __DIR__ . "/../../../vendor/autoload.php";
            $config = (new \Source\Models\Configuracoes\ConfiguracaoCheckout())->findById(4);
            return $config->chave_producao ?? "";
        } catch (\Throwable $e) {
            error_log("[PushMobile] Erro ao buscar chave FCM: " . $e->getMessage());
            return "";
        }
    }

    private static function sendExpo(string $token, string $titulo, string $corpo, array $data = []): void
    {
        $serverKey = self::fcmServerKey();
        if (!$serverKey) {
            error_log("[PushMobile] Chave FCM não configurada (configuracao_checkout id=4)");
            return;
        }

        $payload = json_encode([
            "to"           => $token,
            "notification" => [
                "title" => $titulo,
                "body"  => $corpo,
                "sound" => "default",
            ],
            "data"         => $data,
            "priority"     => "high",
        ]);

        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL            => "https://fcm.googleapis.com/fcm/send",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 10,
            CURLOPT_CUSTOMREQUEST  => "POST",
            CURLOPT_POSTFIELDS     => $payload,
            CURLOPT_HTTPHEADER     => [
                "Authorization: key={$serverKey}",
                "Content-Type: application/json",
            ],
        ]);

        $response = curl_exec($curl);
        $errno    = curl_errno($curl);
        curl_close($curl);

        if ($errno) {
            error_log("[PushMobile] cURL error {$errno} FCM");
        } else {
            error_log("[PushMobile] FCM response: " . $response);
        }
    }
}
