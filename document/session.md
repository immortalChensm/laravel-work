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
