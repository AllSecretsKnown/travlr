<?php

require_once( 'interface/itravlr.php' );
require_once( 'objects/travel.php' );
require_once( 'objects/travel_wrapper.php' );

class Travlr implements iTravlr {


	//Prefixes used in APC cache
	CONST GOES_AROUND  = 'GOES_AROUND';
	CONST COMES_AROUND = 'COMES_AROUND';
	CONST STATIONS     = 'STATIONS';

	//Querys for stations, arrivals and departures
	CONST STATIONS_QUERY = 'stations.json';

	//Private members
	private $auth = 'tagtider:codemocracy';
	private $api_url = 'http://api.tagtider.net/v1/';

	//Will hold all available stations from api.tagtider.net
	private $stations;

	//Time to cache response from api.tagtider.net
	private $travlr_ttl;

	//Error array
	private $error_array;

	//Private wrapper
	private $wrapper;

	function __construct( $ttl = '' ) {
		if ( isset( $ttl ) && $ttl !== '' ) {
			$int_val = intval( $ttl );
			if ( $int_val > 0 ) {
				$this->travlr_ttl = $int_val;
			}

		} else {
			$this->travlr_ttl = 60 * 60;
		}

		$this->stations    = $this->_get_stations();
		$this->error_array = array();
		$this->wrapper     = new TravelWrapper();
	}

	/*
	 * Public function to get Info about all incoming traffic
	 * @param string - Station name
	 * @return Travel Wrapper object
	 */
	public function get_arriving_trains( $station ) {
		if ( ! empty( $station ) ) {
			$arrivals = $this->_process_request( $station, Travlr::COMES_AROUND );
		} else {
			return $this->return_error_message( 'Not a valid station' );
		}

		if ( count( $arrivals ) > 0 ) {

			if ( is_object( $arrivals ) && is_a( $arrivals, 'TravelWrapper' ) ) {
				return $this->wrapper;
			}

			foreach ( $arrivals->transfer as $incoming ) {
				$travel = new Travel( $incoming->arrival, $station, $incoming->origin );
				$this->wrapper->add_travels( $travel );
			}
		} elseif ( $arrivals === null ) {
			return $this->return_error_message( 'Cant find station' );
		}

		return $this->wrapper;
	}

	/*
	 * Public function to get info about all outgoing traffic
	 * @param string - Station name
	 * @return Travel Wrapper object
	 */
	public function get_departing_trains( $station ) {
		if ( ! empty( $station ) ) {
			$departures = $this->_process_request( $station, Travlr::GOES_AROUND );
		} else {
			return $this->return_error_message( 'Not a valid station' );
		}

		if ( count( $departures ) > 0 ) {
			if ( is_object( $departures ) && is_a( $departures, 'TravelWrapper' ) ) {
				return $this->wrapper;
			}
			foreach ( $departures->transfer as $outgoing ) {
				$travel = new Travel( $outgoing->departure, $outgoing->destination, $station );
				$this->wrapper->add_travels( $travel );
			}
		} elseif ( $departures === null ) {
			return $this->return_error_message( 'Cant find station' );
		}

		return $this->wrapper;
	}

	/*
	 * Private function to return error array
	 * @param -
	 * @return Travel Wrapper object
	 */
	private function return_error_message( $message ) {
		$this->wrapper->set_error( $message );
		return $this->wrapper;
	}

	/*
	 * Private function to handle request
	 * @params string Station, string Constant defined in class, describing if we are coming or going
	 * @return array with objects
	 */
	private function _process_request( $station, $coming_or_going ) {
		//Make sure were doing a valid search, help the user a bit
		$last_chars = substr( $station, count( $station ) - 3, 2 );
		if ( strtolower( $last_chars ) !== ' c' ) {
			$station .= ' C';
		}

		$station_prefix = trim( substr( strtolower( $station ), 0, 4 ) );
		$id             = $this->_get_station_id( $station );
		if ( $id == false ) {
			return null;
		}

		$object      = array();
		$json_result = false;

		switch ( $coming_or_going ) {
			case Travlr::GOES_AROUND:
				$query       = 'stations/' . $id . '/transfers/departures.json';
				$json_result = apc_fetch( Travlr::GOES_AROUND . $station_prefix );
				break;
			case Travlr::COMES_AROUND:
				$query       = 'stations/' . $id . '/transfers/arrivals.json';
				$json_result = apc_fetch( Travlr::COMES_AROUND . $station_prefix );
				break;
		}
		if ( $json_result == false ) {
			$json_result = $this->_setup_and_execute_curl( $query );
		}

		if ( $json_result !== null && $json_result !== '' ) {
			if ( $coming_or_going == Travlr::COMES_AROUND ) {
				apc_store( Travlr::COMES_AROUND . $station_prefix, $json_result, $this->travlr_ttl );
			} else {
				apc_store( Travlr::GOES_AROUND . $station_prefix, $json_result, $this->travlr_ttl );
			}
			$object = json_decode( $json_result );
		} elseif ( $json_result === null OR $json_result === '' ) {
			return $this->return_error_message( 'Could not connect to remote API' );
		}
		return $object->station->transfers;
	}

	/*
	 * Private Function to get all stations available
	 * @param -
	 * @return stdClass with id
	 */
	private function _get_stations() {
		$stations = array();
		//Check if we have stations-json in cache
		if ( ! $stations_json = apc_fetch( Travlr::STATIONS ) ) {
			$stations_json = $this->_setup_and_execute_curl( Travlr::STATIONS_QUERY );
		}

		if ( $stations_json !== null ) {
			apc_store( Travlr::STATIONS, $stations_json, $this->travlr_ttl * 24 );
			$stations = json_decode( $stations_json );
		} else {
			return $stations;
		}
		return $stations->stations->station;
	}

	/*
	 * Private Function to get ID for requested Station
	 * @param string - station name
	 * @return station ID
	 */
	private function _get_station_id( $station ) {
		foreach ( $this->stations as $known_station ) {
			if ( strtolower( $known_station->name ) == strtolower( $station ) ) {
				return $known_station->id;
			}
		}
		return null;
	}

	/*
	 * Private function to execute curl request to api.tagtider.net
	 * @param string query
	 * @return response json/xml
	 */
	private function _setup_and_execute_curl( $query ) {

		$full_url = $this->api_url;

		if ( ! isset( $query ) ) {
			return null;
		} else {
			$full_url .= $query;
		}

		try {
			$ch = curl_init();
			curl_setopt( $ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY );
			curl_setopt( $ch, CURLOPT_USERPWD, $this->auth );
			curl_setopt( $ch, CURLOPT_HTTPHEADER, array( 'Accept' => 'application/json' ) );
			curl_setopt( $ch, CURLOPT_URL, $full_url );
			curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );

			$response = curl_exec( $ch );
			curl_getinfo( $ch );
			$http_status = curl_getinfo( $ch, CURLINFO_HTTP_CODE );
			curl_close( $ch );
		} catch ( Exception $e ) {
			exit( 'Could not connect to remote API : ' . $e );
		}

		if ( $http_status == "200" ) {
			return $response;
		} else {
			return null;
		}
	}
}