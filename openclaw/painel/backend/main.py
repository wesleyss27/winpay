from fastapi import FastAPI, Depends, HTTPException, status
from fastapi.middleware.cors import CORSMiddleware
from fastapi.security import OAuth2PasswordRequestForm
from sqlalchemy.orm import Session
from pydantic import BaseModel, EmailStr
from typing import Optional, List
from datetime import datetime
import httpx

from database import engine, obter_db
import modelos
import autenticacao

modelos.Base.metadata.create_all(bind=engine)

app = FastAPI(title="Painel OpenClaw", docs_url="/docs")

app.add_middleware(
    CORSMiddleware,
    allow_origins=["*"],
    allow_credentials=True,
    allow_methods=["*"],
    allow_headers=["*"],
)

OPENCLAW_URL = "http://openclaw-gateway:18789"


# ─── Schemas ──────────────────────────────────────────────────────────────────

class LoginResposta(BaseModel):
    access_token: str
    token_type: str
    papel: str
    nome: str

class ClienteCriar(BaseModel):
    nome: str
    nome_empresa: str
    email: EmailStr
    telefone: Optional[str] = None
    senha: str
    plano: modelos.PlanoEnum = modelos.PlanoEnum.start
    papel: modelos.PapelEnum = modelos.PapelEnum.cliente

class ClienteAtualizar(BaseModel):
    nome: Optional[str] = None
    nome_empresa: Optional[str] = None
    telefone: Optional[str] = None
    plano: Optional[modelos.PlanoEnum] = None
    status: Optional[modelos.StatusEnum] = None

class ClienteResposta(BaseModel):
    id: int
    nome: str
    nome_empresa: str
    email: str
    telefone: Optional[str]
    plano: str
    status: str
    papel: str
    data_inicio: Optional[datetime]

    class Config:
        from_attributes = True

class TicketCriar(BaseModel):
    assunto: str
    mensagem: str

class TicketResposta(BaseModel):
    id: int
    assunto: str
    mensagem: str
    status: str
    criado_em: datetime

    class Config:
        from_attributes = True


# ─── Autenticação ─────────────────────────────────────────────────────────────

@app.post("/auth/login", response_model=LoginResposta)
def login(form: OAuth2PasswordRequestForm = Depends(), db: Session = Depends(obter_db)):
    usuario = db.query(modelos.Cliente).filter(modelos.Cliente.email == form.username).first()
    if not usuario or not autenticacao.verificar_senha(form.password, usuario.senha_hash):
        raise HTTPException(status_code=400, detail="E-mail ou senha incorretos")
    token = autenticacao.criar_token({"sub": usuario.email, "papel": usuario.papel})
    return {"access_token": token, "token_type": "bearer", "papel": usuario.papel, "nome": usuario.nome}


@app.get("/auth/perfil", response_model=ClienteResposta)
def perfil(usuario=Depends(autenticacao.obter_usuario_atual)):
    return usuario


# ─── Admin: Clientes ──────────────────────────────────────────────────────────

@app.get("/admin/clientes", response_model=List[ClienteResposta])
def listar_clientes(db: Session = Depends(obter_db), _=Depends(autenticacao.exigir_admin)):
    return db.query(modelos.Cliente).all()


@app.post("/admin/clientes", response_model=ClienteResposta, status_code=201)
def criar_cliente(dados: ClienteCriar, db: Session = Depends(obter_db), _=Depends(autenticacao.exigir_admin)):
    if db.query(modelos.Cliente).filter(modelos.Cliente.email == dados.email).first():
        raise HTTPException(status_code=400, detail="E-mail já cadastrado")
    cliente = modelos.Cliente(
        nome=dados.nome,
        nome_empresa=dados.nome_empresa,
        email=dados.email,
        telefone=dados.telefone,
        senha_hash=autenticacao.gerar_hash(dados.senha),
        plano=dados.plano,
        papel=dados.papel,
    )
    db.add(cliente)
    db.commit()
    db.refresh(cliente)
    return cliente


