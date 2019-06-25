### session  
session是什么玩意，没必要解释，下面直接解析一下代码  
- session 注册  
 还是一样的道理【就保存在一个数组里】  
 ![session](images/sessions/1.png)  
 
 session()设置和获取操作  
 PS:各种骚用法自己看手册哦  
 设置方法的流程  
 ![session](images/sessions/2.png)  
 很简单，给参数就按参数给的找驱动，没给就默认驱动  
 
  ![session](images/sessions/3.png)  
  配置文件默认是文件驱动  
 ![session](images/sessions/4.png)  
 
 拼装驱动类型并运行  
 ![session](images/sessions/5.png)  
 ![session](images/sessions/6.png)  
 ![session](images/sessions/7.png)   
 
 ```php  
 protected function createNativeDriver()
     {
         $lifetime = $this->app['config']['session.lifetime'];
 
         return $this->buildSession(new FileSessionHandler(
             //文件系统操作对象，session要保存的目录路径，session的过期时间
             $this->app['files'], $this->app['config']['session.files'], $lifetime
         ));
     }
 ```   
  ![session](images/sessions/8.png)  
  ![session](images/sessions/9.png)  
  ![session](images/sessions/10.png)    
  
  session(【'x'=>1,'y'=>2】)  =  
  ![session](images/sessions/11.png)   
  
  直接操作put  
  ![session](images/sessions/12.png)   
  
  获取操作  
  ![session](images/sessions/13.png)    
  
  PS:框架在启动的时候【就是你发起HTTP请求的时候会先运行  
  startSession中间件】为什么先运行？具体看我前面注解的内容  
  
  ![session](images/sessions/14.png)  
    
  ```php  
   protected function startSession(Request $request)
      {
          return tap($this->getSession($request), function ($session) use ($request) {
              $session->setRequestOnHandler($request);
  
              $session->start();
          });
      }

  ```  
  假设默认为文件驱动的话那么会运行如下代码   
  ![session](images/sessions/15.png)
  ![session](images/sessions/16.png)
  ![session](images/sessions/17.png)  
  
  以上在发起一次网络请求时，如果是文件存储的session会话  
  就会读取文件并临时保存在attributes上  
  
  session过期时删除  
  ![session](images/sessions/18.png)    
  
  请求结束时保存  
  ![session](images/sessions/19.png)  
  ![session](images/sessions/20.png)  
  ![session](images/sessions/21.png)    
  
  所以在一次HTTP请求期间【程序运行期间你可以使用session  
  设置数据，获取等操作，请求结束后，自动操作文件】  
  
  动不动操作磁盘上的文件来存储session效率不好！！！  
  
  下面我们分析一下使用redis来保存session会话数据   
  ![session](images/sessions/22.png) 
  ![session](images/sessions/23.png) 
  ![session](images/sessions/24.png) 
  ![session](images/sessions/25.png)   
  
  套路都一样  
  ![session](images/sessions/26.png)   
  
  得出结论：  
  session的存取由sessionManager来管理  
  这家伙封装各种驱动【session.php配置来管】  
  但最终返回的是统一接口的Illuminate\Session\Store类  
  并在框架启动的时候读取【从文件，redis,db等读取】  
  响应结束写入【文件，redis,db等保存】  
  
  其实也是给这几类驱动做了适配操作，让它们的方法兼容  
  缓存系统也是一样的道理  
  
  ![session](images/session/store3.png)  