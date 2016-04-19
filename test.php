
<?php

include('autoload/Autoload.php');
spl_autoload_register(['Autoload', 'load']);

//Streams array
$streams[1] = [
                ['Header' => 1,'Lines' => [['Product'=>'A', 'Quantity' => 1],['Product'=>'C', 'Quantity' => 1]] ],
                ['Header' => 2,'Lines' => [['Product'=>'E', 'Quantity' => 5]] ],
                ['Header' => 3,'Lines' => [['Product'=>'D', 'Quantity' => 4]] ],
                ['Header' => 4,'Lines' => [['Product'=>'A', 'Quantity' => 1],['Product'=>'C', 'Quantity' => 1]] ],
                ['Header' => 5,'Lines' => [['Product'=>'B', 'Quantity' => 3]] ],
                ['Header' => 6,'Lines' => [['Product'=>'D', 'Quantity' => 4]] ]
            ];

//build allocator object with initial inventory 
$Allocator = allocator\AllocatorFactory::build( ['A' => 2, 
                                                 'B' => 3, 
                                                 'C' => 1, 
                                                 'D' => 0, 
                                                 'E' => 0 ] 
                                              );

//allocate
$Allocator->process( $streams );


