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
| Register The Auto Loader 注册自动加载器
|--------------------------------------------------------------------------
|
| Composer provides a convenient, automatically generated class loader for
| our application. We just need to utilize it! We'll simply require it
| into the script here so that we don't have to worry about manual
| loading any of our classes later on. It feels great to relax.
|composer提供了一个便利的，自动生动自动加载器类，对于我们的应用只需要使用它就可以，只需要简单的引入以下文件，就可以
自动加载我们应用的所有类文件
*/

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
|启动了应用程序之后，我们会处理来自客户端的请求，通过Kernel内核【Symfony组件的内核】，然后返回响应结果到客户端的浏览器
让他们享受我们给他准备好的具有创造性和奇妙的应用
*/

//在实例化http内核类时，会把内核设置的中间件类全部添加到路由类里
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

//在处理请求【该请求采用了第三方的Symfony组件Http对PHP的所有超级全局变量进行了封装并映射为对象
//同时对封装的全局变量【数组】里的元素进行了各种操作，同时使该对象实现数组下标形式访问，支持拦截器方式访问等操作
//具体链接在https://symfony.com/doc/current/components/http_foundation.html
$response = $kernel->handle(
//在响应请求之前，会加载相关的配置文件，以及第三方和框架自带的服务提供者并实例化绑定在容器里，如果提供者存在register,boot方法会运行它
    $request = Illuminate\Http\Request::capture()
);

$response->send();

$kernel->terminate($request, $response);
