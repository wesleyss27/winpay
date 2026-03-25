<?php

namespace Source\App\ConsultaVendas;

use League\Plates\Engine;
use Source\Models\Vendas\Venda;
use Source\Models\Usuarios\UsuarioDados;
use Source\Models\Usuarios\Usuario;
use Source\Models\Reembolsos\Reembolso;

class AppConsultaVendas
{
    private $view;

    public function __construct($router)
    {
        $this->view = Engine::create(__DIR__ . "/../../../themes/tema1", "php");
        $this->view->addData(["router" => $router]);
        // Página pública — sem verificação de sessão
    }

    public function consultar($data)
    {
        $termo      = trim(@$_GET['q'] ?? '');
        $vendas     = [];
        $compradores= [];
        $produtores = [];
        $reembolsos = [];
        $erro       = null;
        $buscou     = false;

        if ($termo !== '') {
            $buscou = true;

            if (mb_strlen($termo) < 3) {
                $erro = "Digite ao menos 3 caracteres para pesquisar.";
            } else {
                $conditions = $this->buildConditions($termo);

                if ($conditions) {
                    $whereUD    = implode(' OR ', $conditions);
                    $resultados = (new UsuarioDados())->find($whereUD)->fetch(true);

                    if ($resultados) {
                        foreach ($resultados as $c) {
                            $compradores[$c->id] = $c;
                        }

                        $ids    = implode(',', array_keys($compradores));
                        $vendas = (new Venda())
                            ->find("comprador IN ({$ids})")
                            ->order("id DESC")
                            ->limit(50)
                            ->fetch(true);

                        if ($vendas) {
                            // Batch: produtores
                            $produtorIds = array_unique(array_filter(
                                array_map(fn($v) => (int)$v->produtor, $vendas)
                            ));
                            if ($produtorIds) {
                                $pIds = implode(',', $produtorIds);
                                $pList = (new Usuario())->find("id IN ({$pIds})")->fetch(true);
                                if ($pList) {
                                    foreach ($pList as $p) {
                                        $produtores[$p->id] = $p;
                                    }
                                }
                            }

                            // Batch: reembolsos por venda
                            $vendaIds = implode(',', array_map(fn($v) => (int)$v->id, $vendas));
                            $rebList  = (new Reembolso())->find("venda_id IN ({$vendaIds})")->fetch(true);
                            if ($rebList) {
                                foreach ($rebList as $r) {
                                    // Pode haver mais de um por venda; guarda array
                                    $reembolsos[$r->venda_id][] = $r;
                                }
                            }
                        }
                    }
                }
            }
        }

        echo $this->view->render("consulta_vendas/consulta", [
            "title"      => "Consultar Venda",
            "termo"      => htmlspecialchars($termo, ENT_QUOTES, 'UTF-8'),
            "vendas"     => $vendas,
            "compradores"=> $compradores,
            "produtores" => $produtores,
            "reembolsos" => $reembolsos,
            "erro"       => $erro,
            "buscou"     => $buscou,
        ]);
    }

    private function buildConditions(string $termo): array
    {
        $conditions    = [];
        $apenasDigitos = preg_replace('/\D/', '', $termo);

        // CPF / CNPJ → busca exata
        if (strlen($apenasDigitos) === 11 || strlen($apenasDigitos) === 14) {
            $conditions[] = "cpf_cnpj = '{$apenasDigitos}'";
            return $conditions;
        }

        // E-mail válido → busca exata
        if (filter_var($termo, FILTER_VALIDATE_EMAIL)) {
            $email = addslashes($termo);
            $conditions[] = "email = '{$email}'";
            return $conditions;
        }

        // Telefone (8+ dígitos) → busca parcial
        if (strlen($apenasDigitos) >= 8) {
            $conditions[] = "telefone LIKE '%{$apenasDigitos}%'";
        }

        // Texto: nome e e-mail parcial
        $t = addslashes($termo);
        $conditions[] = "nome LIKE '%{$t}%'";
        $conditions[] = "email LIKE '%{$t}%'";

        return $conditions;
    }
}
