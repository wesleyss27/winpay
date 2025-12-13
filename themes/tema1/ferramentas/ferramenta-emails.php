<?php
  $v->layout("_theme");
?>

<style>
  .email-admin__card h3 {
    font-size: 1.1rem;
    font-weight: 700;
    margin: 0;
  }

  .email-admin__badge {
    display: inline-block;
    padding: 4px 8px;
    border-radius: 8px;
    background: rgba(217, 192, 0, 0.12);
    color: #b18a00;
    font-weight: 600;
    font-size: 0.78rem;
  }

  .email-admin__muted {
    color: #9aa0a6;
    font-size: 0.92rem;
  }

  .email-admin__pill {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 10px;
    border-radius: 12px;
    background: rgba(255, 255, 255, 0.06);
    font-size: 0.9rem;
  }

  .email-admin__stats {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
    gap: 0.8rem;
  }

  .email-admin__stats .stat {
    padding: 0.85rem;
    border-radius: 12px;
    border: 1px solid rgba(255, 255, 255, 0.08);
    background: rgba(255, 255, 255, 0.02);
  }

  .email-admin__stats .stat strong {
    display: block;
    font-size: 1.1rem;
  }
</style>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6" style="margin-bottom:10px">
          <h1 style="font-family:inter"><?= $title ?></h1>
          <span class="email-admin__badge">Envio em massa</span>
        </div>
        <div class="col-sm-6" style="font-family:inter">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="<?= url() ?>">Início</a></li>
            <li class="breadcrumb-item active">Disparo de Emails</li>
          </ol>
        </div>
      </div>
    </div>
  </section>

  <section class="content">
    <div class="container-fluid">
      <?php if ($feedback) : ?>
        <div class="alert alert-<?= $feedback["tipo"] ?> alert-dismissible fade show" role="alert">
          <?= $feedback["mensagem"] ?>
          <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
      <?php endif; ?>

      <div class="row">
        <div class="col-lg-8">
          <div class="card card-dark email-admin__card">
            <div class="card-header">
              <h3 class="card-title">Configurar disparo</h3>
              <span class="email-admin__muted ml-2">Selecione o público, personalize e envie</span>
            </div>
            <div class="card-body">
              <form action="<?= url("emails") ?>" method="post">
                <div class="form-row">
                  <div class="form-group col-md-4">
                    <label for="produto_id">Produto (opcional)</label>
                    <select name="produto_id" id="produto_id" class="form-control">
                      <option value="">Sem filtro por produto</option>
                      <?php if (@$produtos) : ?>
                        <?php foreach ($produtos as $produto) : ?>
                          <option value="<?= $produto->id ?>" <?= @$old["produto_id"] == $produto->id ? "selected" : "" ?>>
                            <?= $produto->nome ?>
                          </option>
                        <?php endforeach; ?>
                      <?php endif; ?>
                    </select>
                    <small class="email-admin__muted">Lista apenas produtos do seu usuário.</small>
                  </div>

                  <div class="form-group col-md-4">
                    <label for="status_filtro">Status das vendas (ativo apenas com produto)</label>
                    <select name="status_filtro" id="status_filtro" class="form-control">
                      <?php foreach ($statusOpcoes as $chave => $opcao) : ?>
                        <option value="<?= $chave ?>" <?= @$old["status_filtro"] == $chave ? "selected" : "" ?>>
                          <?= $opcao["label"] ?>
                        </option>
                      <?php endforeach; ?>
                    </select>
                    <small class="email-admin__muted">Pagas (4/8), Pendentes (1/2/3), Canceladas (5/6/7). Ignorado se você não escolher produto.</small>
                  </div>

                  <div class="form-group col-md-4">
                    <label for="assunto">Assunto</label>
                    <input type="text" class="form-control" id="assunto" name="assunto" value="<?= htmlspecialchars(@$old["assunto"] ?? "", ENT_QUOTES, "UTF-8") ?>" required>
                  </div>
                </div>

                <div class="form-group">
                  <label for="destinatarios_extra">Emails manuais (opcional)</label>
                  <textarea name="destinatarios_extra" id="destinatarios_extra" class="form-control" rows="2" placeholder="email1@dominio.com, email2@dominio.com ou um por linha"><?= htmlspecialchars(@$old["destinatarios_extra"] ?? "", ENT_QUOTES, "UTF-8") ?></textarea>
                  <small class="email-admin__muted">Podemos disparar apenas para essa lista ou somar ao filtro. Separar por vírgula, ponto e vírgula ou quebra de linha.</small>
                </div>

                <div class="form-group">
                  <label for="mensagem-editor">Mensagem</label>
                  <textarea name="mensagem" id="mensagem-editor" class="form-control"><?= @$old["mensagem"] ?? "" ?></textarea>
                </div>

                <div class="d-flex justify-content-between align-items-center">
                  <div class="email-admin__pill">
                    <span class="material-symbols-rounded" style="font-size: 18px;">group</span>
                    Destinatários = emails manuais ou compradores filtrados (se produto escolhido), sem duplicar
                  </div>
                  <button type="submit" class="btn btn-primary btn-lg">
                    <span class="material-symbols-rounded" style="vertical-align: middle; font-size: 20px;">mail</span>
                    Enviar emails
                  </button>
                </div>
              </form>
            </div>
          </div>

          <?php if ($resultadoEnvio) : ?>
            <div class="card card-outline card-success">
              <div class="card-header">
                <h3 class="card-title">Resultado do disparo</h3>
                <span class="email-admin__muted ml-2"><?= $resultadoEnvio["status"] ?> | <?= $resultadoEnvio["total"] ?> destinatário(s)</span>
              </div>
              <div class="card-body">
                <div class="email-admin__stats mb-3">
                  <div class="stat">
                    <span class="email-admin__muted d-block">Total</span>
                    <strong><?= $resultadoEnvio["total"] ?></strong>
                  </div>
                  <div class="stat">
                    <span class="email-admin__muted d-block">Enviados</span>
                    <strong class="text-success"><?= $resultadoEnvio["sucesso"] ?></strong>
                  </div>
                  <div class="stat">
                    <span class="email-admin__muted d-block">Falhas</span>
                    <strong class="text-danger"><?= $resultadoEnvio["falha"] ?></strong>
                  </div>
                  <div class="stat">
                    <span class="email-admin__muted d-block">Filtro (produto/status)</span>
                    <strong><?= $resultadoEnvio["automatico"] ?></strong>
                  </div>
                  <div class="stat">
                    <span class="email-admin__muted d-block">Manuais</span>
                    <strong><?= $resultadoEnvio["manual"] ?></strong>
                  </div>
                </div>

                <div class="table-responsive">
                  <table class="table table-sm table-hover">
                    <thead>
                      <tr>
                        <th>Destinatário</th>
                        <th>Retorno</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php foreach ($resultadoEnvio["logs"] as $log) : ?>
                        <tr>
                          <td>
                            <strong><?= $log["nome"] ?></strong><br>
                            <small><?= $log["email"] ?></small>
                          </td>
                          <td><?= $log["retorno"] ?></td>
                        </tr>
                      <?php endforeach; ?>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          <?php endif; ?>
        </div>

        <div class="col-lg-4">
          <div class="card card-outline card-warning">
            <div class="card-header">
              <h3 class="card-title">Como funciona</h3>
            </div>
            <div class="card-body">
              <p class="email-admin__muted">Filtramos todas as vendas do produto escolhido pelo status e enviamos um email por comprador (evitando duplicados). Você também pode enviar apenas para emails manuais.</p>
              <ul class="list-unstyled">
                <li class="mb-2"><strong>Pagas</strong>: status 4 (Paga) e 8 (Concluída).</li>
                <li class="mb-2"><strong>Pendentes</strong>: status 1, 2 ou 3.</li>
                <li class="mb-2"><strong>Canceladas</strong>: status 5, 6 ou 7.</li>
                <li class="mb-2"><strong>Emails manuais</strong>: digite um ou vários (vírgula, ponto e vírgula ou quebra de linha) para disparo direto.</li>
              </ul>
              <p class="email-admin__muted mb-0">Use o editor para formatar o corpo do email (imagens, links, listas, tabelas, código e cores).</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</div>

