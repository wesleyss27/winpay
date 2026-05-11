import logging
from datetime import date

from flask import Blueprint, jsonify, request
from flask_jwt_extended import get_jwt_identity, jwt_required
from sqlalchemy import or_

from app.extensions import db
from app.models.nota_fiscal import NotaFiscal
from app.models.notazz_integracao import NotazzIntegracao
from app.models.user import User
from app.models.cliente import Cliente
from app.models.produto import Produto
from app.services.notazz import NotazzService
from app.utils.decorators import paginate_query

notas_bp = Blueprint("notas", __name__)
logger = logging.getLogger(__name__)


def _get_current_user() -> User:
    identity = get_jwt_identity()
    return db.session.get(User, int(identity))


def _resolve_produtor(user: User) -> User | None:
    """Return the produtor whose API key should be used."""
    if user.role == "assistente":
        return db.session.get(User, user.produtor_id)
    if user.role == "produtor":
        return user
    return None  # admin cannot emit notas directly


def _apply_nota_filters(query, user: User):
    """Apply role-based visibility to a NotaFiscal query."""
    role = user.role
    if role == "admin":
        return query
    if role == "produtor":
        return query.filter(NotaFiscal.produtor_id == user.id)
    if role == "assistente":
        return query.filter(NotaFiscal.emitente_id == user.id)
    return query.filter(False)


def _parse_competencia(value: str) -> date | None:
    """Parse YYYY-MM-DD string to date, return None on failure."""
    try:
        return date.fromisoformat(value)
    except (ValueError, TypeError):
        return None


def _update_nota_from_notazz_response(nota: NotaFiscal, data: dict) -> None:
    """
    Extract fields from a Notazz consult/emit response and update the nota.
    Field names may vary; we try common patterns.
    """
    nota.notazz_id = data.get("id") or data.get("nfse_id") or nota.notazz_id
    nota.pdf_url = data.get("pdf") or data.get("url_pdf") or nota.pdf_url
    nota.xml_url = data.get("xml") or data.get("url_xml") or nota.xml_url
    nota.numero = data.get("numero") or data.get("nfse_numero") or nota.numero
    nota.chave = data.get("chave") or data.get("chaveNFe") or nota.chave
    nota.serie = data.get("serie") or nota.serie
    nota.protocolo_autorizacao = (
        data.get("protocolo") or data.get("protocolo_autorizacao") or nota.protocolo_autorizacao
    )

    raw_status = data.get("status") or ""
    from app.services.notazz import STATUS_MAP
    if raw_status in STATUS_MAP:
        nota.status = STATUS_MAP[raw_status]

    nota.motivo_status = data.get("motivo") or data.get("mensagem") or nota.motivo_status


def _upsert_cliente(produtor_id: int, dest: dict, nome: str, cpf_cnpj: str, dest_tipo: str) -> None:
    """Insert or update cliente record after a successful nota emission."""
    cliente = Cliente.query.filter_by(produtor_id=produtor_id, cpf_cnpj=cpf_cnpj).first()
    if cliente is None:
        cliente = Cliente(produtor_id=produtor_id, cpf_cnpj=cpf_cnpj, nome=nome, tipo=dest_tipo)
        db.session.add(cliente)
    else:
        cliente.nome = nome
        cliente.tipo = dest_tipo
    cliente.email = dest.get("email") or cliente.email
    cliente.telefone = dest.get("telefone") or cliente.telefone
    cliente.rua = dest.get("rua") or cliente.rua
    cliente.numero = dest.get("numero") or cliente.numero
    cliente.complemento = dest.get("complemento") or cliente.complemento
    cliente.bairro = dest.get("bairro") or cliente.bairro
    cliente.cidade = dest.get("cidade") or cliente.cidade
    cliente.uf = dest.get("uf") or cliente.uf
    cliente.cep = dest.get("cep") or cliente.cep


# ---------------------------------------------------------------------------
# GET /api/notas/clientes  — client search for assistente/produtor
# ---------------------------------------------------------------------------

