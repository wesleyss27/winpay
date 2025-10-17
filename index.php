<?php

// Endpoint especial para salvar token de push notification
if ($_SERVER['REQUEST_METHOD'] === 'POST' && parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) === '/salvar-token') {
    header('Content-Type: application/json');

    try {
        $data = json_decode(file_get_contents('php://input'), true);

        if (!$data || !isset($data['userId'], $data['subscription'])) {
            throw new Exception("Dados inválidos: userId e subscription são obrigatórios.");
        }

        $userId = $data['userId'];
        $subscription = json_encode($data['subscription'], JSON_UNESCAPED_UNICODE);

        $dbPath = __DIR__ . '/db.sqlite';
        $pdo = new PDO('sqlite:' . $dbPath);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Criação automática da tabela
        $pdo->exec("CREATE TABLE IF NOT EXISTS push_subscriptions (
            user_id TEXT PRIMARY KEY,
            subscription TEXT NOT NULL
        )");

        // Salvar ou atualizar
        $stmt = $pdo->prepare("INSERT OR REPLACE INTO push_subscriptions (user_id, subscription) VALUES (:user_id, :subscription)");
        $stmt->execute([
            ':user_id' => $userId,
            ':subscription' => $subscription
        ]);

        echo json_encode([
            'status' => $userId,
            'mensagem' => 'Assinatura salva com sucesso!',
            'caminho_banco' => realpath($dbPath)
        ]);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode([
            'status' => 'erro',
            'mensagem' => $e->getMessage()
        ]);
    }

    exit; // Finaliza aqui, para não continuar o fluxo normal da aplicação
}

//CHAMA O AUTOLOAD
require __DIR__ . "/vendor/autoload.php";

use CoffeeCode\Router\Router;
use Source\App\WebHooks\AppWebHook;

//CONSTANTES FICAM NO ARQUIVO \SOURCE\CONFIG.PHP
$router = new Router(URL_BASE);

/*
* NAMESPACE Source\App\Login
* LOGIN 
* ROTAS PARA LOGIN E PARA QUEM JÁ ESTÁ CADASTRADO 
*/
$router->namespace("Source\App\Login");
$router->group("login");
$router->get("/", "AppLogin:login", "login.login");
$router->get("/{id}", "AppLogin:ativarUsuario", "login.ativar");
$router->post("/", "AppLogin:logar", "login.logar");
$router->post("/enc", "AppLogin:loginEnc", "login.encomendas");

$router->group(null);
$router->get("/cadastro", "AppLogin:cadastro", "appusuarios.cadastro");
$router->post("/cadastrar", "AppLogin:cadastroUsuarios", "appusuarios.cadastrar");

$router->get("/login-master", "AppLoginMaster:login", "login.master");
$router->post("/logar-master", "AppLoginMaster:logar", "logar.master");


/*
* NAMESPACE App\Geral
*/
$router->namespace("Source\App\Geral");

$router->group(null);
$router->get("/", "AppWeb:painel", "appweb.painel");
$router->get("/dados", "AppWeb:painelDados", "appweb.dados");
$router->get("/marketplace", "AppWeb:marketplace", "appweb.marketplace");
$router->get("/links-uteis", "AppWeb:linksUteis", "appweb.links");
$router->get("/cursos", "AppWeb:cursos", "appweb.cursos");

$router->get("/emails", "AppWeb:ferramentaEmail", "appweb.email");
$router->post("/emails", "AppWeb:ferramentaEmail", "appweb.email");

$router->get("/termos-uso", "AppTermos:termosUso", "apptermos.termosUso");
$router->get("/termos-compra", "AppTermos:termosCompra", "apptermos.termosCompra");
$router->get("/politica-privacidade", "AppTermos:politicaPrivacidade", "apptermos.politicaPrivacidade");
$router->get("/politica-disputa", "AppTermos:politicaDisputa", "apptermos.politicaDisputa");
$router->get("/ajuda-usuarios", "AppTermos:ajudaUsuarios", "apptermos.ajudaUsuarios");

/*
* EERROS
*/
$router->group("ops");
$router->get("/{errcode}", "AppWeb:error");

$router->get("/testeResult", "Teste:teste");
$router->post("/teste", "Teste:teste2");


/*
* NAMESPACE Source\App\Vendas
*/
$router->namespace("Source\App\Vendas");

$router->group("vendas");
$router->get("/", "AppVendas:vendas", "vendas.vendas");
$router->get("/{page}", "AppVendas:vendas", "vendas.vendas");
$router->get("/coprodutor", "AppVendas:vendasCoprodutor", "vendas.coprodutor");
$router->get("/coprodutor/{page}", "AppVendas:vendasCoprodutor", "vendas.coprodutor");
$router->post("/motivo", "AppVendas:motivoCancelamento", "vendas.motivo");
$router->post("/editar", "AppVendas:respostaEditar", "vendas.editar");
$router->post("/editarP", "AppVendas:editarP", "vendas.editarP");
$router->get("/exportar", "AppVendas:exportar", "vendas.exportar");
$router->get("/exportar-vendas", "AppVendas:exportarVendas", "vendas.exportarVendas");
$router->post("/codigo-rastreio", "AppVendas:rastreio", "vendas.rastreio");

$router->group(null);
$router->get("/boleto/{venda}", "AppVendas:boletos", "vendas.boleto");


/*
* NAMESPACE App\Produtos
*/
$router->namespace("Source\App\Produtos");

$router->group("produtos");

