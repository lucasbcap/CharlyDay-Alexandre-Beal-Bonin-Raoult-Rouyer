<?php

namespace custumbox\action;

class FavoriAction extends Action
{
    public function __construct()
    {
        parent::__construct();
    }

    public function execute(): string
    {
        $res = "";
        if($_GET['fav'] == 'oui'){
            unserialize($_SESSION['user'])->suppSQL($_GET['id'], "favori");
        }else {
            unserialize($_SESSION['user'])->addSQL($_GET['id'], "favori");
        }
        return $res;
    }
}