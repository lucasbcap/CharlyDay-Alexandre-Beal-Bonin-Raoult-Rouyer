<?php
require_once 'vendor/autoload.php';

use custumbox\db\ConnectionFactory;
use custumbox\dispatcher\Dispatcher;


session_start();

ConnectionFactory::setConfig("config.ini");

$html = new Dispatcher();
$html->run();
