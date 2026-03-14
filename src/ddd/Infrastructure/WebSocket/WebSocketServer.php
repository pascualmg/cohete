<?php

namespace pascualmg\cohete\ddd\Infrastructure\WebSocket;

use Ratchet\Http\HttpServer;
use Ratchet\Server\IoServer;
use Ratchet\WebSocket\WsServer;
use React\EventLoop\LoopInterface;
use React\Socket\SocketServer;

class WebSocketServer
{
    public static function init(
        string $host,
        int    $port,
        LoopInterface $loop,
    ): void {
        $chat = new Chat();
        $wsServer = new WsServer($chat);
        $httpServer = new HttpServer($wsServer);

        $socket = new SocketServer(
            sprintf('%s:%d', $host, $port),
        );

        new IoServer($httpServer, $socket, $loop);

        echo "WebSocket server listening on " . $socket->getAddress() . PHP_EOL;
    }
}
