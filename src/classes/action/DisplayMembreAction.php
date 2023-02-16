<?php

namespace custumbox\action;

use custumbox\Render\MembreRenderer;
use custumbox\Render\Render;

class DisplayMembreAction extends Action
{
    protected int $select;
    public function __construct($selector)
    {
        $this->select = $selector;
        parent::__construct();
    }


    public function execute(): string
    {
        $membreRender = new MembreRenderer();
        return $membreRender->render($this->select);
    }
}