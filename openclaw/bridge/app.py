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

_seen_messages: set = set()
_seen_lock = threading.Lock()
_conv_locks: dict = {}
_conv_locks_mutex = threading.Lock()


def get_conv_lock(conv_id: int) -> threading.Lock:
    with _conv_locks_mutex:
        if conv_id not in _conv_locks:
            _conv_locks[conv_id] = threading.Lock()
        return _conv_locks[conv_id]


def openclaw_agent(conv_id: int, message: str) -> tuple:
    session_key = f"agent:main:conv_{conv_id}"
    cmd = [
        "docker", "exec", OPENCLAW_CONTAINER,
        "openclaw", "agent",
        "--local",
        "--session-key", session_key,
        "--message", message,
        "--json",
    ]
    result = subprocess.run(cmd, capture_output=True, text=True, timeout=300)
    if result.returncode != 0:
        raise RuntimeError(f"openclaw agent falhou: {result.stderr}")

    data = json.loads(result.stdout)
    payloads = data.get("payloads", [])
    text = ""
    media_url = None
    if payloads:
        text = payloads[0].get("text", "").strip()
        media_url = payloads[0].get("mediaUrl")
    if not text:
        text = data.get("meta", {}).get("finalAssistantVisibleText", "Sem resposta.")
    return text, media_url


def chatwoot_reply(conv_id: int, content: str, media_url: str = None):
    url = f"{CHATWOOT_URL}/api/v1/accounts/{CHATWOOT_ACCOUNT}/conversations/{conv_id}/messages"
    headers = {"api_access_token": CHATWOOT_TOKEN}

    if media_url:
        img_data = requests.get(media_url, timeout=30).content
        requests.post(url, headers=headers, files={
            "attachments[]": ("image.png", img_data, "image/png"),
        }, data={"message_type": "outgoing", "content": content or ""})
    else:
        requests.post(url, json={"content": content, "message_type": "outgoing"}, headers=headers, timeout=10)


def process_message(conv_id: int, content: str):
    lock = get_conv_lock(conv_id)
    if not lock.acquire(blocking=False):
        log.warning(f"[conv:{conv_id}] Já processando — ignorando duplicata")
        return
    try:
        log.info(f"[conv:{conv_id}] Processando: {content}")
        reply, media_url = openclaw_agent(conv_id, content)
        log.info(f"[conv:{conv_id}] Resposta: {reply} | media: {media_url}")
        chatwoot_reply(conv_id, reply, media_url)
    except Exception as e:
        log.error(f"[conv:{conv_id}] Erro: {e}")
    finally:
        lock.release()


@app.route("/webhook", methods=["POST"])
def webhook():
    data = request.get_json(silent=True) or {}

    message_type = data.get("message_type")
    log.info(f"Evento: event={data.get('event')} message_type={message_type}")

    if data.get("event") != "message_created":
        return jsonify({"ok": True}), 200

    if message_type not in ("incoming", 0):
        return jsonify({"ok": True}), 200

    msg_id = data.get("id")
    with _seen_lock:
        if msg_id and msg_id in _seen_messages:
            log.info(f"Mensagem duplicada ignorada: id={msg_id}")
            return jsonify({"ok": True}), 200
        if msg_id:
            _seen_messages.add(msg_id)

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
