<?php
namespace custumbox\Render;
abstract class Render
{

    abstract public function render(int $selector):string;

}