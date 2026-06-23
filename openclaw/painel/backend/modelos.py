from sqlalchemy import Column, Integer, String, DateTime, Enum, Boolean, Text
from sqlalchemy.sql import func
from database import Base
import enum


class PlanoEnum(str, enum.Enum):
    start = "start"
    pro = "pro"
    obsidian = "obsidian"


class StatusEnum(str, enum.Enum):
    ativo = "ativo"
    suspenso = "suspenso"
    cancelado = "cancelado"


class PapelEnum(str, enum.Enum):
    admin = "admin"
    cliente = "cliente"


class Cliente(Base):
    __tablename__ = "clientes"

    id = Column(Integer, primary_key=True, index=True)
    nome = Column(String(150), nullable=False)
    nome_empresa = Column(String(150), nullable=False)
    email = Column(String(150), unique=True, index=True, nullable=False)
    telefone = Column(String(20))
    senha_hash = Column(String(255), nullable=False)
    plano = Column(Enum(PlanoEnum), default=PlanoEnum.start)
    status = Column(Enum(StatusEnum), default=StatusEnum.ativo)
    papel = Column(Enum(PapelEnum), default=PapelEnum.cliente)
    data_inicio = Column(DateTime, server_default=func.now())
    criado_em = Column(DateTime, server_default=func.now())
    atualizado_em = Column(DateTime, server_default=func.now(), onupdate=func.now())


class Ticket(Base):
    __tablename__ = "tickets"

    id = Column(Integer, primary_key=True, index=True)
    cliente_id = Column(Integer, nullable=False)
    assunto = Column(String(200), nullable=False)
    mensagem = Column(Text, nullable=False)
    status = Column(String(20), default="aberto")
    criado_em = Column(DateTime, server_default=func.now())
