from datetime import datetime, timezone
from werkzeug.security import generate_password_hash, check_password_hash
from app.extensions import db


class User(db.Model):
    __tablename__ = "users"

    id = db.Column(db.Integer, primary_key=True)
    name = db.Column(db.String(120), nullable=False)
    email = db.Column(db.String(120), unique=True, nullable=False, index=True)
    password_hash = db.Column(db.String(256), nullable=False)
    role = db.Column(db.String(20), nullable=False)  # admin | produtor | assistente
    produtor_id = db.Column(db.Integer, db.ForeignKey("users.id"), nullable=True)
    active = db.Column(db.Boolean, default=True, nullable=False)
    created_at = db.Column(db.DateTime, default=lambda: datetime.now(timezone.utc))
    updated_at = db.Column(
        db.DateTime,
        default=lambda: datetime.now(timezone.utc),
        onupdate=lambda: datetime.now(timezone.utc),
    )

    # Relationships
    assistentes = db.relationship(
        "User",
        backref=db.backref("produtor", remote_side=[id]),
        lazy="dynamic",
        foreign_keys="User.produtor_id",
    )
    integracao = db.relationship(
        "NotazzIntegracao",
        backref="produtor",
        uselist=False,
        foreign_keys="NotazzIntegracao.produtor_id",
    )
    clientes = db.relationship(
        "Cliente",
        backref="produtor",
        lazy="dynamic",
        foreign_keys="Cliente.produtor_id",
    )
    produtos = db.relationship(
        "Produto",
        backref="produtor",
        lazy="dynamic",
        foreign_keys="Produto.produtor_id",
    )

    def set_password(self, password: str) -> None:
        self.password_hash = generate_password_hash(password)

    def check_password(self, password: str) -> bool:
        return check_password_hash(self.password_hash, password)

    def to_dict(self, include_sensitive: bool = False) -> dict:
        data = {
            "id": self.id,
            "name": self.name,
            "email": self.email,
            "role": self.role,
            "produtor_id": self.produtor_id,
            "active": self.active,
            "created_at": self.created_at.isoformat() if self.created_at else None,
            "updated_at": self.updated_at.isoformat() if self.updated_at else None,
        }
        return data

    def __repr__(self) -> str:
        return f"<User {self.email} [{self.role}]>"
