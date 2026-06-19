import os
import json
import threading
import logging
import subprocess
import requests
from flask import Flask, request, jsonify

logging.basicConfig(level=logging.INFO, format="%(asctime)s [%(levelname)s] %(message)s")
log = logging.getLogger("bridge")

app = Flask(__name__)

OPENCLAW_CONTAINER = os.environ.get("OPENCLAW_CONTAINER", "openclaw-gateway")
CHATWOOT_URL       = os.environ.get("CHATWOOT_BASE_URL", "")
CHATWOOT_TOKEN     = os.environ.get("CHATWOOT_API_TOKEN", "")
CHATWOOT_ACCOUNT   = os.environ.get("CHATWOOT_ACCOUNT_ID", "")


def openclaw_agent(conv_id: int, message: str) -> str:
    session_key = f"agent:main:conv_{conv_id}"
    cmd = [
        "docker", "exec", OPENCLAW_CONTAINER,
        "openclaw", "agent",
        "--local",
        "--session-key", session_key,
        "--message", message,
        "--json",
    ]
    result = subprocess.run(cmd, capture_output=True, text=True, timeout=120)
    if result.returncode != 0:
        raise RuntimeError(f"openclaw agent falhou: {result.stderr}")

    data = json.loads(result.stdout)
    payloads = data.get("payloads", [])
    if payloads:
        return payloads[0].get("text", "").strip()
    return data.get("meta", {}).get("finalAssistantVisibleText", "Sem resposta.")


def chatwoot_reply(conv_id: int, content: str):
    requests.post(
        f"{CHATWOOT_URL}/api/v1/accounts/{CHATWOOT_ACCOUNT}/conversations/{conv_id}/messages",
        json={"content": content, "message_type": "outgoing"},
        headers={"api_access_token": CHATWOOT_TOKEN},
        timeout=10,
    )


def process_message(conv_id: int, content: str):
    try:
        log.info(f"[conv:{conv_id}] Processando: {content}")
        reply = openclaw_agent(conv_id, content)
        log.info(f"[conv:{conv_id}] Resposta: {reply}")
        chatwoot_reply(conv_id, reply)
    except Exception as e:
        log.error(f"[conv:{conv_id}] Erro: {e}")


@app.route("/webhook", methods=["POST"])
def webhook():
    data = request.get_json(silent=True) or {}

    message_type = data.get("message_type")
    log.info(f"Evento: event={data.get('event')} message_type={message_type}")

    if data.get("event") != "message_created":
        return jsonify({"ok": True}), 200

    if message_type not in ("incoming", 0):
        return jsonify({"ok": True}), 200

    conv_id = data.get("conversation", {}).get("id")
    content = data.get("content", "").strip()
    contact_name = data.get("contact", {}).get("name", "Usuário")

    if not conv_id or not content:
        return jsonify({"ok": True}), 200

    goal = f"[{contact_name}]: {content}"
    thread = threading.Thread(target=process_message, args=(conv_id, goal), daemon=True)
    thread.start()

    return jsonify({"ok": True}), 200


@app.route("/health", methods=["GET"])
def health():
    return jsonify({"ok": True}), 200
