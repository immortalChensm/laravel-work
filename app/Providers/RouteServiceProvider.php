<?php

namespace App\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * This namespace is applied to your controller routes.
     *
     * In addition, it is set as the URL generator's root namespace.
     *应用的路由根命名空间
     * @var string
     */
    protected $namespace = 'App\Http\Controllers';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        /**
        框架在启动时会运行此boot方法
         **/

        parent::boot();
    }

    /**
     * Define the routes for the application.
     *定义应用的路由
     * @return void
     */
    public function map()
    {
        $this->mapApiRoutes();

        /**
        web路由
         **/
        $this->mapWebRoutes();

        //
    }

    /**
     * Define the "web" routes for the application.
     *
     * These routes all receive session state, CSRF protection, etc.
     *
     * @return void
     */
    protected function mapWebRoutes()
    {
        /**
        Route类门面伪装类
        静态调用触发门面基类的魔术方法__callStatic得到router，交给Application进行实例化返回对象
        会实例化router对象返回该对象对应的类为Illuminate\Routing\Router
        Router->middleware()触发魔术方法
         **/

        $test = "在这里验证Route";
        /**
        Router实例化返回触发其__call魔术方法

        第一次middleware运行时：门面类取得实际类由Application实例化返回对象
        调用middleware时触发Router的__call魔术方法，从而实例化Illuminate\Routing\RouteRegistrar对象返回
        并将web参数保存在RouteRegistrar类下的attribures[middileware]=web保存

        第二namespace运行时：RouteRegistrar的对象运行namespace()时触发__call魔术方法，会验证该方法是否属于[
        'as', 'domain', 'middleware', 'name', 'namespace', 'prefix',
        ]该参数指定的某个元素
        然后保存在该类下的attribures = [[middileware]=web,$this->namespace]应用的命名空间

        第三次调用group时，require引入路由定义文件，并通过门面伪装类找到具体类，运行路由定义文件的方法
        完成路由的处理，并保存在Routing\Route类里和RouteCollection类里
         **/

        $test = "在这里验证Route";
        Route::middleware('web')
             ->namespace($this->namespace)
             ->group(base_path('routes/web.php'));
    }

    /**
     * Define the "api" routes for the application.
     *
     * These routes are typically stateless.
     *
     * @return void
     */
    protected function mapApiRoutes()
    {
        Route::prefix('api')
             ->middleware('api')
             ->namespace($this->namespace)
             ->group(base_path('routes/api.php'));
    }
}
