<?php

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// create sa og kaugalingon nga function
// CRUD for routes
// get possible routes
// get route less time
// add admin middleware
// find reference for algo

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return User::get();
});





Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::get('/route', 'RouteController@index');
    Route::group(['middleware' => ['admin']], function () {
        Route::post('/route', 'RouteController@store');
        Route::put('/route/{route}', 'RouteController@update');
        Route::delete('/route/{route}', 'RouteController@destroy');
    });
});
Route::post('/login', 'AuthController@login');