@notas_bp.route("/clientes", methods=["GET"])
@jwt_required()
def search_clientes():
    user = _get_current_user()
    produtor = _resolve_produtor(user)
    if not produtor:
        return jsonify({"success": False, "message": "Sem acesso."}), 403

    search = request.args.get("search", "").strip()
    query = Cliente.query.filter_by(produtor_id=produtor.id)
    if search:
        like = f"%{search}%"
        query = query.filter(
            or_(Cliente.nome.ilike(like), Cliente.cpf_cnpj.ilike(like))
        )
    query = query.order_by(Cliente.nome).limit(20)
    items = [c.to_dict() for c in query.all()]
    return jsonify({"success": True, "data": {"items": items}}), 200


# ---------------------------------------------------------------------------
# GET /api/notas/catalogo  — service/product catalog for assistente/produtor
# ---------------------------------------------------------------------------

@notas_bp.route("/catalogo", methods=["GET"])
@jwt_required()
def get_catalogo():
    user = _get_current_user()
    produtor = _resolve_produtor(user)
    if not produtor:
        return jsonify({"success": False, "message": "Sem acesso."}), 403

    tipo = request.args.get("tipo")
    query = Produto.query.filter_by(produtor_id=produtor.id, ativo=True)
    if tipo:
        query = query.filter_by(tipo=tipo)
    items = [p.to_dict() for p in query.order_by(Produto.nome).all()]
    return jsonify({"success": True, "data": items}), 200


# ---------------------------------------------------------------------------
# GET /api/notas
# ---------------------------------------------------------------------------

@notas_bp.route("/", methods=["GET"])
@jwt_required()
def list_notas():
    user = _get_current_user()
    page = request.args.get("page", 1, type=int)
    per_page = request.args.get("per_page", 20, type=int)
    status = request.args.get("status")
    date_from = request.args.get("date_from")
    date_to = request.args.get("date_to")

    query = NotaFiscal.query
    query = _apply_nota_filters(query, user)

    if status:
        query = query.filter(NotaFiscal.status == status)
    if date_from:
        query = query.filter(NotaFiscal.created_at >= date_from)
    if date_to:
        query = query.filter(NotaFiscal.created_at <= date_to)

    query = query.order_by(NotaFiscal.created_at.desc())
    result = paginate_query(query, page, per_page)
    result["items"] = [n.to_dict() for n in result["items"]]
    return jsonify({"success": True, "data": result}), 200


# ---------------------------------------------------------------------------
# GET /api/notas/<id>
# ---------------------------------------------------------------------------

@notas_bp.route("/<int:nota_id>", methods=["GET"])
@jwt_required()
def get_nota(nota_id: int):
    user = _get_current_user()
    query = NotaFiscal.query.filter_by(id=nota_id)
    query = _apply_nota_filters(query, user)
    nota = query.first()
    if not nota:
        return jsonify({"success": False, "message": "Nota fiscal não encontrada."}), 404
    return jsonify({"success": True, "data": nota.to_dict()}), 200


# ---------------------------------------------------------------------------
# POST /api/notas/emitir
# ---------------------------------------------------------------------------

