<?php $v->layout("_theme"); ?>

<?php

$tipo_usuario = array(
  1 => "MASTER",
  2 => "ADMINISTRADOR",
  3 => "FUNCION&Aacute;RIO",
  4 => "PRODUTOR",
  5 => "AFILIADO",
  6 => "COMPRADOR"
);

$tipo_doc = array(
  1 => "CPF",
  2 => "CNPJ",
);

$grupo = array(
  1 => "Iniciante",
  2 => "Especialista",
  3 => "Elite",
  4 => "Mestre",
  5 => "Grão Mestre",
  6 => "Guru",
  7 => "Lendário",
);

?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">

  <!--     Fonts and icons     -->
  <link href="https://fonts.googleapis.com/css?family=Poppins:200,300,400,600,700,800" rel="stylesheet" />

  <!-- CSS Files -->
  <link href="<?= url("themes/tema1/dist/css/perfil/black.css?" . time()) ?>" rel="stylesheet" />

  <section class="content profile-page">
    <style>
      .profile-page {
        font-family: 'Inter', sans-serif;
        color: #f5f5f7;
        padding: 0 15px 40px;
      }

      .profile-shell {
        width: 100%;
        max-width: 1180px;
        margin: 0 auto;
      }

      .profile-hero {
        background: radial-gradient(circle at top, rgba(255, 221, 0, 0.25), rgba(7, 7, 7, 0.9));
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 20px;
        padding: 28px;
        margin-bottom: 30px;
        box-shadow: 0 25px 60px rgba(0, 0, 0, 0.35);
      }

      .profile-hero .hero-main {
        display: flex;
        align-items: center;
        gap: 24px;
        flex-wrap: wrap;
      }

      .profile-avatar-wrapper {
        position: relative;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        width: 110px;
        height: 110px;
        background: rgba(0, 0, 0, 0.2);
        border: 2px solid rgba(255, 221, 0, 0.45);
      }

      .profile-avatar-wrapper img {
        width: 100%;
        height: 100%;
        border-radius: 50%;
        object-fit: cover;
      }

      .avatar-action {
        position: absolute;
        bottom: -10px;
        background: #050505;
        border-radius: 999px;
        padding: 4px 12px;
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: .08em;
      }

      .hero-info h2 {
        font-size: 28px;
        margin-bottom: 8px;
      }

      .hero-info .hero-role {
        text-transform: uppercase;
        letter-spacing: .12em;
        font-size: 12px;
        color: rgba(255, 255, 255, 0.7);
      }

      .hero-chips {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
      }

      .chip {
        border-radius: 999px;
        padding: 6px 14px;
        font-size: 12px;
        background: rgba(255, 255, 255, 0.08);
        border: 1px solid rgba(255, 255, 255, 0.08);
      }

      .chip-success {
        border-color: rgba(68, 211, 149, 0.5);
        background: rgba(68, 211, 149, 0.15);
        color: #44d395;
      }

      .chip-warning {
        border-color: rgba(255, 221, 0, 0.3);
        background: rgba(255, 221, 0, 0.12);
        color: #ffdd00;
      }

      .chip-muted {
        color: #bdbdbd;
      }

      .profile-hero .hero-meta {
        margin-top: 24px;
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 12px;
      }

      .meta-card {
        background: rgba(0, 0, 0, 0.35);
        border-radius: 12px;
        padding: 14px;
        border: 1px solid rgba(255, 255, 255, 0.05);
      }

      .meta-card span {
        display: block;
        text-transform: uppercase;
        font-size: 11px;
        letter-spacing: .08em;
        color: #8c8c8c;
        margin-bottom: 4px;
      }

      .profile-layout {
        display: grid;
        grid-template-columns: minmax(0, 2.5fr) minmax(0, 1fr);
        gap: 24px;
      }

      .profile-card {
        background: #0b0b0c;
        border-radius: 20px;
        border: 1px solid rgba(255, 255, 255, 0.06);
        padding: 28px;
        box-shadow: 0 25px 45px rgba(0, 0, 0, 0.35);
        min-width: 0;
      }

      .profile-card__header h3 {
        margin-bottom: 6px;
      }

      .profile-card__header p {
        color: #9da0b5;
        margin-bottom: 0;
      }

      .eyebrow {
        text-transform: uppercase;
        letter-spacing: .2em;
        font-size: 11px;
        color: #7a7a7a;
        margin-bottom: 6px;
      }

      .profile-alert {
        border-radius: 14px;
        padding: 14px 16px;
        margin: 18px 0;
        font-size: 14px;
      }

      .profile-alert.warning {
        background: rgba(255, 193, 7, 0.08);
        border: 1px solid rgba(255, 193, 7, 0.4);
        color: #ffd45d;
      }

      .profile-alert.success {
        background: rgba(68, 211, 149, 0.08);
        border: 1px solid rgba(68, 211, 149, 0.4);
        color: #44d395;
      }

      .profile-form .form-section {
        margin-top: 28px;
      }

      .section-head h6 {
        font-size: 14px;
        text-transform: uppercase;
        letter-spacing: .24em;
        color: #8d8d8d;
        margin-bottom: 6px;
      }

      .section-head p {
        color: #a2a2a2;
        margin-bottom: 18px;
      }

      .form-grid {
        display: grid;
        gap: 16px;
      }

      .form-grid-3 {
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
      }

      .input-block span {
        display: block;
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: .08em;
        color: #9ca3c5;
        margin-bottom: 6px;
      }

      .input-block small {
        display: block;
        margin-top: 6px;
        color: #767e9c;
      }

      .input-block button {
        width: 100%;
      }

      .modern-btn {
        border-radius: 10px;
        border: none;
        padding: 12px 18px;
        text-transform: uppercase;
        letter-spacing: .08em;
        font-weight: 600;
      }

      .modern-btn.secondary {
        background: rgba(255, 221, 0, 0.12);
        color: #ffdd00;
      }

      .profile-actions {
        margin-top: 32px;
        display: flex;
        justify-content: flex-end;
      }

      .profile-sidecard h4 {
        margin-bottom: 10px;
      }

      .profile-sidecard p {
        color: #b1b5c9;
      }

      .profile-sidecard .cta {
        margin-top: 18px;
      }

      .profile-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0 10px;
        margin-top: 20px;
      }

      .profile-table-wrapper {
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 16px;
        overflow: hidden;
        overflow-x: auto;
        background: rgba(255, 255, 255, 0.02);
      }

      .profile-table th,
      .profile-table td {
        border: none;
        background: transparent;
        padding: 12px 14px;
        font-weight: 500;
      }

      .profile-table th {
        font-size: 12px;
        color: #8d8d8d;
        text-transform: uppercase;
        letter-spacing: .08em;
      }

      .profile-table {
        min-width: 520px;
      }

      .profile-table tbody tr td {
        font-size: 16px;
        color: #fff;
      }

      .profile-table tbody tr {
        border-top: 1px solid rgba(255, 255, 255, 0.08);
      }

      .profile-sidecard small {
        color: #7b7f95;
      }

      @media (max-width: 1100px) {
        .profile-layout {
          grid-template-columns: 1fr;
        }
      }

      @media (max-width: 768px) {
        .profile-hero {
          padding: 20px;
        }

        .profile-avatar-wrapper {
          width: 90px;
          height: 90px;
        }

        .profile-actions {
          justify-content: stretch;
        }
      }
    </style>

    <div class="profile-shell">
      <div class="profile-hero">
      <div class="hero-main">
        <a href="javascript:void(0)" data-toggle="modal" data-target="#myFoto" class="profile-avatar-wrapper" title="Alterar foto de perfil">
          <?php if ($usuario->foto) : ?>
            <img src="<?= url("uploads/perfil/$usuario->foto") ?>" alt="Foto de perfil">
          <?php else : ?>
            <img src="<?= url("uploads/perfil/user.png") ?>" alt="Foto padrão">
          <?php endif; ?>
          <span class="avatar-action">Atualizar foto</span>
        </a>

        <div class="hero-info">
          <span class="hero-role"><?= $tipo_usuario[$usuario->tipo_usuario]; ?></span>
          <h2><?= $usuario->nome ?> <?php if (!empty($grupo[$usuario->grupo])) : ?><span class="chip chip-muted"><?= strtoupper($grupo[$usuario->grupo]); ?></span><?php endif; ?></h2>
          <div class="hero-chips">
            <span class="chip <?= @$_SESSION["verificado"] == 1 ? "chip-success" : "chip-warning" ?>">
              <?= @$_SESSION["verificado"] == 1 ? "Conta verificada" : "Conta não verificada" ?>
            </span>
            <span class="chip chip-muted">ID <?= $usuario->id ?></span>
          </div>
        </div>
      </div>

      <div class="hero-meta">
        <div class="meta-card">
          <span>Email</span>
          <strong><?= $dados->email ?></strong>
        </div>
        <div class="meta-card">
          <span>Telefone</span>
          <strong><?= $dados->telefone ?: "—" ?></strong>
        </div>
        <div class="meta-card">
          <span>Login</span>
          <strong><?= $usuario->login ?></strong>
        </div>
      </div>
      </div>

      <div class="profile-layout" style="align-items: flex-start;">
      <div class="profile-card">
        <div class="profile-card__header">
          <p class="eyebrow">Perfil</p>
          <h3>Editar Perfil</h3>
          <p>Atualize os dados pessoais, informações de contato e endereço para manter sua conta segura e verificada.</p>
        </div>

        <?php if (@$_GET["msg"] && @$_GET["classCss"]) : ?>
          <div class="profile-alert <?= $_GET["classCss"] ?>"><?= $_GET["msg"] ?></div>
        <?php endif; ?>

        <div class="profile-alert warning">
          <strong>Importante:</strong> preencha com atenção e anexe os documentos solicitados para liberar todos os serviços da plataforma.
        </div>

        <form action="#" method="post" id="perfil-form" class="profile-form">
          <input type="hidden" name="id" id="id" value="<?= $_SESSION["id"] ?>">

          <div class="form-section">
            <div class="section-head">
              <h6>Dados pessoais</h6>
              <p>Informações que identificam seu perfil dentro da WinPay.</p>
            </div>
            <div class="form-grid form-grid-3">
              <label class="input-block">
                <span>Nome completo</span>
                <input type="text" <?= $usuario->verificado == 1 ? "readonly" : "" ?> name="nome" id="nome" class="form-control input-lg" value="<?= $dados->nome ?>">
              </label>
              <label class="input-block">
                <span>Nome de exibição</span>
                <input type="text" name="nome_abreviado" id="nome_abreviado" class="form-control input-lg" value="<?= $usuario->nome_abreviado ?>">
              </label>
              <label class="input-block">
                <span>Login</span>
                <input type="text" name="login" id="login" class="form-control input-lg" value="<?= $usuario->login ?>">
              </label>
            </div>
          </div>

          <div class="form-section">
            <div class="section-head">
              <h6>Contato e segurança</h6>
              <p>Mantenha seu email atualizado para recuperar a conta e receber notificações.</p>
            </div>
            <div class="form-grid form-grid-3">
              <label class="input-block">
                <span>Email</span>
                <input type="text" <?= $usuario->verificado == 1 ? "readonly" : "" ?> name="email" id="email" class="form-control input-lg" value="<?= $dados->email ?>">
              </label>
              <label class="input-block">
                <span>Senha</span>
                <input type="password" name="senha" id="senha" class="form-control input-lg" value="">
                <input type="hidden" name="senha_atual" value="<?= $usuario->senha ?>">
                <small>Deixe em branco para manter a mesma senha.</small>
              </label>
              <label class="input-block">
                <span>Telefone / Celular</span>
                <input type="text" name="telefone" id="telefone" class="form-control input-lg" value="<?= $dados->telefone ?>">
              </label>
            </div>
          </div>

          <div class="form-section">
            <div class="section-head">
              <h6>Documentos</h6>
              <p>Essas informações são essenciais para validação fiscal e financeira.</p>
            </div>
            <div class="form-grid form-grid-3">
              <label class="input-block">
                <span>Tipo de documento</span>
                <select name="tipo_doc" id="tipo_doc" class="form-control input-lg" <?= $usuario->verificado == 1 ? "readonly" : "" ?>>
                  <option value="1" <?= ($dados->tipo_doc == 1) ? 'selected' : '' ?>>CPF</option>
                  <option value="2" <?= ($dados->tipo_doc == 2) ? 'selected' : '' ?>>CNPJ</option>
                </select>
              </label>
              <label class="input-block">
                <span>CPF / CNPJ</span>
                <input type="text" name="cpf" id="cpf" class="form-control input-lg" value="<?= $dados->cpf_cnpj ?>" <?= $usuario->verificado == 1 ? "readonly" : "" ?>>
              </label>
              <label class="input-block">
                <span>Anexos</span>
                <button type="button" class="btn modern-btn secondary" data-toggle="modal" data-target="#modalDocs" title="Enviar Documentos">Escolher documentos</button>
                <small>Envie RG/CNH e demais documentos solicitados.</small>
              </label>
            </div>
          </div>

          <div class="form-section box-pj">
            <div class="section-head">
              <h6>Dados da empresa</h6>
              <p>Visível apenas para contas registradas como pessoa jurídica.</p>
            </div>
            <div class="form-grid form-grid-3">
              <label class="input-block">
                <span>Razão social</span>
                <input type="text" name="razao_social" id="razao_social" class="form-control input-lg" value="<?= $dados->razao_social ?>">
              </label>
              <label class="input-block">
                <span>Nome fantasia</span>
                <input type="text" name="nome_fantasia" id="nome_fantasia" class="form-control input-lg" value="<?= $dados->nome_fantasia ?>">
              </label>
              <label class="input-block">
                <span>Tipo de empresa</span>
                <select name="tipo_empresa" id="tipo_empresa" class="form-control input-lg" <?= $usuario->verificado == 1 ? "readonly" : "" ?>>
                  <option value="MEI" <?= ($dados->tipo_empresa == "MEI") ? 'selected' : '' ?>>Micro Empreendedor Individual - MEI</option>
                  <option value="LIMITED" <?= ($dados->tipo_empresa == "LIMITED") ? 'selected' : '' ?>>Empresa Limitada</option>
                  <option value="INDIVIDUAL" <?= ($dados->tipo_empresa == "INDIVIDUAL") ? 'selected' : '' ?>>Empresa Individual</option>
                  <option value="ASSOCIATION" <?= ($dados->tipo_empresa == "ASSOCIATION") ? 'selected' : '' ?>>Associação</option>
                </select>
              </label>
            </div>
          </div>

          <div class="form-section">
            <div class="section-head">
              <h6>Endereço principal</h6>
              <p>Use um endereço válido. O CEP ajuda a preencher automaticamente alguns campos.</p>
            </div>
            <div class="form-grid form-grid-3">
              <label class="input-block">
                <span>CEP</span>
                <input type="text" name="cep" id="cep" class="form-control input-lg" value="<?= $dados->cep ?>">
              </label>
              <label class="input-block">
                <span>Rua</span>
                <input type="text" name="rua" id="rua" class="form-control input-lg" value="<?= $dados->rua ?>">
              </label>
              <label class="input-block">
                <span>Número</span>
                <input type="text" name="numero" id="numero" class="form-control input-lg" value="<?= $dados->numero ?>">
              </label>
            </div>
            <div class="form-grid form-grid-3" style="margin-top: 12px;">
              <label class="input-block">
                <span>Complemento</span>
                <input type="text" name="complemento" id="complemento" class="form-control input-lg" value="<?= $dados->complemento ?>">
              </label>
              <label class="input-block">
                <span>Bairro</span>
                <input type="text" name="bairro" id="bairro" class="form-control input-lg" value="<?= $dados->bairro ?>">
              </label>
              <label class="input-block">
                <span>Cidade</span>
                <input type="text" name="cidade" id="cidade" class="form-control input-lg" value="<?= $dados->cidade ?>">
              </label>
            </div>
            <div class="form-grid form-grid-3" style="margin-top: 12px;">
              <label class="input-block">
                <span>Estado</span>
                <?= $v->insert("usuarios/uf", ["estado" => $dados->estado]); ?>
              </label>
              <label class="input-block">
                <span>País</span>
                <input type="text" name="pais" id="pais" class="form-control input-lg" value="<?= $dados->pais ?>">
              </label>
            </div>
          </div>

          <div class="profile-actions">
            <button type="submit" class="btn btn-primary modern-btn" id="salvar">Salvar alterações</button>
          </div>
        </form>
      </div>

      <div class="profile-card profile-sidecard">
        <?php if ($_SESSION["tipo"] == 6) : ?>
          <h4>Quer vender pela WinPay?</h4>
          <p>Se você ainda é comprador, pode dar o próximo passo e liberar os módulos de Produtor e Afiliado. Assim você publica produtos, cria campanhas e acompanha comissões em tempo real.</p>
          <div class="cta">
            <button class="btn btn-outline-primary modern-btn" data-toggle="modal" data-target="#modal-lg" id="cadastro">Tornar-se Afiliado/Produtor <i class="fa fa-chevron-right"></i></button>
          </div>
        <?php else : ?>
          <h4>Resumo da conta</h4>
          <p>Mantenha seus dados fiscais e bancários atualizados para continuar recebendo com segurança.</p>
          <div class="cta">
            <a href="<?= url("banco"); ?>" class="btn btn-outline-primary modern-btn">Gerenciar contas bancárias</a>
          </div>
        <?php endif; ?>
      </div>
      </div>
      <?php if ($_SESSION["tipo"] != 6) : ?>
        <div class="profile-card profile-sidecard" style="margin-top:24px;">
          <h4>Taxas do seu plano</h4>
          <p>Confira as condições atuais aplicadas às suas vendas e ao prazo de liberação de comissões.</p>

          <?php
          $diferenca = strtotime(date("Y-m-d 23:59:59")) - strtotime($usuario->created_at);
          $dias = floor($diferenca / (60 * 60 * 24));
          ?>

          <div class="profile-table-wrapper">
            <table class="profile-table">
              <thead>
                <tr>
                  <th>Taxa transação*</th>
                  <th>Taxa assinatura*</th>
                  <th>Saque cartão**</th>
                  <th>Saque boleto**</th>
                  <th>Saque pix**</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td><?= number_format($usuario->taxa_redrocket, 2, ",", "."); ?>%</td>
                  <td><?= number_format($usuario->taxa_assinatura, 2, ",", "."); ?>%</td>
                  <td><?= $usuario->tempo_saque; ?> dias</td>
                  <td><?= $usuario->tempo_boleto; ?> dias</td>
                  <td><?= $usuario->tempo_pix; ?> dias</td>
                </tr>
              </tbody>
            </table>
          </div>
          <small>* Apenas uma taxa é aplicada por transação de acordo com o tipo de cobrança.</small><br>
          <small>** Tempo para a comissão ficar disponível para saque.</small>

          <?php if ($dias > 30) : ?>
            <div class="cta" style="margin-top: 16px;">
              <button class="btn btn-outline-primary modern-btn" data-toggle="modal" data-target="#modal-taxas"><i class="fa fa-edit"></i> Solicitar ajuste de taxa</button>
            </div>
          <?php endif; ?>
        </div>
      <?php endif; ?>
    </div>
  </section>

