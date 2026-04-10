<?php

namespace Source\App\MobileApi;

/**
 * PushMobile — Gerencia tokens FCM e envio de push notifications para o app mobile.
 * Usa FCM HTTP v1 API com autenticação OAuth2 via Service Account.
 *
 * Tabela SQLite `mobile_push`:
 *   user_id      TEXT PRIMARY KEY
 *   expo_token   TEXT
 *   pref_pix     INTEGER DEFAULT 1
 *   pref_pix_gen INTEGER DEFAULT 0
 *   pref_sale    INTEGER DEFAULT 1
 *   pref_cb      INTEGER DEFAULT 0
 *   pref_payout  INTEGER DEFAULT 1
 *   pref_recap   INTEGER DEFAULT 0
 *   updated_at   DATETIME DEFAULT CURRENT_TIMESTAMP
 */
class PushMobile
{
    private const DB_PATH          = __DIR__ . "/../../../db.sqlite";
    private const SA_PATH          = __DIR__ . "/firebase-service-account.json";
    private const FCM_PROJECT_ID   = "winpay-app";
    private const FCM_V1_URL       = "https://fcm.googleapis.com/v1/projects/" . self::FCM_PROJECT_ID . "/messages:send";

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

    public static function enviar(int $userId, string $titulo, string $corpo, string $tipo, array $extra = []): void
    {
        try {
            $pdo  = self::pdo();
            $stmt = $pdo->prepare("SELECT expo_token, pref_pix, pref_pix_gen, pref_sale, pref_cb, pref_payout, pref_recap FROM mobile_push WHERE user_id = :uid");
            $stmt->execute([":uid" => $userId]);
            $row = $stmt->fetch(\PDO::FETCH_ASSOC);

            if (!$row || empty($row["expo_token"])) {
                return;
            }

            $prefMap = ["pix" => "pref_pix", "pix_gen" => "pref_pix_gen", "sale" => "pref_sale", "cb" => "pref_cb", "payout" => "pref_payout", "recap" => "pref_recap"];
            $prefCol = $prefMap[$tipo] ?? null;

            if ($prefCol && isset($row[$prefCol]) && !(int) $row[$prefCol]) {
                return;
            }

            self::sendFcmV1($row["expo_token"], $titulo, $corpo, array_merge(["type" => $tipo], $extra));
        } catch (\Throwable $e) {
            error_log("[PushMobile] Erro ao enviar push para user {$userId}: " . $e->getMessage());
        }
    }

    public static function enviarParaTodos(string $titulo, string $corpo, string $tipo, array $extra = []): void
    {
        try {
            $pdo     = self::pdo();
            $prefCol = ["pix" => "pref_pix", "pix_gen" => "pref_pix_gen", "sale" => "pref_sale", "cb" => "pref_cb", "payout" => "pref_payout", "recap" => "pref_recap"][$tipo] ?? "pref_sale";

            $stmt   = $pdo->query("SELECT expo_token FROM mobile_push WHERE expo_token IS NOT NULL AND {$prefCol} = 1");
            $tokens = $stmt->fetchAll(\PDO::FETCH_COLUMN);

            foreach ($tokens as $token) {
                self::sendFcmV1($token, $titulo, $corpo, array_merge(["type" => $tipo], $extra));
            }
        } catch (\Throwable $e) {
            error_log("[PushMobile] Erro ao enviar push broadcast: " . $e->getMessage());
        }
    }

    // ── FCM HTTP v1 API ──────────────────────────────────────────────────────