$router->get("/novo", "AppProdutos:novo", "produtos.novo");
$router->post("/novo", "AppProdutos:cadastro", "produtos.cadastro");

$router->get("/", "AppProdutos:produtos", "produtos.home");
$router->get("/p/{page}", "AppProdutos:produtos", "produtos.home");

$router->get("/promovidos", "AppProdutos:produtosPromovidos", "produtos.promovidos");

$router->get("/coproducoes", "AppProdutos:minhasCoproducoes", "produtos.coproducoes");

// DETALHE
$router->get("/detalhe/{codigo}", "AppProdutos:detalhes", "produtos.detalhes");
$router->post("/editar-status", "AppProdutos:editarStatus", "produtos.editarStatus");
$router->post("/inativar-produto", "AppProdutos:inativarProduto", "produtos.inativar");
$router->post("/ativar-produto", "AppProdutos:ativarProduto", "produtos.ativar");
$router->post("/excluir-produto", "AppProdutos:excluirProduto", "produtos.excluir");

// EDITAR
$router->get("/editar/{codigo}", "AppProdutos:editarP", "produtos.editar");
$router->post("/editar", "AppProdutos:editar", "produtos.editarPost");

// CHECKOUTS
$router->get("/checkout/{codigo}", "AppProdutos:checkout", "produtos.checkout");
$router->get("/checkout-produto/{codigo}", "AppProdutos:checkoutGuiado", "produtos.checkoutGuiado");
$router->post("/checkout", "AppProdutos:checkoutCadastro", "produtos.checkoutCadastro");
$router->post("/checkout-editar", "AppProdutos:checkoutEditar", "produtos.checkoutEditar");
$router->post("/checkout-excluir", "AppProdutos:checkoutExcluir", "produtos.checkoutExcluir");

// PLANOS
$router->get("/planos/{codigo}", "AppProdutos:planos", "produtos.planos");
$router->get("/plano-produto/{codigo}", "AppProdutos:planoGuiado", "produtos.planoGuiado");
$router->get("/planos/{codigo}/p/{page}", "AppProdutos:planos", "produtos.planos");
$router->post("/planos", "AppProdutos:planoCadastro", "produtos.planosCadastro");
$router->post("/planos-guiado", "AppProdutos:planoCadastroGuiado", "produtos.planosCadastroGuiado");
$router->post("/planos-editar", "AppProdutos:planoEditar", "produtos.planosEditar");
$router->post("/planos-excluir", "AppProdutos:planoExcluir", "produtos.planosExcluir");

// AFILIADOS
$router->post("/afiliados/{codigo}", "AppProdutos:afiliadosProduto", "produtos.afiliados1");
$router->get("/afiliados/{codigo}", "AppProdutos:afiliados", "produtos.afiliados");

// COPRODUÇÃO
$router->get("/coproducao/{codigo}", "AppProdutos:coproducao", "produtos.coproducao");
$router->post("/aceitar-coproducao", "AppProdutos:aceitarCoproducao", "produtos.aceitarCoproducao");
$router->post("/coproducao", "AppProdutos:coproducaoCadastro", "produtos.coproducaoCadastro");
$router->post("/coproducao-editar", "AppProdutos:coproducaoEditar", "produtos.coproducaoEditar");
$router->post("/coproducao-excluir", "AppProdutos:coproducaoExcluir", "produtos.coproducaoExcluir");

// URL's
$router->get("/urls/{codigo}", "AppProdutos:urls", "produtos.url");
$router->post("/urls", "AppProdutos:urlCadastro", "produtos.urlCadastro");
$router->post("/url-editar", "AppProdutos:urlEditar", "produtos.urlEditar");
$router->post("/url-excluir", "AppProdutos:urlExcluir", "produtos.urlExcluir");

// UPSELL
$router->get("/upsell/{codigo}", "AppProdutos:upsell", "produtos.upsell");
$router->post("/upsell", "AppProdutos:upsellCadastro", "produtos.upsellCadastro");
$router->post("/upsell-editar", "AppProdutos:upsellEditar", "produtos.upsellEditar");
$router->post("/upsell-excluir", "AppProdutos:upsellExcluir", "produtos.upsellExcluir");

// CUPONS
$router->get("/cupons/{codigo}", "AppCupons:cupons", "produtos.cupons");
$router->post("/cupons", "AppCupons:cupomCadastro", "produtos.cupomCadastro");
$router->post("/cupons-editar", "AppCupons:cupomEditar", "produtos.cupomEditar");
$router->post("/cupons-excluir", "AppCupons:cupomExcluir", "produtos.cupomExcluir");
$router->post("/cupons-buscar", "AppCupons:cupomBuscar", "produtos.cupomBuscar");

// NOTIFICAÇÕES
$router->get("/notificacoes/{codigo}", "AppNotificacoes:notificacoesCheckout", "produtos.notificacoes");
$router->post("/notificacoes", "AppNotificacoes:notificacaoCadastro", "produtos.notificacaoCadastro");
$router->post("/notificacoes-editar", "AppNotificacoes:notificacaoEditar", "produtos.notificacaoEditar");
$router->post("/notificacoes-excluir", "AppNotificacoes:notificacaoExcluir", "produtos.notificacaoExcluir");

// TELEVENDAS
$router->get("/televendas/{codigo}", "AppProdutos:televendas", "produtos.televendas");
$router->post("/televendas", "AppProdutos:televendasCadastro", "televendas.cadastro");

