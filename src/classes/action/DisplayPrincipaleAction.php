<?php

namespace custumbox\action;
use custumbox\action\Action;
use custumbox\render\CatalogueRender;

class DisplayPrincipaleAction extends Action
{
    public function __construct()
    {
        parent::__construct();
    }

    public function execute(): string
    {
        $res = "<h2>Liste de Favoris : </h2><br>";              // va afficher la liste des favoris
        $user = unserialize($_SESSION['user']);
        if ($this->http_method == "GET") {
            $array = $user->getSQL("favori");                   // on recupere tout les id des serie dans favori
            if ($array != null) {
                $res .= "<div class='listeGeneral'>";
                foreach ($array as $d) {
                    $produitcourant = new CatalogueRender($d);
                    $res .= $produitcourant->render(1);          // on affiche pour chaque leur titre et leur image
                }
                $res .= "</div>";
            } else {
                $res .= "Aucun produit en favori";
            }



        }
        return $res;
    }
}

