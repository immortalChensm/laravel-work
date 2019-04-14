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
