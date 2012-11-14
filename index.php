<?php
//Require travlr.php
require_once('travlr.php');

//Instantiate
$my_travlr = new Travlr();

//Make a call
//$departures = $my_travlr->what_goes_around('Kalmar c');
$arrivals = $my_travlr->what_comes_around('Kalmar c');

//Work with the result
//var_dump($departures);
var_dump($arrivals);