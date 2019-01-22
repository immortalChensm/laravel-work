<?php
/**
 * Created by PhpStorm.
 * User: 1655664358@qq.com
 * Date: 2019/1/21
 * Time: 14:03
 */
本框架仅用于注释分析
额外的运行测试为：lessons项目

1 框架的扩展包安装
当运行composer require packageName时
composer会将包的信息写入/vendor/composer/installed.json文件

2、针对laravel框架，由于composer.json配置了脚本scripts指令
"scripts": {
    "post-root-package-install": [
        "@php -r \"file_exists('.env') || copy('.env.example', '.env');\""
    ],
        "post-create-project-cmd": [
        "@php artisan key:generate"
    ],
        "post-autoload-dump": [
        "Illuminate\\Foundation\\ComposerScripts::postAutoloadDump",
        "@php artisan package:discover"
    ]
    },
    当安装完成时，将会触发scripts配置里的post-autoload-dump指令
    从而运行
    "Illuminate\\Foundation\\ComposerScripts::postAutoloadDump",文件清理
        "@php artisan package:discover"

        package:discover将会运行PackageDiscoverCommand类的handle方法
        然后读取installed.json文件的所有包信息，并把包名，包的服务提供者类，包的服务提供类别名类
        以key,value形式写入boostrap/cache/package.php文件里



3、php artisan list 命令运行大体流程
    A、当键入php artisan list时会自动检索到ListCommand类并实例化
    当实例化时，其基类的Command会执行构造函数完成命令的基本配置即run configure()方法
    完成命令名称设置，命令描述设置，命令InputDefinition对象【其内有InputArgument,InputOption】
   B、同时运行描述器【会注册xml,json,text,md】描述器
   C、从描述器池里检索某个描述器，并获取Console Application人命令列表展示出框架所拥有的命令