// CAMPANHAS
$router->get("/campanhas/{codigo}", "AppProdutos:campanhas", "produtos.campanhas");
$router->get("/campanhas/{codigo}/{page}", "AppProdutos:campanhas", "produtos.campanhas");
$router->post("/campanha", "AppProdutos:campanhaCadastro", "produtos.campanha");
$router->post("/campanha-editar", "AppProdutos:campanhaEditar", "produtos.campanhaEditar");
$router->post("/campanha-excluir", "AppProdutos:campanhaExcluir", "produtos.campanhaExcluir");

// ROCKETS
$router->group("rockets");
$router->get("/", "AppRockets:produtos", "rockets.home");


/*
* NAMESPACE Source\App\AppEcommerce
*/
$router->namespace("Source\App\Ecommerce");

$router->group("ecommerce");
$router->get("/", "AppEcommerce:ecommerce", "ecommerce.home");
$router->post("/cadastrar", "AppEcommerce:cadastrarEcommerce", "ecommerce.cadastrar");
$router->post("/busca-url", "AppEcommerce:buscarUrlLoja", "ecommerce.url");
$router->post("/modo-cobranca", "AppEcommerce:modoCobranca", "ecommerce.cobranca");

$router->group("loja");
$router->get("/{url_loja}", "AppEcommerce:loja", "ecommerce.loja");
$router->get("/{url_loja}/{produto}/{nome_produto}", "AppEcommerce:produtoLoja", "ecommerce.produto");
$router->get("/{url_loja}/{categoria}", "AppEcommerce:loja", "ecommerce.categoria");
$router->post("/{url_loja}", "AppEcommerce:buscaProduto", "ecommerce.busca");

/*
* NAMESPACE Source\App\Assinaturas
*/
$router->namespace("Source\App\Assinaturas");

$router->group("assinaturas");
$router->get("/", "AppAssinaturas:assinaturas", "assinaturas.home");
$router->get("/{page}", "AppAssinaturas:assinaturas", "assinaturas.home");
$router->get("/extrato", "AppAssinaturas:extratoAssinaturas", "assinaturas.extrato");
$router->get("/pagamentos", "AppAssinaturas:pagamentosAssinaturas", "assinaturas.pagamentos");
$router->post("/editar", "AppAssinaturas:editarAssinatura", "assinaturas.editar");
$router->post("/editarP", "AppAssinaturas:editarP", "vendas.editarP");
$router->get("/exportar", "AppAssinaturas:exportar", "vendas.exportar");
$router->get("/exportar-vendas", "AppAssinaturas:exportarVendas", "vendas.exportarVendas");
$router->get("/verificar-assinaturas", "AppAssinaturas:verificarAssinaturas", "assinaturas.verificar");
$router->get("/input-venda", "AppAssinaturas:renderInputVenda", "assinaturas.inputVenda");
$router->post("/venda-manual", "AppAssinaturas:criarVendaManual", "assinaturas.vendaManual");


/*
* NAMESPACE Source\App\CheckoutTransparente
*/
$router->namespace("Source\App\CheckoutTransparente");

$router->group("checkout-transparente");
$router->get("/configuracao", "AppCheckoutTransparente:checkoutTransparente", "checkout.transparente");
$router->get("/cobrancas", "AppCheckoutTransparente:cobrancas", "transparente.cobrancas");
$router->get("/cobrancas/{page}", "AppCheckoutTransparente:checkoutTransparente", "transparente.cobrancas");
$router->post("/cadastro-cartao", "AppCheckoutTransparente:cadastrarCartao", "checkout.cartao");
$router->post("/excluir-cartao", "AppCheckoutTransparente:excluirCartao", "checkout.cartaoExcluir");
$router->post("/cadastro-adquirente", "AppCheckoutTransparente:cadastrarAdquirente", "checkout.adquirente");
$router->post("/excluir-adquirente", "AppCheckoutTransparente:excluirAdquirente", "checkout.adquirenteExcluir");
$router->post("/novoBoleto", "AppCheckoutTransparente:gerarNovoBoleto", "chkTransparente.novoBoleto");
$router->post("/boletoPagSeguro", "AppPagSeguro:compraBoleto", "boleto.pagseguro");

$router->post("/", "AppTransacao:transacao", "checkout.transacaoTransparente");
$router->post("/pagseguro", "Pagseguro:webhookPagseguro", "webhook.pagseguro");
// $router->post("/testewebhook", "Pagseguro:testewebhook", "webhook.testeteste");
$router->get("/consulta-cielo", "Cielo:consultaPedidos", "cielo.consultaPedidos");
$router->get("/consulta-pagarme", "PagarMe:consultaPedidos", "pagarme.consultaPedidos");
$router->get("/consulta-mp", "MercadoPago:consultaPedidos", "mercadoPago.consultaPedidos");
$router->get("/consulta-moip", "Wirecard:consultaPedidos", "wirecard.consultaPedidos");
$router->get("/consulta-pagseguro", "Pagseguro:consultaPedidos", "pagseguro.consultaPedidos");

$router->get("/cobrar-drop", "AppCheckoutTransparente:cobrarDrop", "cobrar.drop");
$router->get("/retentativa", "AppCheckoutTransparente:retentativaCobranca", "cobrar.drop");
$router->get("/retentativa/{cobranca}", "AppCheckoutTransparente:retentativaCobranca", "cobrar.drop");
$router->post("/retentativa/{cobranca}", "AppCheckoutTransparente:retentativaCobranca", "cobrar.drop1");

$router->post("/estorno", "AppEstorno:estornar", "chkTransparente.estornar");


/*
 * NAMESPACE Source\App\Denuncias"
 */
