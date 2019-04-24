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