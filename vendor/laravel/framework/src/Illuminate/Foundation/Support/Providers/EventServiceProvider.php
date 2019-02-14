<?php

namespace Illuminate\Foundation\Support\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event handler mappings for the application.
     *
     * @var array
     */
    protected $listen = [];

    /**
     * The subscriber classes to register.
     *
     * @var array
     */
    protected $subscribe = [];

    /**
     * Register the application's event listeners.
     *注册事件监听池
     * @return void
     */
    public function boot()
    {
        /**
         * Illuminate\Events\Dispatcher  = Event
         */
        foreach ($this->listens() as $event => $listeners) {
            foreach ($listeners as $listener) {
                //Event是个伪装【所谓的门面】，会触发其基类找到events【events对应的注册在Application的registerBaseProvider里实现】然后实例化
                //事件调度器并执行listen方法
                //同时将事件对应的监听器类进行实例化并保存在事件listeners池里
                Event::listen($event, $listener);
            }
        }

        foreach ($this->subscribe as $subscriber) {
            Event::subscribe($subscriber);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function register()
    {
        //
    }

    /**
     * Get the events and handlers.
     *
     * @return array
     */
    public function listens()
    {
        return $this->listen;
    }
}
