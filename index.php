<?php
//Require travlr.php
require_once( 'travlr.php' );

//Instantiate
$my_travlr = new Travlr();

//Make a call
$travels = $my_travlr->get_arriving_trains( 'Kalmar' );

//Work with the result
foreach ( $travels as $travel ) {
	echo $travel->get_date_and_time() . ' ' . $travel->get_destination() . ' -> ' . $travel->get_origin() . '<br>';
}
