<?php

//CLASSE QUE CONTROLA AS ROTAS DOS Produtos

namespace Source\App\Produtos;

session_start();

use League\Plates\Engine;
use Source\Models\Afiliados\Afiliacao;
use Source\Models\CoProdutor\CoProdutor;
use Source\Models\Produtos\Produto;
use Source\Models\Produtos\Plano;
use Source\App\Sessao\AppSessao;
use Source\Models\Checkout\Checkout;
use Source\Models\Campanhas\Campanha;
use Source\Models\Pixel\Pixel;
use Source\Models\URL\Url;
use Source\Models\Usuarios\UsuarioDados;
use Source\Models\Usuarios\Usuario;
use Source\Models\Vendas\Venda;

use Source\Helpers\Paginador;
use Source\Helpers\File;
use Source\Helpers\Emails;

class AppProdutos
{
    private $view;
    private $pastaProduto;
    private $sessao;
    private $tema;

    public function __construct($router)
    {
        $this->tema = @$_SESSION["tema"] ? @$_SESSION["tema"] : "tema1";
        $this->view = Engine::create(__DIR__ . "/../../../themes/$this->tema", "php");
        $this->view->addData(["router" => $router]);

        $this->pastaProduto = PASTA . "produtos/";

        $this->sessao = (new AppSessao())->verificaSeLogado();
    }

    public function produtos($data)
    {
        $paginador = new Paginador();
        $pagina = @$data["page"];
        $offset = 2;

        $filtro = $this->filtros();
        if($filtro){
            $filtros = $filtro . " AND ";
        } else {
            $filtros = "";
        }

        $produto = new Produto();

        if($_SESSION["tipo"] == 4){
            $idProdutor = @$_SESSION["id"];
            $produtosCount = (new Produto())->find($filtros."usuario = :idc", "idc={$idProdutor}")->count();
            $paginas = ceil($produtosCount / $offset);
            $produtos = $produto->find($filtros."usuario = :idc", "idc={$idProdutor}")->limit($offset)->offset($paginador->paginador($pagina, $offset))->order("id DESC")->fetch(true);
        }
        else if($_SESSION["tipo"] == 3 || $_SESSION["tipo"] == 2){
            $produtosCount = (new Produto())->find($filtros."rascunho = 0")->count();
            $paginas = ceil($produtosCount / $offset);
            $produtos = $produto->find($filtros."rascunho = 0")->limit($offset)->offset($paginador->paginador($pagina, $offset))->order("status ASC")->fetch(true);
        }
        else if ($_SESSION["tipo"] != 5 && $_SESSION["tipo"] != 6){
            $produtosCount = (new Produto())->find($filtros."(rascunho = 0 OR usuario = " . $_SESSION["id"].")")->count();
            $paginas = ceil($produtosCount / $offset);
            $produtos = $produto->find($filtros."(rascunho = 0 OR usuario = " . $_SESSION["id"].")")->limit($offset)->offset($paginador->paginador($pagina, $offset))->order("status ASC")->fetch(true);
        }

        $usuarioDados = (new UsuarioDados())->find("usuario = " . $_SESSION["id"])->fetch();

        echo $this->view->render("produtos/produtos", [
            "title" => "Produtos",
            "produtos" => @$produtos,
            "usuarioDados" => @$usuarioDados,
            "paginador" => @$paginador,
            "total" => @$paginas,
            "pagina" => @$data["page"] ? $data["page"] : 1,
            "msg" => @$data["msg"],
            "classCss" => @$data["classCss"]
        ]);
    }

    public function filtros()
    {
        $nome = @$_GET["nome"];
        $categoria = @$_GET["categoria"];
        $status = @$_GET["status"];
        $comissao = @$_GET["comissao"];
        $sql = array();

        if($nome != "") {
            $sql[] = "nome LIKE '%{$nome}%'";
        }

        if($categoria != "") {
            $sql[] = "categoria = {$categoria}";
        }

        if($status != "") {
            $sql[] = "status = {$status}";
        }

        if($comissao != "") {
            $sql[] = "tipo_atribuicao = {$comissao}";
        }

        $result = count($sql);
        
        for($i = 0; $i < $result - 1; $i++) {
            $sql[$i] .= " AND";
        }
        
        return implode(" ", $sql);
        
    }

    public function produtosPromovidos($data)
    {
        $paginador = new Paginador();
        $pagina = @$data["page"];
        $offset = 16;

        $filtros = $this->filtros();
        if($filtros){
            $filtros .= " AND ";
        }

        $produto = new Produto();

        $id = @$_SESSION["id"];
        $produtosCount = (new Afiliacao())->find($filtros."afiliado = :afiliado", "afiliado=".$id)->count();
        $paginas = ceil($produtosCount / $offset);
        $afiliacao = (new Afiliacao())->find($filtros."afiliado = :afiliado", "afiliado=".$id)->limit($offset)->offset($paginador->paginador($pagina, $offset))->order("id DESC")->fetch(true);
        //var_dump($afiliacao);

        echo $this->view->render("produtos/produtos-promovidos", [
            "title" => "Produtos Que Promovo",
            "afiliacao" => $afiliacao,
            "paginador" => $paginador,
            "total" => $paginas,
            "pagina" => @$data["page"] ? $data["page"] : 1,
            "msg" => @$data["msg"],
            "classCss" => @$data["classCss"]
        ]);
    }

