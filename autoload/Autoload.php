<?php


/**
 * Description of Autoload
 *
 * @author eren
 */
class Autoload {
    
    public static function load( $className ) { 
    
        $file = str_replace('\\', '/', $className).'.class.php';

        if ( file_exists($file) ) {

            require_once($file);
            return true;
        }

        return false;
    }
}
