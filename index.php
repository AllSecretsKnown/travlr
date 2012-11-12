<?php

require_once('travlr.php');

$my_travlr = new Travlr();
$departures = $my_travlr->what_goes_around('Kalmar c');
$arrivals = $my_travlr->what_comes_around('kalmar c');

//var_dump($departures);
var_dump($arrivals);