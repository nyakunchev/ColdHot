#!/usr/bin/env php

<?php

require_once __DIR__ . '/../vendor/autoload.php';

use nyakunchev\ColdHot\Controller\Controller;

$controller = new Controller;
$controller->startGame();