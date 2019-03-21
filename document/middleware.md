## 中间件运行源码  
Illuminate\Foundation\Http\Kernel
```php 
  return (new Pipeline($this->app))
                    ->send($request) //加载全局中间件
                    ->through($this->app->shouldSkipMiddleware() ? [] : $this->middleware)
                    ->then($this->dispatchToRouter());

```   

```php 
 protected function dispatchToRouter()
    {
        return function ($request) {
            $this->app->instance('request', $request);

            return $this->router->dispatch($request);
        };
    }
```   


```php 
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
```  

```php 
protected function prepareDestination(Closure $destination)
    {
        return function ($passable) use ($destination) {
            return $destination($passable);
        };
    }
```   

```php 
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
                }  n

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
```   

是不是越看越优雅？:smile: