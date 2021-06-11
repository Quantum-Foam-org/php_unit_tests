<?php
namespace PHPUnitTest;

spl_autoload_register(function ($class) {
    $namespace = 'PHPUnitTest\\lib\\';
    
    if (strpos($class, $namespace) === 0) {
        $class = substr($class, strlen($namespace)-1);
       
        $classFile = realpath(__DIR__ . str_replace('\\', DIRECTORY_SEPARATOR, $class) . '.php');
        
        if (file_exists($classFile)) {
            require ($classFile);
        }
        unset($classFile);
    }
});
        