@notas_bp.route("/emitir", methods=["POST"])
@jwt_required()
def emitir_nota():
    user = _get_current_user()
    if not user or not user.active:
        return jsonify({"success": False, "message": "Usuário não encontrado ou inativo."}), 403

    produtor = _resolve_produtor(user)
    if not produtor:
        return jsonify(
            {"success": False, "message": "Apenas produtores e assistentes podem emitir notas."}
        ), 403

    integracao = NotazzIntegracao.query.filter_by(produtor_id=produtor.id).first()
    if not integracao:
        return jsonify(
            {"success": False, "message": "Integração Notazz não configurada para este produtor."}
        ), 422

    data = request.get_json(silent=True) or {}
    tipo = (data.get("tipo") or "").strip().lower()
    if tipo not in ("nfse", "nfe"):
        return jsonify({"success": False, "message": "tipo deve ser 'nfse' ou 'nfe'."}), 400

    dest = data.get("destinatario") or {}
    nome = (dest.get("nome") or "").strip()
    cpf_cnpj = (dest.get("cpf_cnpj") or "").strip()
    dest_tipo = (dest.get("tipo") or "").strip().upper()
    valor = data.get("valor")
    descricao = (data.get("descricao") or "").strip()
    competencia_str = (data.get("competencia") or "").strip()

    # Validation
    errors = []
    if not nome:
        errors.append("destinatario.nome")
    if not cpf_cnpj:
        errors.append("destinatario.cpf_cnpj")
    if dest_tipo not in ("F", "J", "E"):
        errors.append("destinatario.tipo (deve ser F, J ou E)")
    if valor is None:
        errors.append("valor")
    if not descricao:
        errors.append("descricao")
    if not competencia_str:
        errors.append("competencia")

    competencia_date = _parse_competencia(competencia_str)
    if competencia_str and not competencia_date:
        errors.append("competencia (formato esperado: YYYY-MM-DD)")

    if errors:
        return jsonify(
            {
                "success": False,
                "message": f"Campos obrigatórios ausentes ou inválidos: {', '.join(errors)}.",
            }
        ), 400

    # Optional overrides
    cnae = data.get("cnae") or integracao.cnae
    lc116 = data.get("lc116") or integracao.lc116
    send_email = integracao.send_email_client

    service = NotazzService(integracao.api_key)

    try:
        if tipo == "nfse":
            result = service.create_nfse(
                nome=nome,
                cpf_cnpj=cpf_cnpj,
                tipo=dest_tipo,
                rua=dest.get("rua"),
                numero=dest.get("numero"),
                complemento=dest.get("complemento"),
                bairro=dest.get("bairro"),
                cidade=dest.get("cidade"),
                uf=dest.get("uf"),
                cep=dest.get("cep"),
                telefone=dest.get("telefone"),
                email=dest.get("email"),
                valor=float(valor),
                descricao=descricao,
                competencia=competencia_str,
                cnae=cnae,
                lc116=lc116,
                send_email=send_email,
            )
        else:  # nfe
            produto_info = data.get("produto_info") or {}
            if not produto_info.get("nome"):
                return jsonify(
                    {"success": False, "message": "produto_info.nome é obrigatório para NF-e."}
                ), 400
            result = service.create_nfe(
                nome=nome,
                cpf_cnpj=cpf_cnpj,
                tipo=dest_tipo,
                rua=dest.get("rua"),
                numero=dest.get("numero"),
                complemento=dest.get("complemento"),
                bairro=dest.get("bairro"),
                cidade=dest.get("cidade"),
                uf=dest.get("uf"),
                cep=dest.get("cep"),
                telefone=dest.get("telefone"),
                email=dest.get("email"),
                valor=float(valor),
                descricao=descricao,
                competencia=competencia_str,
                cnae=cnae,
                lc116=lc116,
                send_email=send_email,
                produto_nome=produto_info.get("nome", "Produto"),
                produto_quantidade=float(produto_info.get("quantidade", 1)),
                produto_valor_unitario=float(produto_info["valor_unitario"])
                if produto_info.get("valor_unitario") is not None
                else None,
            )
    except RuntimeError as exc:
        logger.exception("Notazz service error")
        return jsonify({"success": False, "message": str(exc)}), 502

    # Build nota record regardless of success (to track attempts)
    raw_data = result.get("data") or {}
    nota = NotaFiscal(
        emitente_id=user.id,
        produtor_id=produtor.id,
        external_id=result.get("external_id"),
        tipo=tipo,
        status="pendente",
        valor=float(valor),
        destinatario_nome=nome,
        destinatario_cpf_cnpj=cpf_cnpj,
        destinatario_tipo=dest_tipo,
        destinatario_email=dest.get("email"),
        destinatario_telefone=dest.get("telefone"),
        destinatario_rua=dest.get("rua"),
        destinatario_numero=dest.get("numero"),
        destinatario_complemento=dest.get("complemento"),
        destinatario_bairro=dest.get("bairro"),
        destinatario_cidade=dest.get("cidade"),
        destinatario_uf=dest.get("uf"),
        destinatario_cep=dest.get("cep"),
        descricao=descricao,
        competencia=competencia_date,
    )

    if result["success"]:
        _update_nota_from_notazz_response(nota, raw_data)
        # Fallback: store notazz id from top level if present
        nota.notazz_id = nota.notazz_id or raw_data.get("id") or raw_data.get("nfse_id")
        _upsert_cliente(produtor.id, dest, nome, cpf_cnpj, dest_tipo)
    else:
        nota.status = "rejeitada"
        nota.motivo_status = result.get("message", "")

    db.session.add(nota)
    db.session.commit()

    http_status = 201 if result["success"] else 422
    return jsonify(
        {
            "success": result["success"],
            "message": result.get("message", ""),
            "data": nota.to_dict(),
        }
    ), http_status


