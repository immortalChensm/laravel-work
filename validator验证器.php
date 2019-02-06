<?php
/**
 * Created by PhpStorm.
 * User: 1655664358@qq.com
 * Date: 2019/2/6
 * Time: 21:48
 */
laravel的验证实现原理【表单验证】

1、 Illuminate\Foundation\Providers\FoundationServiceProvider::class
由本类完成FormRequestServiceProvider 表单的注册，主要以key,value[匿名]
将其保存在 Application下的成员属性 $resolvingCallbacks = [FormRequest::class];$afterResolvingCallbacks = [ValidatesWhenResolved::class];

当http请求从路由集合【路由池】里检索到路由，开始由控制器调度器进行调度
同时由反映机制反射控制器的方法【而方法的参数是FromRequest::class】从而在实例它的时候
会去检索$resolvingCallbacks,$afterResolvingCallbacks的方法并运行
    从而得到当前的请求对象，当前的验证规则，当前的验证信息并将其保存在Validator验证器里

    首先先验证认证【authorize】方法，再去验证attributes规则

    未分析完。。。。
