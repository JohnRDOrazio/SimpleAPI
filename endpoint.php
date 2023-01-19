<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if( !class_exists("\Composer\Autoload\ClassLoader") ) {
    spl_autoload_register(function ($class) {

        // project-specific namespace prefix
        $prefix = 'JohnRDOrazio\\SimpleAPI\\';

        // base directory for the namespace prefix
        $baseDir = __DIR__. DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR;

        // does the class use the namespace prefix?
        $len = strlen($prefix);
        if (strncmp($prefix, $class, $len) !== 0) {
            // no, move to the next registered autoloader
            return;
        }

        // get the relative class name
        $relativeClass = substr($class, $len);

        // replace the namespace prefix with the base directory, replace namespace
        // separators with directory separators in the relative class name, append
        // with .php
        $file = $baseDir.str_replace('\\', DIRECTORY_SEPARATOR, $relativeClass).'.php';

        // if the file exists, require it
        if (file_exists($file)) {
            require $file;
        }
    });

}

include_once( "SampleAPI.php" );

$SampleAPI = new SampleAPI();
$SampleAPI->Init();