@app.put("/admin/clientes/{id}", response_model=ClienteResposta)
def atualizar_cliente(id: int, dados: ClienteAtualizar, db: Session = Depends(obter_db), _=Depends(autenticacao.exigir_admin)):
    cliente = db.query(modelos.Cliente).filter(modelos.Cliente.id == id).first()
    if not cliente:
        raise HTTPException(status_code=404, detail="Cliente não encontrado")
    for campo, valor in dados.model_dump(exclude_none=True).items():
        setattr(cliente, campo, valor)
    db.commit()
    db.refresh(cliente)
    return cliente


@app.delete("/admin/clientes/{id}", status_code=204)
def deletar_cliente(id: int, db: Session = Depends(obter_db), _=Depends(autenticacao.exigir_admin)):
    cliente = db.query(modelos.Cliente).filter(modelos.Cliente.id == id).first()
    if not cliente:
        raise HTTPException(status_code=404, detail="Cliente não encontrado")
    db.delete(cliente)
    db.commit()


# ─── Admin: Tickets ───────────────────────────────────────────────────────────

@app.get("/admin/tickets", response_model=List[TicketResposta])
def listar_tickets(db: Session = Depends(obter_db), _=Depends(autenticacao.exigir_admin)):
    return db.query(modelos.Ticket).order_by(modelos.Ticket.criado_em.desc()).all()


# ─── Cliente: Painel ──────────────────────────────────────────────────────────

@app.get("/cliente/meu-plano")
def meu_plano(usuario=Depends(autenticacao.obter_usuario_atual)):
    precos = {"start": 99.90, "pro": 197.90, "obsidian": 397.90}
    return {
        "plano": usuario.plano,
        "status": usuario.status,
        "preco": precos.get(usuario.plano, 0),
        "data_inicio": usuario.data_inicio,
    }


@app.post("/cliente/tickets", response_model=TicketResposta, status_code=201)
def abrir_ticket(dados: TicketCriar, db: Session = Depends(obter_db), usuario=Depends(autenticacao.obter_usuario_atual)):
    ticket = modelos.Ticket(
        cliente_id=usuario.id,
        assunto=dados.assunto,
        mensagem=dados.mensagem,
    )
    db.add(ticket)
    db.commit()
    db.refresh(ticket)
    return ticket


@app.get("/cliente/tickets", response_model=List[TicketResposta])
def meus_tickets(db: Session = Depends(obter_db), usuario=Depends(autenticacao.obter_usuario_atual)):
    return db.query(modelos.Ticket).filter(modelos.Ticket.cliente_id == usuario.id).order_by(modelos.Ticket.criado_em.desc()).all()


@app.post("/cliente/cancelar")
def solicitar_cancelamento(db: Session = Depends(obter_db), usuario=Depends(autenticacao.obter_usuario_atual)):
    ticket = modelos.Ticket(
        cliente_id=usuario.id,
        assunto="Solicitação de cancelamento",
        mensagem=f"Cliente {usuario.nome} ({usuario.nome_empresa}) solicitou cancelamento do plano {usuario.plano}.",
    )
    db.add(ticket)
    db.commit()
    return {"mensagem": "Solicitação de cancelamento registrada. Nossa equipe entrará em contato."}


# ─── Status OpenClaw ──────────────────────────────────────────────────────────

@app.get("/status/openclaw")
async def status_openclaw():
    try:
        async with httpx.AsyncClient(timeout=5) as client:
            resp = await client.get(f"{OPENCLAW_URL}/healthz")
            return {"online": resp.status_code == 200, "status": "operacional"}
    except Exception:
        return {"online": False, "status": "offline"}


# ─── Seed admin inicial ───────────────────────────────────────────────────────

@app.on_event("startup")
def seed_admin():
    db = next(obter_db())
    if not db.query(modelos.Cliente).filter(modelos.Cliente.papel == modelos.PapelEnum.admin).first():
        admin = modelos.Cliente(
            nome="Administrador",
            nome_empresa="QuickCode",
            email="admin@quickcodebr.com.br",
            senha_hash=autenticacao.gerar_hash("admin123"),
            plano=modelos.PlanoEnum.obsidian,
            papel=modelos.PapelEnum.admin,
            status=modelos.StatusEnum.ativo,
        )
        db.add(admin)
        db.commit()