    public function minhasCoproducoes($data)
    {
        $paginador = new Paginador();
        $pagina = @$data["page"];
        $offset = 16;

        $filtros = $this->filtros();
        if($filtros){
            $filtros .= " AND ";
        }

        $produto = new Produto();

        $id = @$_SESSION["id"];
        $produtosCount = (new CoProdutor())->find($filtros."co_produtor = $id")->count();
        $paginas = ceil($produtosCount / $offset);
        $coproducao = (new CoProdutor())->find($filtros."co_produtor = $id")->limit($offset)->offset($paginador->paginador($pagina, $offset))->order("id DESC")->fetch(true);
        //var_dump($afiliacao);

        echo $this->view->render("produtos/minhas-coproducoes", [
            "title" => "Minhas Coproduções",
            "coproducao" => $coproducao,
            "paginador" => $paginador,
            "total" => $paginas,
            "pagina" => @$data["page"] ? $data["page"] : 1,
        ]);
    }

    public function aceitarCoproducao($data)
    {
        $data = filter_var_array($data, FILTER_SANITIZE_STRIPPED);

        $coproducao = (new CoProdutor())->findById($data["id"]);
        $coproducao->aceito = $data["aceito"];

        if($coproducao->save()){
            echo "Salvo com sucesso!";
        } else {
            echo $coproducao->fail()->getMessage();
        }
    }

    public function novo($data)
    {
        $usuarioDados = (new UsuarioDados())->find("usuario = " . $_SESSION["id"])->fetch();
        echo $this->view->render("produtos/novo_produto", [
            "title" => "Novo Produto",
            "usuarioDados" => @$usuarioDados,
        ]);
    }

    public function detalhes($data)
    {
        $afiliacao = new Afiliacao();
        $checkout = new Checkout();
        $produto = (new Produto())->findById($data["codigo"]);
        $usuario = (new UsuarioDados())->find("usuario = $produto->usuario")->fetch();
        $urls = (new Url())->find("produto = " . $produto->id)->fetch(true);
        $planos = (new Plano())->find("produto = $produto->id AND ativo = 1 AND privado = 0")->fetch(true);
        
        echo $this->view->render("produtos/detalhe_produto", [
            "title" => @$produto->nome,
            "produto" => $produto,
            "planos" => $planos,
            "usuario" => $usuario,
            "urls" => $urls,
            "afiliacao" => $afiliacao,
            "checkout" => $checkout,
            "msg" => ""
        ]);
    }

    public function editarStatus($data)
    {
        $data = filter_var_array($data, FILTER_SANITIZE_STRIPPED);

        $id = $data["id"];
        $produto = (new Produto())->findById($id);
        $status = $produto->status;

        if($produto->rascunho == 1){
            $produto->status = 0;
            $produto->save();
            $msg = "Produto ainda como rascunho!"."<br>";
            $classCss = "bg-danger";
        }
        else {

            $plano = (new Plano())->find("produto = $produto->id")->fetch(true);
            
            if($data["status"])
            $produto->status = $data["status"];

            if($data["observacao"])
            $produto->observacao = @$data["observacao"];

            if(@$data["st4rt"])
            $produto->st4rt = @$data["st4rt"];
            $produto->usuario_ultima_alteracao = $_SESSION["id"];

            if($produto->save()){
                $msg = "Atualizado Com Sucesso!";
                $classCss = "bg-success";

                if(@$data["status"] && $plano){
                    foreach($plano as $plano){
                        $plano->status = $data["status"];
                        $plano->save();
                    }
                }

                $email = new Emails();
                $usuario = (new Usuario())->findById($produto->usuario);
                $data["nome"] = $usuario->nome;
                $data["email"] = $usuario->email;
                $data["produto"] = $produto->nome;

                $alertas = array(
                    2 => $email->produtoAprovado($data),
                    3 => $email->produtoReprovado($data),
                    4 => $email->produtoBloqueado($data),
                );

                if($status != $data["status"]){
                    $email->enviaSendGrid($alertas[$data["status"]]);
                }
            } 
            else {
                $msg = "Erro Ao Atualizar!"."<br>";
                $msg .= $produto->fail()->getMessage();
                $classCss = "bg-danger";
            }
        }
        header("Location: " . url("produtos/detalhe/" . $produto->id . "?msg=" . $msg . "&classCss=" . $classCss));
    }

