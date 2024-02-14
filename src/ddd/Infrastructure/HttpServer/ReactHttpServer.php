<?php

namespace pascualmg\reactor\ddd\Infrastructure\HttpServer;

use FriendsOfReact\Http\Middleware\Psr15Adapter\PSR15Middleware;
use Middlewares\ClientIp;
use pascualmg\reactor\ddd\Infrastructure\HttpServer\Kernel\Kernel;
use React\EventLoop\Loop;
use React\EventLoop\LoopInterface;
use React\Http\HttpServer;
use React\Socket\ConnectionInterface;
use React\Socket\SecureServer;
use React\Socket\SocketServer;

class ReactHttpServer
{
    public static function init(
        string $host,
        string $port,
        ?LoopInterface $loop = null,
        bool $isDevelopment = false,
    ): void {
        if (null === $loop) {
            $loop = Loop::get();
        }


        $port8000 = new SocketServer(
            sprintf("%s:%s", $host, $port),
            //      [
            //               'tls' => [
            //                  'local_cert' => __DIR__ . '/localhost.pem'
            //             ]
            //        ],
            //       $loop
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

        if ($isDevelopment) {
            $httpServer->on(
                'error',
                'var_dump'
            );

            $port8000->on('connection', function (ConnectionInterface $connection) {
                $connection->on('data', 'var_dump');
            });
        }
    }
}
