<?php

namespace PHPUnitTest\lib\classes;

use common\logging\Logger as Logger;
use cli\classes\Readline;


class PHPUnitTest extends Readline {
    private $testFiles = [];
    private $opts;
    private $testCounts = ['passing' => 0, 'failing' => 0];
    
    public function __construct(validate\TestOpts $opts) 
    {   
        if (is_dir($opts->path)) {
            $bootstrapFile = sprintf('%s/Bootstrap.php', $opts->path);

            if (file_exists($bootstrapFile) && is_readable($bootstrapFile)) {
                require_once($bootstrapFile);
                $log = sprintf('bootstrap file has been requried, %s', $bootstrapFile);
                Logger::obj()->write($log);
            } else {
                $log = sprintf('bootstrap is not found or cannot be read, %s', $bootstrapFile);
                Logger::obj()->write($log);
            }
            
            $testFilepath = sprintf('%s/classes/%s', $opts->path,'*Test.php');
        
            if (!empty(($testFiles = glob($testFilepath)))){
                $this->testFiles = $testFiles;
            }
        } else {
            $log = sprintf('Test Files not Found make sure that the directory exists and is readable.  DIR: %s', $opts->path);
            Logger::obj()->write($log);
        }
        
        $this->opts = $opts;
    }
    
    public function run() : bool 
    {
        $allPassing = null;
        $spacing = $this->text(str_repeat(" ", 15), 0, 35, 44);
        
        echo $spacing;
        echo $this->text("Begining Unit Test", 1, 32, 46);
        echo $spacing;
        echo "\n\n";
        
        foreach ($this->testFiles as $file) {
            $className = sprintf(
                    '%s\%s', 
                    $this->opts->namespace, 
                    basename($file, '.php')
                    );
            
            try {
                $phpUnitTestObj = new $className();
            } catch(\Error $e) {
                Logger::obj()->writeException($e, -1, true); 
                exit(4);
            }
            
            if ($phpUnitTestObj instanceOf $className) {
                $ref = new \ReflectionClass($className);

                $methods = $ref->getMethods();
                if (!empty($methods)) {
                    $methods = \array_filter($methods, 
                            function($method) { 
                                return strpos($method->getName(), 'test') === 0; 
                            } );
                } else {
                    $log = sprintf('%s no test methods found', $className);
                        Logger::obj()->write($log,0, true);
                }

                if (method_exists($phpUnitTestObj, 'setUp')) {
                    $phpUnitTestObj->setUp();
                }
                
                foreach ($methods as  $method) {
                    $methodName = $method->getName();
                    if ($phpUnitTestObj->{$methodName}() === true) {
                        $log = sprintf('%d.) %s::%s succeeded', ++$this->testCounts['passing'], $className, $methodName);
                        Logger::obj()->write($log,0, true);
                        
                        if (!isset($allPassing)) {
                            $allPassing = true;
                        }
                    } else {
                        $log = sprintf('%d.) %s::%s failed', ++$this->testCounts['passing'], $className, $methodName);
                        Logger::obj()->write($log, 0,true);
                        
                        if ($allPassing === true) {
                            $allPassing = false;
                        }
                    }
                }
            } else {
                $this->testCounts['failing']++;
                
                $log = sprintf('Test Class could not be created, %s', $className);
                Logger::obj()->write($log, -1, true);
                
                if ($allPassing === true) {
                    $allPassing = false;
                }
            }
        }
        
        $spacing = $spacing = $this->text(str_repeat(" ", 5), 0, 35, 47);
        echo "\n";
        echo $this->text($spacing, 0, 35, 44);
        $text = sprintf('Passing Tests %d', $this->testCounts['passing']);
        echo $this->text($text, 0, 35, 46);
        echo "\n";
        echo $this->text($spacing, 0, 35, 44);
        $text = sprintf('Failing Tests %d', $this->testCounts['failing']);
        echo $this->text($text, 0, 35, 46);
        echo "\n\n";
        
        return $allPassing ?? false;
    }
    
    public function __get($name) {
        return ($name === 'testCounts' ? $this->testCounts : false);
    }
}
