### route Dispatcher  
- Http Kernel 类结构图  
![kernel](images/dispatcher/Kernel.png)
![kernel](images/dispatcher/reg5.png)  

- http run workflow  
添加中间件   
![run](images/dispatcher/reg1.png)   

handle  
![kernel](images/dispatcher/reg2.png)   

sendRequestThroughRouter  
![kernel](images/dispatcher/reg3.png)  

then  
![kernel](images/dispatcher/reg4.png)    

Pipeline类结构图  
![kernel](images/dispatcher/Pipeline.png)  
![kernel](images/dispatcher/Pipeline1.png)    

carry   
```php  
 protected function carry()
    {
        return function ($stack, $pipe) {
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

                return method_exists($pipe, $this->method)
                                ? $pipe->{$this->method}(...$parameters)
                                : $pipe(...$parameters);
            };
        };
    }
```   

依次循环中间件类，中间件运行正常后，运行  
![kernel](images/dispatcher/reg6.png)     

router class dispatch  
![kernel](images/dispatcher/reg7.png)   

route find 路由查找  
![kernel](images/dispatcher/reg8.png) 
![kernel](images/dispatcher/reg9.png)   

routeCollection池里检索【开始匹配】  
![kernel](images/dispatcher/reg10.png)   

根据请求方式method检索路由routes  
routeCollection get检索  
![kernel](images/dispatcher/reg11.png)   

循环routes，开始进行匹配  
![kernel](images/dispatcher/reg12.png) 
![kernel](images/dispatcher/reg13.png)   

complie编译路由  
![kernel](images/dispatcher/reg14.png)  
![kernel](images/dispatcher/reg15.png)    

得到路由参数   
如/admin/{name?}这种玩意  
![kernel](images/dispatcher/reg16.png)   
最终返回的结果是[0]=>name   这种玩意     

路由编译时会先处理路由参数  
![kernel](images/dispatcher/reg17.png)     
如路由是这样的  
/admin/{name?}{age?}  则optionals= [name,age],uri=admin/{name}{age}   
这玩意    

最终返回的是Symfony\Component\Routing\Route   
类结构图如下  

![kernel](images/dispatcher/Route.png)   

继续匹配  

获取路由验证器uri,method,schema,host  
![kernel](images/dispatcher/reg18.png)     

路由uri验证是否匹配  
![kernel](images/dispatcher/uri.png)   
路由请求方式是否匹配  
![kernel](images/dispatcher/method.png)    
https/http验证  
![kernel](images/dispatcher/schema.png)   
host验证  
![kernel](images/dispatcher/host.png)        

runRoute运行路由  
![kernel](images/dispatcher/runRoute.png)     
![kernel](images/dispatcher/runRoute1.png)       

收集gather路由  
![kernel](images/dispatcher/gatherRoute.png)  
![kernel](images/dispatcher/route2.png)    

得到定义路由时配置的中间件  
![kernel](images/dispatcher/middleware.png)    
得到控制器内设置的中间件  
![kernel](images/dispatcher/actionMiddleware.png)  
![kernel](images/dispatcher/actionMiddleware1.png)    

从控制器和路由中得到全部的中件间别名，转换为中间件类后返回  
中间件类循环运行正常后，接着运行  

run  
![kernel](images/dispatcher/run1.png)  
判断action是字符串【控制器类】还是匿名方法【函数】  

runController   
![kernel](images/dispatcher/run2.png)    
获取控制器controller   
![kernel](images/dispatcher/controller.png)   
获取控制器的方法action  
![kernel](images/dispatcher/action1.png)   
![kernel](images/dispatcher/action2.png)     
dispatch  
![kernel](images/dispatcher/dispatch1.png)   

控制器方法依赖注入  
![kernel](images/dispatcher/action3.png)    
![kernel](images/dispatcher/action4.png)    
![kernel](images/dispatcher/action5.png)     

callAction  
![kernel](images/dispatcher/call.png)    

callFunction运行路由绑定的匿名函数  
![kernel](images/dispatcher/call1.png)     

response   
![kernel](images/dispatcher/response1.png)   
![kernel](images/dispatcher/response2.png)   
