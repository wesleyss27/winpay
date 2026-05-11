from flask import Blueprint, jsonify, request
from flask_jwt_extended import get_jwt_identity
from app.extensions import db
from app.models.user import User
from app.models.notazz_integracao import NotazzIntegracao
from app.models.nota_fiscal import NotaFiscal
from app.models.cliente import Cliente
from app.models.produto import Produto
from app.utils.decorators import role_required, paginate_query

produtor_bp = Blueprint("produtor", __name__)


def _get_current_produtor() -> User:
    identity = get_jwt_identity()
    return db.session.get(User, int(identity))


# ---------------------------------------------------------------------------
# Dashboard
# ---------------------------------------------------------------------------

@produtor_bp.route("/dashboard", methods=["GET"])
@role_required("produtor")
def dashboard():
    produtor = _get_current_produtor()

    assistente_ids = [a.id for a in produtor.assistentes.filter_by(active=True).all()]
    total_assistentes = len(assistente_ids)
    total_notas = NotaFiscal.query.filter_by(produtor_id=produtor.id).count()

    statuses = ["pendente", "autorizada", "cancelada", "rejeitada", "denegada"]
    notas_por_status = {}
    for status in statuses:
        notas_por_status[status] = NotaFiscal.query.filter_by(
            produtor_id=produtor.id, status=status
        ).count()

    return jsonify(
        {
            "success": True,
            "data": {
                "total_assistentes": total_assistentes,
                "total_notas": total_notas,
                "notas_por_status": notas_por_status,
            },
        }
    ), 200


# ---------------------------------------------------------------------------
# Assistentes
# ---------------------------------------------------------------------------

@produtor_bp.route("/assistentes", methods=["GET"])
@role_required("produtor")
def list_assistentes():
    produtor = _get_current_produtor()
    page = request.args.get("page", 1, type=int)
    per_page = request.args.get("per_page", 20, type=int)

    query = User.query.filter_by(produtor_id=produtor.id, role="assistente").order_by(
        User.created_at.desc()
    )
    result = paginate_query(query, page, per_page)
    result["items"] = [u.to_dict() for u in result["items"]]
    return jsonify({"success": True, "data": result}), 200


@produtor_bp.route("/assistentes", methods=["POST"])
@role_required("produtor")
def create_assistente():
    produtor = _get_current_produtor()
    data = request.get_json(silent=True) or {}
    name = (data.get("name") or "").strip()
    email = (data.get("email") or "").strip().lower()
    password = data.get("password") or ""

    if not name or not email or not password:
        return jsonify(
            {"success": False, "message": "Nome, email e senha são obrigatórios."}
        ), 400

    if len(password) < 6:
        return jsonify(
            {"success": False, "message": "Senha deve ter ao menos 6 caracteres."}
        ), 400

    if User.query.filter_by(email=email).first():
        return jsonify({"success": False, "message": "Email já cadastrado."}), 409

    user = User(
        name=name,
        email=email,
        role="assistente",
        produtor_id=produtor.id,
        active=True,
    )
    user.set_password(password)
    db.session.add(user)
    db.session.commit()

    return jsonify(
        {
            "success": True,
            "message": "Assistente criado com sucesso.",
            "data": user.to_dict(),
        }
    ), 201


@produtor_bp.route("/assistentes/<int:assistente_id>", methods=["PUT"])
@role_required("produtor")
def update_assistente(assistente_id: int):
    produtor = _get_current_produtor()
    user = User.query.filter_by(
        id=assistente_id, produtor_id=produtor.id, role="assistente"
    ).first()
    if not user:
        return jsonify({"success": False, "message": "Assistente não encontrado."}), 404

    data = request.get_json(silent=True) or {}
    name = (data.get("name") or "").strip()
    email = (data.get("email") or "").strip().lower()
    password = data.get("password") or ""
    active = data.get("active")

    if name:
        user.name = name
    if email:
        existing = User.query.filter_by(email=email).first()
        if existing and existing.id != user.id:
            return jsonify({"success": False, "message": "Email já em uso."}), 409
        user.email = email
    if password:
        if len(password) < 6:
            return jsonify(
                {"success": False, "message": "Senha deve ter ao menos 6 caracteres."}
            ), 400
        user.set_password(password)
    if active is not None:
        user.active = bool(active)

    db.session.commit()
    return jsonify(
        {
            "success": True,
            "message": "Assistente atualizado com sucesso.",
            "data": user.to_dict(),
        }
    ), 200


@produtor_bp.route("/assistentes/<int:assistente_id>", methods=["DELETE"])
@role_required("produtor")
def delete_assistente(assistente_id: int):
    produtor = _get_current_produtor()
    user = User.query.filter_by(
        id=assistente_id, produtor_id=produtor.id, role="assistente"
    ).first()
    if not user:
        return jsonify({"success": False, "message": "Assistente não encontrado."}), 404

    user.active = False
    db.session.commit()
    return jsonify({"success": True, "message": "Assistente desativado com sucesso."}), 200