$router->namespace("Source\App\Denuncias");

$router->group("denuncias");
$router->get("/", "AppDenuncia:denuncias", "denuncias.denuncias");
$router->post("/enviar", "AppDenuncia:cadastroDenuncia", "denuncias.cadastrar");
// $router->post("/antecipar", "AppTaxas:editarAntecipacao", "taxa.antecipar");


/*
* NAMESPACE Source\App\Comissao
*/
$router->namespace("Source\App\Comissao");

$router->group("comissao");
$router->get("/", "AppComissao:comissao", "appcomissao.produtor");
$router->get("/{page}", "AppComissao:comissao", "appcomissao.produtor");
$router->get("/redrocket", "AppComissao:comissaoRedrocket", "appcomissao.redrocket");
$router->get("/redrocket/{page}", "AppComissao:comissaoRedrocket", "appcomissao.redrocket");
$router->get("/cadastro", "AppComissao:verificaComissao", "appcomissao.cadastro");
$router->get("/busca", "AppComissao2:buscaComissoes", "appcomissao.busca"); // NOVO CADASTRO COMISSÃO
$router->get("/busca/{venda}", "AppComissao2:buscaComissoes", "appcomissao.busca");
$router->get("/saldo", "AppComissao:verificarSaldo", "appcomissao.saldo");
$router->post("/saque-pagarme", "AppComissao:saquePagarme", "appcomissao.saquePagarme");

$router->group("estorno");
$router->get("/{venda}", "AppComissao:cadastrarEstorno", "appcomissao.estorno");

$router->group("banco");
$router->get("/", "AppComissao:banco", "appcomissao.banco");
$router->get("/{page}", "AppComissao:banco", "appcomissao.banco");
$router->post("/cadastro", "AppComissao:cadastroBanco", "appcomissao.bancoCadastro");
$router->post("/editar", "AppComissao:editarBanco", "appcomissao.bancoEditar");
$router->post("/editar-status", "AppComissao:editarStatusBanco", "appcomissao.bancoStatus");
$router->post("/excluir", "AppComissao:excluirBanco", "appcomissao.bancoExcluir");

$router->group("saques");
$router->get("/", "AppComissao:saque", "appcomissao.saque");
$router->get("/{page}", "AppComissao:saque", "appcomissao.saque");
$router->post("/sacar", "AppComissao:solicitarSaque", "appcomissao.sacar");
$router->post("/", "AppComissao:saqueRedrocket", "appcomissao.saqueRedrocket");
$router->post("/status", "AppComissao:statusSaque", "appcomissao.statusSaque");

$router->group("antecipacao");
$router->get("/", "AppComissao:antecipacao", "appcomissao.antecipacao");
$router->post("/antecipar", "AppComissao:antecipar", "appcomissao.antecipar");
$router->post("/antecipar-assinatura", "AppComissao:anteciparAssinatura", "appcomissao.anteciparAssinatura");


/*
* NAMESPACE Source\App\Usuarios
*/
$router->namespace("Source\App\Usuarios");
$router->group("funcionarios");
$router->get("/", "AppUsuarios:usuarios", "appusuarios.home");
$router->get("/p/{page}", "AppUsuarios:usuarios", "appusuarios.home");

$router->post("/", "AppUsuarios:cadastroFuncionarios", "appusuarios.funcionarios");
$router->post("/editar", "AppUsuarios:editar", "appusuarios.editar");
$router->post("/aprovar", "AppUsuarios:aprovar", "appusuarios.aprovar");
$router->post("/verifica-email", "AppUsuarios:verificaEmail", "appusuarios.email");
$router->post("/verifica-login", "AppUsuarios:verificaLogin", "appusuarios.login");
$router->post("/verifica-cpf", "AppUsuarios:verificaCpf", "appusuarios.cpf");
$router->post("/busca-comprador", "AppUsuarios:buscaComprador", "appusuarios.buscaComprador");

$router->group("usuarios");

$router->post("/esqueceu-senha", "AppUsuarios:esqueceuSenha", "appusuarios.esqueceuSenha");
$router->get("/redefinir-senha/{id}/{codigo}", "AppUsuarios:redefinirSenha", "appusuarios.redefinirSenha");
$router->post("/atualiza-senha", "AppUsuarios:atualizaSenha", "appusuarios.atualizaSenha");

$router->get("/perfild/{id}", "AppUsuarios:perfilUsuarioD", "appusuarios.perfil2");
$router->get("/perfil/{id}", "AppUsuarios:perfilUsuario", "appusuarios.perfil");
$router->post("/perfil", "AppUsuarios:editarUsuarioPerfil", "appusuarios.perfilEditar");
$router->post("/perfil/foto", "AppUsuarios:fotoPerfil", "appusuarios.fotoPerfil");
$router->post("/perfil/documento", "AppUsuarios:documentoPerfil", "appusuarios.documentoPerfil");
$router->post("/perfil/tipo", "AppUsuarios:tipoUsuario", "appusuarios.tipoUsuario");
$router->post("/perfil/taxas", "AppUsuarios:alterarTaxas", "appusuarios.taxas");


/*
* NAMESPACE Source\App\Produtor
*/
$router->namespace("Source\App\Permissao");
$router->group("permissao");

$router->get("/{id}", "AppPermissao:permissao", "permissao.home");
$router->post("/salvar", "AppPermissao:salvarPermissao", "permissao.salvar");
$router->post("/excluir", "AppPermissao:excluirPermissao", "permissao.excluir");

