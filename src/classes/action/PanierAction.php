<?php

namespace custumbox\action;

class PanierAction extends Action
{
    public function __construct()
    {
        parent::__construct();
    }

    public function execute(): string
    {
        $res = "";
        if($_GET['panier'] == 'oui'){
            unserialize($_SESSION['user'])->suppSQL($_GET['idProduit'], "panier");
        }else {
            unserialize($_SESSION['user'])->addPanier($_GET['idProduit'], $_GET['qte']);
        }
        return $res;
    }
}