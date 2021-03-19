<?php

$common_php_dir = '../php_common';
$common_autoload_file = $common_php_dir . '/autoload.php';
require ($common_autoload_file);

$cli_php_dir = '../php_cli';
$cli_autoload_file = $cli_php_dir . '/autoload.php';
require ($cli_autoload_file);

require ('./lib/autoload.php');

use lib\classes\PHPUnitTest;
use lib\classes\validate;
use common\logging\Logger;

\common\Config::obj(__DIR__ . '/config/config.ini');

$opt = new validate\TestOpts();
try {
    $opt->exchangeArray(array_slice($argv, 1));

    if (!isset($opt->path)) {
        exit(Logger::obj()->write('--path must be set to the location of the test files', -1, true, 1));
    }

    if (!isset($opt->namespace)) {
        exit(Logger::obj()->write('--namespace must be set to the namespace of the test files', -1, true, 2));
    }
} catch (\UnexpectedValueException $e) {
    exit(\common\logging\Logger::obj()->writeException($e));
}

$phpUnitTest = new PHPUnitTest($opt);

$allPassing = $phpUnitTest->run();

if ($allPassing) {
    $log = sprintf(
            'Test complete.  Success! All tests are passing.  Total Tests Passing: %d, Total Tests Failing: %d', 
            $phpUnitTest->testCounts['passing'],
            $phpUnitTest->testCounts['failing'],
            );
    exit(Logger::obj()->write($log, 0, true, 0));
} else {
    $log = sprintf(
            'Tesst complete. Failed! Tests are NOT passing. Total Tests Passing: %d, Total Tests Failing: %d',
            $phpUnitTest->testCounts['passing'],
            $phpUnitTest->testCounts['failing']
            );
    exit(Logger::obj()->write($log, -1, true, 3));
}