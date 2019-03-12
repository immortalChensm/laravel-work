## laravel FormRequest 验证注解说明
#### FormRequestServiceProvider的运行过程
- 运行流程  
![formRequestWorkflow1](images/fromRequest1.png)
![formRequestWorkflow2](images/fromRequest2.png)
![formRequestWorkflow3](images/fromRequest3.png)
![formRequestWorkflow4](images/fromRequest4.png)
![formRequestWorkflow5](images/fromRequest5.png)
![formRequestWorkflow6](images/fromRequest6.png)

在这里注册的ValidatesWhenResolved,FormRequest 它们都是属于Request类
- 验证器的使用  
![fromRequest的示例](images/fromRequest7.png)
![fromRequest的示例](images/fromRequest8.png)

该验证器作为控制器的方法的参数controller->method(fromRequest)  时，在控制器调度时   
会进行反射【由Container类处理,[具体看控制器的依赖注入反射](../vendor/laravel/framework/src/Illuminate/Routing/ControllerDispatcher.php)】进行运行其方法  
![fromRequest的示例](images/fromRequest9.png)