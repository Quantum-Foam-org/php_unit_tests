<?php

namespace lib\classes;

use common\logging\Logger as Logger;


class PHPUnitTest {
    private $testFiles = [];
    private $opts;
    
    public function __construct(validate\TestOpts $opts) 
    {
        $testFilepath = sprintf('%s/%s', $opts->path,'*Test.php');
        
        if (is_dir($opts->path) && !empty(($testFiles = glob($testFilepath)))){
            $this->testFiles = $testFiles;
        } else {
            $log = sprintf('Test Files not Found make sure that the directory exists and is readable.  DIR: %s', $opts->path);
            Logger::obj()->write($log);
        }
        
        $this->opts = $opts;
    }
    
    public function run(): void 
    {
        foreach ($this->testFiles as $file) {
            $className = sprintf(
                    '%s\%s', 
                    $this->opts->namespace, 
                    basename($file, '.php')
                    );
            
            $phpUnitTestObj = new $className();
            
            $ref = new \ReflectionClass($className);
            
            $methods = $ref->getMethods();
            if (!empty($methods)) {
                $methods = \array_filter($methods, 
                        function($method) { 
                            return strpos($method, 'Test'); 
                        } );
            }
            
            foreach ($methods as  $method) {
                $methodName = $method->getName();
                if ($phpUnitTestObj->{$methodName}() === true) {
                    $log = sprintf('%s::%s succeeded', $className, $methodName);
                    Logger::obj()->write($log,0, true);
                } else {
                    $log = sprintf('%s::%s failed', $className, $methodName);
                    Logger::obj()->write($log, 0,true);
                }
            }  
        }
    }
}
