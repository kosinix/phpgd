<?php
require_once 'src/autoloader.php';

define('TEST_DIR', __DIR__.'/');

function phpGdTestDeleteTmp(){
    $dir = TEST_DIR.'tmp/';
    foreach( scandir( $dir ) as $file) {
        if ( '.' === $file || '..' === $file ) continue;
        if ( is_dir("$dir/$file") ) {
            phpGdTestRmdirRecursive("$dir/$file");
        } else {
            unlink("$dir/$file");
        }
    }
}

function phpGdTestRmdirRecursive( $dir ) {
    foreach( scandir( $dir ) as $file) {
        if ( '.' === $file || '..' === $file ) continue;
        if ( is_dir("$dir/$file") ) {
            phpGdTestRmdirRecursive("$dir/$file");
        } else {
            unlink("$dir/$file");
        }
    }
    return rmdir($dir);
}