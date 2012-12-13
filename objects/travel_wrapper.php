<?php

class TravelWrapper implements Iterator{

	private $position;

	private $travels;
	private $error;

	public function __construct() {
		$this->travels = array();
		$this->position = 0;
	}

	public function add_travels( Travel $travel ) {
		array_push( $this->travels, $travel );
	}

	public function set_error( $error ) {
		$this->error = $error;
	}

	public function get_travels() {
		return $this->travels;
	}

	public function get_error_message() {
		return $this->error;
	}

	/*
	|--------------------------------------------------------------------------
	| Iterator methods
	|--------------------------------------------------------------------------
	|
	| Travel Wrapper implements Iterator - interface
	| Interface for external iterators or objects that can be iterated themselves internally.
	| Meaning it is traversable with eg. a foreach loop
	|
	|
	*/

	function rewind() {
		$this->position = 0;
	}

	function current() {
		return $this->travels[$this->position];
	}

	function key() {
		return $this->position;
	}

	function next() {
		++$this->position;
	}

	function valid() {
		return isset($this->travels[$this->position]);
	}
}