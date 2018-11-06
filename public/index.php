<?php

/**
 * Laravel - A PHP Framework For Web Artisans
 *
 * @package  Laravel
 * @author   Taylor Otwell <taylor@laravel.com>
 */

define('LARAVEL_START', microtime(true));

/*
|--------------------------------------------------------------------------
| Register The Auto Loader
|--------------------------------------------------------------------------
|
| Composer provides a convenient, automatically generated class loader for
| our application. We just need to utilize it! We'll simply require it
| into the script here so that we don't have to worry about manual
| loading any of our classes later on. It feels great to relax.
|
*/
/**
这是composer自动生成的autoload.php文件
功能：当实例化类时完成自动加载类的功能
composer包的composer.json一般会配置好psr-4的标准加载规范
即类名=文件基目录（根命名空间）+子命名空间（子目录）+类名
 **/
require __DIR__.'/../vendor/autoload.php';

/*
|--------------------------------------------------------------------------
| Turn On The Lights
|--------------------------------------------------------------------------
|
| We need to illuminate PHP development, so let us turn on the lights.
| This bootstraps the framework and gets it ready for use, then it
| will load up this application so that we can run it and send
| the responses back to the browser and delight our users.
|
*/
/**
这是laravel框架的入口文件
 **/
$app = require_once __DIR__.'/../bootstrap/app.php';

/*
|--------------------------------------------------------------------------
| Run The Application
|--------------------------------------------------------------------------
|
| Once we have the application, we can handle the incoming request
| through the kernel, and send the associated response back to
| the client's browser allowing them to enjoy the creative
| and wonderful application we have prepared for them.
|
*/
/**
上面的语句运行之后，会把Http内核的契约类和具体类保存在Application下的
 Application->bindings[Illuminate\Contracts\Http\Kernel::class] = function(){
    return Container->make(App\Http\Kernel::class)
};
 *
 * Application->make()会查找到具体的匿名函数，并运行匿名函数返回具体的Http内核对象
 * Http 内核类位于Illuminate\Foundation\Http 子类继承的位于App\Http下
 * 该子类定义了路由中间件，全局中间件，分组中间件数组【中间数组的每个元素其实是个类】
 * Illuminate\Contracts\Http\Kernel::class找到对应的具体类为App\Http\Kernel::class
 **/
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

/**
处理请求
 **/
$response = $kernel->handle(
    /**
    该Request类是Symfony的一个组件
    该组件的详细文档在https://symfony.com/doc/current/components/http_foundation.html
    大体功能是对PHP的超级全局变量进行了封装,$_GET,$_POST,$_HEADER,$_SERVER等封装为对象
    并组合成组合对象返回
     **/
    $request = Illuminate\Http\Request::capture()
);
/**
响应
 **/
$response->send();

$kernel->terminate($request, $response);
