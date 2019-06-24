### event
- 事件类注册 
Application注册
![eventRegister](images/events/event1.png)  
注册了一个key,value[匿名函数] 

- 事件服务提供类
![eventProvider](images/events/event2.png)

类结构

![EventServiceProvider](images/events/EventServiceProvider.png)  

- 注册事件  
![registerEvent](images/events/event3.png)  

事件调度器结构图  
![dispatcher](images/events/Dispatcher.png)  

往事件里注册事件  
![eventpool](images/events/event4.png)
![eventpool](images/events/event5.png)
![eventpool](images/events/event6.png) 


```php 
public function createClassListener($listener, $wildcard = false)
    {
        return function ($event, $payload) use ($listener, $wildcard) {
            if ($wildcard) {
                return call_user_func($this->createClassCallable($listener), $event, $payload);
            }

            return call_user_func_array(
                $this->createClassCallable($listener), $payload
            );
        };
    }
```  

当在调用的时候必须传$event,$payload参数  
![eventpool](images/events/event7.png)   
解析监听器  得到监听器类的方法
![eventpool](images/events/event8.png)  

默认得到的是class,method=handle  
当前监听类是否实现了队列【继承】  
![eventpool](images/events/event9.png)   

返回如下结果  
```php  
 protected function createClassCallable($listener)
    {
        list($class, $method) = $this->parseClassCallable($listener);

        if ($this->handlerShouldBeQueued($class)) {
            return $this->createQueuedHandlerCallable($class, $method);
        }

        return [$this->container->make($class), $method];
    }
```  

事件注册完成之后的事件池是  
```php 
$this->listeners[$event][] = function ($event, $payload) use ($listener, $wildcard) {
                                      
                             
                                         return call_user_func_array(
                                             $this->createClassCallable($listener), $payload
                                         );
                                     };
                                     
 $this->createClassCallable($listener)  =[监听类的实例，监听类的方法默认是handle],$payload参数                                    
```  

当监听器是个匿名函数时是这样的  
```php  


$this->listeners[$event][] = function ($event, $payload) use ($listener, $wildcard) {
            if ($wildcard) {
                return $listener($event, $payload);
            }

            return $listener(...array_values($payload));
        };
    }
```   

通配符事件监听器  
```php  
public function listen($events, $listener)
    {
        foreach ((array) $events as $event) {
            if (Str::contains($event, '*')) {
                $this->setupWildcardListen($event, $listener);
            } else {
                $this->listeners[$event][] = $this->makeListener($listener);
            }
        }
    }

    /**
     * Setup a wildcard listener callback.
     *
     * @param  string  $event
     * @param  mixed  $listener
     * @return void
     */
    protected function setupWildcardListen($event, $listener)
    {
        $this->wildcards[$event][] = $this->makeListener($listener, true);
    }
```  

- 事件调度运行   
```php  
if (! function_exists('event')) {
    /**
     * Dispatch an event and call the listeners.
     *
     * @param  string|object  $event
     * @param  mixed  $payload
     * @param  bool  $halt
     * @return array|null
     */
    function event(...$args)
    {
        return app('events')->dispatch(...$args);
    }
}  


```  

event('xxx');  
```php  
public function dispatch($event, $payload = [], $halt = false)
    {
        // When the given "event" is actually an object we will assume it is an event
        // object and use the class as the event name and this event itself as the
        // payload to the handler, which makes object based events quite simple.
        list($event, $payload) = $this->parseEventAndPayload(
            $event, $payload
        );

        if ($this->shouldBroadcast($payload)) {
            $this->broadcastEvent($payload[0]);
        }

        $responses = [];

        foreach ($this->getListeners($event) as $listener) {
            $response = $listener($event, $payload);

            // If a response is returned from the listener and event halting is enabled
            // we will just return this response, and not call the rest of the event
            // listeners. Otherwise we will add the response on the response list.
            if ($halt && ! is_null($response)) {
                return $response;
            }

            // If a boolean false is returned from a listener, we will stop propagating
            // the event to any further listeners down in the chain, else we keep on
            // looping through the listeners and firing every one in our sequence.
            if ($response === false) {
                break;
            }

            $responses[] = $response;
        }

        return $halt ? null : $responses;
    }
```    

解析事件  
![getevent](images/events/event11.png)  

返回的$payload=[$event事件对象],$event=事件类名称  

检测事件对象是否属于实现了广播类接口  
![broadcastEvent](images/events/event12.png)  

`$response = $listener($event, $payload);`  根据事件类名称取得对应的监听器匿名函数运行得到结果     

- 事件订阅  
![subscribe](images/events/subscribe1.png)  

实例化订阅类并给参数【Dispatcher调度器】  

![subscribe](images/events/subscribe2.png)    

事件订阅和事件注册一个道理  

最终都是从数组里检索里对应的匿名函数运行！就这么简单！

