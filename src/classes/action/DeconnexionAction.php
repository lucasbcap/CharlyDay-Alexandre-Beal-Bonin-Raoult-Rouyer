<?php

namespace custumbox\action;
use custumbox\action\Action;

class DeconnexionAction extends Action
{
    public function __construct()
    {
        parent::__construct();
    }

    public function execute(): string
    {
        $res ="";
        session_destroy();                  // on detruit la session
        header('location: ./');      // et on reviens a l'accueil
        return $res;
    }
}