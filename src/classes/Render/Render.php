<?php
namespace custumBox\render;
abstract class Render
{

    abstract public function render(int $selector):string;

}