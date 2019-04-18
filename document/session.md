### session  
- session 服务提供类  
```php  
protected function registerSessionManager()
    {
        $this->app->singleton('session', function ($app) {
            return new SessionManager($app);
        });
    }
    
$this->app->singleton('session.store', function ($app) {
            // First, we will create the session manager which is responsible for the
            // creation of the various session drivers when they are needed by the
            // application instance, and will resolve them on a lazy load basis.
            return $app->make('session')->driver();
        });
```   
![session](images/session/1.png)  

- [session使用手册](https://learnku.com/docs/laravel/5.5/session/1301#retrieving-data)  
全局函数的操作  

![global session function](images/session/session1.png)

sessionManager类结构图  
![session](images/session/SessionManager.png)
![session](images/session/SessionManager1.png)    

实例化调用如下方法    
![session](images/session/session2.png)  
![session](images/session/session3.png)  
![session](images/session/session4.png)  
![session](images/session/session5.png)  
![session](images/session/session6.png)  
![session](images/session/session7.png)  
![session](images/session/session8.png)  
![session](images/session/session9.png)  


Store/session结构图  
![session](images/session/Store.png)    

get获取数据  
![session](images/session/get.png)    


- startSession中间件   
handle   
![handle](images/session/handle1.png)

是否配置了session  
![session](images/session/handle2.png)    

startSession  
![session](images/session/handle3.png)    

getSession  
![session](images/session/handle4.png) 

startSession  
![session](images/session/handle5.png)   

- startSession流程  
loadAttribute  
![session](images/session/attribute1.png)  
![session](images/session/handle6.png)  
![session](images/session/attribute2.png)   

token   
![token](images/session/token.png) 
![token](images/session/token1.png)   

在http请求时，框架会运行startSession中间件，根据session.php配置实现存储类型  
如file,redis,database等实例化Store【Illuminate\Session\Store】并返回该对象   
【store内置了Illuminate\Session\FileSessionHandler根据配置文件指定】 然后生成  
sessionId,_token，同时将数据保存在Store【attributes成员数组下】    

session/Store   
![token](images/session/store2.png)     


- request 实例设置Session  
![token](images/session/request1.png)  
![token](images/session/request2.png)    

- session gc 回收  
![token](images/session/gc1.png) 
![token](images/session/gc2.png)  

- response header cookie 设置  
![token](images/session/header.png)     

- [sessionHanlder自定义](https://www.php.net/manual/en/class.sessionhandlerinterface.php)   



- session FileHanlder保存用户设置的session  

session()->put('key','value');  
![token](images/session/save1.png)     
![token](images/session/save2.png)     
![token](images/session/save3.png)     
![token](images/session/save4.png)        



File驱动类型的session大体流程   
每次http请求时，自动读取session file里的文件数据，并保存在session/store类下   
当用户session->put()设置数据时，sessionStart中间件会将数据再写入文件保存   


具体说明代码在startSession中间件这骚货   
[startSession](../vendor/laravel/framework/src/Illuminate/Session/Middleware/StartSession.php) 



session补充
每次http请求时，根据sessionId【session文件】读取数据，并解析保存在Store里的attributes   
并且生成_token=40位随机字符串,并把Store实例保存在当前请求Request里的session变量上  

同时检测session生命周期，到期后自动删除session文件

在http请求结束时，自动将用户设置的数据【如session->put这样的操作】会将数据写入文件保存  
方便下次再读取文件,同时将session名称，对应的sessionid等参数保存在Cookie   
并设置响应头的cookie  

![cookie](images/session/token2.png)