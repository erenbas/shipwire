<?php

namespace allocator;

use validator\Validator;
use inventory\Inventory;
use logger\Logger;

/**
 * Description of AllocatorFactory
 * Factory method to get an instance of Allocator object
 * @author eren
 */
class AllocatorFactory {
    
    public static function build( $inventory ){
        
        $Allocator = new Allocator;
        $Allocator->injectInventory( new Inventory( $inventory ) );
        $Allocator->injectValidator( new Validator( $inventory ) );
        $Allocator->injectLogger( new Logger( 'allocator' ) );
        
        return $Allocator;
    }
}
