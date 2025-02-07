<?php

namespace Source\Models\Ecommerce;

use CoffeeCode\DataLayer\DataLayer;
use Source\Models\Usuarios\Usuario;
use Source\Models\Pixel\Pixel;


class Ecommerce extends DataLayer
{
    public function __construct()
    {

        date_default_timezone_set('America/Sao_Paulo');
        parent::__construct("ecommerce", []);
    }

    /** RETORNA OS DADOS DO USUARIO */
    public function dataUsuario()
    {
        if ($this->usuario)
            return (new Usuario())->findById($this->usuario);
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
}

/** PROPRIEDADES
 * 
 *   id Primária 	int(11)
 *   usuario 	int(11)
 *   habilitar 	tinyint(4)
 *   nome_loja 	varchar(200)
 *   logo 	varchar(300)
 *   informacoes_loja 	text
 *   banner 	varchar(300)
 *   destaques 	varchar(100)
 *   cores 	varchar(100)
 *   duvidas 	text
 *   contatos 	varchar(200)
 *   pixel_facebook 	int(11)
 *   pixel_facebook_api 	int(11)
 *   pixel_taboola 	int(11)
 *   pixel_google_analytics 	int(11)
 *   pixel_google_ads 	int(11)
 *   pixel_google_tag_manager 	int(11)
 *   pixel_tiktok 	int(11)
 *   created_at 	timestamp
 *   updated_at 	timestamp
 *   url_loja 	varchar(200) 
 */
