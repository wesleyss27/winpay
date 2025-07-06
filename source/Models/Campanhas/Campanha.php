<?php

namespace Source\Models\Campanhas;

use CoffeeCode\DataLayer\DataLayer;
use Source\Models\Usuarios\Usuario;
use Source\Models\Produtos\Produto;
use Source\Models\Pixel\Pixel;
use Source\Models\Vendas\Venda;

class Campanha extends DataLayer
{
    public function __construct(){
        
        date_default_timezone_set('America/Sao_Paulo');
        parent::__construct( "campanhas", [] );
    }

    /** RETORNA OS DADOS DO USUARIO */
    public function dataUsuario(){
        return (new Usuario())->findById($this->usuario);
    }

    /** RETORNA OS DADOS DO PRODUTO */
    public function dataProduto(){
        return (new Produto())->findById($this->produto);
    }

    /** RETORNA OS DADOS DO PIXEL */
    public function dataPixel(){
        if (@$this->pixel) {
            return (new Pixel())->findById($this->pixel);
        }
    }

    public function dataPixelFacebook(){
        if ($this->pixel_facebook) {
            return @(new Pixel())->findById($this->pixel_facebook);
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
        if ($this->pixel_kwai) {
            return (new Pixel())->findById($this->pixel_kwai);
        }
    }

    /** RETORNA A QUANTIDADE DE VENDAS */
    public function countVendas(){
        return (new Venda())->find("campanha = $this->id")->count();
    }
}

/** PROPRIEDADES
 * 
 *  id int(11)
 *  codigo varchar(20)
 *  usuario int(11)
 *  produto int(11)
 *  nome varchar(200)
 *  url_destino varchar(200)
 *  pixel int(11)
 *  created_at timestamp
 *  updated_at timestamp
 */