<?php

namespace App\Listeners;

use App\Events\SendSmsEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

//继承ShouldQueue 则为异步监听
class SendSmsListener implements ShouldQueue
{
    /**
     * 任务将被发送到的连接的名称。
     *
     * @var string|null
     */
    // public $connection = 'redis';

    /**
     * 任务将被发送到的队列的名称。
     *
     * @var string|null
     */
    // public $queue = 'listeners';

    /**
     * 任务被处理的延迟时间（秒）。
     *
     * @var int
     */
    // public $delay = 60;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    // public function handleTestEvent1($event)
    // {
    //     echo 'This is TestEvent', $event->a, "<br>";
    // }

    // public function subscribe($event)
    // {
    //     // 订阅
    //     $event->listen(
    //         [TestEvent1::class],
    //         [TestSubscriber::class, 'handleTestEvent1']
    //     );
    // }

    /**
     * Handle the event.
     *
     * @param  \App\Events\SendSmsEvent  $event
     * @return void
     */
    public function handle(SendSmsEvent $event)
    {
        //可改成异步+分发
        \App\Jobs\ProcessSendSms::dispatch($event);
    }
}
