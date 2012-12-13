<?php

interface iTravlr{

	/*
	 * Function to get all Arriving trains to statin X
	 * @scope public
	 * @param string - Station name
	 * @return - TrvaleWrapper object, Traversable
	 */
	public function what_comes_around($station);

	/*
	 * Function to get all departing trains from station X
	 * @scope public
	 * @param string - station name
	 * @return - TrvaleWrapper object, Traversable
	 */
	public function what_goes_around($station);

}