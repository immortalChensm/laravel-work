<?php
/**
 * Created by PhpStorm.
 * User: 1655664358@qq.com
 * Date: 2018/11/18
 * Time: 18:00
 */
/**
框架在启动的时候会分别实例化所有的服务提供类
其中路由服务类【RouteServiceProvider】会被实例化，并且运行boot方法
由于使用Route伪装类【伪装Router类路由器类】此时触发框架的静态魔术方法__callStatic
完成Application[router]实例化并运行middleware方法，此时实例化路由注册器RouteRegistrar
并把middleware=web(Route::middleware(web))保存在RouteRegistrar的attributes[]数组里
同时返回路由注册器对象继承调用namespace(应用的根命名空间)方法，同样保存在路由注册器的attribute[]里

此时加载路由定义文件web.php，并运行，同理会触发伪装类的静态魔术方法实例化Router路由器对象
然后运行group方法里的匿名函数，如果用的话
否则运行Router->get/post....方法

每次运行get,post,any,delete等方法都会将参数遇到为Route对象返回，并保存在RouteCollection路由对象集合池里

当请求时，先匹配请求方式，得到对应的路由集合【该请求方式的集合】，再依次匹配请求的uri得到对应的路由对象Route
然后得到Route对象的中间件【包括路由中间件，控制器中间件】，再运行Route->run，从而运行控制器调度器ControllerDispatcher
完成控制器的运行并响应
在控制器运行时会运行全局中间件，路由中间件，控制器中间件类
 **/