<!-- Modal -->
<div id="myFoto" class="modal fade" role="dialog">
  <div class="modal-dialog modal-lg">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Editar <?= $usuario->nome; ?></h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>

      <form action="<?= $router->route("appusuarios.fotoPerfil"); ?>" method="post" enctype="multipart/form-data">

        <div class="modal-body">
          <input type="hidden" name="idU" value="<?= $usuario->id ?>">
          <input type="hidden" name="foto_atual" value="<?= $usuario->foto ?>">

          Dimensões da foto: máximo 500px X 500px <br>
          Tamanho máximo: 1 MB <br>
          Escolher Foto:
          <input type="file" name="foto" id="" class="form-control">
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-danger" data-dismiss="modal" id="cancelar">Cancelar</button>
          <button class="btn btn-primary">Salvar</button>
        </div>

      </form>

    </div>

  </div>
</div>

<!-- Modal -->
<div id="modalDocs" class="modal fade" role="dialog">
  <div class="modal-dialog modal-lg">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Editar <?= $usuario->nome; ?></h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>

      <form action="<?= $router->route("appusuarios.documentoPerfil"); ?>" method="post" enctype="multipart/form-data">
        <div class="modal-body">
          <input type="hidden" name="idU" value="<?= $usuario->id ?>">
          <input type="hidden" name="foto_atual" value="<?= $usuario->foto ?>">
          Formatos aceitos: pdf, png, jpg, jpeg, zip, rar <br>
          Documentos necessários: RG ou CNH (frente e verso) <br>
          Para pessoas júridicas Cartão CNPJ e Selfie com documento de identificação <br><br>

          Enviar RG/CNH (Frente)/Cartão CNPJ:
          <input type="file" name="documento1" id="" class="form-control" <?= $usuario->verificado == 1 ? "disabled" : "" ?>>
          <?= $usuario->documento1 != "" ? "<a href='" . url("uploads/documentos/$usuario->documento1") . "' target='_blank'>Ver documento</a>" : "" ?><br><br>
          Enviar RG/CNH (Verso):
          <input type="file" name="documento2" id="" class="form-control" <?= $usuario->verificado == 1 ? "disabled" : "" ?>>
          <?= $usuario->documento2 != "" ? "<a href='" . url("uploads/documentos/$usuario->documento2") . "' target='_blank'>Ver documento</a>" : "" ?>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-danger" data-dismiss="modal" id="cancelar">Cancelar</button>
          <button class="btn btn-primary">Salvar</button>
        </div>
      </form>

    </div>

  </div>
