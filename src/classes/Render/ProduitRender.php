<?php

namespace custumbox\Render;

use custumbox\Catalogue\Produit;
use custumbox\Render\Render;

class ProduitRender extends Render {

    protected Produit $produit;

    public function __construct(Produit $produit)
    {
        $this->produit = $produit;
    }
    public function render(int $selector): string
    {
        $html = "";
        // le $selector 1 permet d afficher une video donc quand on clique sur un episode on a bine la video
        // qui est afficher
        if($selector===1) {
            $html =
                "<h1>Titre : {$this->produit->nom}</h1>
                <p>Prix : {$this->produit->prix}</p>
                <p>Lieu : {$this->produit->lieu}</p>
                <img src='".$this->produit->image."'/>";
        }
        if($selector===2){
            $html = "<h1>Titre : {$this->produit->nom}</h1>" .
                "<p>Prix : {$this->produit->prix}</p>" .
                "<p>Lieu : {$this->produit->lieu}</p>" .
                "<img src='$this->produit->image'></img>
                <p>description : {$this->produit->description}</p>
                <p>detail : {$this->produit->detail}</p>
                <p>distance : {$this->produit->distance}</p>
                <p>latitude : {$this->produit->latitude}</p>
                <p>longitude : {$this->produit->longitude}</p>
                <p>stock : {$this->produit->stock}</p>
                     ";
        }
        return $html;
    }
}