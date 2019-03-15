### 数据库ORM注解说明
- [orm使用文档](https://learnku.com/docs/laravel/5.5/eloquent/1332)
- db的关系  
  Illuminate\Database\DatabaseManager----->Illuminate\Database\Connection(可以进行原生的sql)  
  操作------>pdo(原生的pdo了)   
  
  
  查询构造器
  db()->table()->xxx   
  这种操作呢关系如下   
  Illuminate\Database\Query\Builder------>Illuminate\Database\Connection----->pdo
  查询构造器的任何骚操作都是基于Connection的
  
- model  
  ![model register](images/model1.png)
  ![model register](images/model2.png)
  
  静态使用  
   ![model register](images/model3.png)  
   其实还是实例化的啦  
   ORM使用运行流程  
   ![model register](images/model4.png)  
   ![model register](images/model5.png)  
   ![model register](images/model6.png)  
   ![model register](images/model7.png)  
   ![model register](images/model8.png)  
   ![model register](images/model9.png)  
   ![model register](images/model10.png)  
  ` $query = $this->newModelQuery();` 本代码运行后返回Builder，关系如下  
  Illuminate\Database\Eloquent\Builder ---->Illuminate\Database\Query\Builder  
  --->Illuminate\Database\Connection ---->pdo  
   返回查询构造器 是Eloquent的构造器，并且配置好了数据表  
   比如一个插入数据操作  
   
   ![model register](images/model-insert1.png)  
   ![model register](images/model-insert2.png)  
   ![model register](images/model-insert3.png)  
   ![model register](images/model-insert4.png)  
   ![model register](images/model-insert5.png)  
   ![model register](images/model-insert6.png)  
   ![model register](images/model-insert7.png)   
   
   经过层层分析，Model是基于查询构造器(Illuminate\Database\Query\Builder -connection-pdo)运行的  
   插入操作
   
    
  使用Model当然可以调用基于查询构造器的方法   
  ![model register](images/model12.png) 
  
- 关联查询1对1
 [手册](https://learnku.com/docs/laravel/5.5/eloquent-relationships/1333) 
 关联模型定义  
 ![](images/relationone1.png)
 ![](images/relationone2.png)
 ![](images/relationone3.png)
 ![](images/relationone4.png)
 ![](images/relationone5.png)
 
 关联模型查询  
 假设`$phone = User::find(1)->phone;`这样运行后
 ![find](images/find1.png)
 ![find](images/find2.png)
 ![find](images/find3.png)
 ![find](images/find4.png)
 ![find](images/find5.png)  
 
 查询到当然最终是基于连接Connection(pdo)啦，结果返回的时候还要用collect包一下【类似映射】  
 返回
 ![collect](images/find6.png) 
 ![collect集合类](images/collection.png) 