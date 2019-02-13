<?php

namespace Illuminate\Routing;

use ReflectionMethod;
use ReflectionParameter;
use Illuminate\Support\Arr;
use ReflectionFunctionAbstract;

trait RouteDependencyResolverTrait
{
    /**
     * Resolve the object method's type-hinted dependencies.
     *
     * @param  array  $parameters
     * @param  object  $instance
     * @param  string  $method
     * @return array
     */
    protected function resolveClassMethodDependencies(array $parameters, $instance, $method)
    {
        if (! method_exists($instance, $method)) {
            return $parameters;
        }

        return $this->resolveMethodDependencies(
            /**
            反射该对象的方法
            class HelloWorld {
            private function sayHelloTo($name,$arg1,$arg2) {
            return 'Hello ' . $name.' '.$arg1.' '.$arg2;
            }
            }

            $obj = new HelloWorld();
            // 第一个参数可以是对象,也可以是类
            $reflectionMethod = new ReflectionMethod($obj , 'sayHelloTo');
            if(!$reflectionMethod -> isPublic()){
            $reflectionMethod -> setAccessible(true);
            }

            public mixed ReflectionMethod::invoke ( object $object [, mixed $parameter [, mixed $... ]] )
            1. 获得某个类方法的ReflectionMethod
            2. $object 该方法所在的类实例的对象，然后第二参数起对号入座到该方法的每个参数；
            3. 通过invoke就可以执行这个方法了

            echo $reflectionMethod->invoke($obj, 'GangGe','How','are you');

            //也可以把参数作为数组传进来
            echo $reflectionMethod -> invokeArgs($obj,array('GangGe','How','are you'));


             **/
            $parameters, new ReflectionMethod($instance, $method)//反射该对象的方法，会得到该方法，该方法是个对象
        );
    }

    /**
     * Resolve the given method's type-hinted dependencies.
     *
     * @param  array  $parameters
     * @param  \ReflectionFunctionAbstract  $reflector
     * @return array
     */
    public function resolveMethodDependencies(array $parameters, ReflectionFunctionAbstract $reflector)
    {
        $instanceCount = 0;

        $values = array_values($parameters);

        /**
        得到方法的参数
        假设控制器调度是：controller->method()
        method被反射后，可以得到该method的参数
         **/
        foreach ($reflector->getParameters() as $key => $parameter) {
            
            //返回参数的值，值可能是个对象或是普通的参数

            //$parameter  该反向方法【类的函数】的参数
            //$parameters 参数数组
            $instance = $this->transformDependency(
                $parameter, $parameters
            );

            if (! is_null($instance)) {
                $instanceCount++;

                $this->spliceIntoParameters($parameters, $key, $instance);
            } elseif (! isset($values[$key - $instanceCount]) &&
                      $parameter->isDefaultValueAvailable()) {
                $this->spliceIntoParameters($parameters, $key, $parameter->getDefaultValue());
            }
        }

        return $parameters;
    }

    /**
     * Attempt to transform the given parameter into a class instance.
     *会得到反向类方法【函数】的参数，并得到参数的类名
     * 如果能得到参数的类名则会实例化返回对象
     * @param  \ReflectionParameter  $parameter
     * @param  array  $parameters
     * @return mixed
     */
    protected function transformDependency(ReflectionParameter $parameter, $parameters)
    {
        /**
        循环得到方法method
        controller->method(param1,param2)
        得到其param1的类名，如果是的话
         **/
        $class = $parameter->getClass();

        // If the parameter has a type-hinted class, we will check to see if it is already in
        // the list of parameters. If it is we will just skip it as it is probably a model
        // binding and we do not want to mess with those; otherwise, we resolve it here.
        if ($class && ! $this->alreadyInParameters($class->name, $parameters)) {
            return $parameter->isDefaultValueAvailable()//参数是否有默认值
                ? $parameter->getDefaultValue()
                : $this->container->make($class->name);//实例化参数类
        }
    }

    /**
     * Determine if an object of the given class is in a list of parameters.
     *
     * @param  string  $class
     * @param  array  $parameters
     * @return bool
     */
    protected function alreadyInParameters($class, array $parameters)
    {
        return ! is_null(Arr::first($parameters, function ($value) use ($class) {
            return $value instanceof $class;
        }));
    }

    /**
     * Splice the given value into the parameter list.
     *
     * @param  array  $parameters
     * @param  string  $offset
     * @param  mixed  $value
     * @return void
     */
    protected function spliceIntoParameters(array &$parameters, $offset, $value)
    {
        array_splice(
            $parameters, $offset, 0, [$value]
        );
    }
}
