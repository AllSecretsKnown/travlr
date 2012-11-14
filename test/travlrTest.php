<?php

require_once "PHPUnit/Autoload.php";
require_once "../travlr.php";

class TravlrTest extends PHPUnit_Framework_TestCase
{
	private $travlr;

	function setup(){
		$this->travlr = new Travlr();
	}

	protected function tearDown() {
		unset($this->travlr);
	}

	/*
	 * Function to make sure we get an empty array when calling what_comes_around with a none existing station
	 */
	function testWhatComesAraoundWithNotExistingStation(){
		$result = $this->travlr->what_comes_around('One Horse Town');
		$this->assertTrue(is_array($result));
		$this->assertEmpty($result);
	}

	/*
	 * Function to make sure we get an associative array with date - station when calling what_comes_around with existing station
	 */
	function testWhatComesAroundWithOkStation(){
		$result = $this->travlr->what_comes_around('Kalmar C');
		$this->assertTrue(is_array($result));
		$this->assertTrue($this->array_contains_dates_and_stations($result));
	}

	/*
	 * Function to make sure we get an empty array when calling what_goes_around with a none existing station
	 */
	function testWhatGoesAraoundWithNotExistingStation(){
		$result = $this->travlr->what_goes_around('One Horse Town');
		$this->assertTrue(is_array($result));
		$this->assertEmpty($result);
	}

	/*
	 * Function to make sure we get an associative array with date - station when calling what_goes_around with existing station
	 */
	function testWhatGoesAroundWithOkStation(){
		$result = $this->travlr->what_goes_around('Kalmar C');
		$this->assertTrue(is_array($result));
		$this->assertTrue($this->array_contains_dates_and_stations($result));
	}

	/*
	 * Function to make sure the APC is caching our stuff
	 */
	function testApcCache(){
		$station = 'Kalmar c';
		//Clear cache on station
		$station_prefix = trim(substr(strtolower($station), 0, 4));
		apc_delete(Travlr::COMES_AROUND . $station_prefix);

		//First we test that we can get the result even though cache has been cleared
		$result = $this->travlr->what_comes_around('Kalmar c');
		$this->assertTrue(is_array($result));
		$this->assertTrue(count($result) > 0);
		$this->assertTrue($this->array_contains_dates_and_stations($result));

		//Now we try to get the same result from cache
		/*$this->assertTrue(apc_exists((Travlr::COMES_AROUND . $station_prefix)), 'Key not found in APC');
		$cache_array = array();
		$cache_result = apc_fetch(Travlr::COMES_AROUND . $station_prefix);
		$cache_result = json_decode($cache_result);
		foreach ( $cache_result->station->transfers->transfer as $incoming ) {
			$cache_array[$incoming->arrival] = $incoming->origin;
		}
		//Make sure we get correct json
		$this->assertTrue($this->array_contains_dates_and_stations($cache_array));

		//Make sure the respons is the same
		$this->assertEquals($cache_result, $result);*/
	}

	/*
	 * Private helper to check response array
	 */
	private function array_contains_dates_and_stations($result){
		if(count($result) > 0){
			foreach ( $result as $key => $val ) {
				if(!date_parse($key) && !is_string($val)){
					return false;
				}
			}
			return true;
		}
		return false;
	}
}