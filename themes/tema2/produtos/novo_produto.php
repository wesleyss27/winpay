<?php $v->layout("_theme"); ?>

<?php if($_SESSION["tipo"] == 5 || $_SESSION["tipo"] == 6 || $usuarioDados->tipo_doc != 2): ?>
    Para cadastrar um produto, você precisa concluir o cadastro e possuir CNPJ <br>
    Clique no link para concluir o cadastro => <a href="<?= url("usuarios/perfil/".$_SESSION["id"]) ?>" title="Concluir cadastro">Concluir cadastro</a>
<?php elseif($_SESSION["tipo"] != 5): ?>

<div class="">
    <form action="<?= $router->route("produtos.cadastro"); ?>" method="post" enctype="multipart/form-data">
        <!-- [ TERMOS DE USO ] -->
        <div class="form-group">
        <div class="rascunho">
            <input type="checkbox" name="rascunho" id="rascunho" value="1" checked>    
            <label for="rascunho">
                &nbsp; Rascunho**
            </label>

            ** <small>quando esta opção estiver marcada, o produto não será enviado para análise</small>
        </div>
    	</div>
        <div class="row">
            <div class="col-md-4">
                <label>Integrar com Logística St4rt </label>
            </div>
            <div class="col-md-3">
                <select name="st4rt" id="st4rt" class="form-control">
                    <option value="0">Não</option>
                    <option value="1">Sim</option>
                </select>
            </div>
        </div>
        <!-- [ FIM - TERMOS DE USO ] -->

        <!-- [ NOME E DESCRIÇÃO] -->
        <div class="form-group">
        <div class="titulo-secao">Nome e Descrição</div>
        <div class="corpo-secao">
            <div class="row">
                <label for="nome" class="col-xs-12   col-sm-3   col-lg-2   control-label">Nome do produto: <span class="obrigatorio">*</span></label>
                <div class="col-xs-12 col-sm-9 col-lg-10">
                <input type="text" class="form-control" id="nome" name="nome" placeholder="Digite o nome do seu produto" required/>
                </div>
            </div>

            <div class="row">
                <label for="descricao" class="col-xs-12 col-sm-3 col-lg-2 control-label">Descrição: <span class="obrigatorio">*</span></label>
                <div class="col-xs-12 col-sm-9 col-lg-10">
                <textarea class="form-control" id="descricao" name="descricao" placeholder="Descreva bem o seu produto. Quanto melhor a descrição maior a change de conseguir afiliados. Máximo 1000 caracteres" title="" rows="6"  requiredrequired></textarea>
                <label for="descricao" class="" style=" ">   Total de caracteres <span id='totalCaracaterDescricao'>0</span> </label>
                </div>
            </div>
        </div>
        </div>
        <!-- [ FIM - NOME E DESCRIÇAO ] -->

        <br>

        <!-- [ FORMATO E CATEGORIA ] -->
        <div class="form-group">
        <div class="titulo-secao">Tipo e Categoria</div>
        <div class="corpo-secao">
            <div class="row">
                <label for="tipo_produto" class="col-xs-12 col-sm-3 col-lg-2 control-label">Tipo de Produto: <span class="obrigatorio">*</span></label>
                <div class="col-xs-12  col-sm-5   ">

                <select class="form-control" id="tipo_produto" name="tipo_produto" required>
                    <option disabled selected>-- Selecione --</option>
                    <option value='1'>Aplicativos p/ Celular</option>
                    <option value='2'>Áudios, Músicas, Ringtones</option>
                    <option value='3'>Ebooks, Documentos, Texto</option>
                    <option value='4'>Evento</option>
                    <option value='5'>Imagens, Ícones, Fotos</option>
                    <option value='6'>Mentoria/Mastermind</option>
                    <option value='7'>Planilha de Excel</option>
                    <option value='8'>Produto Físico</option>
                    <option value='9'>Screencasts, Filmes</option>
                    <option value='10'>Serviços</option>
                    <option value='11'>Site Membros (Cursos, Video Aulas)/ Serviço de assinatura</option>
                    <option value='12'>Software</option>
                    <option value='13'>Templates, Códigos Fonte</option>
                </select>
                
                </div>
            </div>

            <div class="row">
                <label for="categoria" class="col-xs-12 col-sm-3 col-lg-2 control-label">Categoria: <span class="obrigatorio">*</span></label>
                <div class="col-xs-12  col-sm-5   ">

                <select class="form-control" id="categoria" name="categoria" required>
                    <option disabled selected>-- Selecione --</option>
                    <option value='1'>Administração e Negócios</option>
                    <option value='2'>Animais de Estimação</option>
                    <option value='3'>Arquitetura e Engenharia</option>
                    <option value='4'>Artes e Música</option>
                    <option value='5'>Auto-ajuda e Desenvolvimento Pessoal</option>
                    <option value='6'>Automóveis</option>
                    <option value='7'>Blogs e Redes Sociais</option>
                    <option value='8'>Casa e Jardinagem</option>
                    <option value='9'>Culinária, Gastronomia, Receitas</option>
                    <option value='10'>Design e Templates PSD, PPT ou HTML</option>
                    <option value='11'>Edição de Áudio, Vídeo ou Imagens</option>
                    <option value='12'>Educacional, Cursos Técnicos e Profissionalizantes</option>
                    <option value='13'>Eletrodomésticos</option><option value='46'>Eletrônicos</option>
                    <option value='14'>Eletroportáteis</option>
                    <option value='15'>Entretenimento, Lazer e Diversão</option>
                    <option value='16'>Esportes e Fitness</option>
                    <option value='17'>Filmes e Cinema</option>
                    <option value='18'>Geral</option>
                    <option value='19'>Histórias em Quadrinhos</option>
                    <option value='20'>Idiomas</option>
                    <option value='21'>Informática</option>
                    <option value='22'>Internet Marketing</option>
                    <option value='23'>Investimentos e Finanças</option>
                    <option value='24'>Jogos de Cartas, Poker, Loterias</option>
                    <option value='25'>Jogos de Computador, Jogos Online</option>
                    <option value='26'>Jurídico</option>
                    <option value='27'>Literatura e Poesia</option>
                    <option value='28'>Marketing de Rede</option>
                    <option value='29'>Marketing e Comunicação</option>
                    <option value='30'>Meio Ambiente</option>
                    <option value='31'>Moda e vestuário</option>
                    <option value='32'>Moveis e decoração</option>
                    <option value='33'>Música, Bandas e Shows</option>
                    <option value='34'>Paquera, Sedução e Relacionamentos</option>
                    <option value='35'>Perfumaria</option>
                    <option value='36'>Pessoas com deficiência</option>
                    <option value='37'>Plugins, Widgets e Extensões</option>
                    <option value='38'>Produtividade e Organização Pessoal</option>
                    <option value='39'>Produtos infantis</option>
                    <option value='40'>Relatórios, Artigos e Pesquisas</option>
                    <option value='41'>Religião e Crenças</option>
                    <option value='42'>Romances, Dramas, Estórias e Contos</option>
                    <option value='43'>RPG e Jogos de Mesa</option>
                    <option value='44'>Saúde, Bem-estar e Beleza</option>
                    <option value='45'>Scripts</option>
                    <option value='46'>Segurança do Trabalho</option>
                    <option value='47'>Sexologia e Sexualidade</option>
                    <option value='48'>Snippets (Trechos de Vídeo)</option>
                    <option value='49'>Trader</option>
                    <option value='50'>Turismo</option>
                </select>
                </div>
            </div>
        </div>
        </div>
        <!-- [ FIM - FORMATO E CATEGORIA ] -->
        
        <!-- [ COBRANÇA ] -->
        <div class="form-group">
        <div class="titulo-secao">Cobrança</div>
        <div class="corpo-secao">
            <!--div class="row">
                <label for="tipo_cobranca" class="col-xs-12  col-sm-3 col-lg-2 control-label">Tipo de cobrança: <span class="obrigatorio">*</span></label>
                <div class="col-xs-12  col-sm-9  ">

                <div class="col-xs-3">
                    <div class="input-group">
                        <label class="radio-inline">
                            <input name="tipo_cobranca" id="tipo_cobranca1" type="radio" value="1" checked class="uniform" />
                            Única
                        </label>
                    </div>
                </div>
                <div class="col-xs-3">
                    <div class="input-group">
                        <label class="radio-inline">
                            <input name="tipo_cobranca" id="tipo_cobranca2" type="radio" value="2"  class="uniform" />
                            Recorrente
                        </label>
                    </div>
                </div>
                
            	</div>
        	</div-->

            <!--div class="row">
                <label for="tipo_precificacao" class="col-xs-12  col-sm-3 col-lg-2 control-label">Tipo de precificação: <span class="obrigatorio">*</span></label>
                <div class="col-xs-12 col-sm-10">
                <div class="input-group"> 
                    
                    <div class="input-group" id="divTipoPrecificacao"> 
                    
                    <select class="form-control" id="tipo_precificacao" name="tipo_precificacao" required>
                    <option value='1'>Preço único</option> 
                    <option value='2'>Planos</option>
                        
                    </select>
                    </div>

                </div>
                </div>
            </div-->
        </div>

        <div class="row">
            <label for="preco" class="col-xs-12 col-sm-3 col-lg-2 control-label">Preço: <span class="obrigatorio">*</span></label>
            <div class="col-xs-12  col-sm-4 ">
                <div class="input-group preco"> <span class="input-group-addon"><i class=""><b>R$</b></i> </span>
                    <input id="preco" class="form-control real" name="preco" required />
                </div>                 
            </div>
        </div>
    	</div>
        <!-- [ FIM - COBRANÇA ] -->

        <!-- [ DISPONIBILIDADE ] -->
        <!--div class="form-group">
        <div class="titulo-secao">Disponibilidade</div>
        <div class="corpo-secao">
            <div class="row">
                <label for="disponivel" class="col-xs-12 col-sm-3 col-lg-2 control-label">Disponível para venda: <span class="obrigatorio">*</span></label>
                <div class="col-xs-12  col-sm-3 ">
                <div class="input-group">  
                    <label class=" ">
                    <input name="disponivel" id="disponivel1" type="radio" value="1" checked class=" " />
                        Sim
                    </label>   
                    <label class="r ">
                    <input name="disponivel" id="disponivel2" type="radio" value="0"  class=" " style="margin-left: 10px;" />
                        Não
                    </label> 
                </div>
                </div>
        	</div>

            <div class="row">
                <label for="quantidade_max" class="col-xs-12 col-sm-3 col-lg-2 control-label">Quantidade Máx.: <span class="obrigatorio">*</span></label>
                <div class="col-xs-12  col-sm-8  ">
                    <input type="text" value="1"  style="width: 150px;" class="col-xs-2 col-sm-2 form-control" id="quantidade_max" name="quantidade_max" required />
                    <br><br>
                    <small>Quantidade máxima que o cliente pode comprar em uma única compra.</small>
                </div> 
            </div>
        </div>
    	</div-->
        <!-- [ FIM - DISPONIBILIDADE ] -->

        <!-- [ EMAIL SUPORTE E GARANTIA ] -->
        <div class="form-group">
        <div class="titulo-secao">Suporte e Garantia</div>
        <div class="corpo-secao">
            <div class="row">
                <label for="email_suporte" class="  col-sm-2  control-label">E-mail suporte:</label>
                <div class="col-xs-12  col-sm-4">
                    <input type="text" class="form-control" id="email_suporte" name="email_suporte" />
                </div>
        	</div>

            <div class="row">
                <label for="tempo_garantia" class="col-sm-2 control-label">Tempo de garantia: <span class="obrigatorio">*</span></label>
                <div class="col-xs-12  col-sm-2">

                <select class="form-control" id="garantia" name="garantia" required >
                    <option value='30' selected>30 dias</option>
                    <option value='21'>21 dias</option>
                    <option value='15'>15 dias</option>
                    <option value='7'>7 dias</option>
                </select>
                </div>
            </div>
        </div>
    	</div>
        <!-- [ FIM - EMAIL SUPORTE E GARANTIA ] -->

        <!-- [ URLs PERSONALIZADAS ] -->
        <div class="form-group">
        <div class="titulo-secao">URLs Personalizadas</div>
        <div class="corpo-secao">

            <div class="row">
                <label for="pagina_venda" class="col-md-2 control-label">URL da página de venda: <span class="obrigatorio">*</span></label>
                <div class="col-md-8">
                    <div class="input-group"> <span class="input-group-addon"><i class="fa fa-link urlVenda"></i> </span>
                    <input type="text" class="form-control" id="pagina_venda" name="pagina_venda" required />
                    </div>
                </div>
            </div>

            <div class="row">
                <label for="obrigado_cartao" class="col-md-2 control-label">URL de obrigado cartão: </label>
                <div class="col-md-8">
                    <div class="input-group"> <span class="input-group-addon"><i class="fa fa-link urlVenda"></i> </span>
                    <input type="text" class="form-control margin-top-none" id="obrigado_cartao" name="obrigado_cartao" />
                    </div>
                </div>
            </div>

            <div class="row">
                <label for="obrigado_boleto" class="col-md-2 control-label">URL de obrigado boletos:</label>
                <div class="col-md-8">
                    <div class="input-group"> <span class="input-group-addon"><i class="fa fa-link urlVenda"></i> </span>
                    <input type="text" class="form-control margin-top-none" id="obrigado_boleto" name="obrigado_boleto" />
                    </div>
                </div>
            </div>

            <div class="row">
                <label for="obrigado_analise" class="col-md-2 control-label">URL da página de obrigado para compras em análise:</label>
                <div class="col-md-8">
                    <div class="input-group"> <span class="input-group-addon"><i class="fa fa-link urlVenda"></i> </span>
                    <input type="text" class="form-control margin-top-none" id="obrigado_analise" name="obrigado_analise" />
                    </div>
                </div>
            </div>
        </div>
    	</div>
        <!-- [ FIM - URLs PERSONALIZADAS ] -->

        <!-- [ FOTOS ] -->
        <div class="form-group">
        <div class="titulo-secao">Fotos</div>
        <div class="corpo-secao">
            <div class="row">
                <div class="col-md-12 ">
                <div class="panel">
                    <div class="panel-heading heading-small"> <i class="fa fa-camera-retro"></i> Foto do Produto: <span class="obrigatorio">*</span>
                    </div>
                    <div  action='produto/uploadFoto' id="dropzoneFotos" class="panel-body dropzone dropzone-plugin dz-clickable">

                    <input type="file" name="foto" required>

                    </div>
                    <small>Para melhor exibição use 200px por 200px<Br>Tamanho do arquivo: 500kb<br>Tipos de arquivos aceitos: JPG, PNG</small>
                </div>
                </div>
        	</div>

            <div class="row">
                <label for="url_midia" class="col-md-2 control-label">URL de mídias:</label>
                <div class="col-md-8">
                    <div class="input-group"> <span class="input-group-addon"><i class="fa fa-link urlVenda"></i> </span>
                    <input type="text" class="form-control margin-top-none" id="url_midia" name="url_midias" />
                    </div>
                </div>
            </div>
        </div>
    	</div>
        <!-- [ FIM - FOTOS ] -->

        <!-- [ DOCUMENTOS TÉCNICOS ] -->
        <div class="form-group">
        <div class="titulo-secao">Documentos Técnicos</div>
        <div class="corpo-secao">
            <div class="row">
                <div class="col-md-12 ">
                <div class="panel">
                    <div class="panel-heading heading-small"> <i class="fa fa-cloud-upload"></i> Produto - Documentos Técnicos dos Produtos
                    </div>
                    <div  action='produto/uploadDocumentos' id="dropzoneDocumentos" class="panel-body dropzone dropzone-plugin dz-clickable"  data-container="body" data-toggle="popover" data-trigger="hover"  data-placement="top" data-content="Máximo de 15 arquivos<br>Tamanho máximo por arquivo: 100MB<BR>Se tiver mais arquivos a serem enviados favor compactar" title="" data-original-title="&lt;i class='fa fa-bolt padding-right-sm'&gt;&lt;/i&gt; Dica">
                    <input type="file" name="documentos">
                    </div>
                </div>
        	</div>
        </div>
    	</div>
        <!-- [ FIM - DOCUMENTOS TÉCNICOS ] -->

        <!-- [ TERMOS DE AFILIAÇÃO ] -->
        <div class="form-group">
        <div class="titulo-secao">Termos de Afiliação</div>
        <div class="corpo-secao">
            <div class="row">
                <div class="col-md-12 ">
                <p class="descricaoProduto"> 
                    <h3>Termos de afiliação do produto:</h3>
                        <textarea cols="100" rows="10" class="form-control" name="termos" required></textarea>
                </p>
                </div>
        	</div>
        </div>
    	</div>
        <!-- [ FIM - TERMOS DE AFILIAÇÃO ] -->

        <!-- [ TERMOS DE USO ] -->
        <div class="form-group" style="width: 200px; margin: 0 auto">
                <label>
                    <input type="checkbox" name="aceite_termos" value="1" required> &nbsp; Aceito os termos de uso:
                </label>
    	</div>
        <!-- [ FIM - TERMOS DE USO ] -->

        <!-- [ TERMOS DE USO ] -->
        <div class="form-group">
            <div style="width: 200px; margin: 0 auto">
                <button class="btn btn-primary form-control">Salvar</button>
            </div>
    	</div>
        <!-- [ FIM - TERMOS DE USO ] -->
    </form>
</div>

<script>
    $("#preco").blur(function(e) {
        var valor = $("#preco").val();
        $("#preco").val(valor.replace(",", "."));
    });
</script>

<?php endif; ?>