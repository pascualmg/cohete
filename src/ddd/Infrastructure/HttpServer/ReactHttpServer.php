<?php

namespace pascualmg\reactor\ddd\Infrastructure\HttpServer;

use FriendsOfReact\Http\Middleware\Psr15Adapter\PSR15Middleware;
use Middlewares\ClientIp;
use pascualmg\reactor\ddd\Infrastructure\HttpServer\Kernel\Kernel;
use React\EventLoop\Loop;
use React\EventLoop\LoopInterface;
use React\Http\HttpServer;
use React\Socket\SocketServer;
use Throwable;

class ReactHttpServer
{
    public static function init(
        string $host,
        string $port,
        ?LoopInterface $loop = null
    ): void {

        if(null === $loop) {
            $loop = Loop::get();
        }

        $port8000 = new SocketServer(
            sprintf("%s:%s", $host, $port),
            [],
            $loop
        );

        //https://github.com/friends-of-reactphp/http-middleware-psr15-adapter
        $clientIPMiddleware = new PSR15Middleware(
            (new ClientIp())
        );

        $httpServer = new HttpServer(
            $clientIPMiddleware,
            new Kernel()
        );

        $httpServer->listen($port8000);
        echo "server listening on " . $port8000->getAddress();

        $httpServer->on(
            'error',
            function (Throwable $error) {
                echo 'Error: ' . $error->getMessage() . PHP_EOL;
            }
        );

        //        $port8000->on('connection', function (ConnectionInterface $connection) {
        //            $connection->on('data', 'var_dump');
        //        });
    }
}
