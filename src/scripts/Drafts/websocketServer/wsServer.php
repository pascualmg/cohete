<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL & ~E_DEPRECATED & ~E_USER_DEPRECATED);
require "../../../../vendor/autoload.php";

use pascualmg\reactor\ddd\Infrastructure\Repository\Post\MysqlPostRepository;
use Ratchet\ConnectionInterface;
use Ratchet\MessageComponentInterface;
use Ratchet\Server\IoServer;
use Ratchet\WebSocket\WsServer;
use React\EventLoop\Loop;

$messageComponent = new class () implements MessageComponentInterface {
    public function onOpen(ConnectionInterface $conn): void
    {
        echo 'conexion entrante';
        var_dump($conn);
    }
    public function onClose(ConnectionInterface $conn): void
    {
        var_dump('close');
    }

    public function onError(ConnectionInterface $conn, \Exception $e): void
    {
        var_dump($e);
    }

    public function onMessage(ConnectionInterface $from, $msg): void
    {
        (new MysqlPostRepository())
            ->findAll()
            ->then(function (array $result) use ($from) {
                $loop = Loop::get();
                $sendPostsTimer = $loop->addPeriodicTimer(1, function () use ($from, $result) {
                    foreach ($result as $post) {
                        $from->send((string)$post);
                    }
                });
                $loop->addTimer(10, function () use ($sendPostsTimer, $loop) {
                    $loop->cancelTimer($sendPostsTimer);
                });
            });
    }
};
$wsServer = new WsServer($messageComponent);
$httpServer = new Ratchet\Http\HttpServer($wsServer);
$ioServer = IoServer::factory(
    $httpServer,
    8001,
    '127.0.0.1'
);


$ioServer->run();
