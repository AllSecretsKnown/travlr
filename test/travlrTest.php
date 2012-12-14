<?php

require_once "PHPUnit/Autoload.php";
require_once "../travlr.php";

class TravlrTest extends PHPUnit_Framework_TestCase {
	private $travlr;

	function setup() {
		$this->travlr = new Travlr();
	}

	protected function tearDown() {
		unset( $this->travlr );
	}

	/*
	 * Function to make sure we get an empty array when calling what_comes_around with a none existing station
	 */
	function testGetDepartingTrainsNotExistingStation() {
		$result = $this->travlr->get_departing_trains( 'Not a real city' );
		$this->assertTrue( is_object( $result ) );
		$this->assertEquals( $result->get_error_message(), 'Cant find station' );
	}

	/*
	 * Function to make sure we get an associative array with date - station when calling what_comes_around with existing station
	 */
	function testGetDepartingTrainsOkStation() {
		$result = $this->travlr->get_departing_trains( 'Kalmar C' );
		$this->assertTrue( is_object( $result ) );
		foreach ( $result as $travel ) {
			$this->assertTrue( $this->array_contains_dates_and_stations( $travel->get_date_and_time(), $travel->get_destination() ) );
		}
	}

	/*
	 * Function to make sure we get an empty array when calling what_goes_around with a none existing station
	 */
	function testGetArrivingTrainsWithNotExistingStation() {
		$result = $this->travlr->get_arriving_trains( 'Not a real city' );
		$this->assertTrue( is_object( $result ) );
		$this->assertEquals( $result->get_error_message(), 'Cant find station' );
	}

	/*
	 * Function to make sure we get an associative array with date - station when calling what_goes_around with existing station
	 */
	function testGetArrivingTrainsWithOkStation() {
		$result = $this->travlr->get_arriving_trains( 'Kalmar C' );
		$this->assertTrue( is_object( $result ) );
		foreach ( $result as $travel ) {
			$this->assertTrue( $this->array_contains_dates_and_stations( $travel->get_date_and_time(), $travel->get_destination() ) );
		}
	}

	/*
	 * Function to make sure the APC is caching our stuff
	 */
	function testApcCache() {
		$station = 'Kalmar c';
		//Clear cache on station
		$station_prefix = trim( substr( strtolower( $station ), 0, 4 ) );
		apc_delete( Travlr::COMES_AROUND . $station_prefix );

		//First we test that we can get the result even though cache has been cleared
		$result = $this->travlr->get_arriving_trains( 'Kalmar c' );
		$this->assertTrue( is_object( $result ) );
		foreach ( $result as $travel ) {
			$this->assertTrue( $this->array_contains_dates_and_stations( $travel->get_date_and_time(), $travel->get_destination() ) );
		}

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
	private function array_contains_dates_and_stations( $date, $station ) {

		if ( ! date_parse( $date ) && ! is_string( $station ) ) {
			return false;
		}

		return true;

	}
}