<?php
    try {

        LoginDAO::verificarSeLogado();
        $permissao->verificarPermissao(automacao_sistema);

        require_once ('negocio/activecampaignmon/ActiveCampaignMon.class.php');

    } catch (excetion $e) {

        $_SESSION['ultima_msg_erro'] = $e->getMessage();
        require ('interface/painel/erro.php');

    }

    $activecapaign = new ActiveCampaignMon;
    $activecampaigns = $banco->retornarPorCampo("usuario", $_SESSION["conta_atual"], $activecampaign);

    foreach ($activecampaigns as $activecampaign) {
?>
    <option value=<?php echo $activecampaign->codigo; ?>"><?php $activecampaign->account; ?></option>
<?php
    }
?>