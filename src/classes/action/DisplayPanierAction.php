<?php

namespace custumbox\action;

use custumbox\Render\PanierRenderer;

class DisplayPanierAction extends Action
{
    public function __construct()
    {
        parent::__construct();
    }


    public function execute(): string
    {
        $rendererPanier = new PanierRenderer();
        return $rendererPanier->render(2);
    }
}