$router->get("/paginas", "AppPermissao:paginas", "paginas.home");
$router->post("/paginas/salvar", "AppPermissao:salvarPagina", "paginas.salvar");
$router->post("/paginas/excluir", "AppPermissao:excluirPagina", "paginas.excluir");

$router->get("/usuarios", "AppPermissao:usuariosPermissao", "permissao.usuarios");
$router->post("/usuarios/salvar", "AppPermissao:salvarUsuario", "permissao.salvarUsuario");
$router->post("/usuarios/excluir", "AppPermissao:excluirPagina", "permissao.excluirUsuario");

$router->get("/logar/{permissao}", "AppPermissao:logarSubusuario", "permissao.usuarios");


/*
* NAMESPACE Source\App\Produtor
*/
$router->namespace("Source\App\Produtor");
$router->group("usuarios");
$router->get("/", "AppProdutor:produtor", "app_produtor.produtor");
$router->get("/{page}", "AppProdutor:produtor", "app_produtor.produtor");
$router->get("/atualizagrupos", "AppProdutor:atualizaGrupos", "app_produtor.grupos");
$router->get("/atualizagrupos2", "AppProdutor:enviaEmail2", "app_produtor.email");
$router->post("/editar", "AppProdutor:editarProdutor", "produtor.editar");
$router->post("/inativar", "AppProdutor:inativarProdutor", "produtor.inativar");


/*
* NAMESPACE Source\App\Afiliados
*/
$router->namespace("Source\App\Afiliados");

$router->group("afiliados");
$router->get("/", "AppAfiliados:afiliados", "appafiliados.afiliados");
$router->get("/{page}", "AppAfiliados:afiliados", "appafiliados.afiliados");

$router->post("/afiliacao", "AppAfiliados:afiliacao", "appafiliados.afiliacao");
$router->post("/editar", "AppAfiliados:editarAfiliacao", "appafiliados.editar");
$router->post("/excluir", "AppAfiliados:afiliacaoExcluir", "afiliacao.excluir");

$router->post("/editar-afiliado", "AppAfiliados:editarAfiliado", "afiliado.editar");
$router->post("/inativar", "AppAfiliados:inativarAfiliado", "afiliado.inativar");


/*
* NAMESPACE Source\App\Configuracoes
*/
$router->namespace("Source\App\Configuracoes");

$router->group("taxas");
$router->get("/", "AppTaxas:taxas", "taxa.taxas");
$router->get("/antecipar", "AppTaxas:antecipar", "taxa.antecipacao");
$router->post("/editar", "AppTaxas:editarTaxa", "taxa.editar");
$router->post("/antecipar", "AppTaxas:editarAntecipacao", "taxa.antecipar");
//$router->get("/p/{plano}", "AppCheckout:produto", "checkout.produto");


/*
* NAMESPACE Source\App\Integracoes
*/
$router->namespace("Source\App\Integracoes");

$router->group("integracoes");
$router->get("/", "AppIntegracao:integracoes", "integracoes.disponiveis");
$router->get("/ativas", "AppIntegracao:ativas", "integracoes.ativas");


/*
* NAMESPACE Source\App\Postback
*/
$router->namespace("Source\App\Postback");

$router->group("postbacks");
$router->get("/", "AppPostback:postback", "postback.postbacks");
$router->get("/{page}", "AppPostback:postback", "postback.postbacks");
$router->post("/enviar", "AppPostback:enviarPostback", "postback.enviar");
$router->post("/retorno", "AppPostback:enviarPostbackRetorno", "postback.retorno");
$router->post("/testar", "AppPostback:testarPostback", "postback.testar");
$router->post("/cadastrar", "AppPostback:cadastrarPostback", "postback.cadastrar");
$router->post("/editar", "AppPostback:editarPostback", "postback.editar");
$router->post("/excluir", "AppPostback:postbackExcluir", "postback.excluir");


/*
* NAMESPACE App\Pixel
*/
$router->namespace("Source\App\Pixel");

$router->group("pixel");
$router->get("/", "AppPixel:pixel", "pixel.pixel");
$router->get("/{page}", "AppPixel:pixel", "pixel.pixel");
$router->post("/", "AppPixel:cadastroPixel", "pixel.cadastro");
$router->post("/editar", "AppPixel:editarPixel", "pixel.editar");
$router->post("/excluir", "AppPixel:excluirPixel", "pixel.excluir");


/*
* NAMESPACE Source\App\Api
*/
$router->namespace("Source\App\Api");

$router->group("api");
$router->get("/", "AppApi:api", "api.home");
$router->get("/{page}", "AppApi:api", "api.home");
$router->post("/excluir", "AppApi:excluirApi", "api.excluir");
$router->post("/cadastrar", "AppApi:cadastrarApi", "api.cadastrar");
$router->post("/editar", "AppApi:editarApi", "api.editar");


/*
 * NAMESPACE Source\App\Notazz"
 */
$router->namespace("Source\App\Notazz");

$router->group("notas");
$router->get("/", "AppNotazz:notas", "notazz.notas");
$router->get("/{page}", "AppNotazz:notas", "notazz.notas");
$router->get("/integracao", "AppNotazz:integracao", "notazz.integracao");
$router->get("/integracao/{page}", "AppNotazz:integracao", "notazz.integracao");
$router->post("/cadastrar", "AppNotazz:cadastrarIntegracao", "notazz.cadastrar");
$router->post("/editar", "AppNotazz:editarIntegracao", "notazz.editar");
$router->post("/excluir", "AppNotazz:excluirIntegracao", "notazz.excluir");