<?php $v->start("scripts"); ?>
<script>
  (function() {
    const toggleStatus = () => {
      const hasProduto = $('#produto_id').val() !== '';
      $('#status_filtro').prop('disabled', !hasProduto);
    };

    const initSummernote = () => {
      if (!window.jQuery || !$.fn || !$.fn.summernote) {
        setTimeout(initSummernote, 150);
        return;
      }

      $.getScript('<?= url("themes/tema1/plugins/summernote/lang/summernote-pt-BR.min.js") ?>', function() {
        $('#mensagem-editor').summernote({
          height: 320,
          lang: 'pt-BR',
          placeholder: 'Escreva a mensagem que será enviada para cada comprador filtrado...',
          toolbar: [
            ['style', ['style']],
            ['font', ['bold', 'italic', 'underline', 'strikethrough', 'superscript', 'subscript', 'clear']],
            ['fontname', ['fontname']],
            ['fontsize', ['fontsize']],
            ['color', ['color']],
            ['para', ['ul', 'ol', 'paragraph', 'height']],
            ['table', ['table']],
            ['insert', ['link', 'picture', 'video']],
            ['view', ['fullscreen', 'codeview', 'help']]
          ]
        });
      });
    };

    $(function() {
      $('#produto_id').on('change', toggleStatus);
      toggleStatus();
      initSummernote();
    });
  })();
</script>
<?php $v->end(); ?>
