<?php

namespace inventory;

/**
 * Interface for Inventory Class
 * 
 * @author eren
 */
interface iInventory {
    
    public function allocate( $product, $quantity );
    public function setHeader( $header );
    public function get();
    public function resetTracking();
}