$router->post("/gerar", "AppNotazz:gerarNota", "notazz.gerar");
$router->post("/consultar-cancelar", "AppNotazz:consultar", "notazz.consultarCancelar");

$router->post("/postback", "AppNotazz:postbackNotazz", "notazz.postback");


/*
 * NAMESPACE Source\App\ChatGuru"
 */
$router->namespace("Source\App\ChatGuru");

$router->group("chatguru");
// $router->get("/", "AppNotazz:notas", "notazz.notas");
$router->post("/cadastrar", "AppChatGuru:cadastrarChatGuru", "chat.guru");
$router->post("/editar", "AppChatGuru:editarChatGuru", "editar.chatguru");
$router->post("/excluir", "AppChatGuru:excluirChatGuru", "excluir.chatguru");
$router->post("/anotacao", "AppChatGuru:verificaChat", "anotacao.chatguru");


/*
 * NAMESPACE Source\App\Leadlovers"
 */
$router->namespace("Source\App\Leadlovers");

$router->group("leadlovers");
// $router->get("/", "AppNotazz:notas", "notazz.notas");
$router->post("/cadastro", "AppLeadlovers:cadastro", "cadastro.lead");
$router->post("/editar", "AppLeadlovers:editar", "editar.lead");
$router->post("/excluir", "AppLeadlovers:excluir", "excluir.lead");
$router->post("/verifica-cadastro", "AppLeadlovers:verificaCadastro", "conta.lead");
$router->post("/postback", "AppLeadlovers:postback", "postback.lead");
$router->post("/maquinas", "AppLeadlovers:carregaMaquinas", "maquina.lead");
$router->post("/funil", "AppLeadlovers:carregaFunil", "funil.lead");
$router->post("/nivel", "AppLeadlovers:carregaNivel", "nivel.lead");

/*
 * NAMESPACE Source\App\Shopify"
 */
$router->namespace("Source\App\Shopify");

$router->group("shopify");
$router->post("/cadastro", "AppShopify:cadastrar", "shopify.cadastro");
$router->post("/editar", "AppShopify:editar", "shopify.editar");
$router->get("/produtos", "AppShopify:importarProdutos", "shopify.produtos");
$router->post("/webhook", "AppShopify:webhookAtualiza", "shopify.atualiza");
$router->post("/webhookExcluir", "AppShopify:webhookExcluir", "shopify.Excluir");


// $router->post("/postback", "AppLeadlovers:cadastroPostback", "postback.lead");
// $router->post("/excluir", "AppChatGuru:excluirChatGuru", "excluir.chatguru");
// $router->post("/anotacao", "AppChatGuru:verificaChat", "anotacao.chatguru");


/*
 * NAMESPACE Source\App\Relatorios"
 */
$router->namespace("Source\App\Relatorios");

$router->group("relatorios");
$router->get("/", "AppRelatorios:relatorios");
$router->get("/exportar", "AppRelatorios:exportar");
$router->post("/", "AppRelatorios:relatorios");
$router->post("/busca", "AppRelatorios:buscaRelatorios", "relatorios.busca");

$router->get("/painel", "AppRelatorios:painelRedrocket");
$router->post("/painel", "AppRelatorios:painelRedrocket");

$router->get("/painel-nf", "AppRelatorios:painelNF");
$router->post("/painel-nf", "AppRelatorios:painelNF");

$router->get("/afiliados", "AppRelatoriosAfiliados:vendasAfiliados");
$router->post("/afiliados", "AppRelatoriosAfiliados:vendasAfiliados");

$router->get("/performance-afiliados", "AppRelatoriosAfiliados:performance");
$router->post("/performance-afiliados", "AppRelatoriosAfiliados:performance");

$router->get("/painel_clientes", "AppRelatorios:painelClientes");
$router->post("/painel_clientes", "AppRelatorios:painelClientes");


$router->group("analytics");
$router->get("/", "AppAnalytics:analytics", "analytics.home");


/*
* NAMESPACE Source\App\Checkout1
*/
$router->namespace("Source\App\Checkout1");

$router->group("processos");
$router->get("/notificar", "AppProcessos:formNotificacaoTodos", "processos.notificar");
$router->post("/notificar-todos", "AppProcessos:notificarTodosDisparar", "processos.dispararNotificacaoTodos");

$router->group("checkout");
$router->get("/", "AppCheckout:home", "checkout.home"); // REDIRECIONAR PARA ECOMMERCE
//$router->get("/p/{plano}", "AppCheckout:produto", "checkout.produto");
$router->get("/{plano}", "AppCheckout:produto", "checkout.produto");
$router->get("/{plano}/{venda}", "AppCheckout:produto", "checkout.produtoVenda");

$router->get("/novo", "AppCheckout:checkoutNovo", "checkout.novo");
$router->post("/validar-email", "Validacao:validaEmail", "validacao.email");

$router->get("/pixel", "AppCheckout:pixel", "checkout.pixel");
$router->get("/pixel-taboola", "AppCheckout:pixelTaboola", "checkout.pixelTaboola");
$router->get("/analytics", "AppCheckout:analytics", "checkout.analytics");
$router->post("/transacao", "AppCheckout:transacao", "checkout.transacao");
$router->post("/transacao-pix", "AppCheckout:transacaoPix", "checkout.transacaoPix");
$router->post("/transacao-pix-ass", "AppCheckout:transacaoPixAss", "checkout.transacaoPixAss");
$router->post("/transacao-boleto", "Bradesco:boleto", "checkout.boleto");

