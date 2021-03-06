<?php

namespace Illuminate\Foundation\Bootstrap;

use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\Facades\Facade;
use Illuminate\Foundation\PackageManifest;
use Illuminate\Contracts\Foundation\Application;

class RegisterFacades
{
    /**
     * Bootstrap the given application.
     *
     * @param  \Illuminate\Contracts\Foundation\Application  $app
     * @return void
     */
    public function bootstrap(Application $app)
    {
        Facade::clearResolvedInstances();

        /**

        子门面类伪装具体的类
        门面基类保存Application类，用于实现实例化子门面类对应的具体类
         **/
        Facade::setFacadeApplication($app);

        AliasLoader::getInstance(array_merge(
            //得到配置文件app下类别名
            $app->make('config')->get('app.aliases', []),
            //得到缓存目录下的配置别名包即bootstrap/cache/packages.php
            $app->make(PackageManifest::class)->aliases()
        ))->register();

        /**
        将框架的所有门面【伪装类】配置文件里配置好的
        保存在$this->aliases[]数组里
        当调用门面【伪装类】会自动触发转换为其别名返回
         **/
    }
}