    public function cadastro($data)
    {
        $data = filter_var_array($data, FILTER_SANITIZE_STRIPPED);

        $files = new File();
        
        $produto = (new Produto());
        $id = $produto->find()->limit(1)->order("id DESC")->fetch(true);

        if($id)
        {
            foreach($id as $id)
                $produto->codigo = $id->codigo + 1;
        }     
        else{
            $produto->codigo = "01010";
        }

        $produto->usuario = $_SESSION["id"];
        $produto->rascunho = @$data['rascunho'] != "" ? $data['rascunho'] : 0;
        //$produto->st4rt = $data["st4rt"];
        
        if($data["nome"] == "" || $data["nome"] == " ")
        {
            $msg = "Preencha todos os campos";
            $classCss = "bg-warning";
            header("Location: " . url("produtos/novo?msg=" . $msg . "&classCss=" . $classCss));
            exit;
        }
        $produto->nome = $data["nome"];
        $produto->descricao = $data["descricao"];
        $produto->tipo_produto = $data["tipo_produto"];
        $produto->categoria = $data["categoria"];
        $produto->descricao = $data["descricao"];
        $produto->tipo_cobranca = @$data["tipo_cobranca"];
        $produto->tipo_precificacao = $data["tipo_precificacao"];
        $produto->preco = $data["preco"];
        $produto->disponivel = @$data["disponivel"];
        $produto->quantidade_max = @$data["quantidade_max"];
        $produto->email_suporte = $data["email_suporte"];
        $produto->whatsapp_checkout = $data["whatsapp_checkout"];
        $produto->garantia = $data["garantia"];
        $produto->pagina_venda = $data["pagina_venda"];
        $produto->obrigado_cartao = $data["obrigado_cartao"];
        $produto->obrigado_boleto = $data["obrigado_boleto"];
        $produto->obrigado_analise = $data["obrigado_analise"];

        if($_SESSION["tipo"] == 5){
            $usuario = (new Usuario())->findById($_SESSION["id"]);
            $usuario->tipo_usuario = 4;
            $usuario->save();
            $_SESSION["tipo"] = 4;
        }

        if($produto->rascunho == 1)
            $produto->status = 0;
        else
            $produto->status = 1;

        $produto->aceite_termos = $data["aceite_termos"];
        
        $produto->foto = $files->upload($_FILES["foto"], $this->pastaProduto);
        $produto->url_midias = $data["url_midias"];
        $produto->documentos = $files->upload($_FILES["documentos"], PASTA . "documentos/", 10);

        //$produto->termos = $data["termos"];
        $produto->aceite_termos = $data["aceite_termos"];
		
				echo '<pre>';
				print_r($produto);
				echo '</pre>';

        if($produto->save())
        {

            if($produto->rascunho == 1){
                $msg = "Produto em rascunho salvo com sucesso!";
                $classCss = "bg-info";
            }
            else {
                $msg = "Produto Cadastrado Com Sucesso! Produto será enviado para análise";
                $classCss = "bg-success";

                $data["email"] = "thiago@redrocketpay.com";
                $data["assunto"] = "Novo produto cadastrado";
                $data["mensagem"] = "Novo produto cadastrado <br>" . 
                                    "Nome do produto: $produto->nome <br>" .
                                    "Produtor: " . $produto->dataProdutor()->nome;
                $enviaEmail = (new Emails())->enviaSendGrid($data);
            }

            $dados["url"] = $produto->pagina_venda;
            $dados["descricao"] = "Página de venda";
            $dados["produto"] = $produto->id;
            $dados["pv"] = 1;
            $this->urlCadastro($dados);
        } 
        else 
        {
			
            $msg = "Erro Ao Cadastrar Produto! Tente mais tarde"."<br>";
            $msg .= $produto->fail()->getMessage();
            $classCss = "bg-danger";
        }
		
        $data["msg"] = $msg;
        $data["classCss"] = $classCss;
        header("Location: " . url("produtos/detalhe/" . $produto->id . "?msg=" . $msg . "&classCss=" . $classCss));
    }

    public function editarP($data)
    {
        $produto = (new Produto())->findById($data["codigo"]);
        echo $this->view->render("produtos/editar_produto", [
            "title" => $produto->nome,
            "produto" => $produto,
            "msg" => @$data["msg"] ? $data["msg"] : "",
            "classCss" => @$data["classCss"] ? $data["classCss"] : ""
        ]);
    }