# ---------------------------------------------------------------------------
# Integração Notazz
# ---------------------------------------------------------------------------

@produtor_bp.route("/integracao", methods=["GET"])
@role_required("produtor")
def get_integracao():
    produtor = _get_current_produtor()
    integracao = NotazzIntegracao.query.filter_by(produtor_id=produtor.id).first()
    if not integracao:
        return jsonify({"success": True, "data": None}), 200
    return jsonify({"success": True, "data": integracao.to_dict()}), 200


@produtor_bp.route("/integracao", methods=["POST"])
@role_required("produtor")
def create_integracao():
    produtor = _get_current_produtor()

    existing = NotazzIntegracao.query.filter_by(produtor_id=produtor.id).first()
    if existing:
        return jsonify(
            {
                "success": False,
                "message": "Integração já existe. Use PUT para atualizar.",
            }
        ), 409

    data = request.get_json(silent=True) or {}
    api_key = (data.get("api_key") or "").strip()
    if not api_key:
        return jsonify({"success": False, "message": "api_key é obrigatório."}), 400

    integracao = NotazzIntegracao(
        produtor_id=produtor.id,
        api_key=api_key,
        cnae=data.get("cnae"),
        lc116=data.get("lc116"),
        send_email_client=bool(data.get("send_email_client", True)),
        cidade_padrao=data.get("cidade_padrao"),
        uf_padrao=data.get("uf_padrao"),
        cep_padrao=data.get("cep_padrao"),
    )
    db.session.add(integracao)
    db.session.commit()

    return jsonify(
        {
            "success": True,
            "message": "Integração criada com sucesso.",
            "data": integracao.to_dict(),
        }
    ), 201


@produtor_bp.route("/integracao/<int:integracao_id>", methods=["PUT"])
@role_required("produtor")
def update_integracao(integracao_id: int):
    produtor = _get_current_produtor()
    integracao = NotazzIntegracao.query.filter_by(
        id=integracao_id, produtor_id=produtor.id
    ).first()
    if not integracao:
        return jsonify({"success": False, "message": "Integração não encontrada."}), 404

    data = request.get_json(silent=True) or {}
    if "api_key" in data and data["api_key"]:
        integracao.api_key = data["api_key"].strip()
    if "cnae" in data:
        integracao.cnae = data["cnae"]
    if "lc116" in data:
        integracao.lc116 = data["lc116"]
    if "send_email_client" in data:
        integracao.send_email_client = bool(data["send_email_client"])
    if "cidade_padrao" in data:
        integracao.cidade_padrao = data["cidade_padrao"]
    if "uf_padrao" in data:
        integracao.uf_padrao = data["uf_padrao"]
    if "cep_padrao" in data:
        integracao.cep_padrao = data["cep_padrao"]

    db.session.commit()
    return jsonify(
        {
            "success": True,
            "message": "Integração atualizada com sucesso.",
            "data": integracao.to_dict(),
        }
    ), 200


# ---------------------------------------------------------------------------
# Clientes
# ---------------------------------------------------------------------------

@produtor_bp.route("/clientes", methods=["GET"])
@role_required("produtor")
def list_clientes():
    produtor = _get_current_produtor()
    page = request.args.get("page", 1, type=int)
    per_page = request.args.get("per_page", 20, type=int)
    search = request.args.get("search", "").strip()

    query = Cliente.query.filter_by(produtor_id=produtor.id)
    if search:
        like = f"%{search}%"
        query = query.filter(
            db.or_(
                Cliente.nome.ilike(like),
                Cliente.cpf_cnpj.ilike(like),
            )
        )
    query = query.order_by(Cliente.nome.asc())

    result = paginate_query(query, page, per_page)
    result["items"] = [c.to_dict() for c in result["items"]]
    return jsonify({"success": True, "data": result}), 200


@produtor_bp.route("/clientes", methods=["POST"])
@role_required("produtor")
def create_cliente():
    produtor = _get_current_produtor()
    data = request.get_json(silent=True) or {}

    nome = (data.get("nome") or "").strip()
    cpf_cnpj = (data.get("cpf_cnpj") or "").strip()
    tipo = (data.get("tipo") or "").strip().upper()

    if not nome or not cpf_cnpj or tipo not in ("F", "J", "E"):
        return jsonify(
            {
                "success": False,
                "message": "Nome, cpf_cnpj e tipo (F/J/E) são obrigatórios.",
            }
        ), 400

    cliente = Cliente(
        produtor_id=produtor.id,
        nome=nome,
        cpf_cnpj=cpf_cnpj,
        tipo=tipo,
        email=data.get("email"),
        telefone=data.get("telefone"),
        rua=data.get("rua"),
        numero=data.get("numero"),
        complemento=data.get("complemento"),
        bairro=data.get("bairro"),
        cidade=data.get("cidade"),
        uf=data.get("uf"),
        cep=data.get("cep"),
    )
    db.session.add(cliente)
    db.session.commit()

    return jsonify(
        {
            "success": True,
            "message": "Cliente criado com sucesso.",
            "data": cliente.to_dict(),
        }
    ), 201


