<?php

namespace Illuminate\Pipeline;

use Closure;
use RuntimeException;
use Illuminate\Contracts\Container\Container;
use Illuminate\Contracts\Pipeline\Pipeline as PipelineContract;

class Pipeline implements PipelineContract
{
    /**
     * The container implementation.
     *
     * @var \Illuminate\Contracts\Container\Container
     */
    protected $container;

    /**
     * The object being passed through the pipeline.
     *
     * @var mixed
     */
    protected $passable;

    /**
     * The array of class pipes.
     *
     * @var array
     */
    protected $pipes = [];

    /**
     * The method to call on each pipe.
     *
     * @var string
     */
    protected $method = 'handle';

    /**
     * Create a new class instance.
     *
     * @param  \Illuminate\Contracts\Container\Container|null  $container
     * @return void
     */
    public function __construct(Container $container = null)
    {
        $this->container = $container;
    }

    /**
     * Set the object being sent through the pipeline.
     *
     * @param  mixed  $passable
     * @return $this
     */
    public function send($passable)
    {
        $this->passable = $passable;

        return $this;
    }

    /**
     * Set the array of pipes.
     *
     * @param  array|mixed  $pipes
     * @return $this
     */
    public function through($pipes)
    {
        //这里中间件数组
        $this->pipes = is_array($pipes) ? $pipes : func_get_args();

        return $this;
    }

    /**
     * Set the method to call on the pipes.
     *
     * @param  string  $method
     * @return $this
     */
    public function via($method)
    {
        $this->method = $method;

        return $this;
    }

    /**
     * Run the pipeline with a final destination callback.
     *本方法功能：
     * 1、得到框架定义的全局中间件类
     * 2、实例中间件类得到中间件对象
     * 3、将当前的请求对象，匿名函数封装为数组Array
     * 4、运行中间件对象的handle方法
     * middleware->handle($request,Closure callback)
     * 当它返回true时，循环的中间类将会依次实例化，运行handle，返回false时停止运行
     * 5、所有的中间件类运行完毕，会运行$destination 匿名函数
     *
     * @param  \Closure  $destination
     * @return mixed
     */
    public function then(Closure $destination)
    {
        //$this->pipes 中间件类，元素【中间件类】一个个的弹出
        //$this->carry() 实例化中间件类并运行中间件类的handle方法
        //$destination 匿名函数
        $pipeline = array_reduce(
            /**
            第一次时：为全局中间件
            第二次时：路由定义的中间件类，Kernel内核定义的路由分组中间件类
             **/
            array_reverse($this->pipes), $this->carry(), $this->prepareDestination($destination)
        );
        //$this->passable 是Request类的对象
        $this->passable['test'] = 'jack';
        /**
        protected function dispatchToRouter()
        {
        return function ($request) {
        $this->app->instance('request', $request);

        return $this->router->dispatch($request);
        };
        }

        这里的调用如下$this->passable 当前的请求对象
         **/

        //这里运行的匿名函数是
        /**
         * function ($passable) use ($destination) {
               return $destination($passable);
          };
         */
        return $pipeline($this->passable);
    }

    /**
     * Get the final piece of the Closure onion.
     *
     * @param  \Closure  $destination
     * @return \Closure
     */
    protected function prepareDestination(Closure $destination)
    {
        return function ($passable) use ($destination) {
            return $destination($passable);
        };
    }

    /**
     * Get a Closure that represents a slice of the application onion.
     *依次运行每个中间件
     * 当中间件运行结果返回false时，后面的中程序将无法运行
     * 本方法的精妙之处在第二层的return 匿名函数会作为中间件类的handle方法的next参数
     * 当中间件运行此匿名函数时，会继续循环，当中间件返回布尔值false时中间件就会停止不在循环了
     *
     * @return \Closure
     */
    protected function carry()
    {
        return function ($stack, $pipe) {

            /**
             * $passable 当前请求对象
             * $pipe 中间件类
             */
            return function ($passable) use ($stack, $pipe) {
                if (is_callable($pipe)) {
                    // If the pipe is an instance of a Closure, we will just call it directly but
                    // otherwise we'll resolve the pipes out of the container and call it with
                    // the appropriate method and arguments, returning the results back out.
                    return $pipe($passable, $stack);
                } elseif (! is_object($pipe)) {
                    list($name, $parameters) = $this->parsePipeString($pipe);

                    // If the pipe is a string we will parse the string and resolve the class out
                    // of the dependency injection container. We can then build a callable and
                    // execute the pipe function giving in the parameters that are required.
                    $pipe = $this->getContainer()->make($name);

                    $parameters = array_merge([$passable, $stack], $parameters);
                } else {
                    // If the pipe is already an object we'll just make a callable and pass it to
                    // the pipe as-is. There is no need to do any extra parsing and formatting
                    // since the object we're given was already a fully instantiated object.
                    $parameters = [$passable, $stack];
                }

                //运行中间件的handle方法
                /**
                 * ...$parameters  第一个参数为当前请求的对象Request，第二个参数为
                 *function ($passable) use ($stack, $pipe) {
                     if (is_callable($pipe)) {

                        return $pipe($passable, $stack);
                     } elseif (! is_object($pipe)) {
                         list($name, $parameters) = $this->parsePipeString($pipe);

                         $pipe = $this->getContainer()->make($name);

                          $parameters = array_merge([$passable, $stack], $parameters);
                     } else {

                         $parameters = [$passable, $stack];
                    }
                     return method_exists($pipe, $this->method)
                                   ? $pipe->{$this->method}(...$parameters)
                                   : $pipe(...$parameters);
                 };
                 第二个参数是当前的匿名函数，第一个参数$passable=当前请求对象，$pipe每个中间件类
                 * 示例文件位于lessones/html/php
                 * array_reduce,array_reverse的使用
                 * 当中间件的handle方法返回真时则会继续循环中间件类，返回false时中间件将不在运行
                 */
                return method_exists($pipe, $this->method)
                                ? $pipe->{$this->method}(...$parameters)
                                : $pipe(...$parameters);
            };
        };
    }

    /**
     * Parse full pipe string to get name and parameters.
     *
     * @param  string $pipe
     * @return array
     */
    protected function parsePipeString($pipe)
    {
        list($name, $parameters) = array_pad(explode(':', $pipe, 2), 2, []);

        if (is_string($parameters)) {
            $parameters = explode(',', $parameters);
        }

        return [$name, $parameters];
    }

    /**
     * Get the container instance.
     *
     * @return \Illuminate\Contracts\Container\Container
     * @throws \RuntimeException
     */
    protected function getContainer()
    {
        if (! $this->container) {
            throw new RuntimeException('A container instance has not been passed to the Pipeline.');
        }

        return $this->container;
    }
}
