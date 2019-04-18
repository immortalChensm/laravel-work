<?php
/**
 * Created by PhpStorm.
 * User: 1655664358@qq.com
 * Date: 2019/4/18
 * Time: 22:17
 */

//中间件运行原理测试
abstract class middleware
{
    abstract public function handle($request,$next);
}

class verifyUserMiddleware extends middleware
{
    public function handle($request, $next)
    {
        // TODO: Implement handle() method.
        echo "handler:";
        echo PHP_EOL;
        return $next($request);
    }
}
class verifyUserProfileMiddleware extends middleware
{
    public function handle($request, $next)
    {
        // TODO: Implement handle() method.
        echo "handler:".__CLASS__;
        echo PHP_EOL;
       // return $next($request);
        //throw new RuntimeException("错误了");
        //print_r(func_get_args());
        return $next($request);
    }
}
$middlewareClass = [verifyUserMiddleware::class,verifyUserProfileMiddleware::class];

class Pipe{

    public function send($middlewareClass,$callBack)
    {
        $handler = array_reduce($middlewareClass,$this->carry(),$this->response($callBack));

        //$handler  = 返回实例化好的中间件实例类，并运行中间件类的实例
        $handler(['name'=>'jack','age'=>18]);
    }

    public function response($callBack)
    {
        return function ($response)use($callBack){
            return $callBack($response);
        };
    }

    public function carry(){
        return function ($stack,$pipe){

            echo "outer:".$pipe;
            echo PHP_EOL;
            return function($request)use($stack,$pipe){
                echo "inner:".$pipe;
                echo PHP_EOL;
                if (class_exists($pipe)){
                    $obj = new $pipe;
                    $params = [$request,$stack];
                    return $obj->handle(...$params);
                }

            };
        };
    }
}

(new Pipe())->send($middlewareClass,function ($response){
    print_r($response);
});