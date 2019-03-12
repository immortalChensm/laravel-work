#laravel框架内核运行注解

### laravel扩展包安装时做了什么
1.composer事件
[composer事件](https://docs.phpcomposer.com/articles/scripts.html)
在运行composer命令时，会触发响应指定的事件，从而运行指定的脚本文件

2.laravel 的composer.json文件
![composer.json文件结构](images/composerjson.png)
3.每次安装第三方扩展时，会自动运行包发现指令，并读取vendor/composer/installed.json文件
里的内容，并将每个扩展包的extra额外选项下指定的laravel里的providers,alias下的配置获取
并写入指定的文件packages.php并保存在项目的bootstrap/cache/packages.php里
```php
$packages = [];

        if ($this->files->exists($path = $this->vendorPath.'/composer/installed.json')) {
            $packages = json_decode($this->files->get($path), true);
        }

        $ignoreAll = in_array('*', $ignore = $this->packagesToIgnore());

        $this->write(collect($packages)->mapWithKeys(function ($package) {
            return [$this->format($package['name']) => $package['extra']['laravel'] ?? []];
        })->each(function ($configuration) use (&$ignore) {
            $ignore = array_merge($ignore, $configuration['dont-discover'] ?? []);
        })->reject(function ($configuration, $package) use ($ignore, $ignoreAll) {
            return $ignoreAll || in_array($package, $ignore);
        })->filter()->all());
```

### laravel Illuminate\Foundation\Http\Kernel 内核大体流程
http 内核注册到实例化
```php
$app->singleton(
    /**
    运行后会将其以key,value形式保存在容器的bindings[]里
    我这里叫注册
     **/
    Illuminate\Contracts\Http\Kernel::class,
    App\Http\Kernel::class
);
 //从里面检索并实例化【反射】返回
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
```

http内核实例化时 路由添加中间件类
```php
public function __construct(Application $app, Router $router)
    {
        $this->app = $app;
        $this->router = $router;

        $router->middlewarePriority = $this->middlewarePriority;
        /**
        向路由类添加路由中间件类
        向路由类添加中间件类组
         **/
        foreach ($this->middlewareGroups as $key => $middleware) {
            $router->middlewareGroup($key, $middleware);
        }

        foreach ($this->routeMiddleware as $key => $middleware) {
            $router->aliasMiddleware($key, $middleware);
        }
    }
```
运行handle方法
```php 
 protected function sendRequestThroughRouter($request)
    {
        /**
        将当前的请求对象进行绑定，绑定到Application类的对象下
         **/
        $this->app->instance('request', $request);

        Facade::clearResolvedInstance('request');

        /**
        循环运行本类的成员$this->$bootstrappers[]下的成员数组
         **/
        $this->bootstrap();

        return (new Pipeline($this->app))
                    ->send($request) //加载全局中间件
                    ->through($this->app->shouldSkipMiddleware() ? [] : $this->middleware)
                    ->then($this->dispatchToRouter());

        /**
        $this->dispatchToRouter() 控制器运行之后返回的响应，响应由Symfony的组件完成
         **/
    }
```
此时当前请求相关的【request】已保存【注册】在窗口里的instances[request] = $request里了
`$this->bootstrap()`运行后
- 会完成环境配置文件的解析并将其保存在超级变量$_SERVER,$_ENV上
- 同时处理配置目录config/下的所有配置文件，并保存
- 设置错误，异常等自定义处理
- 注册自定义的伪装【他们叫门面？】
- 注册框架安装的所有扩展包【为laravel写的扩展包，有服务提供注册机制】并运行服务提供类的register方法
- 运行服务提供类的boot方法

### router 路由注册服务【路由服务类处理】
- 路由注册由 RouteServiceProvider 完成
- web路由注册代码
```php
Route::middleware('web')
             ->namespace($this->namespace)
             ->group(base_path('routes/web.php'));
```


