<?php

$common_php_dir = '../php_common';
$common_autoload_file = $common_php_dir . '/autoload.php';
require ($common_autoload_file);

$cli_php_dir = '../php_cli';
$cli_autoload_file = $cli_php_dir . '/autoload.php';
require ($cli_autoload_file);

require ('./lib/autoload.php');

use lib\classes\PHPUnitTest;
use lib\classes\validate as localCli;

\common\Config::obj(__DIR__ . '/config/config.ini');

if ($argc >= 1) {
    $f = new localCli\TestOpts();
    try {
        $f->exchangeArray(array_slice($argv, 1));
    } catch (\UnexpectedValueException $e) {
        exit(\common\logging\Logger::obj()->writeException($e));
    }
}

$phpUnitTest = new PHPUnitTest($f);

$phpUnitTest->run();