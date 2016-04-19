<?php

namespace validator;

/**
 *
 * @author eren
 */
interface iValidator {
    
     public function setStreamId( $id );
     public function setHeader( $id );
     public function validateHeader( $header );
     public function validateStructure( $order );
     public function validateLines( $lines );

}
