### console 应用注解说明
- 注册console kernel  
![console kernel](images/console/kernel1.png)  

- 从容器里检索并实例【反射】  
```php 
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
```  
实例后做如下动作  
![console kernel](images/console/kernel2.png)  
![console kernel](images/console/kernel3.png)  

- run handle  
 
  ![console kernel](images/console/kernel4.png)  
  
  控制台命令注册加载   
   ![console kernel](images/console/command1.png) 
   ![console kernel](images/console/command2.png) 