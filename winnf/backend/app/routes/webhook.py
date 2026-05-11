import logging

from flask import Blueprint, jsonify, request

from app.extensions import db
from app.models.nota_fiscal import NotaFiscal
from app.services.notazz import STATUS_MAP

webhook_bp = Blueprint("webhook", __name__)
logger = logging.getLogger(__name__)


@webhook_bp.route("/notazz", methods=["POST"])
def notazz_postback():
    """
    Receive status update postbacks from Notazz.

    Notazz sends a JSON (or form) body with at minimum:
      - external_id or EXTERNAL_ID
      - status or STATUS
      - and optionally: id, pdf, xml, numero, chave, serie, protocolo, motivo
    """
    # Accept both JSON and form-encoded payloads
    if request.is_json:
        data = request.get_json(silent=True) or {}
    else:
        data = request.form.to_dict()

    logger.info("Notazz postback received: %s", data)

    # Normalise keys (Notazz may send uppercase or lowercase)
    external_id = data.get("external_id") or data.get("EXTERNAL_ID") or ""
    notazz_id = data.get("id") or data.get("ID") or ""
    raw_status = data.get("status") or data.get("STATUS") or ""

    if not external_id and not notazz_id:
        logger.warning("Notazz postback missing identifier fields")
        return jsonify({"success": False, "message": "Identificador ausente."}), 400

    # Find the nota
    nota = None
    if external_id:
        nota = NotaFiscal.query.filter_by(external_id=external_id).first()
    if nota is None and notazz_id:
        nota = NotaFiscal.query.filter_by(notazz_id=str(notazz_id)).first()

    if nota is None:
        logger.warning(
            "Notazz postback: nota not found for external_id=%s notazz_id=%s",
            external_id,
            notazz_id,
        )
        # Return 200 so Notazz does not keep retrying for unknown IDs
        return jsonify({"success": True, "message": "Nota não encontrada (ignorado)."}), 200

    # Update status
    new_status = STATUS_MAP.get(raw_status)
    if new_status:
        nota.status = new_status
    nota.motivo_status = data.get("motivo") or data.get("mensagem") or nota.motivo_status

    # Update document fields if provided
    if data.get("pdf") or data.get("url_pdf"):
        nota.pdf_url = data.get("pdf") or data.get("url_pdf")
    if data.get("xml") or data.get("url_xml"):
        nota.xml_url = data.get("xml") or data.get("url_xml")
    if data.get("numero"):
        nota.numero = str(data["numero"])
    if data.get("chave") or data.get("chaveNFe"):
        nota.chave = data.get("chave") or data.get("chaveNFe")
    if data.get("serie"):
        nota.serie = str(data["serie"])
    if data.get("protocolo") or data.get("protocolo_autorizacao"):
        nota.protocolo_autorizacao = data.get("protocolo") or data.get("protocolo_autorizacao")
    if notazz_id and not nota.notazz_id:
        nota.notazz_id = str(notazz_id)

    db.session.commit()
    logger.info("Nota %s updated via postback to status=%s", nota.id, nota.status)

    return jsonify({"success": True, "message": "Status atualizado com sucesso."}), 200