# ---------------------------------------------------------------------------
# POST /api/notas/lote
# ---------------------------------------------------------------------------

@notas_bp.route("/lote", methods=["POST"])
@jwt_required()
def emitir_lote():
    user = _get_current_user()
    if not user or not user.active:
        return jsonify({"success": False, "message": "Usuário não encontrado ou inativo."}), 403

    produtor = _resolve_produtor(user)
    if not produtor:
        return jsonify(
            {"success": False, "message": "Apenas produtores e assistentes podem emitir notas."}
        ), 403

    integracao = NotazzIntegracao.query.filter_by(produtor_id=produtor.id).first()
    if not integracao:
        return jsonify(
            {"success": False, "message": "Integração Notazz não configurada para este produtor."}
        ), 422

    data = request.get_json(silent=True) or {}
    notas_data = data.get("notas") or []

    if not isinstance(notas_data, list) or len(notas_data) == 0:
        return jsonify({"success": False, "message": "Campo 'notas' deve ser uma lista não vazia."}), 400

    if len(notas_data) > 50:
        return jsonify(
            {"success": False, "message": "Máximo de 50 notas por lote."}
        ), 400

    service = NotazzService(integracao.api_key)
    results = []

    for idx, item in enumerate(notas_data):
        tipo = (item.get("tipo") or "").strip().lower()
        dest = item.get("destinatario") or {}
        nome = (dest.get("nome") or "").strip()
        cpf_cnpj = (dest.get("cpf_cnpj") or "").strip()
        dest_tipo = (dest.get("tipo") or "").strip().upper()
        valor = item.get("valor")
        descricao = (item.get("descricao") or "").strip()
        competencia_str = (item.get("competencia") or "").strip()
        competencia_date = _parse_competencia(competencia_str)

        if tipo not in ("nfse", "nfe") or not nome or not cpf_cnpj or valor is None:
            results.append(
                {
                    "index": idx,
                    "success": False,
                    "message": "Campos obrigatórios ausentes.",
                    "data": None,
                }
            )
            continue

        cnae = item.get("cnae") or integracao.cnae
        lc116 = item.get("lc116") or integracao.lc116

        try:
            if tipo == "nfse":
                result = service.create_nfse(
                    nome=nome, cpf_cnpj=cpf_cnpj, tipo=dest_tipo,
                    rua=dest.get("rua"), numero=dest.get("numero"),
                    complemento=dest.get("complemento"), bairro=dest.get("bairro"),
                    cidade=dest.get("cidade"), uf=dest.get("uf"), cep=dest.get("cep"),
                    telefone=dest.get("telefone"), email=dest.get("email"),
                    valor=float(valor), descricao=descricao,
                    competencia=competencia_str, cnae=cnae, lc116=lc116,
                    send_email=integracao.send_email_client,
                )
            else:
                produto_info = item.get("produto_info") or {}
                result = service.create_nfe(
                    nome=nome, cpf_cnpj=cpf_cnpj, tipo=dest_tipo,
                    rua=dest.get("rua"), numero=dest.get("numero"),
                    complemento=dest.get("complemento"), bairro=dest.get("bairro"),
                    cidade=dest.get("cidade"), uf=dest.get("uf"), cep=dest.get("cep"),
                    telefone=dest.get("telefone"), email=dest.get("email"),
                    valor=float(valor), descricao=descricao,
                    competencia=competencia_str, cnae=cnae, lc116=lc116,
                    send_email=integracao.send_email_client,
                    produto_nome=produto_info.get("nome", "Produto"),
                    produto_quantidade=float(produto_info.get("quantidade", 1)),
                    produto_valor_unitario=float(produto_info["valor_unitario"])
                    if produto_info.get("valor_unitario") is not None else None,
                )
        except RuntimeError as exc:
            results.append(
                {"index": idx, "success": False, "message": str(exc), "data": None}
            )
            continue

        raw_data = result.get("data") or {}
        nota = NotaFiscal(
            emitente_id=user.id,
            produtor_id=produtor.id,
            external_id=result.get("external_id"),
            tipo=tipo,
            status="pendente",
            valor=float(valor),
            destinatario_nome=nome,
            destinatario_cpf_cnpj=cpf_cnpj,
            destinatario_tipo=dest_tipo,
            destinatario_email=dest.get("email"),
            destinatario_telefone=dest.get("telefone"),
            destinatario_rua=dest.get("rua"),
            destinatario_numero=dest.get("numero"),
            destinatario_complemento=dest.get("complemento"),
            destinatario_bairro=dest.get("bairro"),
            destinatario_cidade=dest.get("cidade"),
            destinatario_uf=dest.get("uf"),
            destinatario_cep=dest.get("cep"),
            descricao=descricao,
            competencia=competencia_date,
        )

        if result["success"]:
            _update_nota_from_notazz_response(nota, raw_data)
            nota.notazz_id = nota.notazz_id or raw_data.get("id")
            _upsert_cliente(produtor.id, dest, nome, cpf_cnpj, dest_tipo)
        else:
            nota.status = "rejeitada"
            nota.motivo_status = result.get("message", "")

        db.session.add(nota)
        db.session.flush()  # get ID without committing

        results.append(
            {
                "index": idx,
                "success": result["success"],
                "message": result.get("message", ""),
                "data": nota.to_dict(),
            }
        )

    db.session.commit()

    total = len(notas_data)
    success_count = sum(1 for r in results if r["success"])
    return jsonify(
        {
            "success": True,
            "message": f"{success_count}/{total} notas emitidas com sucesso.",
            "data": results,
        }
    ), 207


