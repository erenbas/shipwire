<?php

namespace validator;

/**
 * Description of Validator
 *
 * @author eren
 */
class Validator extends AbstractValidator {
    
    //set rules in the concrete class
    protected $minDemand = 1;
    protected $maxDemand = 5;
    protected $rootMustHave = ['Header', 'Lines'];
    protected $lineMustHave = ['Product','Quantity'];
    protected $demandLabel = 'Quantity';
    protected $productLabel = 'Product';
 
}
