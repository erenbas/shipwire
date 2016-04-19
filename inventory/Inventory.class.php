<?php

namespace inventory;

/**
 * Description of Invertory
 * 
 * 1) Inbound orders to the allocator should be individually identifyable (ie two streams may generate orders with an identical header, but these orders should be identifyable from their streams)
 * 2) Inventory should be allocated on a first come, first served basis; once allocated, inventory is not available to any other order.
 * 3) Inventory should never drop below 0.
 * 4) If a line cannot be satisfied, it should not be allocated.  Rather, it should be  backordered (but other lines on the same order may still be satisfied).
 * 5) When all inventory is zero, the system should halt and produce output listing, in the order received by the system, the header of each order, the quantity on each line, the quantity allocated to each line, and the quantity backordered for each line.
 *
 * * @author eren
 */

class Inventory implements iInventory {
  
     private $stock = [];
     
     protected $header;
     protected $tracking = [];
     
     /*
      * Constructor expects product list to initialize inventory
      * 
      * @param array $inventory
      */
     public function __construct( array $inventory ){
         
         $this->stock = $inventory;
     }
      
     /*
      * Header setter, also initialize the tracking per order
      * 
      * @param string $header
      */
     public function setHeader( $header ){
         
         $this->header = $header;
         
         $this->initializeTracking();
     }
     
     /*
      * Getter for inventory
      * 
      * @return array 
      */
     public function get(){
         
         return  $this->stock;
     }
     
     /*
      * @return void
      */
     public function resetTracking(){
         
         $this->tracking = array();
     }
     
     /*
      * Main allocator method to allocate order
      * 
      * @param string $product 
      * @param int $quantity
      * @throws Exception
      * @return boolean
      */
         
     public function allocate( $product, $quantity ){
         
         $this->checkInventory();
                
         //calculate remaning quantity after allocation
         $remaining = (int)$this->stock[ $product ] - (int)$quantity;
         
         $this->track( 'requested', $product, $quantity );
         
         if( $remaining >= 0 ){
             
             //set the new quantity after allocation
             $this->stock[ $product ] = $remaining;
             
             $this->track( 'allocated', $product, $quantity );
             
             return true;
         } 
         
         /*
          * Ideally Backorder object places back-order here
          */
         $backOrderQuantity = $this->stock[ $product ] + abs( $remaining );
         
         $this->track( 'backordered', $product, $quantity );
         
         return false;
           
     }
     
     /*
      * Initilaizing the tracking array to keep track of requested, allocated and backordered
      * 
      * @return void
      */
     private function initializeTracking(){
         
         $trackingLabels = [ 'requested' ,'allocated' ,'backordered' ];
         
         //populate tracking array
         foreach( $trackingLabels as $value ) {
             
             $tracking[ $value ] = array_map( function(){ 
                                                        return 0; 
                                                      }, $this->stock
                                          );
         }
         
         $this->tracking[ $this->header ] = $tracking;
     }
     
     /*
      * Checking inventory to see if it is completely empty
      * 
      * @throws InventoryException
      * @return void
      */
     private function checkInventory(){
         
         //check if all inventory items equal to zero
         if ( empty( array_filter( $this->stock ) ) ){
             
             //remove last tracking not desired
             unset( $this->tracking[ $this->header ] );
             
             throw new InventoryException( $this->formatTracking(), 99 );
         }
     }
     
     /*
      * Keeping track of the orders placed by allocator
      * 
      * @return void
      */
     private function track( $type, $product, $quantity ){
         
         //populate tracking array
         $this->tracking[ $this->header ][ $type ][ $product ] += $quantity; 
     }
     
     /*
      * formating tracking as desired
      *  1: 1,0,1,0,0::1,0,1,0,0::0,0,0,0,0
      * 
      * @return string 
      */
     private function formatTracking(){
         
         $tracking = "\n";
         
         foreach( $this->tracking as $header => $order ){
             
             $track = $header . ': ';
              
             foreach( $order as $products ){
                     
                 $track .= implode(',', $products) . '::';
                 
             }
             
             $tracking .= rtrim($track,'::')."\n";
         }
         
         return $tracking;
     }
}
