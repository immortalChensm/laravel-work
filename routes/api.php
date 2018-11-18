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
//


//Route::middleware('auth:api')->get('/user', function (Request $request) {
//    return $request->user();
//});

//实例化dingo的路由器对象
/**
当运行的时候，会将路由参数通过路由适配器【dingo的路由和laravel的路由进行适配】
其实就是将路由参数映射为路由对象，并保存在路由集合池里
每个路由都有版本
class Laravel implements Adapter->routes[version] = 路由集合池【路由对象】
 **/
$api = app('Dingo\Api\Routing\Router');
$api->version('v1',['namespace'=>'App\Http\Controllers\Api'],function ($api) {
    $api->group(['middleware'=>'web'], function ($api) {
        // Endpoints registered here will have the "foo" middleware applied.
        $api->get("user","UserController@index");
    });
});