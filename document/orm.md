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