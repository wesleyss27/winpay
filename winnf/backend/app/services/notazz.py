"""
Notazz API service layer.

All methods return a dict with at least:
  { "success": bool, "data": dict | None, "message": str }
"""
from __future__ import annotations

import uuid
import logging
from datetime import datetime, timezone
from typing import Any

import requests
from flask import current_app

logger = logging.getLogger(__name__)

# Map Notazz postback status strings to internal status values
STATUS_MAP: dict[str, str] = {
    "Pendente": "pendente",
    "Autorizada": "autorizada",
    "Cancelada": "cancelada",
    "Rejeitada": "rejeitada",
    "Denegada": "denegada",
}


class NotazzService:
    """Thin wrapper around the Notazz REST API."""

    def __init__(self, api_key: str) -> None:
        self.api_key = api_key

    @property
    def _base_url(self) -> str:
        return current_app.config["NOTAZZ_API_URL"]

    @property
    def _postback_url(self) -> str:
        return current_app.config["NOTAZZ_POSTBACK_URL"]

    def _post(self, payload: dict[str, Any]) -> dict[str, Any]:
        """Internal POST to Notazz API. Returns parsed JSON or raises."""
        try:
            response = requests.post(
                self._base_url,
                json=payload,
                timeout=30,
            )
            response.raise_for_status()
            return response.json()
        except requests.exceptions.Timeout:
            logger.error("Notazz API timeout")
            raise RuntimeError("Timeout ao conectar com a API Notazz.")
        except requests.exceptions.ConnectionError:
            logger.error("Notazz API connection error")
            raise RuntimeError("Erro de conexão com a API Notazz.")
        except requests.exceptions.HTTPError as exc:
            logger.error("Notazz HTTP error: %s", exc)
            raise RuntimeError(f"Erro HTTP da API Notazz: {exc}")
        except ValueError:
            logger.error("Notazz invalid JSON response")
            raise RuntimeError("Resposta inválida da API Notazz.")

    @staticmethod
    def _parse_response(raw: dict[str, Any]) -> dict[str, Any]:
        """
        Normalise Notazz response into our standard shape.
        Success indicator: codigoProcessamento == "000"
        """
        code = str(raw.get("codigoProcessamento", "")).strip()
        success = code == "000"
        return {
            "success": success,
            "code": code,
            "message": raw.get("mensagemProcessamento", raw.get("message", "")),
            "data": raw,
        }

    # ------------------------------------------------------------------
    # NFS-e
    # ------------------------------------------------------------------

    def create_nfse(
        self,
        *,
        # destinatário
        nome: str,
        cpf_cnpj: str,
        tipo: str,
        rua: str | None,
        numero: str | None,
        complemento: str | None,
        bairro: str | None,
        cidade: str | None,
        uf: str | None,
        cep: str | None,
        telefone: str | None,
        email: str | None,
        # documento
        valor: float,
        descricao: str,
        competencia: str,
        cnae: str | None,
        lc116: str | None,
        send_email: bool = True,
        external_id: str | None = None,
    ) -> dict[str, Any]:
        if external_id is None:
            external_id = str(uuid.uuid4())

        payload: dict[str, Any] = {
            "API_KEY": self.api_key,
            "METHOD": "create_nfse",
            "DESTINATION_NAME": nome,
            "DESTINATION_TAXID": cpf_cnpj,
            "DESTINATION_TAXTYPE": tipo,
            "DESTINATION_STREET": rua or "",
            "DESTINATION_NUMBER": numero or "",
            "DESTINATION_COMPLEMENT": complemento or "",
            "DESTINATION_DISTRICT": bairro or "",
            "DESTINATION_CITY": cidade or "",
            "DESTINATION_UF": uf or "",
            "DESTINATION_ZIPCODE": cep or "",
            "DESTINATION_PHONE": telefone or "",
            "DESTINATION_EMAIL": email or "",
            "DESTINATION_EMAIL_SEND": (email or "") if send_email else "",
            "DOCUMENT_BASEVALUE": f"{valor:.2f}",
            "DOCUMENT_DESCRIPTION": descricao,
            "DOCUMENT_COMPETENCE": competencia,
            "DOCUMENT_CNAE": cnae or "",
            "SERVICE_LIST_LC116": lc116 or "",
            "EXTERNAL_ID": external_id,
            "DOCUMENT_ISSUE_DATE": datetime.now(timezone.utc).strftime(
                "%Y-%m-%d %H:%M:%S"
            ),
            "REQUEST_ORIGIN": "WinNF",
            "REQUEST_RETURN": self._postback_url,
        }

        raw = self._post(payload)
        result = self._parse_response(raw)
        result["external_id"] = external_id
        return result

    def consult_nfse(self, notazz_id: str) -> dict[str, Any]:
        payload = {
            "API_KEY": self.api_key,
            "METHOD": "consult_nfse",
            "DOCUMENT_ID": notazz_id,
        }
        raw = self._post(payload)
        return self._parse_response(raw)

    def cancel_nfse(self, notazz_id: str) -> dict[str, Any]:
        payload = {
            "API_KEY": self.api_key,
            "METHOD": "cancel_nfse",
            "DOCUMENT_ID": notazz_id,
        }
        raw = self._post(payload)
        return self._parse_response(raw)

    # ------------------------------------------------------------------
    # NF-e (model 55)
    # ------------------------------------------------------------------

    def create_nfe(
        self,
        *,
        # destinatário
        nome: str,
        cpf_cnpj: str,
        tipo: str,
        rua: str | None,
        numero: str | None,
        complemento: str | None,
        bairro: str | None,
        cidade: str | None,
        uf: str | None,
        cep: str | None,
        telefone: str | None,
        email: str | None,
        # documento
        valor: float,
        descricao: str,
        competencia: str,
        cnae: str | None,
        lc116: str | None,
        send_email: bool = True,
        external_id: str | None = None,
        # produto
        produto_nome: str = "Produto",
        produto_quantidade: float = 1,
        produto_valor_unitario: float | None = None,
    ) -> dict[str, Any]:
        if external_id is None:
            external_id = str(uuid.uuid4())

        unit_value = produto_valor_unitario if produto_valor_unitario is not None else valor

        payload: dict[str, Any] = {
            "API_KEY": self.api_key,
            "METHOD": "create_nfe_55",
            "DESTINATION_NAME": nome,
            "DESTINATION_TAXID": cpf_cnpj,
            "DESTINATION_TAXTYPE": tipo,
            "DESTINATION_STREET": rua or "",
            "DESTINATION_NUMBER": numero or "",
            "DESTINATION_COMPLEMENT": complemento or "",
            "DESTINATION_DISTRICT": bairro or "",
            "DESTINATION_CITY": cidade or "",
            "DESTINATION_UF": uf or "",
            "DESTINATION_ZIPCODE": cep or "",
            "DESTINATION_PHONE": telefone or "",
            "DESTINATION_EMAIL": email or "",
            "DESTINATION_EMAIL_SEND": (email or "") if send_email else "",
            "DOCUMENT_BASEVALUE": f"{valor:.2f}",
            "DOCUMENT_DESCRIPTION": descricao,
            "DOCUMENT_COMPETENCE": competencia,
            "DOCUMENT_CNAE": cnae or "",
            "SERVICE_LIST_LC116": lc116 or "",
            "EXTERNAL_ID": external_id,
            "DOCUMENT_ISSUE_DATE": datetime.now(timezone.utc).strftime(
                "%Y-%m-%d %H:%M:%S"
            ),
            "REQUEST_ORIGIN": "WinNF",
            "REQUEST_RETURN": self._postback_url,
            "DOCUMENT_PRODUCT": {
                "1": {
                    "DOCUMENT_PRODUCT_NAME": produto_nome,
                    "DOCUMENT_PRODUCT_QTD": produto_quantidade,
                    "DOCUMENT_PRODUCT_UNITARY_VALUE": f"{unit_value:.2f}",
                }
            },
        }

        raw = self._post(payload)
        result = self._parse_response(raw)
        result["external_id"] = external_id
        return result

    def consult_nfe(self, notazz_id: str) -> dict[str, Any]:
        payload = {
            "API_KEY": self.api_key,
            "METHOD": "consult_nfe_55",
            "DOCUMENT_ID": notazz_id,
        }
        raw = self._post(payload)
        return self._parse_response(raw)

    def cancel_nfe(self, notazz_id: str) -> dict[str, Any]:
        payload = {
            "API_KEY": self.api_key,
            "METHOD": "cancel_nfe_55",
            "DOCUMENT_ID": notazz_id,
        }
        raw = self._post(payload)
        return self._parse_response(raw)
