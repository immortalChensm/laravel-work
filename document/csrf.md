### csrf
- [CSRF laravel-china上的手册](https://learnku.com/docs/laravel/5.5/csrf/1295)  

- csrf 中间件  
中间件类会在框架启动的时候【就是一次http请求的时候】会运行所有的中间件类  
其中含有CSRF中间件  
![csrf](images/csrf/csrf1.png)  

CSRF中间件类结构图  
![csrf](images/csrf/VerifyCsrfToken.png)  

csrf handle   
![handle](images/csrf/csrf2.png)  

expectArray 跳过指定的uri不进行csrf验证   
![handle](images/csrf/csrf3.png)  

token匹配验证   
![handle](images/csrf/csrf4.png) 


获取传递过来的token    
![handle](images/csrf/csrf5.png)   

-EncryptCookies  中间件类的骚操作   
结构图   
![cookies](images/csrf/EncryptCookies.png)  

handle 
![cookie](images/csrf/cookie1.png)

解密cookie   
![cookie](images/csrf/cookie2.png)   

- Illuminate\Encryption\Encrypter 类说明  
[openssl加解密码手册](https://www.php.net/manual/en/function.openssl-encrypt.php)  
openssl加解密