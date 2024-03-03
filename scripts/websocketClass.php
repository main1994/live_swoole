<?php
//laravel
use Illuminate\Http\Request;
use Illuminate\Contracts\Http\Kernel;

define('LARAVEL_START', microtime(true));

class websocketClass
{
    private $kernel;
    private $config;

    public $ws = null;
    public $redis = null;
    const PREFIX = 'laravel_database_live';
    public function __construct()
    {
        $this->config = parse_ini_file('config.ini', true);
        //连接本地的 Redis 服务
        $this->redis = new Redis();
        $this->redis->connect($this->config['redis']['host'], $this->config['redis']['port']);
        //服务器重启之后需要清理之前的值
        $clients = $this->redis->smembers(self::PREFIX);
        if (count($clients)) {
            foreach ($clients as $fd) {
                $this->redis->srem(self::PREFIX, $fd);
            }
        }
        //websocket服务器
        $this->ws =  new Swoole\WebSocket\Server($this->config['chart_websocket']['host'], $this->config['chart_websocket']['port']);
        $this->ws->listen($this->config['live_websocket']['host'], $this->config['live_websocket']['port'], SWOOLE_SOCK_TCP);
        $this->ws->set(array(
            'reactor_num'   => 2,     // 线程数
            'worker_num'    => 4,     // 进程数
            'task_worker_num' => 4,
            "enable_coroutine" => true, // 必须开启协程异步化
        ));
        $this->ws->on('WorkerStart', [$this, 'onWorkerStart']);
        $this->ws->on('Request', [$this, 'onRequest']); //websocket继承http，所以可用该方法
        $this->ws->on('Open', [$this, 'onOpen']);
        $this->ws->on('Message', [$this, 'onMessage']);
        $this->ws->on('Task', [$this, 'onTask']);
        $this->ws->on('Finish', [$this, 'onFinish']);
        $this->ws->on('Close', [$this, 'onClose']);
        $this->ws->start();
    }

    public function onWorkerStart($server,  $workerId)
    {
        //加载laravel基础库
        if (file_exists($maintenance = __DIR__ . '/../storage/framework/maintenance.php')) {
            require $maintenance; //如果放request中，则需要改成require_once
        }
        require __DIR__ . '/../vendor/autoload.php'; //如果放request中，则需要改成require_once
        $app = require_once __DIR__ . '/../bootstrap/app.php';
        $this->kernel = $app->make(Kernel::class);
    }

    public function onRequest($request, $response)
    {
        //全局变量数据转换
        $_SERVER = [];
        if (isset($request->server)) {
            foreach ($request->server as $k => $v) {
                $_SERVER[strtoupper($k)] = $v;
            }
        }

        if (isset($request->header)) {
            foreach ($request->header as $k => $v) {
                $_SERVER[strtoupper($k)] = $v;
            }
        }

        $_COOKIE = [];
        if (isset($request->cookie)) {
            foreach ($request->cookie as $k => $v) {
                $_COOKIE[$k] = $v;
            }
        }

        $_GET = [];
        if (isset($request->get)) {
            foreach ($request->get as $k => $v) {
                $_GET[$k] = $v;
            }
        }

        $_POST = [];
        if (isset($request->post)) {
            foreach ($request->post as $k => $v) {
                $_POST[$k] = $v;
            }
        }

        $_POST['http_server'] = $this->ws;

        $_FILES = [];
        if (isset($request->files)) {
            foreach ($request->files as $k => $v) {
                $_FILES[$k] = $v;
            }
        }

        //在项目中加载框架
        ob_start();
        $laravel_response = $this->kernel->handle(
            $laravel_request = Request::capture()
        )->send();
        $this->kernel->terminate($laravel_request, $laravel_response);
        $res = ob_get_contents();
        ob_end_clean();
        $response->end($res);
    }

    /** 
     * 监听ws连接事件
     **/
    public function onOpen(Swoole\WebSocket\Server $ws, $request)
    {
        $this->redis->sadd(self::PREFIX, $request->fd);
        // swoole_timer_tick(50, function () {
        //     $this->ws->task([
        //         'type' => 'login'
        //     ]);
        // });
        echo "server: handshake success with fd{$request->fd}\n";
    }

    public function onMessage(Swoole\WebSocket\Server $ws, $frame)
    {
        echo "receive from {$frame->fd}:{$frame->data},opcode:{$frame->opcode},fin:{$frame->finish}\n";
        // $server->connections 遍历所有websocket连接用户的fd，给所有用户推送
        foreach ($this->ws->ports[0]->connections as $fd) {
            // 需要先判断是否是正确的websocket连接，否则有可能会push失败
            if ($this->ws->isEstablished($fd)) {
                $this->ws->push($fd, $frame->data);
            }
        }
    }

    //处理异步任务(此回调函数在task进程中执行)。
    public function onTask($serv, $task_id, $reactor_id, $data)
    {
        echo "New AsyncTask[id={$task_id}]" . PHP_EOL;
        //返回任务执行的结果
        $serv->finish("{$data} -> OK");
    }

    //处理异步任务的结果(此回调函数在worker进程中执行)。
    public function onFinish($serv, $task_id, $data)
    {
        echo "AsyncTask[{$task_id}] Finish: {$data}" . PHP_EOL;
    }

    //监听连接关闭事件。
    public function onClose($server, $fd)
    {
        $this->redis->srem(self::PREFIX, $fd);
        echo "Client: {$fd} Close.\n";
    }
}

$server = new websocketClass();
