<?php

namespace Illuminate\Routing;

use Closure;
use BadMethodCallException;
use Illuminate\Support\Arr;
use InvalidArgumentException;
/**
路由注册类
 **/
class RouteRegistrar
{
    /**
     * The router instance.
     *
     * @var \Illuminate\Routing\Router
     */
    protected $router;

    /**
     * The attributes to pass on to the router.
     *
     * @var array
     */
    protected $attributes = [];

    /**
     * The methods to dynamically pass through to the router.
     *
     * @var array
     */
    protected $passthru = [
        'get', 'post', 'put', 'patch', 'delete', 'options', 'any',
    ];

    /**
     * The attributes that can be set through this class.
     *可用于设置本类的属性列表
     * @var array
     */
    protected $allowedAttributes = [
        'as', 'domain', 'middleware', 'name', 'namespace', 'prefix',
    ];

    /**
     * The attributes that are aliased.
     *
     * @var array
     */
    protected $aliases = [
        'name' => 'as',
    ];

    /**
     * Create a new route registrar instance.
     *
     * @param  \Illuminate\Routing\Router  $router
     * @return void
     */
    public function __construct(Router $router)
    {
        /**
        保存路由对象
        该路由已经保存了分组中间件类，路由中间件类数组
         **/
        $this->router = $router;
    }

    /**
     * Set the value for a given attribute.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @return $this
     *
     * @throws \InvalidArgumentException
     */
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

    /**
     * Route a resource to a controller.
     *
     * @param  string  $name
     * @param  string  $controller
     * @param  array  $options
     * @return \Illuminate\Routing\PendingResourceRegistration
     */
    public function resource($name, $controller, array $options = [])
    {
        return $this->router->resource($name, $controller, $this->attributes + $options);
    }

    /**
     * Create a route group with shared attributes.
     *
     * @param  \Closure|string  $callback
     * @return void
     */
    public function group($callback)
    {
        /**
        传递属性数组，路由文件地址
         **/
        $this->router->group($this->attributes, $callback);
    }

    /**
     * Register a new route with the given verbs.
     *
     * @param  array|string  $methods
     * @param  string  $uri
     * @param  \Closure|array|string|null  $action
     * @return \Illuminate\Routing\Route
     */
    public function match($methods, $uri, $action = null)
    {
        return $this->router->match($methods, $uri, $this->compileAction($action));
    }

    /**
     * Register a new route with the router.
     *
     * @param  string  $method
     * @param  string  $uri
     * @param  \Closure|array|string|null  $action
     * @return \Illuminate\Routing\Route
     */
    protected function registerRoute($method, $uri, $action = null)
    {
        /**
        当运行Route::get("user/test","UsersController@test");时会得到
        $method=get
        $url = user/test
        $action = UsersController@test
         
        $this->>attributest[保存了中间件类，命名空间]，合并后得到
        $this->attributest[middleware,namespace,uses=>[controller]]
         **/
        if (! is_array($action)) {
            $action = array_merge($this->attributes, $action ? ['uses' => $action] : []);
        }

        $temp = "运行路由定义文件时的动作";
        /**
         
        $method=get|post|delete|put....
        $this->router->get();
         **/
        return $this->router->{$method}($uri, $this->compileAction($action));
    }

    /**
     * Compile the action into an array including the attributes.
     *
     * @param  \Closure|array|string|null  $action
     * @return array
     */
    protected function compileAction($action)
    {
        if (is_null($action)) {
            return $this->attributes;
        }

        if (is_string($action) || $action instanceof Closure) {
            $action = ['uses' => $action];
        }

        return array_merge($this->attributes, $action);
    }

    /**
     * Dynamically handle calls into the route registrar.
     *
     * @param  string  $method
     * @param  array  $parameters
     * @return \Illuminate\Routing\Route|$this
     */
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
}
