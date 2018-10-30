<?php

/*
|--------------------------------------------------------------------------
| Create The Application
|--------------------------------------------------------------------------
|
| The first thing we will do is create a new Laravel application instance
| which serves as the "glue" for all the components of Laravel, and is
| the IoC container for the system binding all of the various parts.
|第一件事情就是创建一个application实例，该实例依赖laravel所有的组件
同时它也是一个控制反转的容器，用于系统绑定所有的部件
*/

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
接下来，我们要绑定重要的接口，绑定到容器里，当需要它的时候我们能处理，这个内核就会处理接受进来的网络请求
不管是web还是终端都可以
*/

/*
 *绑定内核类，该类下有中间件，以及系统运行时要加载的组件
 *如环境配置文件
 *config目录下的配置文件
 *服务提供类等
 *分别绑定控制台和web的请求处理 
 * 以及异常绑定
 */
$app->singleton(
    Illuminate\Contracts\Http\Kernel::class,
    App\Http\Kernel::class
);

$app->singleton(
    Illuminate\Contracts\Console\Kernel::class,
    App\Console\Kernel::class
);

$app->singleton(
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