@produtor_bp.route("/clientes/<int:cliente_id>", methods=["PUT"])
@role_required("produtor")
def update_cliente(cliente_id: int):
    produtor = _get_current_produtor()
    cliente = Cliente.query.filter_by(id=cliente_id, produtor_id=produtor.id).first()
    if not cliente:
        return jsonify({"success": False, "message": "Cliente não encontrado."}), 404

    data = request.get_json(silent=True) or {}
    fields = [
        "nome", "cpf_cnpj", "tipo", "email", "telefone",
        "rua", "numero", "complemento", "bairro", "cidade", "uf", "cep",
    ]
    for field in fields:
        if field in data:
            value = data[field]
            if field == "tipo" and value:
                value = value.upper()
            setattr(cliente, field, value)

    db.session.commit()
    return jsonify(
        {
            "success": True,
            "message": "Cliente atualizado com sucesso.",
            "data": cliente.to_dict(),
        }
    ), 200


@produtor_bp.route("/clientes/<int:cliente_id>", methods=["DELETE"])
@role_required("produtor")
def delete_cliente(cliente_id: int):
    produtor = _get_current_produtor()
    cliente = Cliente.query.filter_by(id=cliente_id, produtor_id=produtor.id).first()
    if not cliente:
        return jsonify({"success": False, "message": "Cliente não encontrado."}), 404

    db.session.delete(cliente)
    db.session.commit()
    return jsonify({"success": True, "message": "Cliente removido com sucesso."}), 200


# ---------------------------------------------------------------------------
# Produtos / Serviços
# ---------------------------------------------------------------------------

@produtor_bp.route("/produtos", methods=["GET"])
@role_required("produtor")
def list_produtos():
    produtor = _get_current_produtor()
    page = request.args.get("page", 1, type=int)
    per_page = request.args.get("per_page", 20, type=int)
    ativo = request.args.get("ativo")

    query = Produto.query.filter_by(produtor_id=produtor.id)
    if ativo is not None:
        query = query.filter_by(ativo=ativo.lower() == "true")
    query = query.order_by(Produto.nome.asc())

    result = paginate_query(query, page, per_page)
    result["items"] = [p.to_dict() for p in result["items"]]
    return jsonify({"success": True, "data": result}), 200


@produtor_bp.route("/produtos", methods=["POST"])
@role_required("produtor")
def create_produto():
    produtor = _get_current_produtor()
    data = request.get_json(silent=True) or {}

    nome = (data.get("nome") or "").strip()
    tipo = (data.get("tipo") or "").strip().lower()

    if not nome or tipo not in ("produto", "servico"):
        return jsonify(
            {
                "success": False,
                "message": "Nome e tipo (produto/servico) são obrigatórios.",
            }
        ), 400

    produto = Produto(
        produtor_id=produtor.id,
        nome=nome,
        tipo=tipo,
        cnae=data.get("cnae"),
        lc116=data.get("lc116"),
        valor_padrao=data.get("valor_padrao"),
        ativo=bool(data.get("ativo", True)),
    )
    db.session.add(produto)
    db.session.commit()

    return jsonify(
        {
            "success": True,
            "message": "Produto criado com sucesso.",
            "data": produto.to_dict(),
        }
    ), 201


@produtor_bp.route("/produtos/<int:produto_id>", methods=["PUT"])
@role_required("produtor")
def update_produto(produto_id: int):
    produtor = _get_current_produtor()
    produto = Produto.query.filter_by(id=produto_id, produtor_id=produtor.id).first()
    if not produto:
        return jsonify({"success": False, "message": "Produto não encontrado."}), 404

    data = request.get_json(silent=True) or {}
    fields = ["nome", "tipo", "cnae", "lc116", "valor_padrao", "ativo"]
    for field in fields:
        if field in data:
            value = data[field]
            if field == "tipo" and value:
                value = value.lower()
            if field == "ativo":
                value = bool(value)
            setattr(produto, field, value)

    db.session.commit()
    return jsonify(
        {
            "success": True,
            "message": "Produto atualizado com sucesso.",
            "data": produto.to_dict(),
        }
    ), 200


@produtor_bp.route("/produtos/<int:produto_id>", methods=["DELETE"])
@role_required("produtor")
def delete_produto(produto_id: int):
    produtor = _get_current_produtor()
    produto = Produto.query.filter_by(id=produto_id, produtor_id=produtor.id).first()
    if not produto:
        return jsonify({"success": False, "message": "Produto não encontrado."}), 404

    db.session.delete(produto)
    db.session.commit()
    return jsonify({"success": True, "message": "Produto removido com sucesso."}), 200
