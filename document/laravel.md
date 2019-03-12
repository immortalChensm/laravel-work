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
- web路由注册代码流程
```php
Route::middleware('web')
             ->namespace($this->namespace)
             ->group(base_path('routes/web.php'));
```
`Route::middleware('web')` 此时将触发以下代码  
```php
Illuminate\Support\Facades\Facade

public static function __callStatic($method, $args)
    {
        /**
        static::$resolvedInstance[$name] = static::$app[$name];
        运行后得到Application类的对象，并且调用Application[$name] 该方法会触发ArrayAccess接口并实例化当前的门面子类如Route
         **/
        $instance = static::getFacadeRoot();

        /****/
        if (! $instance) {
            throw new RuntimeException('A facade root has not been set.');
        }

        return $instance->$method(...$args);
    }

public static function getFacadeRoot()
    {
        /**
        得到当前调用的门面伪装类并使用Application实例化返回
         **/
        return static::resolveFacadeInstance(static::getFacadeAccessor());
    }
protected static function getFacadeAccessor()
    {
        return 'router';
    }
    protected static function resolveFacadeInstance($name)
        {
            if (is_object($name)) {
                return $name;
            }
    
            if (isset(static::$resolvedInstance[$name])) {
                return static::$resolvedInstance[$name];
            }
    
            return static::$resolvedInstance[$name] = static::$app[$name];
        }
```


`static::$app[$name];`最终是容器从已经注册的池里检索到`'router'=> [\Illuminate\Routing\Router::class, \Illuminate\Contracts\Routing\Registrar::class, \Illuminate\Contracts\Routing\BindingRegistrar::class],
`Illuminate\Routing\Router::class类实例【反射】后返回

