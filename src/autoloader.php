<?php
spl_autoload_register( 'phpGdAutoloader' ); // Register autoloader
function phpGdAutoloader( $className ) {
    if ( 0 === strpos( $className, 'PhpGd' ) ) { // Position 0
        $classeDir = __DIR__ . DIRECTORY_SEPARATOR ;
        $classFile = str_replace( '\\', DIRECTORY_SEPARATOR, $className ) . '.php';
        require_once $classeDir . $classFile;
    }
}