<?php

//CLASSE QUE CONTROLA AS ROTAS DOS PRODUTORES

namespace Source\App\Produtor;

session_start();

use League\Plates\Engine;

use Source\Models\Produtor\Produtor;
use Source\Models\Produtos\Produto;
use Source\Models\Usuarios\Usuario;
use Source\Models\Usuarios\UsuarioDados;
use Source\Helpers\Paginador;
use Source\Models\Vendas\Venda;
use Source\App\Sessao\AppSessao;
use Source\Helpers\Emails;
use Source\Models\Comissao\Comissao;
use Source\Models\Configuracoes\Taxa;

class AppProdutor
{
    private $view;
    private $sessao;
    private $tema;

    public function __construct($router)
    {
        $this->tema = @$_SESSION["tema"] ? @$_SESSION["tema"] : "tema1";
        $this->view = Engine::create(__DIR__ . "/../../../themes/$this->tema", "php");
        $this->view->addData(["router" => $router]);
    }

    public function produtor($data)
    {
        $this->sessao = (new AppSessao())->verificaSeLogado();

        $filtros = $this->filtros();

        $paginador = new Paginador();
        $pagina = @$data["page"];
        $offset = 30;
        $produtorCount = (new Usuario())->find($filtros)->count();
        $paginas = ceil($produtorCount / $offset);
        $usuarios = (new Usuario())->find($filtros)->limit($offset)->offset($paginador->paginador($pagina, $offset))->order("documento1 DESC")->fetch(true);
        $usuarioDados = new UsuarioDados();
        $vendas = (new Venda());

        if (@$_GET["tipo"] == 4) {
            $countVendas = (new Usuario())->find("usuario.id IN (SELECT id FROM vendas WHERE produtor = usuario.id)", null, "COUNT(*) as count")->fetch();
            // var_dump($countVendas);
        } elseif (@$_GET["tipo"] == 5) {
            $countVendas = (new Usuario())->find("usuario.id IN (SELECT id FROM vendas WHERE afiliado = usuario.id)", null, "COUNT(*) as count")->fetch();
            // var_dump($countVendas);
        }

        echo $this->view->render("produtor", [
            "title" => "Usuários",
            "usuarios" => $usuarios,
            "count" => $produtorCount,
            "countVendas" => @$countVendas,
            "usuarioDados" => $usuarioDados,
            "vendas" => $vendas,
            "paginador" => $paginador,
            "total" => $paginas,
            "pagina" => @$data["page"] ? $data["page"] : 1,
            "msg" => @$data["msg"],
            "classCss" => @$data["classCss"]
        ]);
    }

    public function filtros()
    {
        $tipo = @$_GET["tipo"];
        $grupo = @$_GET["grupo"];
        $grupo_afiliado = @$_GET["grupo_afiliado"];
        $grupo_produtor = @$_GET["grupo_produtor"];
        $grupo_coprodutor = @$_GET["grupo_coproducao"];
        $status = @$_GET["status"];
        $nome = @$_GET["nome"];
        $st4rt = @$_GET["st4rt"];
        $sql = array();

        if ($tipo != "") {
            $sql[] = "tipo_usuario = $tipo";
        }

        if ($grupo != "") {
            $sql[] = "grupo = $grupo";
        }

        if ($grupo_afiliado != "") {
            $sql[] = "grupo_afiliado = $grupo_afiliado";
        }

        if ($grupo_produtor != "") {
            $sql[] = "grupo_produtor = $grupo_produtor";
        }

        if ($grupo_coprodutor != "") {
            $sql[] = "grupo_coprodutor = $grupo_coprodutor";
        }

        if ($status != "") {
            $sql[] = "ativo = $status";
        }

        if ($nome != "") {
            $sql[] = "(nome LIKE '%$nome%' OR email LIKE '%$nome%')";
        }

        if ($st4rt != "") {
            $sql[] = "cliente_st4rt = $st4rt";
        }

        $result = count($sql);

        for ($i = 0; $i < $result - 1; $i++) {
            $sql[$i] .= " AND";
        }

        return implode(" ", $sql);
    }

