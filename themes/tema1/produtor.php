<?php
$v->layout("_theme");

if ($_SESSION["tipo"] != 1 && $_SESSION["tipo"] != 2) {
  header("Location: " . URL_BASE);
}

$ativo = array(
  "" => "NÃO",
  0  => "NÃO",
  1  => "SIM",
  2  => "NÃO"
);

$grupo = array(
  1 => "Iniciante",
  2 => "Especialista",
  3 => "Elite",
  4 => "Mestre",
  5 => "Grão Mestre",
  6 => "Guru",
  7 => "Lendário"
);

$cores = array(
  "" => "#FFC107",
  0 => "#DC3545",
  1 => "#28A745",
  2 => "#DC3545",
);
?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h4 style="font-family:inter"><?= $title ?></h4>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="./">Início</a></li>
            <li class="breadcrumb-item">Produtores</li>

          </ol>
        </div>
      </div>
    </div><!-- /.container-fluid -->
  </section>

  <!-- Main content -->
  <section class="content">

    <div class="callout">
      <form action="#">
        <div class="row">
          <div class="col-md-3">
            <b>Nome:</b>
            <input type="text" name="nome" id="nome1" class="form-control" value="<?= @$_GET["nome"] ?>">
          </div>

          <div class="col-md-3">
            <b>Tipo:</b>
            <select name="tipo" id="tipo" class="form-control">
              <option value="">Selecionar</option>
              <option value="4" <?= @$_GET["tipo"] == 4 ? "selected" : "" ?>>Produtor</option>
              <option value="5" <?= @$_GET["tipo"] == 5 ? "selected" : "" ?>>Afiliado</option>
              <option value="6" <?= @$_GET["tipo"] == 6 ? "selected" : "" ?>>Comprador</option>
            </select>
          </div>

          <div class="col-md-3">
            <b>Status:</b>
            <select name="status" id="status" class="form-control">
              <option value="" selected>Selecionar</option>
              <option value="0" <?= @$_GET["status"] == "0" ? "selected" : "" ?>>Inativo</option>
              <option value="1" <?= @$_GET["status"] == 1 ? "selected" : "" ?>>Ativo</option>
            </select>
          </div>

          <div class="col-md-3">
            <b>St4rt:</b>
            <select name="st4rt" id="st4rt" class="form-control">
              <option value="" selected>Selecionar</option>
              <option value="0" <?= @$_GET["st4rt"] == "0" ? "selected" : "" ?>>Não</option>
              <option value="1" <?= @$_GET["st4rt"] == 1 ? "selected" : "" ?>>Sim</option>
            </select>
          </div>


        </div>

        <br>

        <div class="row">
          <div class="col-md-3">
            <b>Grupo Geral:</b>
            <select name="grupo" id="grupo" class="form-control">
              <option value="">Selecionar</option>
              <option value="1" <?= @$_GET["grupo"] == 1 ? "selected" : "" ?>>Iniciante</option>
              <option value="2" <?= @$_GET["grupo"] == 2 ? "selected" : "" ?>>Especialista</option>
              <option value="3" <?= @$_GET["grupo"] == 3 ? "selected" : "" ?>>Elite</option>
              <option value="4" <?= @$_GET["grupo"] == 4 ? "selected" : "" ?>>Mestre</option>
              <option value="5" <?= @$_GET["grupo"] == 5 ? "selected" : "" ?>>Grão Mestre</option>
              <option value="6" <?= @$_GET["grupo"] == 6 ? "selected" : "" ?>>Guru</option>
              <option value="7" <?= @$_GET["grupo"] == 7 ? "selected" : "" ?>>Lendário</option>
            </select>
          </div>

          <div class="col-md-3">
            <b>Grupo Afiliado:</b>
            <select name="grupo_afiliado" id="grupo_afiliado" class="form-control">
              <option value="">Selecionar</option>
              <option value="1" <?= @$_GET["grupo_afiliado"] == 1 ? "selected" : "" ?>>Iniciante</option>
              <option value="2" <?= @$_GET["grupo_afiliado"] == 2 ? "selected" : "" ?>>Especialista</option>
              <option value="3" <?= @$_GET["grupo_afiliado"] == 3 ? "selected" : "" ?>>Elite</option>
              <option value="4" <?= @$_GET["grupo_afiliado"] == 4 ? "selected" : "" ?>>Mestre</option>
              <option value="5" <?= @$_GET["grupo_afiliado"] == 5 ? "selected" : "" ?>>Grão Mestre</option>
              <option value="6" <?= @$_GET["grupo_afiliado"] == 6 ? "selected" : "" ?>>Guru</option>
              <option value="7" <?= @$_GET["grupo_afiliado"] == 7 ? "selected" : "" ?>>Lendário</option>
            </select>
          </div>

          <div class="col-md-3">
            <b>Grupo Produtor:</b>
            <select name="grupo_produtor" id="grupo_produtor" class="form-control">
              <option value="">Selecionar</option>
              <option value="1" <?= @$_GET["grupo_produtor"] == 1 ? "selected" : "" ?>>Iniciante</option>
              <option value="2" <?= @$_GET["grupo_produtor"] == 2 ? "selected" : "" ?>>Especialista</option>
              <option value="3" <?= @$_GET["grupo_produtor"] == 3 ? "selected" : "" ?>>Elite</option>
              <option value="4" <?= @$_GET["grupo_produtor"] == 4 ? "selected" : "" ?>>Mestre</option>
              <option value="5" <?= @$_GET["grupo_produtor"] == 5 ? "selected" : "" ?>>Grão Mestre</option>
              <option value="6" <?= @$_GET["grupo_produtor"] == 6 ? "selected" : "" ?>>Guru</option>
              <option value="7" <?= @$_GET["grupo_produtor"] == 7 ? "selected" : "" ?>>Lendário</option>
            </select>
          </div>

          <div class="col-md-2">
            <b>Grupo Coprodução:</b>
            <select name="grupo_coproducao" id="grupo_coproducao" class="form-control">
              <option value="">Selecionar</option>
              <option value="1" <?= @$_GET["grupo_coproducao"] == 1 ? "selected" : "" ?>>Iniciante</option>
              <option value="2" <?= @$_GET["grupo_coproducao"] == 2 ? "selected" : "" ?>>Especialista</option>
              <option value="3" <?= @$_GET["grupo_coproducao"] == 3 ? "selected" : "" ?>>Elite</option>
              <option value="4" <?= @$_GET["grupo_coproducao"] == 4 ? "selected" : "" ?>>Mestre</option>
              <option value="5" <?= @$_GET["grupo_coproducao"] == 5 ? "selected" : "" ?>>Grão Mestre</option>
              <option value="6" <?= @$_GET["grupo_coproducao"] == 6 ? "selected" : "" ?>>Guru</option>
              <option value="7" <?= @$_GET["grupo_coproducao"] == 7 ? "selected" : "" ?>>Lendário</option>
            </select>
          </div>

          <div class="col-md-1">
            <span style="color:#fff">|</span>
            <button type="submit" class="form-control btn btn-primary">Buscar</button>
          </div>
        </div>
      </form>
    </div>
    <!-- Default box -->
    <div class="card card-body">
      <b>Legenda:</b>
      <div class="row">
        <span style="width: 20px; height: 20px; border-radius: 50%; background: #F7BA06"></span> &nbsp; Não enviou documentos &nbsp;
        <span style="width: 20px; height: 20px; border-radius: 50%; background: #26A142"></span> &nbsp; Usuário verificado &nbsp;
        <span style="width: 20px; height: 20px; border-radius: 50%; background: #D53342"></span> &nbsp; Documentos não aprovados
      </div>

      <br>
      <p>
        Total: <?= $count ?><br>
        Usuários que vendem: <span id="usuariosVendaCount">0</span>
      </p>
    </div>

    <div class="card">

      <?php if ($usuarios) : ?>
        <script>
          var dados = [];
        </script>

        <style>
          .verificado::after {
            content: "";
            border-bottom: 3px solid #fff;
            height: 5px;
          }
        </style>

        <div class="card-body p-0" style="overflow: auto">
          <table class="table projects" style="font-size:13px">
            <thead>
              <tr style="font-family:inter">
                <th scope="col">#</th>
                <th scope="col">Nome</th>
                <th scope="col">Tempo de Saque</th>
                <th scope="col">Taxa REDROCKET</th>
                <th scope="col">Taxa Assinatura</th>
                <th scope="col">Desde</th>
                <th scope="col">Ativo</th>
                <th scope="col">St4rt</th>
                <th scope="col">Vendas</th>
                <th scope="col">Ação</th>
              </tr>
            </thead>

            <tbody>
              <?php
              $i = 1;
              $usuariosVendaCount = 0;

              foreach ($usuarios as $usuario) :
                $venda = $vendas->find("produtor = $usuario->id || afiliado = $usuario->id")->count();

                $dados = $usuarioDados->find("usuario = $usuario->id")->fetch();

                $usuariosVendaCount = $venda > 0 ? $usuariosVendaCount + 1 : $usuariosVendaCount + 0;
              ?>
                <tr style="font-family:inter; border-left: 20px solid <?= $cores[$usuario->verificado] ?>" class="verificado">
                  <th scope="row" style="text-align: center">
                    <?= $usuario->id; ?><br>
                    <?php if ($usuario->foto) {
                      $foto = $usuario->foto;
                    } else {
                      $foto = url("uploads/perfil/user.png");
                    } ?>
                    <img src="<?= $foto; ?>" alt="" width="30px" style="border-radius: 50%">
                  </th>
                  <td><?= $usuario->nome . "<br>" . $usuario->email . "<br>" . @$dados->cpf_cnpj . "<br>" . $usuario->login; ?></td>
                  <td align="center"><?= $usuario->tempo_saque; ?></td>
                  <td align="center"><?= $usuario->taxa_redrocket; ?>%</td>
                  <td align="center"><?= $usuario->taxa_assinatura; ?>%</td>
                  <td><?= formataData($usuario->created_at); ?></td>
                  <td align="center">
                    <small>
                      Ativo: <?= @$ativo[$usuario->ativo] ?> <br>
                      Status: <?= @$ativo[$usuario->status] ?><br>
                      Verificado: <?= @$ativo[$usuario->verificado] ?>
                    </small>
                  </td>
                  <td align="center"><?= @$ativo[$usuario->cliente_st4rt]; ?></td>
                  <td align="center"><?= $venda; ?></td>
                  <td>
                    <a class="btn btn-warning btn-sm" data-toggle="modal" data-target="#ideditar" onclick="editar(<?= $i; ?>)"><i class="fas fa-edit"></i></a>
                    <?php if ($usuario->ativo == 1) : ?>
                      <a class="btn btn-danger btn-sm" onclick="ativo(<?= $usuario->id; ?>, 0)"><i class="fas fa-ban"></i></a>
                    <?php else : ?>
                      <a class="btn btn-success btn-sm" onclick="ativo(<?= $usuario->id; ?>, 1)"><i class="fas fa-check"></i></a>
                    <?php endif; ?>
                    <?php if ($usuario->verificado != 1 && $usuario->documento1 && $usuario->documento2) : ?>
                      <button class='btn btn-success btn-sm btAprovar' data-toggle='modal' data-target='#aprovar' data-id='<?= $usuario->id ?>' onclick="modalAprovar(<?= $i ?>)"><i class='fas fa-thumbs-up'></i></button>
                    <?php endif; ?>
                  </td>
                </tr>

                <script>
                  dados[<?= $i ?>] = {
                    id: "<?= @$usuario->id ?>",
                    nome: "<?= @$usuario->nome ?>",
                    email: "<?= @$usuario->email ?>",
                    tipo_usuario: "<?= @$usuario->tipo_usuario ?>",
                    tempo_saque: "<?= @$usuario->tempo_saque ?>",
                    tempo_pix: "<?= @$usuario->tempo_pix ?>",
                    tempo_boleto: "<?= @$usuario->tempo_boleto ?>",
                    //taxa_saque : "<?= @$usuario->taxa_saque ?>",
                    taxa_redrocket: "<?= @$usuario->taxa_redrocket ?>",
                    taxa_assinatura: "<?= @$usuario->taxa_assinatura ?>",
                    taxa_drop: "<?= @$usuario->taxa_drop ?>",
                    dropshipper: "<?= @$usuario->dropshipper ?>",
                    taxa_ecommerce: "<?= @$usuario->taxa_ecommerce ?>",
                    valor_ecommerce: "<?= @$usuario->valor_ecommerce ?>",
                    observacao: "<?= preg_replace("/\r|\n/", "", @$usuario->observacao) ?>",
                    verificado: "<?= @$usuario->verificado ?>",
                    ativo: "<?= @$usuario->ativo ?>",
                    status: "<?= @$usuario->status ?>",
                    grupo: "<?= @$usuario->grupo ?>",
                    grupo_produtor: "<?= @$usuario->grupo_produtor ?>",
                    grupo_coprodutor: "<?= @$usuario->grupo_coprodutor ?>",
                    grupo_afiliado: "<?= @$usuario->grupo_afiliado ?>",
                    documento1: "<?= @$usuario->documento1 ?>",
                    documento2: "<?= @$usuario->documento2 ?>",
                    cliente_st4rt: "<?= @$usuario->cliente_st4rt ?>",

                    razao_social: "<?= @$dados->razao_social ?>",
                    nome_fantasia: "<?= @$dados->nome_fantasia ?>",

                    tipo_doc: "<?= @$dados->tipo_doc ?>",
                    cpf: "<?= @$dados->cpf_cnpj ?>",

                    telefone: "<?= @$dados->telefone ?>",

                    rua: "<?= @$dados->rua ?>",
                    numero: "<?= @$dados->numero ?>",
                    complemento: "<?= @$dados->complemento ?>",
                    bairro: "<?= @$dados->bairro ?>",
                    cidade: "<?= @$dados->cidade ?>",
                    estado: "<?= @$dados->estado ?>",
                    pais: "<?= @$dados->pais ?>",
                    cep: "<?= @$dados->cep ?>",
                  }
                </script>
              <?php $i++;
              endforeach; ?>

            </tbody>
          </table>

          <br>
          <?php
          //EXIBE A NAVEGAÇÃO DO PAGINADOR
          $uri = explode("?", $_SERVER["REQUEST_URI"], 2);
          $link = URL_BASE . "/usuarios/";

          //VARIÁVEIS VÊM DO CONTROLADOR AppProdutor
          $paginador->paginas($total, $pagina, $link, @$uri[1]);
          $url = explode("?", $_SERVER["REQUEST_URI"]);
          if (@$url[1] != "") {
            $conc = "&";
          } else {
            $conc = "";
          }
          echo "<br>";
        else :
          ?>
          <h4>Não existem produtores cadastrados ou ativos!</h4>
        <?php
        endif;
        ?>

        <!-- cadastar -->
        <!-- end cadastrar -->

        <!-- editar -->
        <div class="modal fade" id="ideditar">
          <div class="modal-dialog modal-lg">
            <div class="modal-content">

              <div class="modal-header">
                <h3 class="card-title"><i class="fa fa-info-circle"></i> Editar Usuário</h3>
                <button type="button" class="close" data-dismiss="modal"> &times;</button>
              </div>

              <form action="#" method="post" style="font-family:inter; font-size:12px">
                <div class="modal-body">

                  <input type="hidden" name="id" id="id">

                  <div class="row">
                    <div class="col-md-6">
                      <b><span id="tipo_doc"></span>: </b> <span id="cpf"></span><br>
                      <b>Email: </b> <span id="email"></span><br>
                      <b>Tipo: </b><span id="tipo_user"></span>

                      <div class="grupo_produtor">
                        <b>Grupo Produtor: </b><span id="grupo_produtor"></span>
                      </div>

                      <div class="grupo_afiliado">
                        <b>Grupo Afiliado: </b><span id="grupo_afiliado"></span>
                      </div>

                      <div class="grupo_coprodutor">
                        <b>Grupo Coprodutor: </b><span id="grupo_coprodutor"></span>
                      </div>

                    </div>

                    <div class="col-md-6">
                      <b>Telefone: </b> <span id="telefone"></span><br>
                      <b>Cidade: </b> <span id="cidade"></span> - <span id="estado"></span><br>
                    </div>
                  </div>

                  <br>
                  <div class="row">
                    <div class="col-md-4">
                      <b>Tempo de saque geral:</b>
                      <input type="text" name="tempo_saque" id='tempo_saque' class="form-control input-lg" placeholder="Tempo de saque">
                    </div>

                    <div class="col-md-4">
                      <b>Tempo de saque pix:</b>
                      <input type="text" name="tempo_pix" id='tempo_pix' class="form-control input-lg" placeholder="Tempo de saque">
                    </div>

                    <div class="col-md-4">
                      <b>Tempo de saque boleto:</b>
                      <input type="number" name="tempo_boleto" id='tempo_boleto' min="3" class="form-control input-lg" placeholder="Tempo de saque">
                      <small>Será aplicado para boletos do Bradesco. Tempo mínimo 3 dias</small>
                    </div>
                  </div>

                  <br>

                  <div class="row">

                    <div class="col-md-4">
                      <b>Taxa REDROCKET:</b>
                      <input type="text" name="taxa_redrocket" id='taxa_redrocket' class="form-control input-lg">
                    </div>

                    <div class="col-md-4">
                      <b>Taxa Assinatra:</b>
                      <input type="text" name="taxa_assinatura" id='taxa_assinatura' class="form-control input-lg">
                      <br>
                    </div>

                    <div class="col-md-4">
                      <b>Taxa Drop:</b>
                      <input type="text" name="taxa_drop" id='taxa_drop' class="form-control input-lg">
                    </div>
                  </div>

                  <br>

                  <div class="row">

                  <div class="col-md-4">
                      <b>Usuário Dropshipper:</b>
                      <select name="usuario_drop" id="usuario_drop" class="form-control input-lg">
                        <option value="1">Sim</option>
                        <option value="0">Não</option>
                      </select>
                    </div>

                    <div class="col-md-4">
                      <b>Taxa Ecommerce:</b>
                      <input type="text" name="taxa_ecommerce" id='taxa_ecommerce' class="form-control input-lg">
                    </div>

                    <div class="col-md-4">
                      <b>Valor Assinatura Ecommerce:</b>
                      <input type="text" name="valor_ecommerce" id='valor_ecommerce' class="form-control input-lg">
                      <br>
                    </div>
                  </div>

                  <br>

                  <div class="row">

                    <div class="col-md-4">
                      <b>Cliente St4rt:</b>
                      <select name="cliente_st4rt" id="cliente_st4rt" class="form-control input-lg">
                        <option value="1">Sim</option>
                        <option value="0">Não</option>
                      </select>
                    </div>

                    <br>
                    <!-- </div> -->

                    <!-- <div class="row"> -->
                    <div class="col-md-4">
                      <b>Ativo:</b>
                      <select name="ativo" id="ativo" class="form-control input-lg" required="required">
                        <option value="1">Ativo</option>
                        <option value="0">Inativo</option>
                      </select>
                    </div>

                    <div class="col-md-4">
                      <b>Email Verificado:</b>
                      <select name="status_user" id="status_user" class="form-control input-lg" required="required">
                        <option value="0">Não</option>
                        <option value="1">Sim</option>
                      </select>
                    </div>
                  </div>
                </div>

                <div class="modal-footer justify-content-between">
                  <button type="button" class="btn btn-danger" data-dismiss="modal" id="cancelar">Cancelar</button>
                  <button class="btn btn-primary" id="salvar">Salvar</button>
                </div>
              </form>
            </div>

          </div>

        </div>
        <!-- end editar -->

        <!-- excluir plano -->
        <div class="modal fade" id="idexcluir">
          <div class="modal-dialog">
            <div class="modal-content">
              <div class="modal-header">
                <h4 class="modal-title">Bloquear?</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>

              <div class="modal-footer justify-content-between">
                <button class="btn btn-default" data-dismiss="modal">Não</button>
                <button type="submit" class="btn btn-primary" name=""><i class="fa fa-check"></i> Sim</button>
              </div>
            </div>
          </div>
        </div>
        <!-- end excluir plano -->
        </div>
    </div>
  </section>

