<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/
$api = app('Dingo\Api\Routing\Router');
$api->version('v1', function ($api) {

    $api->group(['prefix' => 'oauth'], function ($api) {
        $api->post('token', '\Laravel\Passport\Http\Controllers\AccessTokenController@issueToken');
    });

    $api->group(['namespace' => 'App\Http\Controllers'], function ($api) {
        $api->post('user', 'UserController@signUp');
    });


    // Authorized requests
    $api->group(['middleware' => ['auth:api', 'cors']], function ($api) {

        $api->group(['namespace' => 'App\Http\Controllers'], function ($api) {

            $api->post('lan', 'LanController@createLan');

            $api->post('lan/{lan_id}/book/{seat_id}', 'SeatController@bookSeat');

        });

    });


});