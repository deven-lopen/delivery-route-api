<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RoutePoint extends Model
{
    public $origin;
	public $destination;
	public $time;
	public $cost;
	
	public function __construct($origin, $destination, $time, $cost) {
		$this->origin = $origin;
		$this->destination = $destination;
		$this->time = $time;
		$this->cost = $cost;
	}
}