</div>

<!-- aprovar -->
<div class="modal fade" id="aprovar">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">

      <div class="modal-header">
        <h3 class="card-title"><i class="fa fa-info-circle"></i> Aprovar Usuário</h3>
        <button type="button" class="close" data-dismiss="modal"> &times;</button>
      </div>

      <div class="modal-body" style="background: #f4f6f9">
        <form action="#" name="form_aprovar">
          <input type="hidden" name="idAprovar" id="idAprovar">
          <div class="row">
            <div class="col-md-6">

              <div class="card card-body">
                <h4>Dados pessoais:</h4>
                <div class="row borda2">
                  <div class="col-md-5"><b>Nome: </b></div>
                  <div class="col-md-7"><span id="nome_verificar"></span></div>
                </div>

                <div class="row borda2">
                  <div class="col-md-5"><b>Email: </b></div>
                  <div class="col-md-7"><span id="email_verificar"></span></div>
                </div>

                <div class="row borda2">
                  <div class="col-md-5"><b>Telefone: </b></div>
                  <div class="col-md-7"><span id="telefone_verificar"></span></div>
                </div>

                <div class="row borda2">
                  <div class="col-md-5"><b><span id="doc_verificar"></span>:</b></div>
                  <div class="col-md-7"><span id="cpf_verificar"></span></div>
                </div>

                <div class="pj">
                  <div class="row borda2">
                    <div class="col-md-5"><b>Razão Social: </b></div>
                    <div class="col-md-7"><span id="razao_social_verificar"></span></div>
                  </div>

                  <div class="row borda2">
                    <div class="col-md-5"><b>Nome Fantasia: </b></div>
                    <div class="col-md-7"><span id="nome_fantasia_verificar"></span></div>
                  </div>
                </div>

              </div>
            </div>

            <div class="col-md-6">
              <div class="card card-body">

                <h4>Endereço:</h4>
                <div class="row borda2">
                  <div class="col-md-5"><b>Endereço: </b></div>
                  <div class="col-md-7"><span id="rua_verificar"></span></div>
                </div>

                <div class="row borda2">
                  <div class="col-md-5"><b>Número: </b></div>
                  <div class="col-md-7"><span id="numero_verificar"></span></div>
                </div>

                <div class="row borda2">
                  <div class="col-md-5"><b>Complemento: </b></div>
                  <div class="col-md-7"><span id="complemento_verificar"></span></div>
                </div>

                <div class="row borda2">
                  <div class="col-md-5"><b>Bairro: </b></div>
                  <div class="col-md-7"><span id="bairro_verificar"></span></div>
                </div>

                <div class="row borda2">
                  <div class="col-md-5"><b>Cidade: </b></div>
                  <div class="col-md-7"><span id="cidade_verificar"></span></div>
                </div>

                <div class="row borda2">
                  <div class="col-md-5"><b>Estado: </b></div>
                  <div class="col-md-7"><span id="estado_verificar"></span></div>
                </div>

                <div class="row borda2">
                  <div class="col-md-5"><b>País: </b></div>
                  <div class="col-md-7"><span id="pais_verificar"></span></div>
                </div>

                <div class="row borda2">
                  <div class="col-md-5"><b>CEP: </b></div>
                  <div class="col-md-7"><span id="cep_verificar"></span></div>
                </div>

              </div>
            </div>


          </div>

          <div class="row">
            <div class="col-md-12">
              <div class="card card-body">

                <div class="docs">
                  <div class="row borda2">
                    <div class="col-md-5"><b>Imagem frente: </b></div>
                    <div class="col-md-7"><a href="#" id="documento1_verificar" target="_blank">Documentos</a></div>
                  </div>

                  <div class="row borda2">
                    <div class="col-md-5"><b>Imagem trás: </b></div>
                    <div class="col-md-7"><a href="#" id="documento2_verificar" target="_blank">Documentos</a></div>
                  </div>
                </div>

              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-md-6">
              <div class="card card-body">
                <b>Adicionar Observação:</b><small>Não inserir quebra de linha</small>
                <textarea name="observacao" id="observacao" cols="30" rows="5" class="form-control"></textarea>
              </div>
            </div>

            <div class="col-md-6">
              <div class="card card-body">
                <b>Verificado?</b>
                <select name="verificado" id="verificado" class="form-control">
                  <option value="1">Verificado</option>
                  <option value="2">Não Verificado</option>
                </select>
              </div>
            </div>
          </div>
        </form>
      </div>

      <div class="modal-footer justify-content-between">
        <button type="button" class="btn btn-danger" data-dismiss="modal" id="cancelar">Cancelar</button>
        <button class="btn btn-success" id="aprovarUser">Salvar</button>
      </div>
    </div>
  </div>
