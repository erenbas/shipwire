<?php

namespace validator;

use Exception;
/**
 * Description of AbstractValidator
 * Abstract class to validate order, please set the rules in concrete class
 * @author eren
 */
abstract class AbstractValidator implements iValidator {
   
    //concrete class will define the rules
    protected $minDemand;
    protected $maxDemand;
    protected $rootMustHave = [];
    protected $lineMustHave = [];
    protected $demandLabel;
    protected $productLabel;
    
    //properties
    protected $headerCollection = [[]];
    protected $streamId;
    protected $lineId;
    protected $header;
    protected $inventory;
    
    /*
     * @param array $inventory initial inventory 
     */
    
    public function __construct( $inventory ){
        
        $this->inventory = $inventory;
    }
    
    public function setStreamId( $id ){
        
        $this->streamId = $id;
    }
    
    public function setHeader( $id ){
        
        $this->header = $id;
    }
       
    /*
     * Validating header, check for duplicate header in the stream
     * 
     * @throws Exception
     * @return void
     */
    public function validateHeader($header ){
        
        //two streams may generate orders with an identical header, but these orders should be identifyable from their streams)
        if(  isset($this->headerCollection[ $this->streamId ]) && 
                  array_search( $header, $this->headerCollection[ $this->streamId ] ) !== false ){
            
            throw new Exception('Invalid Header: '.$header.' is already exist');
        }
        
        //new header for the stream put into basket for varification
        $this->headerCollection[ $this->streamId ][] = $header;
    }
    
    /*
     * Validating structure of the order
     * 
     * @throws Exception
     * @return void
     */
    public function validateStructure( $order ){
        
        foreach( $this->rootMustHave as $rule ){
            
            if( !isset($order[$rule]) ) {
                
                throw new Exception('Invalid Format: '.$rule.' not found');
                
            } 
        }
    }
    
    /*
     * Validating structure and demand for the lines
     * 
     * @throws Exception
     * @return void
     */
    public function validateLines( $lines ){
         
        foreach( $lines as $line ){
            
            $this->validateLine( $line );
            $this->validateProduct( $line[ $this->productLabel ] );
            $this->validateDemand( $line[ $this->demandLabel ] );
            
        } 
    }
    
    /*
     * Validating line structure
     * 
     * @throws Exception
     * @return void
     */
    private function validateLine( $line ){
        
        foreach( $this->lineMustHave as $rule ){
            
            if( !isset($line[$rule]) ) {
                
                throw new Exception('Invalid Format: '.$rule.' not found');
            }
        }
    }
    
    /*
     * Validating demand
     * 
     * @throws Exception
     * @return void
     */
    private function validateDemand( $demand ){
        
        if( (int)$demand < $this->minDemand || (int)$demand > $this->maxDemand ){
            
            throw new Exception('Invalid Demand: '.$demand );
        }
    }
    
    private function validateProduct( $product ){
        
          //check if inventory has this product
         if( !isset( $this->inventory[ $product ] ) ){
             
             throw new Exception('Invalid Order: Product '.$product.' not found');
         }
        
    }
}
