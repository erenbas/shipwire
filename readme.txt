
Author: Eren Bas
Email: erenbash@gmail.com

###########
## INTRO ##
###########

A single-threaded application to allocate stream of orders.

Note: Ideally data source should be on a database where row locking mechanism 
(storage engine like innoDB) only allows one transaction at a time and multiple threads can allocate orders.

##################
## REQUIREMENTS ##
##################   

- PHP 5.6 OR greater

################
## HOW TO RUN ##
################

Run the following command in the terminal:

--> php demo.php

The command above will generate streams of orders with random values and do allocation.
It will print the progress to the screen as in array format and also will log it into logs folder 
in the main project with current date. Ex: 20160419_allocator.log

#############
## TESTING ##  
#############

Due to time restrictions, the application is not heavily tested. 
It requires unit testing, SERIOUSLY. 

From terminal:

--> php test.php

It will produce expected output.

The command above will test the application with the values below:

----------------- TEST VALUES ---------------------------------
For instance:
  If the initial conditions are:
  A x 2
  B x 3
  C x 1
  D x 0
  E x 0

  And the input is:
  {"Header": 1, "Lines": {"Product": "A", "Quantity": "1"}{"Product": "C", "Quantity": "1"}}
  {"Header": 2, "Lines": {"Product": "E", "Quantity": "5"}}
  {"Header": 3, "Lines": {"Product": "D", "Quantity": "4"}}
  {"Header": 4, "Lines": {"Product": "A", "Quantity": "1"}{"Product": "C", "Quantity": "1"}}
  {"Header": 5, "Lines": {"Product": "B", "Quantity": "3"}}
  {"Header": 6, "Lines": {"Product": "D", "Quantity": "4"}}
 
  The output should be (in whatever format you choose):
  1: 1,0,1,0,0::1,0,1,0,0::0,0,0,0,0
  2: 0,0,0,0,5::0,0,0,0,0::0,0,0,0,5
  3: 0,0,0,4,0::0,0,0,0,0::0,0,0,4,0
  4: 1,0,1,0,0::1,0,0,0,0::0,0,1,0,0
  5: 0,3,0,0,0::0,3,0,0,0::0,0,0,0,0
---------------------------------------------------------------

######################
## HOW TO READ LOGS ##
######################

streamId: id of the stream
Header: id of the header
Pre Inventory: inventory before allocation 
Post Inventory: inventory after allocation

Below is the successful allocation log. Pre and Post Inventory is serialized arrays.

----------------------------------------------------------------------------
[16:39:25] -> streamId => 1 Header => 5715702d0e0c8
[16:39:25] -> Pre Inventory => a:5:{s:1:"A";i:43;s:1:"B";i:50;s:1:"C";i:49;s:1:"D";i:47;s:1:"E";i:7;}
[16:39:25] -> Product => A Quantity => 4
[16:39:25] -> Product => B Quantity => 5
[16:39:25] -> Product => D Quantity => 3
[16:39:25] -> Product => E Quantity => 5
[16:39:25] -> Post Inventory => a:5:{s:1:"A";i:39;s:1:"B";i:45;s:1:"C";i:49;s:1:"D";i:44;s:1:"E";i:2;}
[16:39:25] -> Status => Success
----------------------------------------------------------------------------

Below is an example of invalid order:

----------------------------------------------------------------------------
[16:39:25] -> streamId => 0 Header => 5715702d0e07f
[16:39:25] -> Pre Inventory => a:5:{s:1:"A";i:43;s:1:"B";i:50;s:1:"C";i:49;s:1:"D";i:47;s:1:"E";i:11;}
[16:39:25] -> Product => E Quantity => 3
[16:39:25] -> Product => G Quantity => 1
[16:39:25] -> Error => Invalid Order: Product G not found
[16:39:25] -> Post Inventory => a:5:{s:1:"A";i:43;s:1:"B";i:50;s:1:"C";i:49;s:1:"D";i:47;s:1:"E";i:8;}
[16:39:25] -> Status => Invalid Order
----------------------------------------------------------------------------


#############################
## APPLICATION DESCRIPTION ##
#############################


------------------------------------------------------

Initial conditions:
  Initially, the system contains inventory of
  A x 150
  B x 150
  C x 100
  D x 100
  E x 200

  Initially, the system contains no orders

Data source:
  There should be a data source capable of generating one or more streams of orders.
  An order consists of a unique identifier (per stream) we will call the "header", and a demand for between zero and five units each of A,B,C,D, and E, except that there must be at least one unit demanded.
  A valid order (in whatever format you choose): {"Header": 1, "Lines": {"Product": "A", "Quantity": "1"},{"Product": "C", "Quantity": "4"}}
  An invalid order: {"Header": 1, "Lines": {"Product": "B", "Quantity": "0"}}
  Another invalid order: {"Header": 1, "Lines": {"Product": "D", "Quantity": "6"}}

Inventory allocator:
  There should be an inventory allocator which allocates inventory to the inbound data according to the following rules:
  1) Inbound orders to the allocator should be individually identifyable (ie two streams may generate orders with an identical header, but these orders should be identifyable from their streams)
  2) Inventory should be allocated on a first come, first served basis; once allocated, inventory is not available to any other order.
  3) Inventory should never drop below 0.
  4) If a line cannot be satisfied, it should not be allocated.  Rather, it should be  backordered (but other lines on the same order may still be satisfied).
  5) When all inventory is zero, the system should halt and produce output listing, in the order received by the system, the header of each order, the quantity on each line, the quantity allocated to each line, and the quantity backordered for each line.
  For instance:
  If the initial conditions are:
  A x 2
  B x 3
  C x 1
  D x 0
  E x 0

  And the input is:
  {"Header": 1, "Lines": {"Product": "A", "Quantity": "1"}{"Product": "C", "Quantity": "1"}}
  {"Header": 2, "Lines": {"Product": "E", "Quantity": "5"}}
  {"Header": 3, "Lines": {"Product": "D", "Quantity": "4"}}
  {"Header": 4, "Lines": {"Product": "A", "Quantity": "1"}{"Product": "C", "Quantity": "1"}}
  {"Header": 5, "Lines": {"Product": "B", "Quantity": "3"}}
  {"Header": 6, "Lines": {"Product": "D", "Quantity": "4"}}
 
  The output should be (in whatever format you choose):
  1: 1,0,1,0,0::1,0,1,0,0::0,0,0,0,0
  2: 0,0,0,0,5::0,0,0,0,0::0,0,0,0,5
  3: 0,0,0,4,0::0,0,0,0,0::0,0,0,4,0
  4: 1,0,1,0,0::1,0,0,0,0::0,0,1,0,0
  5: 0,3,0,0,0::0,3,0,0,0::0,0,0,0,0
