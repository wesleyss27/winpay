#!/bin/bash
set -e

# ─── Providers de AI — preencha as chaves que quiser usar ────────────────────
ANTHROPIC_API_KEY=""        # Claude  — sk-ant-...
OPENAI_API_KEY=""           # OpenAI  — sk-...
GEMINI_API_KEY=""           # Google  — AIza...
GROQ_API_KEY=""             # Groq    — gsk_...
OPENROUTER_API_KEY=""       # OpenRouter

# Modelo padrão do agente (provider/modelo)
# Exemplos: anthropic/claude-sonnet-4-5  |  openai/gpt-4o  |  groq/llama-3.3-70b-versatile
AGENT_PRIMARY_MODEL="anthropic/claude-sonnet-4-5"

# ─── Chatwoot ─────────────────────────────────────────────────────────────────
CHATWOOT_BASE_URL=""        # ex: https://chatwoot.suaempresa.com
CHATWOOT_API_TOKEN=""
CHATWOOT_ACCOUNT_ID=""
# ──────────────────────────────────────────────────────────────────────────────

if [[ -z "$CHATWOOT_BASE_URL" || -z "$CHATWOOT_API_TOKEN" || -z "$CHATWOOT_ACCOUNT_ID" ]]; then
  echo "Preencha pelo menos as variáveis do Chatwoot antes de rodar."
  exit 1
fi

if [[ -z "$ANTHROPIC_API_KEY" && -z "$OPENAI_API_KEY" && -z "$GEMINI_API_KEY" && -z "$GROQ_API_KEY" && -z "$OPENROUTER_API_KEY" ]]; then
  echo "Preencha pelo menos uma chave de AI provider."
  exit 1
fi

echo "▶ Gerando tokens..."
OPENCLAW_GATEWAY_TOKEN=$(openssl rand -hex 32)
OPENCLAW_WEBHOOK_SECRET=$(openssl rand -hex 32)

echo "▶ Escrevendo .env..."
cat > .env <<EOF
OPENCLAW_GATEWAY_TOKEN=${OPENCLAW_GATEWAY_TOKEN}
OPENCLAW_WEBHOOK_SECRET=${OPENCLAW_WEBHOOK_SECRET}
CHATWOOT_BASE_URL=${CHATWOOT_BASE_URL}
CHATWOOT_API_TOKEN=${CHATWOOT_API_TOKEN}
CHATWOOT_ACCOUNT_ID=${CHATWOOT_ACCOUNT_ID}
EOF

# Adiciona apenas as chaves preenchidas
[[ -n "$ANTHROPIC_API_KEY"   ]] && echo "ANTHROPIC_API_KEY=${ANTHROPIC_API_KEY}"     >> .env
[[ -n "$OPENAI_API_KEY"      ]] && echo "OPENAI_API_KEY=${OPENAI_API_KEY}"           >> .env
[[ -n "$GEMINI_API_KEY"      ]] && echo "GEMINI_API_KEY=${GEMINI_API_KEY}"           >> .env
[[ -n "$GROQ_API_KEY"        ]] && echo "GROQ_API_KEY=${GROQ_API_KEY}"               >> .env
[[ -n "$OPENROUTER_API_KEY"  ]] && echo "OPENROUTER_API_KEY=${OPENROUTER_API_KEY}"   >> .env

echo "▶ Garantindo permissões da pasta de config..."
mkdir -p ./data/config ./data/auth
chown -R 1000:1000 ./data/ || true

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

echo "▶ Configurando modelo padrão do agente..."
docker run --rm \
  -v "$(pwd)/data/config:/home/node/.openclaw" \
  -v "$(pwd)/data/auth:/home/node/.config/openclaw" \
  ghcr.io/openclaw/openclaw:latest \
  node dist/index.js config set --batch-json \
  "[{\"path\":\"agents.main.model\",\"value\":\"${AGENT_PRIMARY_MODEL}\"}]" 2>/dev/null || \
  echo "  (config agents.main.model não suportado nesta versão — configure pelo UI)"

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
echo "OPENCLAW_GATEWAY_TOKEN=${OPENCLAW_GATEWAY_TOKEN}" > ./data/config/.env
chown 1000:1000 ./data/config/.env || true

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
