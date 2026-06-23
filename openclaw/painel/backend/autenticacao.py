from datetime import datetime, timedelta
from jose import JWTError, jwt
from passlib.context import CryptContext
from fastapi import Depends, HTTPException, status
from fastapi.security import OAuth2PasswordBearer
from sqlalchemy.orm import Session
from database import obter_db
import modelos
import os

CHAVE_SECRETA = os.environ.get("CHAVE_SECRETA", "chave-secreta-trocar-em-producao")
ALGORITMO = "HS256"
EXPIRACAO_MINUTOS = 60 * 24

contexto_senha = CryptContext(schemes=["bcrypt"], deprecated="auto")
oauth2_scheme = OAuth2PasswordBearer(tokenUrl="/auth/login")


def verificar_senha(senha: str, hash: str) -> bool:
    return contexto_senha.verify(senha, hash)


def gerar_hash(senha: str) -> str:
    return contexto_senha.hash(senha)


def criar_token(dados: dict) -> str:
    copia = dados.copy()
    copia["exp"] = datetime.utcnow() + timedelta(minutes=EXPIRACAO_MINUTOS)
    return jwt.encode(copia, CHAVE_SECRETA, algorithm=ALGORITMO)


def obter_usuario_atual(token: str = Depends(oauth2_scheme), db: Session = Depends(obter_db)):
    excecao = HTTPException(
        status_code=status.HTTP_401_UNAUTHORIZED,
        detail="Token inválido ou expirado",
        headers={"WWW-Authenticate": "Bearer"},
    )
    try:
        payload = jwt.decode(token, CHAVE_SECRETA, algorithms=[ALGORITMO])
        email = payload.get("sub")
        if not email:
            raise excecao
    except JWTError:
        raise excecao
    usuario = db.query(modelos.Cliente).filter(modelos.Cliente.email == email).first()
    if not usuario:
        raise excecao
    return usuario


def exigir_admin(usuario=Depends(obter_usuario_atual)):
    if usuario.papel != modelos.PapelEnum.admin:
        raise HTTPException(status_code=403, detail="Acesso restrito a administradores")
    return usuario
