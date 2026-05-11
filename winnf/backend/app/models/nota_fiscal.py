from datetime import datetime, timezone
from app.extensions import db


class NotaFiscal(db.Model):
    __tablename__ = "notas_fiscais"

    id = db.Column(db.Integer, primary_key=True)

    # Who emitted and whose API key was used
    emitente_id = db.Column(db.Integer, db.ForeignKey("users.id"), nullable=False)
    produtor_id = db.Column(db.Integer, db.ForeignKey("users.id"), nullable=False)

    # Notazz identifiers
    notazz_id = db.Column(db.String(80), nullable=True)
    external_id = db.Column(db.String(80), nullable=True, unique=True, index=True)

    # Nota type and status
    tipo = db.Column(db.String(10), nullable=False)  # nfse | nfe
    status = db.Column(db.String(20), nullable=False, default="pendente")
    # pendente | autorizada | cancelada | rejeitada | denegada

    # Financial
    valor = db.Column(db.Float, nullable=False)

    # Destinatario
    destinatario_nome = db.Column(db.String(150), nullable=False)
    destinatario_cpf_cnpj = db.Column(db.String(20), nullable=False)
    destinatario_tipo = db.Column(db.String(1), nullable=False)  # F | J | E
    destinatario_email = db.Column(db.String(120), nullable=True)
    destinatario_telefone = db.Column(db.String(20), nullable=True)
    destinatario_rua = db.Column(db.String(150), nullable=True)
    destinatario_numero = db.Column(db.String(20), nullable=True)
    destinatario_complemento = db.Column(db.String(80), nullable=True)
    destinatario_bairro = db.Column(db.String(80), nullable=True)
    destinatario_cidade = db.Column(db.String(80), nullable=True)
    destinatario_uf = db.Column(db.String(2), nullable=True)
    destinatario_cep = db.Column(db.String(9), nullable=True)

    # Service/document info
    descricao = db.Column(db.Text, nullable=True)
    competencia = db.Column(db.Date, nullable=True)

    # Notazz response fields
    pdf_url = db.Column(db.String(512), nullable=True)
    xml_url = db.Column(db.String(512), nullable=True)
    numero = db.Column(db.String(40), nullable=True)
    chave = db.Column(db.String(80), nullable=True)
    serie = db.Column(db.String(10), nullable=True)
    protocolo_autorizacao = db.Column(db.String(80), nullable=True)
    motivo_status = db.Column(db.Text, nullable=True)

    created_at = db.Column(db.DateTime, default=lambda: datetime.now(timezone.utc))
    updated_at = db.Column(
        db.DateTime,
        default=lambda: datetime.now(timezone.utc),
        onupdate=lambda: datetime.now(timezone.utc),
    )

    # Relationships
    emitente = db.relationship(
        "User", foreign_keys=[emitente_id], backref="notas_emitidas"
    )
    produtor = db.relationship(
        "User", foreign_keys=[produtor_id], backref="notas_produtor"
    )

    def to_dict(self) -> dict:
        return {
            "id": self.id,
            "emitente_id": self.emitente_id,
            "produtor_id": self.produtor_id,
            "notazz_id": self.notazz_id,
            "external_id": self.external_id,
            "tipo": self.tipo,
            "status": self.status,
            "valor": self.valor,
            "destinatario_nome": self.destinatario_nome,
            "destinatario_cpf_cnpj": self.destinatario_cpf_cnpj,
            "destinatario_tipo": self.destinatario_tipo,
            "destinatario_email": self.destinatario_email,
            "destinatario_telefone": self.destinatario_telefone,
            "destinatario_rua": self.destinatario_rua,
            "destinatario_numero": self.destinatario_numero,
            "destinatario_complemento": self.destinatario_complemento,
            "destinatario_bairro": self.destinatario_bairro,
            "destinatario_cidade": self.destinatario_cidade,
            "destinatario_uf": self.destinatario_uf,
            "destinatario_cep": self.destinatario_cep,
            "descricao": self.descricao,
            "competencia": self.competencia.isoformat() if self.competencia else None,
            "pdf_url": self.pdf_url,
            "xml_url": self.xml_url,
            "numero": self.numero,
            "chave": self.chave,
            "serie": self.serie,
            "protocolo_autorizacao": self.protocolo_autorizacao,
            "motivo_status": self.motivo_status,
            "created_at": self.created_at.isoformat() if self.created_at else None,
            "updated_at": self.updated_at.isoformat() if self.updated_at else None,
        }

    def __repr__(self) -> str:
        return f"<NotaFiscal id={self.id} tipo={self.tipo} status={self.status}>"
