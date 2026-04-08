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
            user_id     TEXT    PRIMARY KEY,
            expo_token  TEXT,
            pref_pix    INTEGER DEFAULT 1,
            pref_sale   INTEGER DEFAULT 1,
            pref_cb     INTEGER DEFAULT 0,
            pref_payout INTEGER DEFAULT 1,
            pref_recap  INTEGER DEFAULT 0,
            updated_at  DATETIME DEFAULT CURRENT_TIMESTAMP
        )");

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
        $stmt = $pdo->prepare("SELECT pref_pix, pref_sale, pref_cb, pref_payout, pref_recap FROM mobile_push WHERE user_id = :uid");
        $stmt->execute([":uid" => $userId]);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);

        return $row ? [
            "pix"    => (bool) $row["pref_pix"],
            "sale"   => (bool) $row["pref_sale"],
            "cb"     => (bool) $row["pref_cb"],
            "payout" => (bool) $row["pref_payout"],
            "recap"  => (bool) $row["pref_recap"],
        ] : [
            "pix" => true, "sale" => true, "cb" => false, "payout" => true, "recap" => false,
        ];
    }

    public static function salvarPreferencias(int $userId, array $prefs): void
    {
        $pdo  = self::pdo();
        $stmt = $pdo->prepare("
            INSERT INTO mobile_push (user_id, pref_pix, pref_sale, pref_cb, pref_payout, pref_recap, updated_at)
            VALUES (:uid, :pix, :sale, :cb, :payout, :recap, CURRENT_TIMESTAMP)
            ON CONFLICT(user_id) DO UPDATE SET
                pref_pix    = excluded.pref_pix,
                pref_sale   = excluded.pref_sale,
                pref_cb     = excluded.pref_cb,
                pref_payout = excluded.pref_payout,
                pref_recap  = excluded.pref_recap,
                updated_at  = CURRENT_TIMESTAMP
        ");
        $stmt->execute([
            ":uid"    => $userId,
            ":pix"    => (int) ($prefs["pix"]    ?? true),
            ":sale"   => (int) ($prefs["sale"]   ?? true),
            ":cb"     => (int) ($prefs["cb"]      ?? false),
            ":payout" => (int) ($prefs["payout"] ?? true),
            ":recap"  => (int) ($prefs["recap"]   ?? false),
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
            $stmt = $pdo->prepare("SELECT expo_token, pref_pix, pref_sale, pref_cb, pref_payout, pref_recap FROM mobile_push WHERE user_id = :uid");
            $stmt->execute([":uid" => $userId]);
            $row = $stmt->fetch(\PDO::FETCH_ASSOC);

            if (!$row || empty($row["expo_token"])) {
                return; // usuário sem token registrado
            }

            // Verifica preferência
            $prefMap = ["pix" => "pref_pix", "sale" => "pref_sale", "cb" => "pref_cb", "payout" => "pref_payout", "recap" => "pref_recap"];
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

            // Expo aceita até 100 tokens por chamada
            foreach (array_chunk($tokens, 100) as $chunk) {
                $messages = array_map(fn($t) => self::buildMessage($t, $titulo, $corpo, $extra), $chunk);
                self::sendExpoBatch($messages);
            }
        } catch (\Throwable $e) {
            error_log("[PushMobile] Erro ao enviar push broadcast: " . $e->getMessage());
        }
    }

    // ── Expo Push API ────────────────────────────────────────────────────────

    private static function buildMessage(string $token, string $titulo, string $corpo, array $data = []): array
    {
        return [
            "to"    => $token,
            "sound" => "default",
            "title" => $titulo,
            "body"  => $corpo,
            "data"  => $data,
        ];
    }

    private static function sendExpo(string $token, string $titulo, string $corpo, array $data = []): void
    {
        self::sendExpoBatch([self::buildMessage($token, $titulo, $corpo, $data)]);
    }

    private static function sendExpoBatch(array $messages): void
    {
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL            => "https://exp.host/--/api/v2/push/send",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 10,
            CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST  => "POST",
            CURLOPT_POSTFIELDS     => json_encode($messages),
            CURLOPT_HTTPHEADER     => [
                "Accept: application/json",
                "Accept-Encoding: gzip, deflate",
                "Content-Type: application/json",
            ],
        ]);

        $response = curl_exec($curl);
        $errno    = curl_errno($curl);
        curl_close($curl);

        if ($errno) {
            error_log("[PushMobile] cURL error {$errno} ao chamar Expo Push API");
        } else {
            error_log("[PushMobile] Expo response: " . $response);
        }
    }
}
