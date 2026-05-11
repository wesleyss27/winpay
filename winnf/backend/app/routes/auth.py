from flask import Blueprint, jsonify, request
from flask_jwt_extended import (
    create_access_token,
    create_refresh_token,
    get_jwt,
    get_jwt_identity,
    jwt_required,
)
from app.extensions import db, token_blacklist
from app.models.user import User

auth_bp = Blueprint("auth", __name__)


def _get_user(user_id: int) -> "User | None":
    return db.session.get(User, user_id)


def _make_tokens(user: User) -> tuple[str, str]:
    """Create access and refresh tokens with role claim."""
    additional_claims = {"role": user.role, "name": user.name}
    access_token = create_access_token(
        identity=str(user.id), additional_claims=additional_claims
    )
    refresh_token = create_refresh_token(
        identity=str(user.id), additional_claims=additional_claims
    )
    return access_token, refresh_token


# POST /api/auth/login
@auth_bp.route("/login", methods=["POST"])
def login():
    data = request.get_json(silent=True) or {}
    email = (data.get("email") or "").strip().lower()
    password = data.get("password") or ""

    if not email or not password:
        return jsonify({"success": False, "message": "Email e senha são obrigatórios."}), 400

    user = User.query.filter_by(email=email).first()
    if not user or not user.check_password(password):
        return jsonify({"success": False, "message": "Credenciais inválidas."}), 401

    if not user.active:
        return jsonify({"success": False, "message": "Usuário inativo."}), 403

    access_token, refresh_token = _make_tokens(user)
    return jsonify(
        {
            "success": True,
            "message": "Login realizado com sucesso.",
            "data": {
                "access_token": access_token,
                "refresh_token": refresh_token,
                "user": user.to_dict(),
            },
        }
    ), 200


# POST /api/auth/refresh
@auth_bp.route("/refresh", methods=["POST"])
@jwt_required(refresh=True)
def refresh():
    identity = get_jwt_identity()
    claims = get_jwt()

    user = _get_user(int(identity))
    if not user or not user.active:
        return jsonify({"success": False, "message": "Usuário não encontrado ou inativo."}), 404

    additional_claims = {"role": claims.get("role"), "name": claims.get("name")}
    access_token = create_access_token(
        identity=identity, additional_claims=additional_claims
    )
    return jsonify(
        {
            "success": True,
            "message": "Token renovado.",
            "data": {"access_token": access_token},
        }
    ), 200


# POST /api/auth/logout
@auth_bp.route("/logout", methods=["POST"])
@jwt_required()
def logout():
    jti = get_jwt().get("jti")
    token_blacklist.add(jti)
    return jsonify({"success": True, "message": "Logout realizado com sucesso."}), 200


# GET /api/auth/me
@auth_bp.route("/me", methods=["GET"])
@jwt_required()
def me():
    identity = get_jwt_identity()
    user = _get_user(int(identity))
    if not user:
        return jsonify({"success": False, "message": "Usuário não encontrado."}), 404
    return jsonify({"success": True, "data": user.to_dict()}), 200


# PUT /api/auth/me
@auth_bp.route("/me", methods=["PUT"])
@jwt_required()
def update_me():
    identity = get_jwt_identity()
    user = _get_user(int(identity))
    if not user:
        return jsonify({"success": False, "message": "Usuário não encontrado."}), 404

    data = request.get_json(silent=True) or {}
    name = (data.get("name") or "").strip()
    password = data.get("password") or ""

    if not name and not password:
        return jsonify(
            {"success": False, "message": "Nenhum campo para atualizar."}
        ), 400

    if name:
        user.name = name
    if password:
        if len(password) < 6:
            return jsonify(
                {"success": False, "message": "Senha deve ter ao menos 6 caracteres."}
            ), 400
        user.set_password(password)

    db.session.commit()
    return jsonify(
        {
            "success": True,
            "message": "Perfil atualizado com sucesso.",
            "data": user.to_dict(),
        }
    ), 200
