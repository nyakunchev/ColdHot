<?php

$vendorGit = __DIR__ . '/../vendor/autoload.php';
$autoPackagist = __DIR__ . '/../../../autoload.php';

if (file_exists($vendorGit)) {
    require_once($vendorGit);
} else {
    require_once($autoPackagist);
}

use function nyakunchev\cold_hot\Controller\key;

if (isset($argv[1])) {
    if ($argv[1] === "-r" || $argv[1] === "--replay") {
        key($argv[1], $argv[2]);
    } else {
        key($argv[1], null);
    }
} else {
    key("-n", null);
}