    public function editar($data)
    {
        $data = filter_var_array($data, FILTER_SANITIZE_STRIPPED);

        $id = $data["id"];
        $produto = (new Produto())->findById($id);
        $files = new File();
        
        $produto->rascunho = @$data['rascunho'] != "" ? 1 : 0;
        $produto->st4rt = $data["st4rt"];
        $produto->nome = $data["nome"];
        $produto->descricao = $data["descricao"];
        $produto->tipo_produto = $data["tipo_produto"];
        $produto->categoria = $data["categoria"];
        $produto->descricao = $data["descricao"];
        $produto->tipo_cobranca = $data["tipo_cobranca"];
        $produto->tipo_precificacao = $data["tipo_precificacao"];
        $produto->preco = $data["preco"];
        $produto->disponivel = $data["disponivel"];
        $produto->quantidade_max = $data["quantidade_max"];
        $produto->email_suporte = $data["email_suporte"];
        $produto->whatsapp_checkout = $data["whatsapp_checkout"];
        $produto->garantia = $data["garantia"];
        $produto->pagina_venda = $data["pagina_venda"];
        $produto->obrigado_cartao = $data["obrigado_cartao"];
        $produto->obrigado_boleto = $data["obrigado_boleto"];
        $produto->obrigado_analise = $data["obrigado_analise"];

        if($produto->rascunho == 1)
            $produto->status = 0;
        elseif($produto->rascunho == 0 && ($produto->status == 0 || $produto->status == 1)) {
            $produto->status = 1;
            $data["email"] = "thiago@redrocketpay.com";
            $data["assunto"] = "Novo produto cadastrado";
            $data["mensagem"] = "Novo produto cadastrado <br>" . 
                                "Nome do produto: $produto->nome <br>" .
                                "Produtor: " . $produto->dataProdutor()->nome;
            $enviaEmail = (new Emails())->enviaSendGrid($data);
        }
        else
            $produto->status = $data["status"];
        
        if(empty(@$_FILES["foto"]["name"])){
            $produto->foto = $data["foto_atual"];
        } else {
            $produto->foto = $files->upload($_FILES["foto"], $this->pastaProduto);
        }
        
        if(empty(@$_FILES["documentos"]["name"])){
            $produto->documentos = $data["doc_atual"];
        } else {
            $produto->documentos = $files->upload($_FILES["documentos"], PASTA . "documentos/", 10);
        }
        $produto->url_midias = $data["url_midias"];
        $produto->termos = $data["termos"];

        if($produto->save()){
            //var_dump($produto);
            $msg = "Atualizado Com Sucesso!";
            $classCss = "bg-success";
        } 
        else {
            $msg = "Erro Ao Atualizar!"."<br>";
            $msg .= $produto->fail()->getMessage();
            $classCss = "bg-danger";
        }

        $data["msg"] = $msg;
        $data["classCss"] = $classCss;
        $data["codigo"] = $produto->id;

        //echo url("produtos/detalhe/" . $produto->id . "?msg=" . $msg . "&classCss=" . $classCss);
        header("Location: " . url("produtos/detalhe/" . $produto->id . "?msg=" . $msg . "&classCss=" . $classCss));
    }

    public function afiliados($data)
    {
        $produto = (new Produto())->findById($data["codigo"]);
        $afiliacao = (new Afiliacao())->find("produto = :produto", "produto=".$data["codigo"])->fetch(true);
        $vendas = (new Venda());

        echo $this->view->render("produtos/afiliados_produto", [
            "title" => "Afiliação",
            "produto" => $produto,
            "afiliacao" => $afiliacao,
            "vendas" => $vendas,
        ]);
    }

    public function afiliadosProduto($data)
    {
        $data = filter_var_array($data, FILTER_SANITIZE_STRIPPED);
        $produto = (new Produto())->findById($data["id"]);
        
        $produto->abrir_afiliacao = @$data["abrir_afiliacao"];
        $produto->afiliados_aprovacao = @$data["afiliados_aprovacao"];
        $produto->visualizacao = @$data["visualizacao"];
        $produto->libera_comprador = @$data["libera_comprador"];
        $produto->tempo_cookie = @$data["tempo_cookie"];
        $produto->tipo_comissao = @$data["tipo_comissao"];
        $produto->comissao_afiliado = @$data["comissao_afiliado"];
        $produto->tipo_atribuicao = @$data["tipo_atribuicao"];
        $produto->termos = $data["termos"];
        $produto->descricao_afiliados = @$data["descricao_afiliados"];

        if($produto->save()){
            echo "Salvo com sucesso";
        }
        else {
            echo "Erro ao salvar <br>";
            echo $produto->fail()->getMessage();
        }
    }

    public function afiliacaoExcluirTodos($data)
    {
        $data = filter_var_array($data, FILTER_SANITIZE_STRIPPED);

        $afiliacao = (new Afiliacao())->find("produto = " . $data["id"])->fetch(true);
        
        if($afiliacao){
            foreach($afiliacao as $afiliacao){
                $data["nome"] = $afiliacao->dataAfiliado()->nome;
                $data["email"] = $afiliacao->dataAfiliado()->email;
                $data["produto"] = $afiliacao->dataProduto()->nome;

                $emailAfiliado = $email->afiliacaoExcluir($data);
                $email->enviaSendGrid($emailAfiliado);

                $afiliacao->destroy();
            }
        }
    }

