<?php

namespace custumbox\Render;

use customBox\Produit\Produit;
use customBox\render\Render;

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
                "<h1>Titre : {$this->produit->nom}</h1>" .
                "<h1>Prix : {$this->produit->prix}</h1>" .
                "<h1>Lieu : {$this->produit->lieu}</h1>" .
                "<img>{$this->produit->image}</img>";
        }
        return $html;
    }
}