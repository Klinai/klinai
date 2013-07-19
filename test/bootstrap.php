<?php

defined('KLINAI_LIB_PATH') || define('KLINAI_LIB_PATH', __DIR__ . '../library/Klinai/');

function klinaiAutoload ($className) {
    if ( !preg_match('#^(?<major_ns>Klinai)\(?<className>(.*)$#', $className,$matches) ) {
        return false;
    }

    $classPath = str_replace('\\', DIRECTORY_SEPARATOR, $matches['className']);

    $filePath = KLINAI_LIB_PATH . $classPath;
    if ( !file_exists($filePath) ) {
        return false;
    }
    require_once $filePath;

    return class_exists($className);
}

spl_autoload_register(klinaiAutoload);