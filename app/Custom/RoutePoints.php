<?php

namespace App\Custom;

class RoutePoints {
	
	public $origin, $destination, $time, $cost;
	public function __construct($origin, $destination, $time, $cost) {
		$this->origin = $origin;
		$this->destination = $destination;
		$this->time = $time;
		$this->cost = $cost;
	}
}