    public function coproducao($data)
    {
        $produto = (new Produto())->findById($data["codigo"]);
        $coproducao = (new CoProdutor())->find("produto = :produto", "produto=".$data["codigo"])->fetch(true);

        echo $this->view->render("produtos/coproducao_produto", [
            "title" => "Coprodução",
            "produto" => $produto,
            "coproducao" => $coproducao,
            "msg" => ""
        ]);
    }

    public function coproducaoCadastro($data)
    {
        $data = filter_var_array($data, FILTER_SANITIZE_STRIPPED);

        $coproducao = new CoProdutor();

        $gerente = array(
            0 => "coprodutor",
            1 => "gerente"
        );

        $coprodutor= $coproducao->find("co_produtor = '" . $data["co_produtor"] . "' AND produto = '" . $data["produto"] . "'")->fetch(true);
        if($coprodutor > 0) 
        {
            echo "Produtor já cadastrado!";
        } 
        else 
        {

            $coproducao->produto = $data["produto"];
            $coproducao->co_produtor = $data["co_produtor"];
            $coproducao->tipo_comissao = $data["tipo_comissao"];
            $coproducao->comissao = $data["comissao"];
            $coproducao->tipo_comissao_afiliados = @$data["tipo_comissao_afiliados"];
            $coproducao->comissao_afiliados = @$data["comissao_afiliados"];
            $coproducao->gerente = $data["gerente"];
            $coproducao->aceito = $data["aceito"];
            $coproducao->todos_afiliados = $data["todos_afiliados"];
            $coproducao->data_limite = @$data["data_limite"];

            if($coproducao->save())
            {
                echo "Salvo com sucesso";

                $email = new Emails();
                $usuario = (new Usuario())->findById($coproducao->co_produtor);
                $data["nome"] = $usuario->nome;
                $data["email"] = $usuario->email;
                $data["produto"] = $coproducao->dataProduto()->nome;
                $data["gerente"] = $gerente[$coproducao->gerente];
                
                $coproducaoEmail = $email->coproducaoCadastro($data);
                $email->enviaSendGrid($coproducaoEmail);
                
            }
            else 
            {
                echo "Erro ao salvar <br>";
                echo $coproducao->fail()->getMessage();
            }
        }
    }

    public function coproducaoEditar($data)
    {
        $data = filter_var_array($data, FILTER_SANITIZE_STRIPPED);

        $gerente = array(
            0 => "coprodutor",
            1 => "gerente"
        );

        $coproducao = (new CoProdutor())->findById($data["id"]);

        $coproducao->produto = $data["produto"];
        $coproducao->co_produtor = $data["co_produtor"];
        $coproducao->tipo_comissao = $data["tipo_comissao"];
        $coproducao->comissao = $data["comissao"];
        $coproducao->tipo_comissao_afiliados = @$data["tipo_comissao_afiliados"];
        $coproducao->comissao_afiliados = @$data["comissao_afiliados"];
        $coproducao->gerente = $data["gerente"];
        $coproducao->aceito = $data["aceito"];
        $coproducao->todos_afiliados = $data["todos_afiliados"];
        $coproducao->data_limite = @$data["data_limite"];

        if($coproducao->save())
        {
            echo "Salvo com sucesso";

            $email = new Emails();
            $usuario = (new Usuario())->findById($coproducao->co_produtor);
            $data["nome"] = $usuario->nome;
            $data["email"] = $usuario->email;
            $data["produto"] = $coproducao->dataProduto()->nome;
            $data["gerente"] = $gerente[$coproducao->gerente];
            
            $coproducaoEmail = $email->coproducaoEditar($data);
            $email->enviaSendGrid($coproducaoEmail);
        }
        else 
        {
            echo "Erro ao salvar <br>";
            echo $coproducao->fail()->getMessage();
        }
    }

    public function coproducaoExcluir($data)
    {
        $data = filter_var_array($data, FILTER_SANITIZE_STRIPPED);

        $gerente = array(
            0 => "coprodutor",
            1 => "gerente"
        );

        $coproducao = (new CoProdutor())->findById($data["id"]);
        $email = new Emails();
        $usuario = (new Usuario())->findById($coproducao->co_produtor);
        $data["nome"] = $usuario->nome;
        $data["email"] = $usuario->email;
        $data["produto"] = $coproducao->dataProduto()->nome;
        $data["gerente"] = $gerente[$coproducao->gerente];
        
        $coproducaoEmail = $email->coproducaoExcluir($data);
        $email->enviaSendGrid($coproducaoEmail);

        if($coproducao->destroy()){
            echo "Excluido com sucesso";
        }
        else {
            echo "Erro ao excluir <br>";
            echo $coproducao->fail()->getMessage();
        }
    }

