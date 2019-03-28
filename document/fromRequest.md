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
![fromRequest的示例](images/fromRequest10.png)
![fromRequest的示例](images/fromRequest11.png)
![fromRequest的示例](images/fromRequest12.png)

然后运行如下代码  
![fromRequest的示例](images/fromRequest13.png)  
调用验证方法validate  
![fromRequest的示例](images/fromRequest14.png)  
![fromRequest的示例](images/fromRequest15.png)  

- 验证器的验证  
```php 
protected function validateAttribute($attribute, $rule)
    {
        $this->currentRule = $rule;

        list($rule, $parameters) = ValidationRuleParser::parse($rule);

        if ($rule == '') {
            return;
        }

        // First we will get the correct keys for the given attribute in case the field is nested in
        // an array. Then we determine if the given rule accepts other field names as parameters.
        // If so, we will replace any asterisks found in the parameters with the correct keys.
        if (($keys = $this->getExplicitKeys($attribute)) &&
            $this->dependsOnOtherFields($rule)) {
            $parameters = $this->replaceAsterisksInParameters($parameters, $keys);
        }

        $value = $this->getValue($attribute);

        // If the attribute is a file, we will verify that the file upload was actually successful
        // and if it wasn't we will add a failure for the attribute. Files may not successfully
        // upload if they are too large based on PHP's settings so we will bail in this case.
        if ($value instanceof UploadedFile && ! $value->isValid() &&
            $this->hasRule($attribute, array_merge($this->fileRules, $this->implicitRules))
        ) {
            return $this->addFailure($attribute, 'uploaded', []);
        }

        // If we have made it this far we will make sure the attribute is validatable and if it is
        // we will call the validation method with the attribute. If a method returns false the
        // attribute is invalid and we will add a failure message for this failing attribute.
        $validatable = $this->isValidatable($rule, $attribute, $value);

        if ($rule instanceof RuleContract) {
            return $validatable
                    ? $this->validateUsingCustomRule($attribute, $value, $rule)
                    : null;
        }

        $method = "validate{$rule}";

        if ($validatable && ! $this->$method($attribute, $value, $parameters, $this)) {
            $this->addFailure($attribute, $rule, $parameters);
        }
    }
```   

![验证过程示例](images/validate1.png)
![验证过程示例](images/validate2.png)
[具体验证方法详情](../vendor/laravel/framework/src/Illuminate/Validation/Concerns/ValidatesAttributes.php)   


- 验证失败后的一个流程   
![validate fail](images/validate/1.png)
![validate fail](images/validate/2.png)
![validate fail](images/validate/3.png)
![validate fail](images/validate/4.png)
![validate fail](images/validate/5.png)
![validate fail](images/validate/6.png) 
![validate fail](images/validate/7.png)  

当验证失败后，throw 一个验证异常类，这个时候被异常【自定义的异常处理类，框架在启动的时候  
注册好了】捕获到，然后判断异常类型  
![validate fail](images/validate/8.png)  
![validate fail](images/validate/9.png)    

返回的验证异常类最终是读取验证失败时保存的错误信息   
![validate fail](images/validate/10.png)  
![validate fail](images/validate/11.png)    


针对错误验证时的消息重写   
![validate fail](images/validate/12.png) 

