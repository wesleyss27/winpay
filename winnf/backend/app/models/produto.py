from datetime import datetime, timezone
from app.extensions import db


class Produto(db.Model):
    __tablename__ = "produtos"

    id = db.Column(db.Integer, primary_key=True)
    produtor_id = db.Column(db.Integer, db.ForeignKey("users.id"), nullable=False)
    nome = db.Column(db.String(150), nullable=False)
    tipo = db.Column(db.String(10), nullable=False)  # produto | servico
    cnae = db.Column(db.String(20), nullable=True)
    lc116 = db.Column(db.String(20), nullable=True)
    valor_padrao = db.Column(db.Float, nullable=True)
    ativo = db.Column(db.Boolean, default=True, nullable=False)
    created_at = db.Column(db.DateTime, default=lambda: datetime.now(timezone.utc))
    updated_at = db.Column(
        db.DateTime,
        default=lambda: datetime.now(timezone.utc),
        onupdate=lambda: datetime.now(timezone.utc),
    )

    def to_dict(self) -> dict:
        return {
            "id": self.id,
            "produtor_id": self.produtor_id,
            "nome": self.nome,
            "tipo": self.tipo,
            "cnae": self.cnae,
            "lc116": self.lc116,
            "valor_padrao": self.valor_padrao,
            "ativo": self.ativo,
            "created_at": self.created_at.isoformat() if self.created_at else None,
            "updated_at": self.updated_at.isoformat() if self.updated_at else None,
        }

    def __repr__(self) -> str:
        return f"<Produto {self.nome} [{self.tipo}]>"