    public function checkout($data)
    {
        $checkout = (new Checkout())->find("produto = :produto", "produto=".$data["codigo"])->fetch(true);
        $produto = (new Produto())->findById($data["codigo"]);
        $plano = (new Plano())->find("produto = '{$data["codigo"]}'")->fetch(true);
        $pixel = (new Pixel())->find("usuario = '{$_SESSION['id']}'")->fetch(true);

        echo $this->view->render("produtos/checkout", [
            "title" => "Checkout",
            "checkout" => $checkout,
            "produto" => $produto,
            "plano" => $plano,
            "pixel" => $pixel,
        ]);
    }

    public function checkoutCadastro($data)
    {
        $data = filter_var_array($data, FILTER_SANITIZE_STRIPPED);

        $numero_de_bytes = 4;
        $resultado_bytes = random_bytes($numero_de_bytes);
        $codigo = strtoupper(bin2hex($resultado_bytes));

        $files = new File();
        $checkout = new Checkout();

        $checkout->codigo = $codigo;
        $checkout->descricao = $data["descricao"];
        $checkout->produto = $data["produto"];

        if(@$data["plano"])
        $checkout->plano = @$data["plano"];
        else
        $checkout->plano = NULL;

        $checkout->privado = @$data["privado"];
        $checkout->cupom = @$data["cupom"];
        $checkout->valor_cupom = @$data["valor_cupom"];
        $checkout->so_cartao = @$data["so_cartao"];
        $checkout->usuario = $_SESSION["id"];
        $checkout->banner = $files->upload($_FILES["banner"], PASTA . "checkout/");
        $checkout->aviso = $files->upload($_FILES["aviso"], PASTA . "checkout/");

        if(@$data["vencimento_boletos"])
            $checkout->vencimento_boletos = $data["vencimento_boletos"];

        if(@$data["usar_frete"]){
            $checkout->usar_frete           = $data["usar_frete"];
            $checkout->valor_frete          = $data["valor_frete"];
            $checkout->prazo_entrega        = $data["prazo_entrega"];
            $checkout->frete_gratis         = $data["frete_gratis"];
            $checkout->prazo_entrega_gratis = $data["prazo_entrega_gratis"];
        }

        $checkout->usar_contador = @$data["usar_contador"];
        
        if($checkout->usar_contador == 1){
            $checkout->texto_contador = @$data["texto_contador"];
            $checkout->acao_contador = @$data["acao_contador"];
            $checkout->aumento = @$data["aumento"];
        }

        $checkout->pixel_facebook = @$data["pixel_facebook"] ? $data["pixel_facebook"] : NULL;
        // $checkout->pixel_google = @$data["pixel_google"] ? $data["pixel_google"] : NULL;
        $checkout->pixel_taboola = @$data["pixel_taboola"] ? $data["pixel_taboola"] : NULL;

        if($checkout->save()){
            $msg = "Salvo com sucesso";
            // $msg .= $checkout->fail()->getMessage();
            $classCss = "bg-success";
        }
        else {
            $msg  = "Erro ao salvar <br>";
            $msg .= $checkout->fail()->getMessage();
            $classCss = "bg-warning";
        }

        header("Location: " . url("produtos/checkout/" . $checkout->produto . "?msg=" . $msg . "&classCss=" . $classCss));
    }

    public function checkoutEditar($data)
    {
        $data = filter_var_array($data, FILTER_SANITIZE_STRIPPED);

        $files = new File();
        $checkout = (new Checkout())->findById($data["id"]);
        $checkout->descricao = $data["descricao"];
        $checkout->produto = $data["produto"];

        if(@$data["plano"])
        $checkout->plano = @$data["plano"];
        else
        $checkout->plano = NULL;

        $checkout->privado = @$data["privado"];
        $checkout->cupom = @$data["cupom"];
        $checkout->valor_cupom = @$data["valor_cupom"];
        $checkout->so_cartao = @$data["so_cartao"];
        $checkout->usuario = $_SESSION["id"];

        $checkout->usar_contador = @$data["usar_contador"];
        $checkout->texto_contador = @$data["texto_contador"];
        $checkout->acao_contador = @$data["acao_contador"];
        $checkout->aumento = @$data["aumento"];

        if(@$data["vencimento_boletos"])
            $checkout->vencimento_boletos = $data["vencimento_boletos"];

        if(@$data["usar_frete"] == 0 || @$data["usar_frete"] == 1){
            $checkout->usar_frete           = $data["usar_frete"];
            $checkout->valor_frete          = $data["valor_frete"];
            $checkout->prazo_entrega        = $data["prazo_entrega"];
            $checkout->frete_gratis         = $data["frete_gratis"];
            $checkout->prazo_entrega_gratis = $data["prazo_entrega_gratis"];
        }

        if($_FILES["banner"]["name"] == ""){
            $checkout->banner = $data["banner_atual"];
        } else {
            $checkout->banner = $files->upload($_FILES["banner"], PASTA . "checkout/");
        }

        if(@$_FILES["aviso"]["name"] == ""){
            $checkout->aviso = $data["aviso_atual"];
        } else {
            $checkout->aviso = $files->upload($_FILES["aviso"], PASTA . "checkout/");
        }
        
        $checkout->pixel_facebook = @$data["pixel_facebook"] ? $data["pixel_facebook"] : NULL;
        // $checkout->pixel_google = @$data["pixel_google"] ? "" : "";
        $checkout->pixel_taboola = @$data["pixel_taboola"] ? $data["pixel_taboola"] : NULL;

        // if(@$data["pixel_facebook"]) $checkout->pixel_facebook = @$data["pixel_facebook"];
        // else $checkout->pixel_facebook = NULL;

        // if(@$data["pixel_google"]) $checkout->pixel_google = @$data["pixel_google"];
        // else $checkout->pixel_google = NULL;

        // if(@$data["pixel_taboola"]) $checkout->pixel_taboola = @$data["pixel_taboola"];
        // else $checkout->pixel_taboola = NULL;

        if($checkout->save()){
            $msg = "Atualizado com sucesso<br>" . $checkout->plano;
            // $msg .= $checkout->fail()->getMessage();
            $classCss = "bg-success";
        }
        else {
            $msg  = "Erro ao atualizar <br>";
            $msg .= $checkout->fail()->getMessage();
            $classCss = "bg-danger";
        }

        header("Location: " . url("produtos/checkout/" . $checkout->produto . "?msg=" . $msg . "&classCss=" . $classCss));
    }

