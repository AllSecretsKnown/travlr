<?php

interface iTravlr{

	/*
	 * Function to get all Arriving trains to statin X
	 * @scope public
	 * @param string - Station name
	 * @return - TrvaleWrapper object, Traversable
	 */
	public function get_arriving_trains($station);

	/*
	 * Function to get all departing trains from station X
	 * @scope public
	 * @param string - station name
	 * @return - TrvaleWrapper object, Traversable
	 */
	public function get_departing_trains($station);

}