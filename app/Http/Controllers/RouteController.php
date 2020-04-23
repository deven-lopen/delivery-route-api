<?php

namespace App\Http\Controllers;

use App\Models\Route;
use App\Custom\GraphRoutes;
use Illuminate\Http\Request;

class RouteController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'origin' => 'required',
            'destination' => 'required',
        ]);

        $origin = strtoupper($request->origin);
        $destination = strtoupper($request->destination);

        $directRoute = Route::where('origin', $origin)
                ->where('destination', $destination)->get();
        if (count($directRoute) > 0) {
            return array(
                'message' => "Direct route not allowed!"
            );
        }

        $originRoute = Route::where('origin', $origin)->get();
        if (count($originRoute) < 1) {
            return array(
                'message' => "No avaialable route found!"
            );
        }

        $routes = Route::get();
        $graphRoutes = new GraphRoutes();
        foreach ($routes as $key => $route) {
            $graphRoutes->add(
                strtoupper($route->origin),
                strtoupper($route->destination),
                $route->time,
                $route->cost
            );
        }

        $graphRoutes->findRoutes($origin, $destination);
        $shortestRoute = $graphRoutes->shortestRoute();
        
        if ($shortestRoute) {
            return array(
                'path' => implode(" -> ", $shortestRoute['path']),
                'time' => $shortestRoute['time'],
                'cost' => $shortestRoute['cost'],
            );
        }
        
        return array(
            'message' => "No avaialable route found!"
        );
    }

    public function store(Request $request)
    {
        $request->merge(['origin' => strtoupper($request->origin)]);
        $request->merge(['destination' => strtoupper($request->destination)]);
        return Route::create($this->validator($request));
    }

    public function update(Request $request, $id) 
    {
        $this->validator($request);
        
        $route = Route::find($id);
        if ($route) {
            if ($request->has('origin')) {
                $route->origin = strtoupper($request->origin);
            }
            if ($request->has('destination')) {
                $route->destination = strtoupper($request->destination);
            }
            if ($request->has('time')) {
                $route->time = $request->time;
            }
            if ($request->has('cost')) {
                $route->cost = $request->cost;
            }

            $route->save();
            return $route;
        }
        
        return array(
            'message' => 'No record found!'
        );
    }

    public function destroy($id)
    {
        $route = Route::find($id);
        if ($route) {
            $route->delete();
            return array(
                'message' => 'Record deleted!'
            );
        }

        return array(
            'message' => 'No record found!'
        );
    }

    public function validator($request)
    {
        return $request->validate([
            'origin' => 'required',
            'destination' => 'required|different:origin',
            'time' => 'required',
            'cost' => 'required',
        ]);
    }
}