    public function checkoutExcluir($data)
    {
        $data = filter_var_array($data, FILTER_SANITIZE_STRIPPED);

        $checkout = (new Checkout())->findById($data["id"]);
        if($checkout->destroy()){
            echo "Excluido com sucesso";
        }
        else {
            echo "Erro ao excluir <br>";
            echo $checkout->fail()->getMessage();
        }
    }

    public function planos($data)
    {
        $paginador = new Paginador();
        $pagina = @$data["page"];
        $offset = 16;

        $produto = (new Produto())->findById($data["codigo"]);
        $planosCount = (new Produto())->find()->count();
        $paginas = ceil($planosCount / $offset);
        $planos = (new Plano())->find("produto = '{$data["codigo"]}'")->limit($offset)->offset($paginador->paginador($pagina, $offset))->fetch(true);

        echo $this->view->render("produtos/planos", [
            "title" => "Planos",
            "produto" => $produto,
            "planos" => $planos,
            "paginador" => @$paginador,
            "total" => @$paginas,
            "pagina" => @$data["page"] ? $data["page"] : 1,
            "msg" => ""
        ]);
    }

    public function planoCadastro($data)
    {
        $data = filter_var_array($data, FILTER_SANITIZE_STRIPPED);

        $codigo = mt_rand(10000, 999999);

        $plano = new Plano();

        $plano->codigo = $codigo;
        $plano->nome = $data["nome"];
        $plano->produto = $data["produto"];
        $plano->quantidade = @$data["quantidade"];
        $plano->usuario = $_SESSION["id"];
        $plano->preco = $data["preco"];
        $plano->descricao = $data["descricao"];
        $plano->privado = @$data["privado"] == "" ? 0 : 1;
        $plano->status = $data["status"];
        $plano->ativo = @$data["ativo"] == "" ? 0 : 1;

        if($plano->save()){
            echo "Salvo com sucesso";
        }
        else {
            echo $plano->fail()->getMessage();
        }
    }

    public function planoEditar($data)
    {
        $data = filter_var_array($data, FILTER_SANITIZE_STRIPPED);

        $plano = (new Plano())->findById($data["id"]);

        $plano->nome = $data["nome"];
        $plano->quantidade = @$data["quantidade"];
        $plano->preco = $data["preco"];
        $plano->descricao = $data["descricao"];
        $plano->privado = @$data["privado"] == "" ? 0 : 1;
        $plano->status = $data["status"];
        $plano->ativo = @$data["ativo"] == "" ? 0 : 1;

        if($plano->save()){
            echo "Atualizado com sucesso";
        }
        else {
            echo $plano->fail()->getMessage();
        }
    }

    public function planoExcluir($data)
    {
        $data = filter_var_array($data, FILTER_SANITIZE_STRIPPED);

        $checkout = (new Plano())->findById($data["id"]);
        if($checkout->destroy()){
            echo "Excluido com sucesso";
        }
        else {
            echo $checkout->fail()->getMessage();
        }
    }

    /** URLS */

