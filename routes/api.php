<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

$api = app('Dingo\Api\Routing\Router');
$api->version('v1', function ($api) {
    $api->post('users/login', 'App\Http\Controllers\Api\AuthController@users');
    
    $api->group(['middleware'=>'api.auth'],function($api){
        $api->get("users/profile",'App\Http\Controllers\Api\AuthController@info');  
    });
});