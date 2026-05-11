from flask import Blueprint, jsonify, request
from flask_jwt_extended import jwt_required
from app.extensions import db
from app.models.user import User
from app.models.nota_fiscal import NotaFiscal
from app.utils.decorators import role_required, paginate_query

admin_bp = Blueprint("admin", __name__)


# GET /api/admin/dashboard
@admin_bp.route("/dashboard", methods=["GET"])
@role_required("admin")
def dashboard():
    total_produtores = User.query.filter_by(role="produtor", active=True).count()
    total_assistentes = User.query.filter_by(role="assistente", active=True).count()
    total_notas = NotaFiscal.query.count()

    statuses = ["pendente", "autorizada", "cancelada", "rejeitada", "denegada"]
    notas_por_status = {}
    for status in statuses:
        notas_por_status[status] = NotaFiscal.query.filter_by(status=status).count()

    return jsonify(
        {
            "success": True,
            "data": {
                "total_produtores": total_produtores,
                "total_assistentes": total_assistentes,
                "total_notas": total_notas,
                "notas_por_status": notas_por_status,
            },
        }
    ), 200


# GET /api/admin/usuarios
@admin_bp.route("/usuarios", methods=["GET"])
@role_required("admin")
def list_usuarios():
    page = request.args.get("page", 1, type=int)
    per_page = request.args.get("per_page", 20, type=int)
    role_filter = request.args.get("role")

    query = User.query
    if role_filter:
        query = query.filter_by(role=role_filter)
    query = query.order_by(User.created_at.desc())

    result = paginate_query(query, page, per_page)
    result["items"] = [u.to_dict() for u in result["items"]]
    return jsonify({"success": True, "data": result}), 200


# GET /api/admin/produtores
@admin_bp.route("/produtores", methods=["GET"])
@role_required("admin")
def list_produtores():
    page = request.args.get("page", 1, type=int)
    per_page = request.args.get("per_page", 20, type=int)

    query = User.query.filter_by(role="produtor").order_by(User.created_at.desc())
    result = paginate_query(query, page, per_page)
    result["items"] = [u.to_dict() for u in result["items"]]
    return jsonify({"success": True, "data": result}), 200


# POST /api/admin/produtores
@admin_bp.route("/produtores", methods=["POST"])
@role_required("admin")
def create_produtor():
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

    user = User(name=name, email=email, role="produtor", active=True)
    user.set_password(password)
    db.session.add(user)
    db.session.commit()

    return jsonify(
        {
            "success": True,
            "message": "Produtor criado com sucesso.",
            "data": user.to_dict(),
        }
    ), 201


# PUT /api/admin/produtores/<id>
@admin_bp.route("/produtores/<int:produtor_id>", methods=["PUT"])
@role_required("admin")
def update_produtor(produtor_id: int):
    user = User.query.filter_by(id=produtor_id, role="produtor").first()
    if not user:
        return jsonify({"success": False, "message": "Produtor não encontrado."}), 404

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
            "message": "Produtor atualizado com sucesso.",
            "data": user.to_dict(),
        }
    ), 200


# DELETE /api/admin/produtores/<id>
@admin_bp.route("/produtores/<int:produtor_id>", methods=["DELETE"])
@role_required("admin")
def delete_produtor(produtor_id: int):
    user = User.query.filter_by(id=produtor_id, role="produtor").first()
    if not user:
        return jsonify({"success": False, "message": "Produtor não encontrado."}), 404

    user.active = False
    # Also deactivate all assistentes under this produtor
    User.query.filter_by(produtor_id=produtor_id, role="assistente").update(
        {"active": False}
    )
    db.session.commit()
    return jsonify({"success": True, "message": "Produtor desativado com sucesso."}), 200


# GET /api/admin/notas
@admin_bp.route("/notas", methods=["GET"])
@role_required("admin")
def list_notas():
    page = request.args.get("page", 1, type=int)
    per_page = request.args.get("per_page", 20, type=int)
    status = request.args.get("status")
    produtor_id = request.args.get("produtor_id", type=int)
    date_from = request.args.get("date_from")
    date_to = request.args.get("date_to")

    query = NotaFiscal.query

    if status:
        query = query.filter(NotaFiscal.status == status)
    if produtor_id:
        query = query.filter(NotaFiscal.produtor_id == produtor_id)
    if date_from:
        query = query.filter(NotaFiscal.created_at >= date_from)
    if date_to:
        query = query.filter(NotaFiscal.created_at <= date_to)

    query = query.order_by(NotaFiscal.created_at.desc())
    result = paginate_query(query, page, per_page)
    result["items"] = [n.to_dict() for n in result["items"]]
    return jsonify({"success": True, "data": result}), 200