# ---------------------------------------------------------------------------
# POST /api/notas/<id>/consultar
# ---------------------------------------------------------------------------

@notas_bp.route("/<int:nota_id>/consultar", methods=["POST"])
@jwt_required()
def consultar_nota(nota_id: int):
    user = _get_current_user()
    query = NotaFiscal.query.filter_by(id=nota_id)
    query = _apply_nota_filters(query, user)
    nota = query.first()

    if not nota:
        return jsonify({"success": False, "message": "Nota fiscal não encontrada."}), 404

    if not nota.notazz_id:
        return jsonify(
            {"success": False, "message": "Nota ainda não possui ID Notazz para consultar."}
        ), 422

    integracao = NotazzIntegracao.query.filter_by(produtor_id=nota.produtor_id).first()
    if not integracao:
        return jsonify({"success": False, "message": "Integração Notazz não encontrada."}), 422

    service = NotazzService(integracao.api_key)

    try:
        if nota.tipo == "nfse":
            result = service.consult_nfse(nota.notazz_id)
        else:
            result = service.consult_nfe(nota.notazz_id)
    except RuntimeError as exc:
        return jsonify({"success": False, "message": str(exc)}), 502

    if result["success"]:
        raw_data = result.get("data") or {}
        _update_nota_from_notazz_response(nota, raw_data)
        db.session.commit()

    return jsonify(
        {
            "success": result["success"],
            "message": result.get("message", ""),
            "data": nota.to_dict(),
        }
    ), 200


# ---------------------------------------------------------------------------
# POST /api/notas/<id>/cancelar
# ---------------------------------------------------------------------------

@notas_bp.route("/<int:nota_id>/cancelar", methods=["POST"])
@jwt_required()
def cancelar_nota(nota_id: int):
    user = _get_current_user()
    query = NotaFiscal.query.filter_by(id=nota_id)
    query = _apply_nota_filters(query, user)
    nota = query.first()

    if not nota:
        return jsonify({"success": False, "message": "Nota fiscal não encontrada."}), 404

    if nota.status == "cancelada":
        return jsonify({"success": False, "message": "Nota já está cancelada."}), 422

    if not nota.notazz_id:
        return jsonify(
            {"success": False, "message": "Nota ainda não possui ID Notazz para cancelar."}
        ), 422

    integracao = NotazzIntegracao.query.filter_by(produtor_id=nota.produtor_id).first()
    if not integracao:
        return jsonify({"success": False, "message": "Integração Notazz não encontrada."}), 422

    service = NotazzService(integracao.api_key)

    try:
        if nota.tipo == "nfse":
            result = service.cancel_nfse(nota.notazz_id)
        else:
            result = service.cancel_nfe(nota.notazz_id)
    except RuntimeError as exc:
        return jsonify({"success": False, "message": str(exc)}), 502

    if result["success"]:
        nota.status = "cancelada"
        nota.motivo_status = result.get("message", "")
        db.session.commit()

    return jsonify(
        {
            "success": result["success"],
            "message": result.get("message", ""),
            "data": nota.to_dict(),
        }
    ), 200
