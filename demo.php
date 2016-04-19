<?php

include('autoload/Autoload.php');
spl_autoload_register(['Autoload', 'load']);


$DataSource = new source\DataSource();

//change values here to generate different test streams
$DataSource->setOrderCount( 5, 10 );
$DataSource->setInventoryRange( 'A', 'F' );
$DataSource->setDemandRange( 1, 6 );
$DataSource->setStreamCount( 2 );

//Randomly generate streams of orders to test
$orders = $DataSource->generateStreams();

//print_r( json_encode($orders) );

//build allocator object with initial inventory 
$Allocator = allocator\AllocatorFactory::build( ['A' => 150, 
                                                 'B' => 150, 
                                                 'C' => 100, 
                                                 'D' => 100, 
                                                 'E' => 200 ] 
                                              );

//allocate
$Allocator->process( $orders );