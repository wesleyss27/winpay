#!/bin/bash
set -e

CONFIG_FILE="${HOME}/.openclaw/openclaw.json"

if [ ! -f "$CONFIG_FILE" ]; then
  echo "▶ Configurando OpenClaw pela primeira vez..."

  openclaw config set --batch-json '[
    {"path":"gateway.mode","value":"local"},
    {"path":"gateway.bind","value":"lan"},
    {"path":"gateway.webLoginProvider","value":"https://openclaw.winpay.digital"},
    {"path":"gateway.controlUi.allowedOrigins","value":["https://openclaw.winpay.digital"]}
  ]'

  if [ -n "$OPENCLAW_GATEWAY_TOKEN" ]; then
    echo "OPENCLAW_GATEWAY_TOKEN=${OPENCLAW_GATEWAY_TOKEN}" > "${HOME}/.openclaw/.env"
  fi
fi

echo "▶ Iniciando OpenClaw gateway..."
exec openclaw gateway run
