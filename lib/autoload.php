<?php
namespace lib;

spl_autoload_register(function ($class) {
    if (strpos($class, 'PHPUnitTest\\lib\\') === 0) {
        $class = substr($class, 3);
        $classFile = realpath(__DIR__ . '/' . str_replace('\\', DIRECTORY_SEPARATOR, $class) . '.php');
        if (file_exists($classFile)) {
            require ($classFile);
        }
        unset($classFile);
    }
});