$router->get("/finalizar-compra/", "AppCheckout:finalizarCompra", "checkout.finalizar");
$router->get("/finalizar-compra/{venda}", "AppCheckout:finalizarCompra", "checkout.finalizar");

$router->get("/boleto/{venda}", "AppCheckout:imprimirBoleto", "checkout.imprimirBoleto");
$router->get("/boleto-pedidos", "Bradesco:consultaPedidos", "bradesco.pedidos");
$router->get("/pix-pedidos", "Bradesco:consultaPix", "bradesco.pix");

$router->get("/processando", "AppCheckout:obrigadoProcessando", "checkout.processando");
$router->get("/pedido-reservado", "AppCheckout:obrigadoBoleto", "checkout.reserva");
$router->get("/obrigado", "AppCheckout:obrigadoCartao", "checkout.obrigado");
$router->get("/recusado", "AppCheckout:recusado", "checkout.recusado");
$router->get("/redirect", "AppCheckout:redirect", "checkout.redirect");

$router->get("/atualizar-pix", "AppCheckout:atualizarPix", "checkout.atualizarPix");
$router->get("/servico-pix", "AppCheckout:verificarServicoPix", "checkout.servicoPix");
$router->post("/cancela-pix", "AppEstorno:cancelamentoPix", "checkout.cancelamentoPix");
$router->post("/cancela-venda-pix", "AppEstorno:cancelarVenda", "checkout.cancelarVenda");
$router->post("/cancelamento-pix", "AppEstorno:autenticidade", "checkout.autenticidade");

$router->post("/estornar", "AppEstorno:estornar", "checkout.estornar");
$router->post("/solicitar-estorno", "AppEstorno:solicitarEstorno", "checkout.solicitarEstorno");
$router->get("/comprador", "AppEstorno:customer", "checkout.customer");

$router->post("/novo-boleto", "AppCheckout:gerarNovoBoleto", "checkout.novoBoleto");
$router->post("/alerta-chargeback", "AppEstorno:alertaChargeback", "chargeback.alerta");
$router->post("/desbloquear-venda", "AppEstorno:desbloquearComissoes", "chargeback.desbloqueio");
$router->post("/estorno-completo", "AppEstorno:estornoCompleto", "estorno.completo");

// $router->post("/processos", "AppProcessos:processos", "checkout.processos");

$router->get("/termos", "Termos:termos", "termos.termos");
$router->get("/privacidade", "Termos:privacidade", "termos.privacidade");

$router->group("abandono");
$router->get("/", "AppCarrinhoAbandonado:abandonos", "abandono.abandonos");
$router->get("/{page}", "AppCarrinhoAbandonado:abandonos", "abandono.abandonos");
$router->post("/", "AppCarrinhoAbandonado:cadastroCarrinho", "abandono.cadastro");
$router->get("/excluir", "AppCarrinhoAbandonado:excluirCarrinho", "abandono.excluir");
$router->get("/exportar", "AppCarrinhoAbandonado:exportar", "abandono.exportar");
$router->get("/exportar-venda", "AppCarrinhoAbandonado:exportarVendas", "abandono.exportarVenda");


$router->group("cielo");
$router->get("/", "Cielo:consultaPedidos", "cielo.consulta");


/**
 * NAMESPACE Source\App\Asaas
 */
$router->namespace("Source\App\Asaas");
$router->group("asaas");
$router->get("/vendas", "AsaasStatus:dadosVendas", "asaas.vendas");


/*
* NAMESPACE Source\App\Processos
*/
$router->namespace("Source\App\Processos");

$router->group("processos");
$router->post("/", "AppProcessos:processos", "processos.processos");
$router->get("/p", "AppProcessos:cadastroUsuarioVenda", "processos.processos");
$router->get("/boleto", "AppProcessos:boletoVencidoChatGuru", "processos.boletoVencido");
$router->get("/turma", "AppProcessos:cadastroAlunoTurma", "processos.processos");
$router->post("/push", "AppProcessos:pushGoogle", "processos.processos");
$router->post("/notifica", "AppProcessos:notificacaoGoogle", "processos.notifica");
$router->post("/leadloversintegracao", "AppProcessos:leadlovers", "integracao.lead");


/*
* NAMESPACE Source\App\WebHooks
*/
$router->namespace("Source\App\WebHooks");

$router->group("webhook");
$router->get("/notificacao", "AppWebHook:criarNotificacaoWebhook", "webhook.notificacao");
$router->post("/", "AppWebHook:webhook", "webhook.webhook");
$router->get("/vendas", "AppWebHook:webhookVendas", "webhook.vendas"); // ENVIA VENDAS PARA ST4RT
$router->post("/pagarme", "AppWebHook:postbackPagarMe", "webhook.pagarme");
$router->post("/pagarmev5", "AppWebHook:webhookPagarMe", "webhook.pagarmev5");
$router->post("/mercadopago", "AppWebHook:webhookMercadopago", "webhook.mercadoPago");
$router->post("/bspay", "AppWebHook:webhookBspay", "webhook.bspay");
$router->post("/stripe", "AppWebHook:webhookStripe", "webhook.stripe");
$router->post("/pagseguro", "AppWebHook:webhookPagseguro", "webhook.pagseguro");


/*
* NAMESPACE Source\App\Bling
*/
$router->namespace("Source\App\Bling");

