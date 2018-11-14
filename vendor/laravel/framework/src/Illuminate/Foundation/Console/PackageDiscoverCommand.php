<?php

namespace Illuminate\Foundation\Console;

use Illuminate\Console\Command;
use Illuminate\Foundation\PackageManifest;

class PackageDiscoverCommand extends Command
{
    /**
     * The console command signature.
     *
     * @var string
     */
    protected $signature = 'package:discover';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Rebuild the cached package manifest';

    /**
     * Execute the console command.
     *
     * @param  \Illuminate\Foundation\PackageManifest  $manifest
     * @return void
     */
    public function handle(PackageManifest $manifest)
    {
        /**
        运行php artisan package:discover时运行此文件的方法
        针对laravel框架，安装第三方包时会自动更新boostrap/cache/下的相关类库包配置文件
         **/
        file_put_contents("discover.log","运行包查找命令");
        $manifest->build();

        foreach (array_keys($manifest->manifest) as $package) {
            $this->line("<info>Discovered Package:</info> {$package}");
        }

        $this->info('Package manifest generated successfully.');
    }
}
