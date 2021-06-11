<?php

namespace PHPUnitTest\lib\classes\validate;

use cli\classes as cli;

class TestOpts extends cli\Flag {
	protected $path;
        protected $namespace;
	
	protected $config = [
		'path' => 
                    [
                        FILTER_SANITIZE_STRING, 
                        [
                            'flags' => 
                            FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH
                        ]
                    ],
                'namespace' =>
                    [
                        FILTER_SANITIZE_STRING,
                        [
                            'flags' =>
                            FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH
                        ]
                    ]
            ];
        
        public function __set($name, $value)
        {
            parent::__set($name, $value);
            
            if ($name === 'path') {
                $this->path = realpath($this->path);
            }
            
            return $value;
        }
}