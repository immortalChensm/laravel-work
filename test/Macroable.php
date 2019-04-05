<?php
/**
 * Created by PhpStorm.
 * User: 1655664358@qq.com
 * Date: 2019/4/5
 * Time: 23:46
 */
trait Macroable
{
    protected static $macro = [];

    public static function macro($name,$macro)
    {
        static::$macro[$name] = $macro;
    }

    public static function mixin($mixin)
    {
        $methods = (new ReflectionClass($mixin))->getMethods(ReflectionMethod::IS_PUBLIC|ReflectionMethod::IS_PROTECTED);

        foreach ($methods as $method){
            $method->setAccessible(true);

            static::$macro[$method->getName()] = $method->invoke($mixin);
        }
    }

    public static function hasMacro($name)
    {
        return isset(static::$macro[$name]);
    }

    public static function __callStatic($name, $arguments)
    {
        // TODO: Implement __callStatic() method.
        if (!static::hasMacro($name)){
            throw new BadMethodCallException("没有这方法嘛");
        }

        if (static::$macro[$name] instanceof Closure){

            //将匿名函数绑定到当前的类，并运行当前的匿名函数
            return call_user_func_array(Closure::bind(static::$macro[$name],null,static::class),$arguments);
        }

        //运行当前的匿名函数
        return call_user_func_array(static::$macro[$name],$arguments);

    }

    public function __call($name, $arguments)
    {
        // TODO: Implement __call() method.
        if (!static::hasMacro($name)){
            throw new BadMethodCallException("没有这方法嘛");
        }

        $method = static::$macro[$name];
        if ($method instanceof Closure){
            //将当前方法绑定到当前类并运行此方法
            return call_user_func_array($method->bindTo($this,static::class),$arguments);
        }
        return call_user_func_array($method,$arguments);
    }

}