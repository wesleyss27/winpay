from datetime import datetime, timezone
from app.extensions import db


class NotazzIntegracao(db.Model):
    __tablename__ = "notazz_integracoes"

    id = db.Column(db.Integer, primary_key=True)
    produtor_id = db.Column(
        db.Integer, db.ForeignKey("users.id"), nullable=False, unique=True
    )
    api_key = db.Column(db.String(256), nullable=False)
    cnae = db.Column(db.String(20), nullable=True)
    lc116 = db.Column(db.String(20), nullable=True)
    send_email_client = db.Column(db.Boolean, default=True, nullable=False)
    cidade_padrao = db.Column(db.String(80), nullable=True)
    uf_padrao = db.Column(db.String(2), nullable=True)
    cep_padrao = db.Column(db.String(9), nullable=True)
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
            "api_key": self.api_key,
            "cnae": self.cnae,
            "lc116": self.lc116,
            "send_email_client": self.send_email_client,
            "cidade_padrao": self.cidade_padrao,
            "uf_padrao": self.uf_padrao,
            "cep_padrao": self.cep_padrao,
            "created_at": self.created_at.isoformat() if self.created_at else None,
            "updated_at": self.updated_at.isoformat() if self.updated_at else None,
        }

    def __repr__(self) -> str:
        return f"<NotazzIntegracao produtor_id={self.produtor_id}>"