    public function editarProdutor($data)
    {
        $this->sessao = (new AppSessao())->verificaSeLogado();
        $taxa = (new Taxa())->findById(1);

        // var_dump(@$data);
        // exit;
        $data = filter_var_array($data, FILTER_SANITIZE_STRIPPED);

        $produtor = (new Usuario())->findById($data["id"]);
        $produtor->tempo_saque = $data["tempo_saque"];
        $produtor->tempo_pix = $data["tempo_pix"];
        $produtor->tempo_boleto = @$data["tempo_boleto"] < 3 ? 3 : @$data["tempo_boleto"];
        //$produtor->taxa_saque = $data["taxa_saque"];
        $produtor->taxa_redrocket = @$data["taxa_redrocket"];
        $produtor->taxa_assinatura = @$data["taxa_assinatura"];
        $produtor->dropshipper = @$data["usuario_drop"];
        $produtor->taxa_drop = @$data["taxa_drop"] != "" ? @$data["taxa_drop"] : $taxa->taxa_drop;
        $produtor->taxa_ecommerce = @$data["taxa_ecommerce"];
        $produtor->valor_ecommerce = @$data["valor_ecommerce"];
        // $produtor->observacao = @$data["observacao"];
        $produtor->ativo = $data["ativo"];
        $produtor->cliente_st4rt = @$data["cliente_st4rt"];
        $produtor->status = $data["status_user"];

        if ($produtor->save()) {
            echo "Atualizado Com Sucesso!";
        } else {
            echo $produtor->fail()->getMessage();
        }
    }

    public function inativarProdutor($data)
    {
        $this->sessao = (new AppSessao())->verificaSeLogado();

        $data = filter_var_array($data, FILTER_SANITIZE_STRIPPED);

        $produtor = (new Usuario())->findById($data["id"]);
        $produtor->ativo = $data["ativo"];
        if ($produtor->save()) {
            echo "Atualizado com sucesso";
        } else {
            echo $produtor->fail()->getMessage();
        }
    }

    private function deleteProdutor(array $data): void
    {
        $this->sessao = (new AppSessao())->verificaSeLogado();

        if (empty($data["id"])) {
            return;
        }

        $id = filter_var($data["id"], FILTER_VALIDATE_INT);
        $user = (new Produtor())->findById($id);
        if ($user) {
            $user->destroy();
        }

        $callback["remove"] = true;
        echo json_encode($callback);
    }

