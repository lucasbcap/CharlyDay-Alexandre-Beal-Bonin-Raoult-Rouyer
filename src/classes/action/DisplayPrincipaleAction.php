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

        $res="test";
        return $res;

    }

}