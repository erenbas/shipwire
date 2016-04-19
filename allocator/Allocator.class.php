<?php

namespace allocator;

use Exception;
use inventory\InventoryException;

/**
 * Description of Allocator
 * Class is responsable for allocating order
 * 
 * @author eren
 */
class Allocator implements iAllocator {
    
    private $lineSatisfied;
    
    protected $Validator;
    protected $Inventory;
    
    protected $progress = [];
    
    /*
     * Dependency injection
     * 
     * @param Object $Validator Validator object to validate order
     */
    public function injectValidator( $Validator ){
        
        $this->Validator = $Validator;
    }
     
    /*
     * Dependency injection
     * 
     * @param Object $Inventory Inventory object has the inventory
     */
    public function injectInventory( $Inventory ){
        
        $this->Inventory = $Inventory;
    }
    
    /*
     * Dependency injection
     * 
     * @param Object $Logger 
     */
    public function injectLogger( $Logger ){
        
        $this->Logger = $Logger;
    }
    
    /*
     * Processing orders
     * 
     * @param array $streams 3 dimensional array holds stream of orders
     * @return void
     */
    public function process( $streams ){
        
        $this->logStreams( $streams );
        
        $i = 0;
        foreach( $streams as $streamId => $stream ){
            
            $this->Validator->setStreamId( $streamId );
            $this->Inventory->resetTracking();
            
            $i++; $c = 0;
            foreach( $stream as $order ) {
                    
                    $c++;
                    //get inventory before processing order
                    $preInventory = $this->Inventory->get();

                    // keep track of the progress to show client
                    $this->progress[$i][$c] = [ 'streamId'=> $streamId, 'Order' => $order ];
                    $this->Logger->log( 'streamId => '.$streamId." Header => ".$order['Header'] );
                    $this->Logger->log( 'Pre Inventory => '.serialize($preInventory) );
                    
                    $this->Inventory->setHeader( $order['Header'] );
                    
                    $this->Validator->setHeader( $order['Header'] );
                    
                try {
                    
                    $this->Validator->validateStructure( $order );

                    $this->Validator->validateHeader( $order['Header'] );
                    
                    $this->Validator->validateLines( $order['Lines'] );
                    
                    $this->processLines( $order['Lines'] );
                
                } catch ( InventoryException $e ) {    
                    
                    $this->Logger->log( 'Status => Inventory Empty');
                    $this->Logger->close();
                    throw new Exception( $e->getMessage() );
                    
                } catch ( Exception $e ) {
                                        
                    $this->progress[$i][$c]['errors'][] = $e->getMessage();
                    $this->Logger->log('Error => '.$e->getMessage());
                }
                
                $this->progress[$i][$c]['inventory'] = [ 'pre' => $preInventory, 'post' => $this->Inventory->get() ];
                $this->progress[$i][$c]['status']    = $this->getOrderStatus( $this->progress[$i][$c] );
                
                $this->Logger->log( 'Post Inventory => '.serialize($this->Inventory->get()) );
                $this->Logger->log( 'Status => '.$this->getOrderStatus( $this->progress[$i][$c] ) );
            }
        }
        
        $this->Logger->close();
        //print the progress for the user
        print_r($this->progress);
    }
    
    /*
     * Order lines will be allocated here
     * 
     * @param array $lines 
     * 
     * @throws Exception, InventoryException
     * @return void
     */
    private function processLines( $lines ){
       
        foreach( $lines as $line ){
            
            $this->Logger->log( 'Product => ' . $line['Product'] . ' Quantity => '.$line['Quantity'] );
            
            $this->allocate( $line['Product'], $line['Quantity'] );
            
        }
        
    }
    
    /*
     * Adapter for the Inventory object, uses Inventory::allocate method
     * 
     * @param string $product
     * @param int $quantity
     * @throws Exception, InventoryException
     * @return void
     */
    private function allocate( $product, $quantity ) {
        
        $this->Inventory->allocate( $product, $quantity );
        
    }
    
    /*
     * Helper method for logger, formatting array for logging
     * 
     * @param array $streams
     * @return void 
     */
    private function logStreams( $streams ){
        
        $this->Logger->log( 'ALLOCATION STARTED' );
        
        foreach( $streams as $streamid => $orders ){
            
            $this->Logger->log( 'Stream: '.$streamid );
            
            foreach( $orders as $order ){
                
                $this->Logger->log( json_encode( $order ) );
        
            }
        }
    }
    
    /*
     * Setting the order status for logger and proccess, keeping tack of the sanity
     * 
     * @param array $order
     */
    private function getOrderStatus( $order ){
        
        if( isset($order['errors']) ) {
                    
            $status = 'Invalid Order';

        } else {
            
            $status = 'Success';
        }
        
        return $status;
    }
}
