<?php
/**
 * Created by PhpStorm.
 * User: 1655664358@qq.com
 * Date: 2019/2/13
 * Time: 18:30
 */
laravel在启动的时候将会由Bus和Queue服务提供者进行注册

当用户进行调度的时候【即任务类->dispatch()】时，会自动生成一个job任务对象
任务对象【一般会返回PendingDispatch】当实例化后，析构函数会运行
 app(Dispatcher::class)->dispatch($this->job);
此时将实例化调度器，并根据队列配置文件queue.php文件里的默认队列连接，实例化不同的
队列连接器【如database,redis等队列连接器】
然后将当前的任务job做处理【一般是json字符串】
如果列队连接是数据库则先创造job表并生成此表，生成的queue[表结构]名称和对应的任务
将会保存在数据库里

用户php artisan queue:work 时【将从命令池里检索workerCommand对象】进行处理handle
，同样也会得到队列连接器，如果是数据库的话则会从数据库里取出任务并运行


