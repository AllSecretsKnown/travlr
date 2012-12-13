<?php

class Travel {

	private $date;
	private $destination;
	private $origin;

	public function __construct( $date, $destination, $origin ) {
		$this->date        = $date;
		$this->destination = $destination;
		$this->origin      = $origin;
	}

	public function get_date_and_time() {
		return $this->date;
	}

	public function get_destination() {
		return $this->destination;
	}

	public function get_origin() {
		return $this->origin;
	}
}