    private static function sendFcmV1(string $token, string $titulo, string $corpo, array $data = []): void
    {
        $accessToken = self::getAccessToken();
        if (!$accessToken) {
            error_log("[PushMobile] Não foi possível obter access token FCM");
            return;
        }

        // Converte data para strings (FCM v1 exige string nos data fields)
        $dataStr = [];
        foreach ($data as $k => $v) {
            $dataStr[(string)$k] = (string)$v;
        }

        $payload = json_encode([
            "message" => [
                "token"        => $token,
                "notification" => [
                    "title" => $titulo,
                    "body"  => $corpo,
                ],
                "data"    => $dataStr,
                "android" => [
                    "priority"     => "high",
                    "notification" => [
                        "sound"        => "default",
                        "channel_id"   => "default",
                    ],
                ],
            ],
        ]);

        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL            => self::FCM_V1_URL,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 10,
            CURLOPT_CUSTOMREQUEST  => "POST",
            CURLOPT_POSTFIELDS     => $payload,
            CURLOPT_HTTPHEADER     => [
                "Authorization: Bearer {$accessToken}",
                "Content-Type: application/json",
            ],
        ]);

        $response = curl_exec($curl);
        $errno    = curl_errno($curl);
        curl_close($curl);

        if ($errno) {
            error_log("[PushMobile] cURL error {$errno} ao enviar FCM v1");
        } else {
            error_log("[PushMobile] FCM v1 response: " . $response);
        }
    }

    private static function tokenCachePath(): string
    {
        return sys_get_temp_dir() . "/winpay_fcm_token.json";
    }

    /**
     * Gera (ou lê do cache) um OAuth2 access token para a FCM v1 API
     * usando o Service Account JSON.
     */
    private static function getAccessToken(): string
    {
        $cachePath = self::tokenCachePath();

        if (file_exists($cachePath)) {
            $cache = json_decode(file_get_contents($cachePath), true);
            if (!empty($cache["token"]) && !empty($cache["expires"]) && time() < $cache["expires"]) {
                return $cache["token"];
            }
        }

        // Lê service account
        if (!file_exists(self::SA_PATH)) {
            error_log("[PushMobile] Service account JSON não encontrado: " . self::SA_PATH);
            return "";
        }

        $sa = json_decode(file_get_contents(self::SA_PATH), true);
        if (empty($sa["private_key"]) || empty($sa["client_email"])) {
            error_log("[PushMobile] Service account JSON inválido");
            return "";
        }

        // Monta JWT
        $now    = time();
        $header  = self::base64url(json_encode(["alg" => "RS256", "typ" => "JWT"]));
        $payload = self::base64url(json_encode([
            "iss"   => $sa["client_email"],
            "scope" => "https://www.googleapis.com/auth/firebase.messaging",
            "aud"   => "https://oauth2.googleapis.com/token",
            "iat"   => $now,
            "exp"   => $now + 3600,
        ]));

        $sigInput = "{$header}.{$payload}";
        $privKey  = openssl_pkey_get_private($sa["private_key"]);
        if (!$privKey) {
            error_log("[PushMobile] Não foi possível carregar chave privada do service account");
            return "";
        }

        openssl_sign($sigInput, $signature, $privKey, OPENSSL_ALGO_SHA256);
        $jwt = "{$sigInput}." . self::base64url($signature);

        // Troca JWT por access token
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL            => "https://oauth2.googleapis.com/token",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 10,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => http_build_query([
                "grant_type" => "urn:ietf:params:oauth:grant-type:jwt-bearer",
                "assertion"  => $jwt,
            ]),
            CURLOPT_HTTPHEADER => ["Content-Type: application/x-www-form-urlencoded"],
        ]);

        $response = curl_exec($curl);
        curl_close($curl);

        $result = json_decode($response, true);
        $token  = $result["access_token"] ?? "";

        if ($token) {
            @file_put_contents(self::tokenCachePath(), json_encode([
                "token"   => $token,
                "expires" => $now + 3000,
            ]));
        } else {
            error_log("[PushMobile] Falha ao obter access token: " . $response);
        }

        return $token;
    }

    private static function base64url(string $data): string
    {
        return rtrim(strtr(base64_encode($data), "+/", "-_"), "=");
    }
}
