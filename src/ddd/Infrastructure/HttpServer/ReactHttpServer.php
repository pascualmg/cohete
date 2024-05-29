<?php

namespace pascualmg\cohete\ddd\Infrastructure\HttpServer;

use FriendsOfReact\Http\Middleware\Psr15Adapter\PSR15Middleware;
use Middlewares\ClientIp;
use pascualmg\cohete\ddd\Infrastructure\HttpServer\Kernel\Kernel;
use React\EventLoop\Loop;
use React\EventLoop\LoopInterface;
use React\Http\HttpServer;
use React\Socket\ConnectionInterface;
use React\Socket\SecureServer;
use React\Socket\SocketServer;

class ReactHttpServer
{
    /**
     * Initializes the async server.
     *
     * @param string $host The host to bind the server to.
     * @param string $port The port to bind the server to.
     * @param LoopInterface|null $loop The event loop to use. If not provided, the default loop will be used.
     * @param bool $isDevelopment Whether the server is in development mode.
     *
     * @return void
     */
    public static function init(
        string $host,
        string $port,
        ?LoopInterface $loop = null,
        bool $isDevelopment = false,
    ): void {
        if (null === $loop) {
            $loop = Loop::get();
        }

        //if you want ssl check doc
        $socket = new SocketServer(
            sprintf("%s:%s", $host, $port),
        );

        //https://github.com/friends-of-reactphp/http-middleware-psr15-adapter
        $clientIPMiddleware = new PSR15Middleware(
            (new ClientIp())
        );

        $requestDumperMiddleware = new PSR15Middleware(
            new RequestDumper()
        );

        $responseDumperMiddleware =  new PSR15Middleware(
            new ResponseDumper()
        );
        $httpServer = new HttpServer(
            $clientIPMiddleware,
            $requestDumperMiddleware,
            $responseDumperMiddleware,
            new Kernel()
        );

        $httpServer->listen($socket);
        echo "server listening on " . $socket->getAddress();

        $httpServer->on(
            'error',
            'var_dump'
        );


    }
}
