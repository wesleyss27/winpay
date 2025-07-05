<?php

namespace Source\Models\Checkout;

use CoffeeCode\DataLayer\DataLayer;
use Source\Models\CartaoCheckoutTransparente\Adquirente;
use Source\Models\Frete\Frete;
use Source\Models\Usuarios\Usuario;
use Source\Models\Produtos\Produto;
use Source\Models\Produtos\Plano;
use Source\Models\Pixel\Pixel;

class Checkout extends DataLayer
{
    public function __construct(){
        
        date_default_timezone_set('America/Sao_Paulo');
        parent::__construct( "checkout", [] );
    }

    /** RETORNA OS DADOS DO PRODUTOR */
    public function dataProdutor(){
        if ($this->usuario) {
            return @(new Usuario())->find("id = :uid", "uid={$this->usuario}")->fetch();
        }
    }

    /** RETORNA OS DADOS DO PRODUTO */
    public function dataProduto(){
        if ($this->produto) {
            return @(new Produto())->find("id = :uid", "uid={$this->produto}")->fetch();
        }
    }

    /** RETORNA OS DADOS DO PLANO */
    public function dataPlano(){
        if ($this->plano) {
            return @(new Plano())->find("id = :uid", "uid={$this->plano}")->fetch();
        }
    }

    public function dataPixelFacebook(){

        if ($this->pixel_facebook) {
            return @(new Pixel())->findById($this->pixel_facebook);
        }else {
            return @(new Pixel())->findById($this->pixel_facebook_api);
        }

    }

    public function dataPixelFacebookApi(){
        if ($this->pixel_facebook_api) {
            return @(new Pixel())->findById($this->pixel_facebook_api);
        }
    }

    public function dataPixelGoogleAnalitics(){
        if ($this->pixel_google_analitics) {
            return @(new Pixel())->findById($this->pixel_google_analitics);
        }
    }

    public function dataPixelGoogleAds(){
        if ($this->pixel_google_ads) {
            return @(new Pixel())->findById($this->pixel_google_ads);
        }
    }

    public function dataPixelGoogleTagManager(){
        if ($this->pixel_google_tag_manager) {
            return @(new Pixel())->findById($this->pixel_google_tag_manager);
        }
    }

    public function dataPixelTaboola(){
        if ($this->pixel_taboola) {
            return (new Pixel())->findById($this->pixel_taboola);
        }
    }

    public function dataPixelTikTok(){
        if ($this->pixel_tiktok) {
            return (new Pixel())->findById($this->pixel_tiktok);
        }
    }

    public function dataPixelKwai(){
        if ($this->pixel_tiktok) {
            return (new Pixel())->findById($this->pixel_kwai);
        }
    }
    
    public function dataFrete(){
        if ($this->id_frete) {
            return (new Frete())->findById($this->id_frete);
        }
    }

    public function dataAdquirente(){
        if ($this->adquirente) {
            return (new Adquirente())->findById($this->adquirente);
        }
    }

}

/** PROPRIEDADES
 * 
 *  id int(11)
 *  codigo varchar(20)
 *  descricao varchar(200)
 *  produto int(11)
 *  plano int(11)
 *  cupom varchar(10)
 *  valor_cupom decimal(10,2)
 *  so_cartao tinyint(4)
 *  usuario int(11)
 *  banner varchar(200)
 *  aviso varchar(200)
 *  created_at timestamp
 *  updated_at timestamp
 */