    public function atualizaGrupos()
    {
        $produtor_soma = 0;
        $afiliado_soma = 0;
        $coprodutor_soma = 0;
        $faturamento = array();
        $i = 0;
        echo "<pre>";
        $vendas = (new Venda())->find("status_transacao = 4")->fetch(true);

        // INICIO VERIFICAÇÃO DE FATURAMENTO DO USUARIO POR GRUPO (PRODUTOR, COPRODUTOR, AFILIADO)
        foreach (@$vendas as $venda) {
            // echo "<br>";
            // echo "!------------------ VENDA $venda->id ---------------------!><br>";
            $produtor = @$venda->produtor;
            $afiliado = @$venda->afiliado;
            // echo "!- ID PRODUTOR -!><br>";
            // var_dump(@$produtor);
            $produtor_soma = (new Comissao())->find("tipo = 1 AND descricao LIKE '%Comissão Produtor%' AND venda = {$venda->id} AND usuario = {$produtor}", null, "SUM(valor) as valor")->fetch();
            // @$soma_produtor = $produtor_soma->valor;
            // echo "!- SOMA DO PRODUTOR: $produtor<br>";
            // echo @$soma_produtor . "<br>";
            @$faturamento[$produtor]["produtor"]["valor"] += @$produtor_soma->valor;
            // $produtor_soma = @$produtor_soma + @$prod_comissao->valor;
            // var_dump($produtor_soma);
            if (@$afiliado != "") {
                // echo "!- ID DO AFILIADO: -!><br>";
                // var_dump(@$afiliado);
                $afiliado_soma = (new Comissao())->find("tipo = 1 AND descricao LIKE '%Comissão afiliado%' AND venda = {$venda->id} AND usuario = {$afiliado}", null, "SUM(valor) as valor")->fetch();
                // @$soma_afiliado += $afiliado_soma->valor; 
                // echo "!- SOMA DO AFILIADO: $afiliado<br>";
                // echo $soma_afiliado . "<br>";
                @$faturamento[$afiliado]["afiliado"]["valor"] += @$afiliado_soma->valor;
            }
            if (@$venda->coprodutores != "") {
                $coprodutores = explode("/", @$venda->coprodutores);
                foreach (@$coprodutores as $coprodutor) {
                    if (@$coprodutor != "/" && @$coprodutor != "") {
                        $id_coprodutor = @$coprodutor;
                        // echo "!-- ID COPRODUTOR --!><br>";
                        // var_dump(@$id_coprodutor);
                        $coprodutor_soma = (new Comissao())->find("tipo = 1 AND descricao LIKE '%Comissão coprodução%' AND venda = {$venda->id} AND usuario = {$id_coprodutor}", null, "SUM(valor) as valor")->fetch();
                        // @$soma_coprodutor += $coprodutor_soma->valor; 
                        // echo "!- SOMA DO COPRODUTOR: $id_coprodutor<br>";
                        // echo $soma_coprodutor . "<br>";
                        @$faturamento[$coprodutor]["coprodutor"]["valor"] += @$coprodutor_soma->valor;
                    }
                }
            }
        }
        // var_dump($faturamento);
        // exit;
        foreach ($faturamento as $id_usuario => $valores) {
            $usuarios = (new Usuario())->findById(@$id_usuario);
            $grupo_produtor = $usuarios->grupo_produtor;
            $grupo_coprodutor = $usuarios->grupo_coprodutor;
            $grupo_afiliado = $usuarios->grupo_afiliado;
            // var_dump($id_usuario);
            // var_dump($valores);
            echo "<pre>";
            foreach ($valores as $grupo => $grupo_valor) {
                echo "<pre>";

                foreach (@$grupo_valor as $grupo_valor => $valor_grupo) {
                    var_dump($valor_grupo);
                    $grupo_valor = number_format(@$valor_grupo, 2, ".", "");
                    echo "!USUARIO: $usuarios->id";
                    if (@$grupo && $grupo == "produtor") {
                        if (@$valor_grupo > 0 && @$valor_grupo <= 99999.99) {
                            $usuarios->grupo_produtor = 1;
                        } elseif (@$valor_grupo >= 100000 && @$valor_grupo <= 249999.99) {
                            $usuarios->grupo_produtor = 2;
                            $grupo_faturamento = "100.000,00";
                            $grupo_titulo = "Especialista";
                        } elseif (@$valor_grupo >= 250000 && @$valor_grupo <= 499999.99) {
                            $usuarios->grupo_produtor = 3;
                            $grupo_faturamento = "250.000,00";
                            $grupo_titulo = "Elite";
                        } elseif (@$valor_grupo >= 500000 && @$valor_grupo <= 999999.99) {
                            $usuarios->grupo_produtor = 4;
                            $grupo_faturamento = "500.000,00";
                            $grupo_titulo = "Mestre";
                        } elseif (@$valor_grupo >= 1000000 && @$valor_grupo <= 4999999.99) {
                            $usuarios->grupo_produtor = 5;
                            $grupo_faturamento = "1.000.000,00";
                            $grupo_titulo = "Grão Mestre";
                        } elseif (@$valor_grupo >= 5000000 && @$valor_grupo <= 14999999.99) {
                            $usuarios->grupo_produtor = 6;
                            $grupo_faturamento = "5.000.000,00";
                            $grupo_titulo = "Guru";
                        } elseif (@$valor_grupo >= 15000000) {
                            $usuarios->grupo_produtor = 7;
                            $grupo_faturamento = "15.000.000,00";
                            $grupo_titulo = "Lendário";
                        }

                        echo "!PRODUTOR- GRUPO: $usuarios->grupo_produtor";
                        echo "<br>FATURAMENTO $valor_grupo";

                        // ENVIO DE EMAIL CASO USUARIO MUDE DE GRUPO
                        if (@$grupo_produtor != "" && @$usuarios->grupo_produtor > 1) {
                            if (@$grupo_produtor != @$usuarios->grupo_produtor) {
                                $data["id_usuario"] = @$usuarios->id;
                                $data["grupo_titulo"] = @$grupo_titulo;
                                $data["grupo_categoria"] = "PRODUTOR";
                                $data["grupo_faturamento"] = @$grupo_faturamento;
                                $this->enviaEmail($data);
                            }
                        }
                    } elseif (@$grupo && $grupo == "afiliado") {
                        if (@$valor_grupo > 0 && @$valor_grupo <= 99999.99) {
                            $usuarios->grupo_afiliado = 1;
                        } elseif (@$valor_grupo >= 100000 && @$valor_grupo <= 249999.99) {
                            $usuarios->grupo_afiliado = 2;
                            $grupo_faturamento = "100.000,00";
                            $grupo_titulo = "Especialista";
                        } elseif (@$valor_grupo >= 250000 && @$valor_grupo <= 499999.99) {
                            $usuarios->grupo_afiliado = 3;
                            $grupo_faturamento = "250.000,00";
                            $grupo_titulo = "Elite";
                        } elseif (@$valor_grupo >= 500000 && @$valor_grupo <= 999999.99) {
                            $usuarios->grupo_afiliado = 4;
                            $grupo_faturamento = "500.000,00";
                            $grupo_titulo = "Mestre";
                        } elseif (@$valor_grupo >= 1000000 && @$valor_grupo <= 4999999.99) {
                            $usuarios->grupo_afiliado = 5;
                            $grupo_faturamento = "1.000.000,00";
                            $grupo_titulo = "Grão Mestre";
                        } elseif (@$valor_grupo >= 5000000 && @$valor_grupo <= 14999999.99) {
                            $usuarios->grupo_afiliado = 6;
                            $grupo_faturamento = "5.000.000,00";
                            $grupo_titulo = "Guru";
                        } elseif (@$valor_grupo >= 15000000) {
                            $usuarios->grupo_afiliado = 7;
                            $grupo_faturamento = "15.000.000,00";
                            $grupo_titulo = "Lendário";
                        }
                        echo "!AFILIADO- GRUPO: $usuarios->grupo_afiliado";
                        echo "<br>FATURAMENTO $valor_grupo";

                        // ENVIO DE EMAIL CASO USUARIO MUDE DE GRUPO
                        if (@$grupo_afiliado != "" && @$usuarios->grupo_afiliado > 1) {
                            if (@$grupo_afiliado != @$usuarios->grupo_afiliado) {
                                $data["id_usuario"] = @$usuarios->id;
                                $data["grupo_titulo"] = @$grupo_titulo;
                                $data["grupo_categoria"] = "AFILIADO";
                                $data["grupo_faturamento"] = @$grupo_faturamento;
                                $this->enviaEmail($data);
                            }
                        }
                    } elseif (@$grupo && @$grupo == "coprodutor") {
                        if (@$valor_grupo > 0 && @$valor_grupo <= 99999.99) {
                            $usuarios->grupo_coprodutor = 1;
                        } elseif (@$valor_grupo >= 101000 && @$valor_grupo <= 249999.99) {
                            $usuarios->grupo_coprodutor = 2;
                            $grupo_faturamento = "100.000,00";
                            $grupo_titulo = "Especialista";
                        } elseif (@$valor_grupo >= 250000 && @$valor_grupo <= 499999.99) {
                            $usuarios->grupo_coprodutor = 3;
                            $grupo_faturamento = "250.000,00";
                            $grupo_titulo = "Elite";
                        } elseif (@$valor_grupo >= 500000 && @$valor_grupo <= 999999.99) {
                            $usuarios->grupo_coprodutor = 4;
                            $grupo_faturamento = "500.000,00";
                            $grupo_titulo = "Mestre";
                        } elseif (@$valor_grupo >= 1000000 && @$valor_grupo <= 4999999.99) {
                            $usuarios->grupo_coprodutor = 5;
                            $grupo_faturamento = "1.000.000,00";
                            $grupo_titulo = "Grão Mestre";
                        } elseif (@$valor_grupo >= 5000000 && @$valor_grupo <= 14999999.99) {
                            $usuarios->grupo_coprodutor = 6;
                            $grupo_faturamento = "5.000.000,00";
                            $grupo_titulo = "Guru";
                        } elseif (@$valor_grupo >= 15000000) {
                            $usuarios->grupo_coprodutor = 7;
                            $grupo_faturamento = "15.000.000,00";
                            $grupo_titulo = "Lendário";
                        }
                        echo "!COPRODUTOR- GRUPO: $usuarios->grupo_coprodutor";
                        echo "<br>FATURAMENTO $valor_grupo";

                        // ENVIO DE EMAIL CASO USUARIO MUDE DE GRUPO
                        if (@$grupo_coprodutor != "" && @$usuarios->grupo_coprodutor > 1) {
                            if (@$grupo_coprodutor != @$usuarios->grupo_coprodutor) {
                                $data["id_usuario"] = @$usuarios->id;
                                $data["grupo_titulo"] = @$grupo_titulo;
                                $data["grupo_categoria"] = "COPRODUTOR";
                                $data["grupo_faturamento"] = @$grupo_faturamento;
                                $this->enviaEmail($data);
                            }
                        }
                    }
                    $usuarios->save();
                }
            }
        }
    }

