<?php

function benchmark_autoload($class) {

    $class_directories = array(
        './',
        './Observer/',
        './Exception/',
    );

    for ($i = count($class_directories); $i--;) {
        $filename = dirname(__FILE__) . '/' . $class_directories[$i] . $class . '.php';
        if (file_exists($filename)) {
            require_once($filename);

            return true;
        }
    }

    return false;
}

spl_autoload_register('benchmark_autoload');