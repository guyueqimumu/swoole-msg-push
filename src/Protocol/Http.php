<?php

namespace msgPush\Protocol;

use Swoole\WebSocket\Server as webSocketServer;


class Http
{
    public $server;

    public function __construct(array $options)
    {
        $this->server = new webSocketServer('0.0.0.0', '9501');

        $this->server->on("request", array($this, 'request'));

        $this->server->on("message", array($this, 'message'));
    }

    public function setHeader(\Swoole\Http\Response $response)
    {
        $response->header('Access-Control-Allow-origin', "*");
    }

    public function request(\Swoole\Http\Request $request, \Swoole\Http\Response $response)
    {
        list($controller, $action) = explode('/', trim($request->server['request_uri'], '/'));
        $class = "\\msgPush\\Api\\Controller\\" . ucfirst($controller);
        $this->setHeader($response);
        $data = (new $class($request))->$action($response);
        $this->broadcast($request->post['content']);
        $response->end("{\"code\":\"ok\"}");
    }


    /**
     * 来着websocket协议收到消息触发事件
     * @param webSocketServer $server
     * @param \Swoole\Websocket\Frame $frame
     */
    public function message(\Swoole\Websocket\Server $server, \Swoole\Websocket\Frame $frame)
    {
        echo "receive from {$frame->fd}:{$frame->data},opcode:{$frame->opcode},fin:{$frame->finish}\n";
    }


    public function start()
    {
        $this->server->start();
    }
    /**
     * 广播
     * @param $msg
     */
    public function broadcast($msg)
    {
        foreach ($this->server->connection_list() as $fd) {
            if($this->server->isEstablished ($fd)){
                $this->server->push($fd, $msg);
            }
        };
    }

}