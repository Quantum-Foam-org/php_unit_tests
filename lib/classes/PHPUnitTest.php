<?php

namespace lib\classes;

use common\logging\Logger as Logger;


class PHPUnitTest {
    private $testFiles = [];
    private $opts;
    
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
            
        foreach ($this->testFiles as $file) {
            $className = sprintf(
                    '%s\%s', 
                    $this->opts->namespace, 
                    basename($file, '.php')
                    );
            
            $phpUnitTestObj = new $className();
                
            if ($phpUnitTestObj instanceOf $className) {
                $ref = new \ReflectionClass($className);

                $methods = $ref->getMethods();
                if (!empty($methods)) {
                    $methods = \array_filter($methods, 
                            function($method) { 
                                return strpos($method, 'Test'); 
                            } );
                } else {
                    $log = sprintf('%s no test methods found', $className);
                        Logger::obj()->write($log,0, true);
                }

                foreach ($methods as  $method) {
                    $methodName = $method->getName();
                    if ($phpUnitTestObj->{$methodName}() === true) {
                        $log = sprintf('%s::%s succeeded', $className, $methodName);
                        Logger::obj()->write($log,0, true);
                        
                        if (!isset($allPassing)) {
                            $allPassing = true;
                        }
                    } else {
                        $log = sprintf('%s::%s failed', $className, $methodName);
                        Logger::obj()->write($log, 0,true);
                        unset($allPassing);
                    }
                }
            } else {
                $log = sprintf('Test Class could not be created, %s', $className);
                Logger::obj()->write($log, -1, true);
                unset($allPassing);
            }
        }
        
        return $allPassing ?? false;
    }
}