接着运行`Illuminate\Routing\Router->middleware('web')`激活魔术方法__call,从而运行如下代码  
```php 

public function __call($method, $parameters)
    {
        if (static::hasMacro($method)) {
            return $this->macroCall($method, $parameters);
        }

        /**
        Router类运行不存在的时候会运行到此
        当运行中间件方法时 $parameters=middle(web)传递过来的中间件别名参数

         当路由器调用：middleware,namesapce,domain,as时
         **/
        if ($method == 'middleware') {
            return (new RouteRegistrar($this))->attribute($method, is_array($parameters[0]) ? $parameters[0] : $parameters);
        }

        return (new RouteRegistrar($this))->attribute($method, $parameters[0]);
    }
```
此时是运行`new RouteRegistrar($this))`路由注册器,运行如下代码，保存路由属性
```php 
public function attribute($key, $value)
    {
        /**
        $key【可能是个方法method】
        判断不是本类规定的属性时
         **/
        if (! in_array($key, $this->allowedAttributes)) {
            throw new InvalidArgumentException("Attribute [{$key}] does not exist.");
        }

        //在此查看$this->>aliases[]和$this->>attributes[]数组
        //$this->attributes[middleware] = web;
        $this->attributes[Arr::get($this->aliases, $key, $key)] = $value;

        /**
        保存了中间件别名
        应用的命名空间
         **/
        $test = "show";
        return $this;
    }
```
   - 路由属性  
   
      | as | domain | middleware | name | namespace | prefix |
      |----|--------|------------|------|-----------|--------|
      | 别名| 域名   | 中间件     |  路由名称| 路由指向的空间 | 路由的前缀|
      
      
      
   此时` Route::middleware('web')` 运行完此方法后返回`RouteRegistrar`，接着运行  
   `RouteRegistrar->namespace($this->namespace)`，然后激活如下代码  
   ```php 
   RouteRegistrar类
   public function __call($method, $parameters)
       {
           /**
           [
           'get', 'post', 'put', 'patch', 'delete', 'options', 'any',
           ]当运行以上方法时
   
           Route::group(['middleware'=>'user.verify','prefix'=>'admin'],function (){
           Route::get("user/index","UsersController@index");
   
           Route::get("user/test","UsersController@test");
           });
   
   
   
            **/
           if (in_array($method, $this->passthru)) {
               return $this->registerRoute($method, ...$parameters);
           }
   
           /**
           [
           'as', 'domain', 'middleware', 'name', 'namespace', 'prefix',
           ]运行以上方法时
            **/
           if (in_array($method, $this->allowedAttributes)) {
               if ($method == 'middleware') {
                   return $this->attribute($method, is_array($parameters[0]) ? $parameters[0] : $parameters);
               }
   
               return $this->attribute($method, $parameters[0]);
           }
   
           throw new BadMethodCallException("Method [{$method}] does not exist.");
       }
   ```  
   ,运行后保存路由的属性-命名空间，接着运行`RouteRegistrar->group(base_path('routes/web.php'));`,此时加载  
   路由目录下的web路由文件，运行如下代码  
   ```php 
   //RouteRegistrar类 在实例化的时候$this->router = Illuminate\Routing\Router 实例
   //(new RouteRegistrar($this=Illuminate\Routing\Router))
   public function group($callback)
       {
           /**
           传递属性数组，路由文件地址
            **/
           $this->router->group($this->attributes, $callback);
       }
       
       protected function loadRoutes($routes)
           {
       
               if ($routes instanceof Closure) {
       
                   $temp = "运行路由定义的匿名函数";
                   $routes($this);
               } else {
                   $router = $this;
       
                   require $routes;//运行路由web.php文件
               }
           }
   ```
   假设路由web.php内容如下
   ```php 
   Route::group(['middleware'=>'user.verify','prefix'=>'admin'],function (){
       //Route::get("user/index","UsersController@index");
   
       /**
       Route触发门面基类并实例Router->get("user/test","UsersController@test")方法
        **/
       Route::get("user/index","UsersController@test");
   });
   ```
   此时会运行类似如下代码   [在运行前会把路由属性保存在组堆里groupStack]
   ```php 
   public function get($uri, $action = null)
       {
           /**
           会运行路由定义的get方法
           如Route::get(uri,action);
            **/
           return $this->addRoute(['GET', 'HEAD'], $uri, $action);
       }
   ```
   接着运行如下代码   
   ```php 
   protected function addRoute($methods, $uri, $action)
       {
           /**
           添加到路由集合类里
           得到路由对象
            **/
           return $this->routes->add($this->createRoute($methods, $uri, $action));
       }
       
       protected function createRoute($methods, $uri, $action)
           {
               // If the route is routing to a controller we will parse the route action into
               // an acceptable array format before registering it and creating this route
               // instance itself. We need to build the Closure that will call this out.
               if ($this->actionReferencesController($action)) {
                   //得到完整的控制器【带有命名空间】数组
                   $action = $this->convertToControllerAction($action);
               }
       
               /**
               $action会得到类似
                [
                   users=App\Http\Controllers\UsersController@index
                   controller=App\Http\Controllers\UsersController@index
                ]
       
                $method = [GET,HEAD]
                
                $uri = "users/index"
                **/
               $route = $this->newRoute(
                   //方法，完整的uri,action
                   $methods, $this->prefix($uri), $action
               );
       
               // If we have groups that need to be merged, we will merge them now after this
               // route has already been created and is ready to go. After we're done with
               // the merge we will be ready to return the route back out to the caller.
               if ($this->hasGroupStack()) {
                   $this->mergeGroupAttributesIntoRoute($route);
               }
       
               $this->addWhereClausesToRoute($route);
       
               return $route;
           }
   ```   
   
   路由集合类`$this->routes = new RouteCollection;`,路由的请求方法method  
   路由的地址uri,路由的行为action会映射为route路由对象 并保存在路由集合里[注册？]
   路由的uri会从路由属性prefix(groupStack里取prefix)拼接完整   
   路由的action其中uses,controller会从路由属性(groupStack)里取namespace构成完整的  
   controller拼接组合，route(Illuminate\Routing\Route) 中的action成员保存数据结构如下   
   ![route 的action结构图](images/route-action.png)
   [laravel 路由定义](https://learnku.com/docs/laravel/5.5/routing/1293)
   
   路由注册最终运行如下代码   
   ```php
   protected function addToCollections($route)
    {
        /**
        将路由添加到路由数组里
         路由对象取得域名，请求的uri参数
         **/
        $domainAndUri = $route->getDomain().$route->uri();

        /**
        routest[请求方式][服务器地址] = [路由对象]
         **/
        foreach ($route->methods() as $method) {
            //路由的请求方法-路由的完整uri=路由对象
            //路由对象保存了大量的路由定义文件里的规则
            //$this->routes[请求方法][完整的uri]=路由对象
            $this->routes[$method][$domainAndUri] = $route;
        }

        //路由请求方法+完整的请求uri=路由对象
        $this->allRoutes[$method.$domainAndUri] = $route;
    }
   ```
   路由注册完成后，routeCollection里保存的路由结构图如下   
   ![route路由结构图](images/routecollection_all.png)
   ![route路由结构图](images/routecollection_all1.png)
   ![route路由结构图](images/routecollection_all2.png)
      






