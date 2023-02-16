<?php

namespace custumbox\action;
use custumbox\action\Action;

class DisplayPrincipaleAction extends Action
{
    public function __construct()
    {
        parent::__construct();
    }

    public function execute(): string
    {
        $res = "CECI EST LA PAGE DE BASE";
        return $res;

    }

}