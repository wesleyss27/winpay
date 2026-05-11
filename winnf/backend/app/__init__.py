import os
from flask import Flask
from .config import config_by_name
from .extensions import db, jwt, cors


def create_app(config_name: str | None = None) -> Flask:
    if config_name is None:
        config_name = os.environ.get("FLASK_ENV", "development")

    app = Flask(__name__)
    app.url_map.strict_slashes = False
    app.config.from_object(config_by_name.get(config_name, config_by_name["default"]))

    # Extensions
    db.init_app(app)
    jwt.init_app(app)
    cors.init_app(
        app,
        resources={r"/api/*": {"origins": "*"}},
        supports_credentials=False,
    )

    # Blueprints
    from .routes.auth import auth_bp
    from .routes.admin import admin_bp
    from .routes.produtor import produtor_bp
    from .routes.notas import notas_bp
    from .routes.webhook import webhook_bp

    app.register_blueprint(auth_bp, url_prefix="/api/auth")
    app.register_blueprint(admin_bp, url_prefix="/api/admin")
    app.register_blueprint(produtor_bp, url_prefix="/api/produtor")
    app.register_blueprint(notas_bp, url_prefix="/api/notas")
    app.register_blueprint(webhook_bp, url_prefix="/api/webhook")

    # Health check
    @app.route("/api/health")
    def health():
        from flask import jsonify
        return jsonify({"success": True, "message": "WinNF API running"}), 200

    # Create tables and seed admin on first run
    with app.app_context():
        db.create_all()
        _seed_admin()

    return app


def _seed_admin() -> None:
    from .models.user import User
    from werkzeug.security import generate_password_hash

    if User.query.filter_by(email="admin@winnf.com").first():
        return

    admin = User(
        name="Admin WinNF",
        email="admin@winnf.com",
        password_hash=generate_password_hash("admin123"),
        role="admin",
        active=True,
    )
    db.session.add(admin)
    db.session.commit()
