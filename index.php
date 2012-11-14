<?php

require_once('travlr.php');
$station = 'Kalmar C';
$station_prefix = trim(substr(strtolower($station), 0, 4));
apc_delete(Travlr::COMES_AROUND . $station_prefix);

$my_travlr = new Travlr();
//$departures = $my_travlr->what_goes_around('Kalmar c');
$arrivals = $my_travlr->what_comes_around('Kalmar c');

//var_dump($departures);
//var_dump($arrivals);
$cache_result = apc_fetch(Travlr::COMES_AROUND . $station_prefix);
$cache_result = json_decode($cache_result);
foreach ( $cache_result->station->transfers->transfer as $incoming ) {
	$cache_array[$incoming->arrival] = $incoming->origin;
}
var_dump($cache_array);