    // ENVIA EMAIL CASO USUARIO MUDE DE GRUPO - PRODUTOR | AFILIADO | COPRODUTOR
    public function enviaEmail($data)
    {
        // var_dump(@$data);
        $usuarios = (new Usuario())->findById(@$data["id_usuario"]);
        $email = new Emails();
        $grupo_titulo = @$data["grupo_titulo"];
        $grupo_categoria = @$data["grupo_categoria"];
        $grupo_faturamento = @$data["grupo_faturamento"];

        if (@$grupo_categoria == "") {
            $mensagem = '<p>Você acaba de atingir a marca de <b class="valor"> R$ ' . @$grupo_faturamento . '</b> de faturamento e com essa conquista atingiu o nível</p><br>
                         <div class="categoria">' . @$grupo_titulo . '</div><br> 
                         Um grande abraço e boas vendas';
        } else {
            $mensagem = '<p>Você acaba de atingir a marca de <b class="valor"> R$ ' . @$grupo_faturamento . '</b> de faturamento como <b>' . @$grupo_categoria . '</b> e com essa conquista atingiu o nível</p><br>
            <div class="categoria">' . @$grupo_titulo . '</div><br> 
            Um grande abraço e boas vendas';
        }

        $emails[1] = @$usuarios->email;
        // $emails[2] = "pedrohenriquedasilva100@gmail.com";
        $emails[2] = "suporte@winpay.digital";

        foreach ($emails as $emails) {
            $msg["email"] = $emails;
            // echo "EMAIL: $msg[email]";
            $msg["assunto"] = "Parabéns! Você subiu de nível";
            $msg["nome"] = @$usuarios->nome_abreviado;
            $msg["mensagem"] =  '<header class="topo center">
            <h1><img src="https://i.makeagif.com/media/5-05-2016/KF7Fl2.gif" style="margin-left: 28%" width="50%"></h1>
            <div style="font-size:18px; text-align:center">
            <h2>Parabéns ' . @$usuarios->nome_abreviado . '</h2><br>
           ' . $mensagem . '</div>
            </header>';
            $email->enviaPhpMailer(@$msg);
        }
        echo "<br>$msg[mensagem]";

        echo "<br>****ENVIADO EMAIL PARA ESSE USUARIO****";
    }

