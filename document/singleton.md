### Application singleton 方法的骚操作
- Application的继承  
```php 
namespace Illuminate\Foundation;
class Application extends Container implements ApplicationContract, HttpKernelInterface
class Container implements ArrayAccess, ContainerContract
```     
[php ArrayAccess接口文档](https://www.php.net/manual/en/class.arrayaccess.php)  
Application的继承关系图   
![Application继承关系全局图](images/application/app.png)
![Application继承关系全局图](images/application/app2.png)
图片  
![Application](images/application/single1.png)