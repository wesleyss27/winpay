<?php

namespace Source\App\Api;

use Source\Models\Vendas\Venda;
use Source\Models\Enderecos\Endereco;
use Source\Models\Usuarios\UsuarioDados;

class AppEnderecoApi
{
    public function __construct($router) {}

    private function json($data, int $status = 200): void
    {
        http_response_code($status);
        header("Content-Type: application/json");
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
    }

    private function autenticar(array $data): bool
    {
        $key = $data["api_key"] ?? ($_SERVER["HTTP_X_API_KEY"] ?? "");
        return hash_equals(API_KEY_ENDERECO, $key);
    }

    public function atualizarEndereco(array $data): void
    {
        // Aceita JSON body além de form-data
        $contentType = $_SERVER["CONTENT_TYPE"] ?? "";
        if (strpos($contentType, "application/json") !== false) {
            $json = json_decode(file_get_contents("php://input"), true);
            if (is_array($json)) {
                $data = array_merge($data, $json);
            }
        }

        if (!$this->autenticar($data)) {
            $this->json(["erro" => "Não autorizado"], 401);
            return;
        }

        $transacao = trim($data["transacao"] ?? "");
        if (!$transacao) {
            $this->json(["erro" => "transacao e obrigatorio"], 400);
            return;
        }

        $rua    = trim($data["rua"]    ?? "");
        $bairro = trim($data["bairro"] ?? "");
        $cidade = trim($data["cidade"] ?? "");
        $uf     = strtoupper(trim($data["uf"] ?? $data["estado"] ?? ""));
        $cep    = preg_replace('/\D/', '', $data["cep"] ?? "");

        if (!$rua || !$bairro || !$cidade || !$uf || !$cep) {
            $this->json(["erro" => "rua, bairro, cidade, uf e cep sao obrigatorios"], 400);
            return;
        }

        $venda = (new Venda())->find("transacao = :t", "t={$transacao}")->fetch();
        if (!$venda) {
            $this->json(["erro" => "Transacao nao encontrada"], 404);
            return;
        }

        $numero      = trim($data["numero"]      ?? "S/N");
        $complemento = trim($data["complemento"] ?? "");

        // Prioridade 1: registro em enderecos (checkout com endereco_obrigatorio)
        if ($venda->endereco) {
            $endereco = (new Endereco())->findById($venda->endereco);
            if ($endereco) {
                $endereco->rua         = $rua;
                $endereco->numero      = $numero;
                $endereco->complemento = $complemento;
                $endereco->bairro      = $bairro;
                $endereco->cidade      = $cidade;
                $endereco->estado      = $uf;
                $endereco->cep         = $cep;
                $endereco->save();
                $this->json(["sucesso" => true, "tabela" => "enderecos", "id" => $endereco->id]);
                return;
            }
        }

        // Fallback: campos de entrega em usuario_dados
        $comprador = (new UsuarioDados())->findById($venda->comprador);
        if (!$comprador) {
            $this->json(["erro" => "Comprador nao encontrado"], 404);
            return;
        }

        $comprador->rua_entrega         = $rua;
        $comprador->numero_entrega      = $numero;
        $comprador->complemento_entrega = $complemento;
        $comprador->bairro_entrega      = $bairro;
        $comprador->cidade_entrega      = $cidade;
        $comprador->estado_entrega      = $uf;
        $comprador->cep_entrega         = $cep;
        $comprador->save();

        $this->json(["sucesso" => true, "tabela" => "usuario_dados", "id" => $comprador->id]);
    }
}
