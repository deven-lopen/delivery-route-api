<?php

namespace App\Custom;

use App\Custom\RoutePoints;

class GraphRoutes {
	
	public $allRoutes, $foundRoute, $shortestRoute = array();

	public function findRoutes($origin, $destination) {

        $queuePoints = array();
        $currentRoute = array();

        $queue = 0;
        $possible = 1;

        $nextRoute = $this->allRoutes[$origin];
        while ($possible == 1 && $nextRoute) {

            if (count($currentRoute) < 2) {
                foreach ($nextRoute as $key => $route) {
                    if ($key != 0) {
                        $queuePoints[$queue][] = $route;
                        $queue++;
                    } else {
                        if (!$currentRoute) {
                            $currentRoute[] = $route;
                        }
                    }
                }
            }

            $endCurrent = end($currentRoute);
            if (isset($this->allRoutes[$endCurrent->destination])) {

                $current = 1;
                $nextRoute = $this->allRoutes[$endCurrent->destination];

                while($current == 1) {

                    foreach ($nextRoute as $key => $route) {

                        if ($key == 0) {
                            $multiCurrentRoute = $currentRoute;
                            $currentRoute[] = $route;
                        } else {
                            $multiCurrentRoute[] = $route;
                            $queuePoints[$queue] = $multiCurrentRoute;
                            $queue++;
                        }

                        if ($route->destination == $destination) {
                            $current = 0;
                            $this->foundRoute[] = $currentRoute;
                            $currentRoute = array();
                        }

                    }

                    if (count($currentRoute) > 0) {
                        $endCurrent = end($currentRoute);
                        if (isset($this->allRoutes[$endCurrent->destination])) {
                            $nextRoute = $this->allRoutes[$endCurrent->destination];
                        } else {
                            $current = 0;
                            $currentRoute = array();
                        }
                    }
                }
                
            }

            if (count($queuePoints) > 0) {
                $nextRoute = $queuePoints[0];
                $currentRoute = $queuePoints[0];
                unset($queuePoints[0]);
                $queuePoints = array_values($queuePoints);
            } else {
                $possible = 0;
            }

		}
		
		return $this->foundRoute;
	}

	public function shortestRoute() {

		$this->shortestRoute = array();
        $foundRoute = $this->foundRoute;
        if (!$foundRoute) {
            $foundRoute = array();
        }

        $autoRoutes = 0;
		foreach ($foundRoute as $key => $routeList) {

			$this->shortestRoute[$autoRoutes]['time'] = 0;
			$this->shortestRoute[$autoRoutes]['cost'] = 0;
			foreach ($routeList as $key => $route) {
				$this->shortestRoute[$autoRoutes]['time'] += $route->time;
				$this->shortestRoute[$autoRoutes]['cost'] += $route->cost;
				if ($key == 0) {
					$this->shortestRoute[$autoRoutes]['path'][] = $route->origin;
				}
				$this->shortestRoute[$autoRoutes]['path'][] = $route->destination;
			}

			$autoRoutes++;
		}

        if ($this->shortestRoute) {
            $this->lessTime();
        }
		return $this->shortestRoute;
	}

	public function lessTime() {

		$minTime = min(array_column($this->shortestRoute, 'time'));
		$shortestRoute = collect($this->shortestRoute)->map(function ($route) use ($minTime) {
			if ($route['time'] == $minTime) {
				return $route;
			}
		});

		$this->shortestRoute = array_values(
			array_filter($shortestRoute->toArray())
		);
		
		if (count($this->shortestRoute) > 1) {
			$this->lessCost();
		} else {
			$this->shortestRoute = isset($this->shortestRoute[0]) ? $this->shortestRoute[0] : $this->shortestRoute;
		}
	}

	public function lessCost() {

		$minCost = min(array_column($this->shortestRoute, 'cost'));
		$shortestRoute = collect($this->shortestRoute)->map(function ($route) use ($minCost) {
			if ($route['cost'] == $minCost) {
				return $route;
			}
		});
		
		$this->shortestRoute = array_values(
			array_filter($shortestRoute->toArray())
        );
		$this->shortestRoute = isset($this->shortestRoute[0]) ? $this->shortestRoute[0] : $this->shortestRoute;
	}

	public function add($origin, $destination, $time, $cost) {
		if (!isset($this->allRoutes[$origin])) {
			$this->allRoutes[$origin] = array();
		}
		array_push($this->allRoutes[$origin], new RoutePoints($origin, $destination, $time, $cost));
	}
	
}