### jwt-auth注解  
- jwt composer file  
![jwt composer.json](images/jwt/composer.json.png)  
框架在启动的时候自然会运行此文件！  
jwt扩展包安装完之后框架会将其扩展选项的配置放入如下文件，以便框架运行时加载  
![jwt extra file](images/jwt/jwtinstall.png) 
- jwt auth composer 包  
[jwt auth](https://packagist.org/packages/tymon/jwt-auth)  
- jwt auth 1.0.x版本  
[doc](https://jwt-auth.readthedocs.io/en/develop/)

- jwt auth laravel官网上的说明  
[laravel web jwt](https://learnku.com/articles/10885/full-use-of-jwt)

- jwt 详细说明 
[引用laravel 官网上的一位大牛的文档](https://learnku.com/articles/17883)  


- jwt 配置文件发布  
![jwt publish](images/jwt/jwtinstall4.png)  
![jwt publish](images/jwt/jwtinstall1.png)  
![jwt publish](images/jwt/jwtinstall2.png)  
![jwt publish](images/jwt/jwtinstall3.png)  
![jwt publish](images/jwt/jwtinstall5.png)  
![jwt publish](images/jwt/jwtinstall6.png)   

- jwt auth 使用  
  auth(api)->xxx()  
  
![jwt publish](images/jwt/auth1.png) 
![jwt publish](images/jwt/auth2.png) 
![jwt publish](images/jwt/auth3.png) 
![jwt publish](images/jwt/auth4.png) 
![jwt publish](images/jwt/auth5.png)  

- jwt auth login 操作  
![jwt attempt](images/jwt/login1.png) 
![jwt attempt](images/jwt/login2.png) 
![jwt attempt](images/jwt/login3.png)   
基于模型的查询构造器，根据账号检索一条用户记录，然后hash验证密码是否正确 
[hash验证](http://php.net/manual/zh/function.password-verify.php)  

- jwt auth login 登录生成token  
![jwt login](images/jwt/token1.png) 
![jwt login](images/jwt/token2.png) 
![jwt login](images/jwt/token3.png) 
![jwt login](images/jwt/token4.png)   


json web token   
头部，载荷，签名，其中截荷含有用户的唯一id值   
![jwt login](images/jwt/token5.png)     

获取用户信息  
  
![jwt login](images/jwt/token6.png)   
![jwt login](images/jwt/token7.png)   

json web token decode  
 ![jwt login](images/jwt/token8.png)   
 
- json web token 底层代码边缘疯狂试探  
 ![jwt](images/jwt/token9.png)  
 ![jwt](images/jwt/token10.png)    
 [json web token /signature 的使用手册](https://packagist.org/packages/namshi/jose)  
  ![jwt hash](images/jwt/token11.png)     
  ![jwt hash](images/jwt/token12.png)      
  通过不要脸的阅读代码得到底层是跑这个  
  [hash-hmac用法](http://php.net/manual/en/function.hash-hmac.php)   
  ![hash-hmac用法](images/jwt/token13.png)   
  ![hash-equal](images/jwt/token14.png)     
  
  当然jwt-auth运行的是如下代码  
  ![hmac](images/jwt/token15.png)  
  ![hmac](images/jwt/token16.png)  
  ![hmac](images/jwt/token17.png)   
  ![hmac](images/jwt/token18.png)   
  
  
结论：json web token 实现原理   
[php hash_hmac使用文档](http://php.net/manual/en/function.hash-hmac-algos.php) 

1、头部   header
一个json字符串，会base64处理    
2、载荷【具体消息，一般存放用户的标识】   payload
也是json字符串，含有过期，日期等结构，可自定义  base64

3、签名   signature
var singature = hash_hmac(alog,header.payload,true);   

4、验证  
同样按上面的流程签名，然后hash_euqal验证是否匹配【防止数据篡改】     

得出json web token 其实就靠这几个破函数处理，其中载荷存放了关键的重要信息，同时防止用户  
拦截篡改数据就做了一个签名验签操作   

载荷存放用户的关键信息【如标识id】，接收后进行验证，验证成功说明数据正常，再base64_decode  
得到载荷的信息，再查一下数据库over    



 