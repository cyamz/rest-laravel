<?php

namespace App\Libraries;

class WebSocketTool
{
    /** @var /WebSocket/Client */
    protected static $client = null;

    private static function request($type, $data)
    {
        $time = date('Y-m-d H:i:s');
        $sign = encrypt([
            'type' => $type,
            'time' => $time,
        ], true);

        $data['type'] = $type;
        $data['time'] = $time;
        $data['sign'] = $sign;

        $client = self::getClient();

        try {
            \Log::channel('websocket_client')->info(json_encode($data));
            $client->text(json_encode($data));
        } catch (\Throwable $th) {
            \Log::channel('websocket_client')->error($th->getMessage());
        }

        return '';
    }

    private static function getClient()
    {
        if (!self::$client || !self::$client->isConnected()) {
            self::$client = new \WebSocket\Client(env('WEBSOCKET_URI'));
        }

        return self::$client;
    }

    public static function pushOne($fds, $data)
    {
        if (!is_array($fds)) {
            $fds = [$fds];
        }

        foreach ($fds as $fd) {
            if ($fd) {
                self::request('push_one', [
                    'fd' => $fd,
                    'data' => $data,
                ]);
            }
        }
    }

    public static function pushAll($data)
    {
        self::request('push_all', [
            'data' => $data,
        ]);

        return 1;
    }

    public static function check($fd)
    {
        return self::request('check', [
            'fd' => $fd,
        ]);
    }

}
