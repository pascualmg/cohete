<?php

namespace pascualmg\reactor\ddd\Infrastructure\scripts\Drafts;

use React\EventLoop\Loop;
use React\Socket\ConnectionInterface;
use React\Socket\SocketServer;
use Rx\Disposable\CallbackDisposable;
use Rx\Observable;
use Rx\ObserverInterface;
use Throwable;

class ObservableSocket
{
    public static function of(string $port, string $host = '0.0.0.0'): Observable
    {
        $uri = "$host:$port";
        echo "listening on $uri";

        return Observable::create(
            static function (ObserverInterface $observer) use ($uri) {
                $reactSocketServer = null;
                try {
                    $reactSocketServer = new SocketServer($uri, [], Loop::get());
                } catch (Throwable $throwable) {
                    $observer->onError($throwable);
                }
                if (null === $reactSocketServer) {
                    return;
                }

                $reactSocketServer->on('connection', static function (ConnectionInterface $connection) use ($observer) {
                    echo "Conexion Entrante...desde " . $connection->getRemoteAddress();
                    $connection->on('data', static function ($data) use ($observer, $connection) {
                        $observer->onNext([$data, $connection]);
                    });

                    $connection->on('error', function ($error) use ($observer) {
                        $observer->onError($error);
                    });

                    $connection->on('close', function () use ($observer) {
                        $observer->onCompleted();
                    });

                    new CallbackDisposable(function () use ($connection) {
                        $connection->close();
                    });
                });
            }
        );
    }


}