    public function urls($data)
    {
        $paginador = new Paginador();
        $pagina = @$data["page"];
        $offset = 16;

        $produto = (new Produto())->findById($data["codigo"]);
        $urlCount = (new Url())->find("produto = '{$data["codigo"]}'")->count();
        $paginas = ceil($urlCount / $offset);
        $urls = (new Url())->find("produto = '{$data["codigo"]}'")->limit($offset)->offset($paginador->paginador($pagina, $offset))->fetch(true);

        echo $this->view->render("produtos/urls", [
            "title" => "Urls",
            "produto" => $produto,
            "urls" => $urls,
            "paginador" => @$paginador,
            "total" => @$paginas,
            "pagina" => @$data["page"] ? $data["page"] : 1,
            "msg" => ""
        ]);
    }

    public function urlCadastro($data)
    {
        $data = filter_var_array($data, FILTER_SANITIZE_STRIPPED);

        $url = new Url();

        $url->descricao = $data["descricao"];
        $url->url = $data["url"];
        $url->usuario = $_SESSION["id"];
        $url->produto = $data["produto"];
        $url->privada = @$data["privada"] == "" ? 0 : 1;
        $url->pv = @$data["pv"] == "" ? 0 : 1;

        if($url->save()){
            echo "Salvo com sucesso";
        }
        else {
            echo $url->fail()->getMessage();
        }
    }

    public function urlEditar($data)
    {
        $data = filter_var_array($data, FILTER_SANITIZE_STRIPPED);

        $url = (new Url())->findById($data["id"]);

        $url->descricao = $data["descricao"];
        $url->url = $data["url"];
        $url->privada = @$data["privada"] == "" ? 0 : 1;
        $url->pv = @$data["pv"] == "" ? 0 : 1;

        if($url->save()){
            echo "Salvo com sucesso";
        }
        else {
            echo $url->fail()->getMessage();
        }
    }

    public function urlExcluir($data)
    {
        $data = filter_var_array($data, FILTER_SANITIZE_STRIPPED);

        $url = (new Url())->findById($data["id"]);
        if($url->destroy()){
            echo "Excluido com sucesso";
        }
        else {
            echo $url->fail()->getMessage();
        }
    }
    /** FIM URLS */

    public function campanhas($data)
    {
        $paginador = new Paginador();
        $pagina = @$data["page"];
        $offset = 10;

        $afiliacao = new Afiliacao();
        $checkout = new Checkout();
        $pixel = (new Pixel())->find("usuario = {$_SESSION["id"]}")->fetch(true);

        $produto = (new Produto())->findById($data["codigo"]);
        $campanhasCount = (new Campanha())->find("usuario = '{$_SESSION["id"]}'")->count();
        $paginas = ceil($campanhasCount / $offset);
        $campanhas = (new Campanha())->find("produto = '{$data["codigo"]}' AND usuario = '{$_SESSION["id"]}'")->limit($offset)->offset($paginador->paginador($pagina, $offset))->fetch(true);

        $urls = (new Url())->find("produto = " . $produto->id)->fetch(true);

        echo $this->view->render("produtos/campanhas", [
            "title" => "Campanhas",
            "produto" => $produto,
            "urls" => $urls,
            "campanhas" => $campanhas,
            "paginador" => @$paginador,
            "total" => @$paginas,
            "pagina" => @$data["page"] ? $data["page"] : 1,
            "afiliacao" => $afiliacao,
            "checkout" => $checkout,
            "pixel" => $pixel,
            "msg" => ""
        ]);
    }

    public function campanhaCadastro($data)
    {
        $data = filter_var_array($data, FILTER_SANITIZE_STRIPPED);

        $codigo = mt_rand(10000, 999999);

        $campanha = new Campanha();

        $campanha->codigo = $codigo;
        $campanha->nome = $data["nome"];
        $campanha->produto = $data["produto"];
        $campanha->usuario = $_SESSION["id"];
        $campanha->url_destino = $data["url_destino"];
        $campanha->analytics = @$data["analytics"];
        
        if(@$data["pixel"])
        $campanha->pixel = @$data["pixel"];

        if($campanha->save()){
            echo "Salvo com sucesso";
        }
        else {
            echo $campanha->fail()->getMessage();
        }
    }

    public function campanhaEditar($data)
    {
        $data = filter_var_array($data, FILTER_SANITIZE_STRIPPED);

        $campanha = (new Campanha())->findById($data["id"]);

        $campanha->nome = $data["nome"];
        $campanha->url_destino = $data["url_destino"];
        $campanha->analytics = @$data["analytics"];

        if(@$data["pixel"])
        $campanha->pixel = $data["pixel"];

        if($campanha->save()){
            echo "Salvo com sucesso";
        }
        else {
            echo $plano->fail()->getMessage();
        }
    }

    public function campanhaExcluir($data)
    {
        $data = filter_var_array($data, FILTER_SANITIZE_STRIPPED);

        $campanha = (new Campanha())->findById($data["id"]);
        if($campanha->destroy()){
            echo "Excluido com sucesso";
        }
        else {
            echo $campanha->fail()->getMessage();
        }
    }
}