</div>

<!-- cadastar -->
<div class="modal fade" id="modal-lg">
  <div class="modal-dialog">
    <div class="modal-content">

      <div class="modal-header">
        <h3 class="card-title"><i class="fa fa-info-circle"></i> O que é afiliado digital?</h3>
        <button type="button" class="close" data-dismiss="modal"> &times;</button>
      </div>

      <div class="modal-body">
        <p>
          Se você está antenado no mundo digital e se interessa pelo empreendedorismo,
          provavelmente já ouviu falar sobre o programa de Afiliados.
        </p>

        <p>
          <b>Mas afinal, o que é afiliado?</b> <br>
          Bom, trata-se de alguém que indica produtos feitos por outras pessoas e recebe comissões
          pelas vendas realizadas.
        </p>

        Se você deseja se tornar um produtor ou afiliado RedRocket, clique no botão abaixo <br><br>
        <button class="btn btn-outline-primary btn-sm" id="afiliado">Tornar-se Afiliado/Produtor</button>
      </div>
    </div>

  </div>

</div>

<!-- Modal -->
<div id="modal-taxas" class="modal fade" role="dialog">
  <div class="modal-dialog modal-lg">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Alterar taxas de recebimento</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>

      <form action="<?= $router->route("appusuarios.taxas"); ?>" method="post" enctype="multipart/form-data">
        <div class="modal-body" style="background: #f4f6f9">
          <input type="hidden" name="idU" value="<?= $usuario->id ?>">
          <input type="hidden" name="foto_atual" value="<?= $usuario->foto ?>">

          <div class="card card-body">
            Escolha o tempo e a taxa de recebimento:

            <select name="taxas" id="taxas" class="form-control">
              <option value="30" <?= $usuario->tempo_saque == 30 ? "selected" : "" ?>>30 dias - 3,49%</option>
              <option value="15" <?= $usuario->tempo_saque == 15 ? "selected" : "" ?>>15 dias - 5,49%</option>
              <!-- <option value="3" <?= $usuario->tempo_saque == 3 ? "selected" : "" ?>>3 dias - 7,49%</option> -->
            </select>
          </div>
        </div>

        <div class="modal-footer justify-content-between">
          <button type="button" class="btn btn-danger" data-dismiss="modal" id="cancelar">Cancelar</button>
          <button class="btn btn-primary">Salvar</button>
        </div>
      </form>
    </div>
  </div>
