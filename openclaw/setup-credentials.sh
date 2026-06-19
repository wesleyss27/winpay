#!/bin/bash
set -e

# ─── Variáveis — preencha antes de rodar ──────────────────────────────────────
ANTHROPIC_API_KEY=""
CHATWOOT_BASE_URL=""        # ex: https://chatwoot.suaempresa.com
CHATWOOT_API_TOKEN=""
CHATWOOT_ACCOUNT_ID=""
# ──────────────────────────────────────────────────────────────────────────────

if [[ -z "$ANTHROPIC_API_KEY" || -z "$CHATWOOT_BASE_URL" || -z "$CHATWOOT_API_TOKEN" || -z "$CHATWOOT_ACCOUNT_ID" ]]; then
  echo "Preencha as variáveis no topo do script antes de rodar."
  exit 1
fi

echo "▶ Gerando tokens..."
OPENCLAW_GATEWAY_TOKEN=$(openssl rand -hex 32)
OPENCLAW_WEBHOOK_SECRET=$(openssl rand -hex 32)

echo "▶ Escrevendo .env..."
cat > .env <<EOF
OPENCLAW_GATEWAY_TOKEN=${OPENCLAW_GATEWAY_TOKEN}
OPENCLAW_WEBHOOK_SECRET=${OPENCLAW_WEBHOOK_SECRET}
ANTHROPIC_API_KEY=${ANTHROPIC_API_KEY}
CHATWOOT_BASE_URL=${CHATWOOT_BASE_URL}
CHATWOOT_API_TOKEN=${CHATWOOT_API_TOKEN}
CHATWOOT_ACCOUNT_ID=${CHATWOOT_ACCOUNT_ID}
EOF

echo "▶ Garantindo permissões da pasta de config..."
mkdir -p data/config data/auth
chown -R 1000:1000 data/

echo "▶ Configurando gateway.mode e gateway.bind..."
docker run --rm \
  -v "$(pwd)/data/config:/home/node/.openclaw" \
  -v "$(pwd)/data/auth:/home/node/.config/openclaw" \
  ghcr.io/openclaw/openclaw:latest \
  node dist/index.js config set --batch-json \
  '[{"path":"gateway.mode","value":"local"},{"path":"gateway.bind","value":"lan"}]'

echo "▶ Configurando origens permitidas no Control UI..."
docker run --rm \
  -v "$(pwd)/data/config:/home/node/.openclaw" \
  -v "$(pwd)/data/auth:/home/node/.config/openclaw" \
  ghcr.io/openclaw/openclaw:latest \
  node dist/index.js config set --batch-json \
  '[{"path":"gateway.controlUi.allowedOrigins","value":["https://openclaw.winpay.digital"]}]'

echo "▶ Ativando plugin Webhooks..."
docker run --rm \
  -v "$(pwd)/data/config:/home/node/.openclaw" \
  -v "$(pwd)/data/auth:/home/node/.config/openclaw" \
  ghcr.io/openclaw/openclaw:latest \
  node dist/index.js config set --batch-json \
  '[
    {"path":"plugins.entries.webhooks.enabled","value":true},
    {"path":"plugins.entries.webhooks.config.routes.chatwoot.sessionKey","value":"agent:main:main"},
    {"path":"plugins.entries.webhooks.config.routes.chatwoot.secret.source","value":"env"},
    {"path":"plugins.entries.webhooks.config.routes.chatwoot.secret.provider","value":"default"},
    {"path":"plugins.entries.webhooks.config.routes.chatwoot.secret.id","value":"OPENCLAW_WEBHOOK_SECRET"},
    {"path":"plugins.entries.webhooks.config.routes.chatwoot.description","value":"Chatwoot bridge"}
  ]'

echo "▶ Escrevendo token no config dir..."
echo "OPENCLAW_GATEWAY_TOKEN=${OPENCLAW_GATEWAY_TOKEN}" > data/config/.env
chown 1000:1000 data/config/.env

echo "▶ Subindo serviços..."
docker-compose down 2>/dev/null || true
docker-compose up -d --build

echo ""
echo "✔ Pronto!"
echo ""
echo "  Control UI : https://openclaw.winpay.digital"
echo "  Token      : ${OPENCLAW_GATEWAY_TOKEN}"
echo "  Webhook URL: https://openclaw.winpay.digital/webhook"
echo "  Webhook Secret para o Chatwoot: ${OPENCLAW_WEBHOOK_SECRET}"
echo ""
echo "  Aprovação de dispositivo (após conectar na UI):"
echo "  docker-compose exec openclaw-gateway node dist/index.js devices approve <ID>"
