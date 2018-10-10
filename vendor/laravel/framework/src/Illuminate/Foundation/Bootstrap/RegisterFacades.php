<?php

namespace Illuminate\Foundation\Bootstrap;

use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\Facades\Facade;
use Illuminate\Foundation\PackageManifest;
use Illuminate\Contracts\Foundation\Application;
//注册门面类
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

        Facade::setFacadeApplication($app);

        //将app.php里的类别名放在别名加载器里的alias[]数组里保存
        AliasLoader::getInstance(array_merge(
            //得到配置文件的类别名数组即app.php里的别名
            $app->make('config')->get('app.aliases', []),
            $app->make(PackageManifest::class)->aliases()
        ))->register();
        //然后进行注册，实际上注册将加载器的load方法，而load方法是调用php的spl_autoload_register方法
        //当实例化这些静态类的时候，会触发load方法完成class_alias别名转换并返回类的别名
    }
}
