<?php

namespace lib\classes\validate;

use cli\classes as cli;

class TestOpt extends cli\Flag {
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
                'namepsace' =>
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
                $value = realpath($name);
            }
            
            return $value;
        }
}