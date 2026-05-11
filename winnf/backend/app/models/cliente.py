from datetime import datetime, timezone
from app.extensions import db


class Cliente(db.Model):
    __tablename__ = "clientes"

    id = db.Column(db.Integer, primary_key=True)
    produtor_id = db.Column(db.Integer, db.ForeignKey("users.id"), nullable=False)
    nome = db.Column(db.String(150), nullable=False)
    cpf_cnpj = db.Column(db.String(20), nullable=False)
    tipo = db.Column(db.String(1), nullable=False)  # F | J | E
    email = db.Column(db.String(120), nullable=True)
    telefone = db.Column(db.String(20), nullable=True)
    rua = db.Column(db.String(150), nullable=True)
    numero = db.Column(db.String(20), nullable=True)
    complemento = db.Column(db.String(80), nullable=True)
    bairro = db.Column(db.String(80), nullable=True)
    cidade = db.Column(db.String(80), nullable=True)
    uf = db.Column(db.String(2), nullable=True)
    cep = db.Column(db.String(9), nullable=True)
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
            "cpf_cnpj": self.cpf_cnpj,
            "tipo": self.tipo,
            "email": self.email,
            "telefone": self.telefone,
            "rua": self.rua,
            "numero": self.numero,
            "complemento": self.complemento,
            "bairro": self.bairro,
            "cidade": self.cidade,
            "uf": self.uf,
            "cep": self.cep,
            "created_at": self.created_at.isoformat() if self.created_at else None,
            "updated_at": self.updated_at.isoformat() if self.updated_at else None,
        }

    def __repr__(self) -> str:
        return f"<Cliente {self.nome} [{self.cpf_cnpj}]>"
