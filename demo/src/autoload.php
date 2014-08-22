<?php

spl_autoload_register(function($className) {
    $className = ltrim($className, '\\');
    $fileName  = '';
    $namespace = '';
    if ($lastNsPos = strrpos($className, '\\')) {
        $namespace = substr($className, 0, $lastNsPos);
        $className = substr($className, $lastNsPos + 1);
        $fileName  = str_replace('\\', DIRECTORY_SEPARATOR, $namespace) . DIRECTORY_SEPARATOR;
    }
    $fileName = dirname(__FILE__)
                .DIRECTORY_SEPARATOR
                .$fileName
                .str_replace('_', DIRECTORY_SEPARATOR, $className) 
                .'.php';
    
    if (!file_exists($fileName)) return false;
    require_once $fileName;
});