</div>
<!-- end aprovar -->

<?= $v->start("scripts") ?>
<script>
  $("#usuariosVendaCount").html(<?= $usuariosVendaCount ?>);

  let idAprovar = 0;
  $("#taxa_saque").blur(function(e) {
    var valor = $("#taxa_saque").val();
    $("#taxa_saque").val(valor.replace(",", "."));
  });

  $("#aprovarUser").click(function(e) {
    e.preventDefault();
    var form = $("form[name=form_aprovar]");
    aprovar(form, 1);
  });

  $(function() {
    $('.btAprovar').click(function() {
      idAprovar = $(this).data('id');
      // $("#idAprovar").attr("value", idAprovar);
    })
  })

  function aprovar(form, verificado) {
    console.log(idAprovar);

    $.ajax({
      url: "<?= $router->route("appusuarios.aprovar") ?>",
      data: form.serialize(),
      type: "POST",
      //datatype: "json",
      beforeSend: function() {
        console.log("atualizando");
      },
      error: function() {
        console.log("O servidor não conseguiu processar o pedido");
      },
      success: function(callback) {
        console.log("callback ", callback);
        alert(callback);
        if (callback == "Cadastrado Com Sucesso!") {
          $('form').each(function() {
            this.reset();
          });
          location.reload();
        }
        if (callback == "Atualizado Com Sucesso!") {
          $('form').each(function() {
            this.reset();
          });
          location.reload();
        }
      }

    });
  }

  $(".pj").hide();
  $(".docs").hide();

  function modalAprovar(i) {
    var tipo_documentos = {
      1: "CPF",
      2: "CNPJ"
    }
    console.log("modal");
    var id = dados[i]["id"];
    var email = dados[i]["email"];
    var nome = dados[i]["nome"];
    var telefone = dados[i]["telefone"];

    var razao_social = dados[i]["razao_social"];
    var nome_fantasia = dados[i]["nome_fantasia"];
    var cpf = dados[i]["cpf"];
    var doc = dados[i]["tipo_doc"];

    var rua = dados[i]["rua"];
    var numero = dados[i]["numero"];
    var complemento = dados[i]["complemento"];
    var bairro = dados[i]["bairro"];
    var cidade = dados[i]["cidade"];
    var estado = dados[i]["estado"];
    var pais = dados[i]["pais"];
    var cep = dados[i]["cep"];

    var documento1 = dados[i]["documento1"];
    var documento2 = dados[i]["documento2"];

    var observacao = dados[i]["observacao"];
    var verificado = dados[i]["verificado"];

    console.log(id, nome, email, telefone, cidade);

    $("#idAprovar").attr("value", id);
    $("#nome_verificar").html(nome);
    $("#email_verificar").html(email);
    $("#telefone_verificar").html(telefone);
    $("#doc_verificar").html(tipo_documentos[doc]);
    $("#cpf_verificar").html(cpf);

    if (observacao)
      $("#observacao").html(observacao);

    if (verificado)
      $("#verificado option[value=" + verificado + "]").prop("selected", true);

    if (doc == 2) {
      $(".pj").show();
      $("#razao_social_verificar").html(razao_social);
      $("#nome_fantasia_verificar").html(nome_fantasia);
    } else {
      $(".pj").hide();
    }

    $("#rua_verificar").html(rua);
    $("#numero_verificar").html(numero);
    $("#complemento_verificar").html(complemento);
    $("#bairro_verificar").html(bairro);
    $("#cidade_verificar").html(cidade);
    $("#estado_verificar").html(estado);
    $("#pais_verificar").html(pais);
    $("#cep_verificar").html(cep);

    if (documento1 && documento2) {
      $(".docs").show();
      $("#documento1_verificar").attr("href", "uploads/documentos/" + documento1).html(documento1);
      $("#documento2_verificar").attr("href", "uploads/documentos/" + documento2).html(documento2);
    } else {
      $(".docs").show();
      $(".docs").html("Documentos não enviados");
    }

    var tipo = {
      1: "MASTER",
      2: "ADMINISTRADOR",
      3: "FUNCIONÁRIO",
      4: "PRODUTOR",
      5: "AFILIADO",
      6: "COMPRADOR"
    };
  }

  function editar(i) {
    $('form').each(function() {
      this.reset();
    });
    var id = dados[i]["id"];
    var email = dados[i]["email"];
    var nome = dados[i]["nome"];
    var tempo_saque = dados[i]["tempo_saque"];
    var tempo_pix = dados[i]["tempo_pix"];
    var tempo_boleto = dados[i]["tempo_boleto"];
    //var taxa_saque = dados[i]["taxa_saque"];
    var taxa_redrocket = dados[i]["taxa_redrocket"];
    var taxa_assinatura = dados[i]["taxa_assinatura"];
    var taxa_drop = dados[i]["taxa_drop"];
    var dropshipper = dados[i]["dropshipper"];
    var taxa_ecommerce = dados[i]["taxa_ecommerce"];
    var valor_ecommerce = dados[i]["valor_ecommerce"];
    var observacao = dados[i]["observacao"];
    var ativo = dados[i]["ativo"];
    var grupo = dados[i]["grupo"];
    var grupo_produtor = dados[i]["grupo_produtor"];
    var grupo_coprodutor = dados[i]["grupo_coprodutor"];
    var grupo_afiliado = dados[i]["grupo_afiliado"];
    var cliente_st4rt = dados[i]["cliente_st4rt"];
    var status = dados[i]["status"];

    console.log(id, ativo, cliente_st4rt, taxa_redrocket, "status:", status, "grupo:", grupo);

    var grupos = {
      1: "Iniciante",
      2: "Especialista",
      3: "Elite",
      4: "Mestre",
      5: "Grão Mestre",
      6: "Guru",
      7: "Lendario"
    };

    $("#nome_produtor").html(nome);
    $("#id").attr("value", id);
    $("#email").attr("value", email);
    $("#nome").attr("value", nome);
    $("#tempo_saque").attr("value", tempo_saque);
    $("#tempo_pix").attr("value", tempo_pix);
    $("#tempo_boleto").prop("value", tempo_boleto);
    //$("#taxa_saque").attr({"value": taxa_saque});
    $("#taxa_redrocket").attr("value", taxa_redrocket);
    $("#taxa_assinatura").attr("value", taxa_assinatura);
    $("#taxa_drop").attr("value", taxa_drop);
    // $("#observacao").attr({"value": observacao});
    $("#ativo option[value=" + ativo + "]").attr({
      "selected": true
    });

    $("#usuario_drop option[value=" + dropshipper + "]").prop({
      "selected": true
    });

    $("#taxa_ecommerce").attr("value", taxa_ecommerce);
    $("#valor_ecommerce").attr("value", valor_ecommerce);

    if (grupo != "")
      $("#grupo2 option[value=" + grupo + "]").attr({
        "selected": true
      });

    $("#cliente_st4rt option[value=" + cliente_st4rt + "]").prop({
      "selected": true
    });
    $("#status_user option[value=" + status + "]").prop({
      "selected": true
    });

    if (dados[i]["tipo_doc"] == 1)
      $("#tipo_doc").html("CPF");
    else
      $("#tipo_doc").html("CNPJ");

    var tipo = {
      1: "MASTER",
      2: "ADMINISTRADOR",
      3: "FUNCIONÁRIO",
      4: "PRODUTOR",
      5: "AFILIADO",
      6: "COMPRADOR"
    };

    $("#cpf").html(dados[i]["cpf"]);
    $("#email").html(dados[i]["email"]);
    $("#tipo_user").html(tipo[dados[i]["tipo_usuario"]]);
    $("#telefone").html(dados[i]["telefone"]);
    $("#cidade").html(dados[i]["cidade"]);
    $("#estado").html(dados[i]["estado"]);

    if (grupo_produtor != "" && grupo_produtor > 0) {
      $(".grupo_produtor").show();
      $("#grupo_produtor").html(grupos[grupo_produtor]);

    } else
      $(".grupo_produtor").hide();

    if (grupo_afiliado != "" && grupo_afiliado > 0) {
      $(".grupo_afiliado").show();
      $("#grupo_afiliado").html(grupos[grupo_afiliado]);
    } else
      $(".grupo_afiliado").hide();

    if (grupo_coprodutor != "" && grupo_coprodutor > 0) {
      $(".grupo_coprodutor").show();
      $("#grupo_coprodutor").html(grupos[grupo_coprodutor]);
    } else
      $(".grupo_coprodutor").hide();

  }

  $("#salvar").click(function(e) {
    e.preventDefault();
    salvarEditar(1);
  });

  function salvarEditar(salvar) {
    if (salvar == 1) {
      url = "<?= $router->route("produtor.editar"); ?>";
    } else {
      url = "<?= $router->route("appusuarios.editar") ?>";
    }

    var form = $("form");
    $.ajax({

      url: url,
      data: form.serialize(),
      type: "POST",
      beforeSend: function() {
        console.log("atualizando");
      },
      error: function() {
        console.log("O servidor não conseguiu processar o pedido");
      },
      success: function(callback) {
        console.log("callback ", callback);
        alert(callback);
        if (callback == "Atualizado Com Sucesso!") {
          $('form').each(function() {
            this.reset();
          });
          location.reload();
        }
      }

    });
  }

  function ativo(id, ativo) {
    $.ajax({
      url: "<?= $router->route("produtor.inativar"); ?>",
      data: {
        id: id,
        ativo: ativo
      },
      type: "POST",
      beforeSend: function() {
        console.log("atualizando");
      },
      error: function() {
        console.log("O servidor não conseguiu processar o pedido");
      },
      success: function(callback) {
        console.log("callback ", callback);
        alert(callback);
        if (callback == "Atualizado com sucesso") {
          location.reload();
        }
      }

    });
  }
</script>
<?= $v->end() ?>