$router->group("bling");
$router->get("/", "AppBling:bling", "bling.home");
$router->get("/{page}", "AppBling:bling", "bling.home");
$router->post("/excluir", "AppBling:excluirBling", "bling.excluir");
$router->post("/cadastrar", "AppBling:cadastrarBling", "bling.cadastrar");
$router->post("/editar", "AppBling:editarBling", "bling.editar");
$router->post("/criar-pedido", "AppBling:criarPedido", "bling.criarPedido");


/*
* NAMESPACE Source\App\Hotjar
*/
$router->namespace("Source\App\Hotjar");

$router->group("hotjar");
$router->get("/", "AppHotjar:hotjar", "hotjar.home");
$router->get("/{page}", "AppHotjar:hotjar", "hotjar.home");
$router->post("/cadastrar", "AppHotjar:cadastrarHotjar", "hotjar.cadastrar");
$router->post("/editar", "AppHotjar:editarHotjar", "hotjar.editar");
$router->post("/excluir", "AppHotjar:excluirHotjar", "hotjar.excluir");

/*
* NAMESPACE Source\App\Notificacao
*/
$router->namespace("Source\App\Notificacao");

$router->group("notificacoes");
$router->get("/", "AppNotificacao:notificacoes", "notificacao.notificacao");
$router->get("/p/{page}", "AppNotificacao:notificacoes", "notificacao.notificacao");
$router->post("/", "AppNotificacao:postbackNotificacao", "notificacao.postback");
$router->get("/fcm", "AppNotificacao:painelFcm", "notificacao.fcm");
$router->post("/fcm", "AppNotificacao:enviarFcm", "notificacao.fcm.enviar");



/*
* NAMESPACE Source\App\Cookies
*/
$router->namespace("Source\App\Cookies");

$router->group(null);
$router->get("/a/{afiliacao}", "AppCookies:cookies", "cookies.cookies");
$router->get("/a/{produto}/{afiliacao}", "AppCookies:cookies3", "cookies.cookies");
$router->get("/a/{produto}/{afiliacao}/", "AppCookies:cookies3", "cookies.cookies");
//Cookies vindo do televendas
$router->get("/t/{produto}/{atendente}", "AppCookies:cookiesTelevendas", "cookies.televendas");
$router->get("/c/{campanha}", "AppCookies:campanha", "cookies.campanha");

/*
* NAMESPACE Source\App\Whatsapp"
*/
$router->namespace("Source\App\Whatsapp");

$router->group("whatsapp");
$router->get("/notificacoes-inteligentes", "AppNotificacoesInteligentes:notificacoes", "whatsapp.notificacoes");
$router->get("/notificacoes-inteligentes/{page}", "AppNotificacoesInteligentes:notificacoes", "whatsapp.cadastro");
$router->post("/cadastrar-notificacoes", "AppNotificacoesInteligentes:cadastrarNotificacoesInteligentes", "whatsapp.cadastrarNI");
$router->post("/editar-notificacoes", "AppNotificacoesInteligentes:editarNotificacoesInteligentes", "whatsapp.editarNI");
$router->post("/excluir-notificacoes", "AppNotificacoesInteligentes:excluirNotificacao", "whatsapp.excluirNI");
$router->post("/editar", "AppNotazz:editarIntegracao", "notazz.editar");
$router->post("/excluir", "AppNotazz:excluirIntegracao", "notazz.excluir");

$router->post("/gerar", "AppNotazz:gerarNota", "notazz.gerar");
$router->post("/consultar-cancelar", "AppNotazz:consultar", "notazz.consultarCancelar");

$router->post("/postback", "AppNotazz:postbackNotazz", "notazz.postback");


/*
* NAMESPACE Source\App\Televendas"
*/
$router->namespace("Source\App\Televendas");
$router->group("televendas");
$router->post("/novoBoleto", "AppTelevendas:gerarNovoBoleto", "televendas.novoBoleto");
$router->get("/produtos", "AppProdutosTelevendas:produtos", "televendas.produtos");

/*
* NAMESPACE Source\App\ActiveCampaign
*/
$router->namespace("Source\App\ActiveCampaign");

$router->group("activecampaign");
$router->post("/activecampaign/cadastrar", "AppActiveCampaign:cadastrar", "activecampaign.cadastrar");
$router->post("/activecampaign/editar", "AppActiveCampaign:cadastrar", "activecampaign.editar");
$router->post("/activecampaign/excluir", "AppActiveCampaign:excluirActiveCampaign", "activecampaign.excluir");

/*
* NAMESPACE Source\App\ActiveCampaign
*/
$router->namespace("Source\App\RdStation");

$router->group("rdstation");
$router->post("/rdstation/cadastrar", "AppRdStation:cadastrar", "rdstation.cadastrar");
$router->post("/rdstation/editar", "AppRdStation:cadastrar", "rdstation.editar");
$router->post("/rdstation/excluir", "AppRdStation:excluirRdStation", "rdstation.excluir");

/*
* NAMESPACE Source\App\Mex
*/
$router->namespace("Source\App\Mex");

$router->group("mex");
$router->post("/mex/cadastrar", "AppMex:cadastrar", "mex.cadastrar");
$router->post("/mex/editar", "AppMex:editarMex", "mex.editar");
$router->post("/mex/excluir", "AppMex:excluirMex", "mex.excluir");

/*
 * NAMESPACE Source\App\Frete"
 */
$router->namespace("Source\App\Frete");
$router->group("frete");
$router->post("/", "AppEncomendas:calcularFrete", "encomendas.frete"); //Calcula frete Correios e JadLog


/*
* Processos
*/
$router->dispatch();

if ($router->error()) {
    $router->redirect("/ops/{$router->error()}");
}
