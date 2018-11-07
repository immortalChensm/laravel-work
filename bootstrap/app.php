<?php

/*
|--------------------------------------------------------------------------
| Create The Application
|--------------------------------------------------------------------------
|
| The first thing we will do is create a new Laravel application instance
| which serves as the "glue" for all the components of Laravel, and is
| the IoC container for the system binding all of the various parts.
|
*/
/**
实例化Application
该类在执行构造函数时大体完成了如下功能
A、绑定框架的各个目录的路径信息
B、基础的对象绑定
C、基本的服务提供类绑定
D、框架核心类的别名设置，在调用时传递别名找到具体类
Application实现了ArrayAccess接口，实现了对象可以以数组下标形式访问
 **/
$app = new Illuminate\Foundation\Application(
    realpath(__DIR__.'/../')
);

/*
|--------------------------------------------------------------------------
| Bind Important Interfaces
|--------------------------------------------------------------------------
|
| Next, we need to bind some important interfaces into the container so
| we will be able to resolve them when needed. The kernels serve the
| incoming requests to this application from both the web and CLI.
|
*/
/**
该方法会将契约类和具体的类绑定
具体的类会封装为一个匿名函数保存在Application成员bindings[]下
同时具体的类可能会运行 reboundCallbacks回调数组函数

 **/
$app->singleton(
    /**
    针对web的绑定

     **/
    Illuminate\Contracts\Http\Kernel::class,
    App\Http\Kernel::class
);

$app->singleton(
    /**
    针对cli的绑定
     **/
    Illuminate\Contracts\Console\Kernel::class,
    App\Console\Kernel::class
);

$app->singleton(
    /**
    针对异常的绑定
     **/
    Illuminate\Contracts\Debug\ExceptionHandler::class,
    App\Exceptions\Handler::class
);

/*
|--------------------------------------------------------------------------
| Return The Application
|--------------------------------------------------------------------------
|
| This script returns the application instance. The instance is given to
| the calling script so we can separate the building of the instances
| from the actual running of the application and sending responses.
|
*/

return $app;
