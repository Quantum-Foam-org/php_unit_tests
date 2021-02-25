<?php

namespace lib\classes;

use common\logging\Logger as Logger;


class PHPUnitTest {
    private $testFiles = [];
    
    public function __construct(validate\TestOpts $opts) 
    {
        $testFilepath = sprintf('%s/%s', $opts->path,'*Test.php');
        
        if (is_dir($opts->path) && !empty(($testFiles = glob($opts->path)))){
            $this->testFiles = $testFilepath;
        } else {
            $log = sprintf('Test Files not Found make sure that the directory exists and is readable.  DIR: %s', $opts->path);
            Logger::obj()->write($log);
        }
    }
    
    public function run(): void 
    {
        foreach ($this->testFiles as $file) {
            $className = basename($file, '.php');
            
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
                if ($phpUnitTestObj->$method() === true) {
                    $log = sprintf('%s::%s succeeded', $className, $method);
                    Logger::obj()->write($log);
                } else {
                    $log = sprintf('%s::%s failed', $className, $method);
                    Logger::obj()->write($log);
                }
            }  
        }
    }
}