    // ENVIA EMAIL CASO USUARIO MUDE DE GRUPO - FATURAMENTO GERAL (PRODUTOR + AFILIADO + COPROD)
    public function enviaEmail2($data)
    {
        $faturamento_total = (new Comissao())->find("tipo = 1 AND venda = venda", null, "usuario, SUM(valor) as soma, COUNT(*) as total")->group("usuario")->fetch(true);
        foreach ($faturamento_total as $faturamento_total) {
            echo "<pre>";
            // echo "ID FATURAMENTO TOTAL: $faturamento_total->usuario ";
            // var_dump($faturamento_total->soma);
            // var_dump($faturamento_total->total);
            // echo "<br>";
            $valor_total = number_format(@$faturamento_total->soma, 2, ".", "");
            $usuarios = (new Usuario())->findById(@$faturamento_total->usuario);
            $grupo_geral = @$usuarios->grupo;

            // FATURAMENTO GERAL
            if (@$valor_total > 0 && @$valor_total <= 99999.99)
                $usuarios->grupo = 1;
            elseif (@$valor_total >= 100000 && @$valor_total <= 249999.99) {
                $usuarios->grupo = 2;
                $grupo_faturamento = "100.000,00";
                $grupo_titulo = "Especialista";
            } elseif (@$valor_total >= 250000 && @$valor_total <= 499999.99) {
                $usuarios->grupo = 3;
                $grupo_faturamento = "250.000,00";
                $grupo_titulo = "Elite";
            } elseif (@$valor_total >= 500000 && @$valor_total <= 999999.99) {
                $usuarios->grupo = 4;
                $grupo_faturamento = "500.000,00";
                $grupo_titulo = "Mestre";
            } elseif (@$valor_total >= 1000000 && @$valor_total <= 4999999.99) {
                $usuarios->grupo = 5;
                $grupo_faturamento = "1.000.000,00";
                $grupo_titulo = "Grão Mestre";
            } elseif (@$valor_total >= 5000000 && @$valor_total <= 14999999.99) {
                $usuarios->grupo = 6;
                $grupo_faturamento = "5.000.000,00";
                $grupo_titulo = "Guru";
            } elseif (@$valor_total >= 15000000) {
                $usuarios->grupo = 7;
                $grupo_faturamento = "15.000.000,00";
                $grupo_titulo = "Lendário";
            }
            echo "!PRODUTOR $usuarios->id - GRUPO DE FATURAMENTO TOTAL: $usuarios->grupo";
            echo "<br>FATURAMENTO $valor_total";
            $usuarios->save();

            echo "<br>GRUPO GERAL ANTIGO: $grupo_geral | GRUPO GERAL ATUALIZADO: $usuarios->grupo";
            // if (@$usuarios->id == 33) {
            if (@$grupo_geral != "" && @$usuarios->grupo > 1) {
                if (@$grupo_geral != $usuarios->grupo) {
                    $data["id_usuario"] = @$usuarios->id;
                    $data["grupo_titulo"] = @$grupo_titulo;
                    $data["grupo_faturamento"] = @$grupo_faturamento;
                    $this->enviaEmail($data);
                }
            }
        }
    }
    // FIM VERIFICAÇÃO DE FATURAMENTO DO USUARIO POR GRUPO
    // 1   - 100    - Iniciante
    // 101 - 250    - Especialista
    // 251 - 500    - Elite
    // 501 - 1 M    - Mestre
    // 1M  - 5 M    - Grão Mestre
    // 5M  - 15M    - Guru
    // 15M - 30M    - Lendário
}
