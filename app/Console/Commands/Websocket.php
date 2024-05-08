<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class Websocket extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'websocket';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'this is websocket';

    /**
     * Create a new command instance.
     *
     * @return void
     */

    private $ws;

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $this->start();
    }

    public function start()
    {
        //创建WebSocket Server对象，监听端口,，通过nginx方向代理支持wss
        $this->ws = new \Swoole\WebSocket\Server('0.0.0.0', 8001);
        //监听WebSocket连接打开事件
        $this->ws->on('Open', function ($ws, $request) {
            \Log::channel('websocket')->info('connect: ' . $request->fd);
            $ws->push($request->fd, json_encode([
                'action' => 'connected',
                'id' => $request->fd
            ]));
        });

        //监听WebSocket消息事件
        $this->ws->on('Message', function ($ws, $frame) {
            if (strtolower($frame->data) == 'ping') {
                $ws->push($frame->fd, 'pong');
                $result = 1;
                goto end;
            }

            $result = -1;

            $frame_data = @json_decode($frame->data, true);
            if (!$frame_data) {
                $result = -2;
                goto end;
            }

            // 连接id
            $fd = intval($frame_data['fd']) ?? 0;

            // 加密签名
            $type = $frame_data['type'] ?? '';
            $time = $frame_data['time'] ?? '';
            if (!$type || !$time) {
                $result = -4;
                goto end;
            }

            // 传参data，需为json
            $data = $frame_data['data'] ?? [];
            $data_json = json_encode($data);
            
            switch ($type) {
                case 'push_one':
                    \Log::channel('websocket')->info('push one(fd=' . $fd . '): ' . $data_json);
                    if (!$fd) {
                        $result = -30;
                        goto end;
                    }
                    if (!$data) {
                        $result = -6;
                        goto end;
                    }
                    $result = $this->push($fd, $data_json);
                    break;
                case 'push_all':
                    if (!$data) {
                        $result = -6;
                        goto end;
                    }
                    \Log::channel('websocket')->info('push all: ' . $data_json);
                    $result = $this->pushAll($data_json);
                    break;
                case 'check':
                    \Log::channel('websocket')->info('check(id=' . $fd . '): ' . $data_json);
                    if (!$fd) {
                        $result = -31;
                        goto end;
                    }
                    $result = $this->checkOk($fd);
                    break;
                default:
            }

            end:
            \Log::channel('websocket')->info('fd: ' . ($ws->fd ?? 0) . ';res: ' . $result . ';data:' . $frame->data);

            echo $result;
        });

        $this->ws->on('request', function ($request, $response) {

        });

        //监听WebSocket连接关闭事件
        $this->ws->on('Close', function ($ws, $fd) {
            \Log::channel('websocket')->info('close: ' . $fd);
        });

        $this->ws->start();
    }

    private function push(int $fd, $data)
    {
        //广播推送
        if ($this->ws->isEstablished($fd)) {
            $this->ws->push($fd, $data);
            return 1;
        }

        return 0;
    }

    private function pushAll($data)
    {
        foreach ($this->ws->connections as $fd) {
            //广播推送
            $fd = intval($fd);
            if ($this->ws->isEstablished($fd)) {
                $this->ws->push($fd, $data);
            }
        }

        return 1;
    }

    private function checkOk(int $fd)
    {
        return $this->ws->isEstablished($fd) ? 1 : 0;
    }

}