</div>
<!-- end cadastrar -->

<?= $v->start("scripts") ?>
<script>
  function mostrar() {
    var tipo = $("#tipo_doc").val();
    if (tipo == 1) {
      $(".box-pj").hide();
    } else {
      $(".box-pj").show();
    }
  }
  mostrar();

  $("#tipo_doc").change(() => {
    mostrar();
  });

  $("#salvar").click(function(e) {
    e.preventDefault();
    salvarEditar("<?= $router->route("appusuarios.perfilEditar"); ?>");
  });

  $("#afiliado").click(function(e) {
    e.preventDefault();
    salvarEditar("<?= $router->route("appusuarios.tipoUsuario"); ?>");
  });

  function salvarEditar(url) {

    var form = $("#perfil-form");

    $.ajax({

      url: url,
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
        if (callback == "Atualizado Com Sucesso!") {
          location.reload();
        }
      }

    });
  }

  $("#cep").focusout(function() {
    $.ajax({
      url: 'https://api.pagar.me/1/zipcodes/' + $(this).val(),

      dataType: 'json',
      success: function(resposta) {
        $("#rua").val(resposta.street);
        $("#bairro").val(resposta.neighborhood);
        $("#cidade").val(resposta.city);
        $("#estado").val(resposta.state);
        $("#pais").val("BR");
      }
    });
  });

  $("#cep_entrega").focusout(function() {
    $.ajax({
      url: 'https://api.pagar.me/1/zipcodes/' + $(this).val(),

      dataType: 'json',
      success: function(resposta) {
        $("#rua_entrega").val(resposta.street);
        $("#bairro_entrega").val(resposta.neighborhood);
        $("#cidade_entrega").val(resposta.city);
        $("#estado_entrega").val(resposta.state);
        $("#pais_entrega").val("BR");
      }
    });
  });
</script>
<?= $v->end() ?>

</body>

</html>
