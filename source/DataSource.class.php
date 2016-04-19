<?php

namespace source;

/**
 * Description of DataSource
 * data source capable of generating one or more streams of orders, 
 * An order consists of a unique identifier (per stream) we will call the "header", 
 * and a demand for between zero and five units each of A,B,C,D, and E, 
 * except that there must be at least one unit demanded.
 * 
 * @author eren
 */
class DataSource {
    
    private $numOrders = 10;
    private $orders = [];
    private $inventoryRange = ['A','B','C','D','E','F','G'];
    private $minDemand = 0;
    private $maxDemand = 8;
    private $maxStream = 3;
    
    public function setInventoryRange( $min, $max ) {
        
        $this->inventoryRange = range( $min, $max );
    }
    
    public function setOrderCount( $min, $max ){
        
        $this->numOrders = rand( $min, $max );
    }
    
    public function setDemandRange( $min, $max ){
        
        $this->minDemand = $min;
        $this->maxDemand = $max;
    }
    
    public function setStreamCount( $count ){
        
        $this->maxStream = $count;
    }
    
    /*
     * generates random streams
     * 
     * @return array $orders
     */
    
    public function generateStreams(){
        
       for( $s = 0; $s < $this->maxStream; $s++ ){ 
           
           for( $i = 0; $i < $this->numOrders; $i++ ){

               $this->orders[ $s ][ $i ] = [ 'Header' => uniqid(), 'Lines' => $this->generateLines() ];
           }
       }
       
        return $this->orders;
    }
    
    /*
     * Generate order lines product => quantity
     * 
     * $return array $lines
     */
    
    private function generateLines(){
        
        $lineCount = rand( 1, sizeof( $this->inventoryRange ) );
        
        $i = 0;
        $lines = [];
        $inventoryRange = $this->inventoryRange;
        
        while( true ){
            $i++;
            
            $key = array_rand( $inventoryRange );
            
            $lines[] = [ 
                      'Product'  => $this->inventoryRange[ $key ], 
                      'Quantity' => rand( $this->minDemand, $this->maxDemand )
                     ];
            
            unset( $inventoryRange[ $key ] );
            
            if( $i >= $lineCount ){ break;  }
        }
        
        return $lines;
        
    }
}
