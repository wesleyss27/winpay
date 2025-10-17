<?php
/**
 * EXEMPLO DE USO DO MODAL GLOBAL WINPAY
 * 
 * Este arquivo mostra como usar o novo estilo global de modais
 * que segue o padrão moderno e escuro do sistema.
 */

// Exemplo de modal simples
?>

<!-- Modal Exemplo 1: Modal Básico -->
<div id="modalExemplo1" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <!-- Header -->
            <div class="modal-header">
                <h4>
                    Título do Modal
                    <small>Informações adicionais</small>
                </h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            
            <!-- Body -->
            <div class="modal-body">
                <div class="card card-body">
                    <div class="titulo">Informações Principais</div>
                    
                    <div class="row borda1">
                        <div class="col-md-5"><b>Campo 1:</b></div>
                        <div class="col-md-7">Valor do campo 1</div>
                    </div>
                    
                    <div class="row borda1">
                        <div class="col-md-5"><b>Campo 2:</b></div>
                        <div class="col-md-7">Valor do campo 2</div>
                    </div>
                    
                    <div class="row borda1">
                        <div class="col-md-5"><b>Link:</b></div>
                        <div class="col-md-7"><a href="#">Link de exemplo</a></div>
                    </div>
                </div>
            </div>
            
            <!-- Footer -->
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">
                    Fechar <i class="fa fa-times-circle"></i>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Exemplo 2: Modal com Formulário -->
<div id="modalExemplo2" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4>Editar Informações</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            
            <div class="modal-body">
                <form>
                    <div class="card card-body">
                        <div class="titulo">Dados do Formulário</div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <label>Nome:</label>
                                <input type="text" class="form-control" value="João Silva">
                            </div>
                            <div class="col-md-6">
                                <label>Email:</label>
                                <input type="email" class="form-control" value="joao@email.com">
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-12">
                                <label>Observações:</label>
                                <textarea class="form-control" rows="3">Observações aqui...</textarea>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            
            <div class="modal-footer">
                <button type="button" class="btn btn-success">
                    Salvar <i class="fa fa-save"></i>
                </button>
                <button type="button" class="btn btn-danger" data-dismiss="modal">
                    Cancelar <i class="fa fa-times"></i>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Exemplo 3: Modal com Tabela -->
<div id="modalExemplo3" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4>Lista de Itens</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            
            <div class="modal-body">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nome</th>
                            <th>Status</th>
                            <th>Data</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>1</td>
                            <td>Item 1</td>
                            <td><span class="badge">Ativo</span></td>
                            <td>21/08/2025</td>
                        </tr>
                        <tr>
                            <td>2</td>
                            <td>Item 2</td>
                            <td><span class="badge">Inativo</span></td>
                            <td>20/08/2025</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">
                    Fechar <i class="fa fa-times-circle"></i>
                </button>
            </div>
        </div>
    </div>
</div>

<script>
// Exemplo de como abrir os modais
function abrirModalExemplo1() {
    $('#modalExemplo1').modal('show');
}

function abrirModalExemplo2() {
    $('#modalExemplo2').modal('show');
}

function abrirModalExemplo3() {
    $('#modalExemplo3').modal('show');
}
</script>

<!-- Botões para testar os modais -->
<div style="padding: 20px;">
    <h3>Teste dos Modais Globais</h3>
    <button class="btn btn-primary" onclick="abrirModalExemplo1()">Modal Básico</button>
    <button class="btn btn-success" onclick="abrirModalExemplo2()">Modal com Formulário</button>
    <button class="btn btn-warning" onclick="abrirModalExemplo3()">Modal com Tabela</button>
</div>
