import os
import time
import threading
import logging
import requests
from flask import Flask, request, jsonify

logging.basicConfig(level=logging.INFO, format="%(asctime)s [%(levelname)s] %(message)s")
log = logging.getLogger("bridge")

app = Flask(__name__)

OPENCLAW_URL     = os.environ.get("OPENCLAW_GATEWAY_URL", "http://openclaw-gateway:18789")
OPENCLAW_SECRET  = os.environ.get("OPENCLAW_WEBHOOK_SECRET", "")
CHATWOOT_URL     = os.environ.get("CHATWOOT_BASE_URL", "")
CHATWOOT_TOKEN   = os.environ.get("CHATWOOT_API_TOKEN", "")
CHATWOOT_ACCOUNT = os.environ.get("CHATWOOT_ACCOUNT_ID", "")

POLL_INTERVAL = 2
POLL_TIMEOUT  = 60


def openclaw_post(payload: dict) -> dict:
    resp = requests.post(
        f"{OPENCLAW_URL}/plugins/webhooks/chatwoot",
        json=payload,
        headers={"Authorization": f"Bearer {OPENCLAW_SECRET}"},
        timeout=10,
    )
    resp.raise_for_status()
    return resp.json()


def chatwoot_reply(conv_id: int, content: str):
    requests.post(
        f"{CHATWOOT_URL}/api/v1/accounts/{CHATWOOT_ACCOUNT}/conversations/{conv_id}/messages",
        json={"content": content, "message_type": "outgoing"},
        headers={"api_access_token": CHATWOOT_TOKEN},
        timeout=10,
    )


def process_message(conv_id: int, goal: str):
    try:
        log.info(f"[conv:{conv_id}] Criando flow: {goal}")
        result = openclaw_post({"action": "create_flow", "goal": goal})
        log.info(f"[conv:{conv_id}] Resposta create_flow: {result}")
        flow_id = result.get("result", {}).get("flowId")
        if not flow_id:
            log.error(f"[conv:{conv_id}] flowId não retornado")
            return

        deadline = time.time() + POLL_TIMEOUT
        while time.time() < deadline:
            time.sleep(POLL_INTERVAL)
            poll = openclaw_post({"action": "get_flow", "flowId": flow_id})
            flow = poll.get("result", {})
            status = flow.get("status", "")
            log.info(f"[conv:{conv_id}] Status flow: {status}")

            if status in ("done", "resolved", "finished"):
                reply = (
                    flow.get("output")
                    or flow.get("summary")
                    or flow.get("result")
                    or "Sem resposta."
                )
                log.info(f"[conv:{conv_id}] Respondendo no Chatwoot: {reply}")
                chatwoot_reply(conv_id, str(reply))
                return

            if status in ("failed", "cancelled"):
                chatwoot_reply(conv_id, "Ocorreu um erro ao processar sua mensagem.")
                return

        log.warning(f"[conv:{conv_id}] Timeout aguardando resposta do OpenClaw")

    except Exception as e:
        log.error(f"[conv:{conv_id}] Erro: {e}")


@app.route("/webhook", methods=["POST"])
def webhook():
    data = request.get_json(silent=True) or {}

    if data.get("event") != "message_created":
        return jsonify({"ok": True}), 200

    msg = data.get("message", {})
    if msg.get("message_type") not in ("incoming", 0):
        return jsonify({"ok": True}), 200

    conv_id = data.get("conversation", {}).get("id")
    content = msg.get("content", "").strip()
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
