<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require "../../../../vendor/autoload.php";

use Pascualmg\Rx\ddd\Infrastructure\Repository\Post\MysqlPostRepository;
use Ratchet\ConnectionInterface;
use Ratchet\MessageComponentInterface;
use Ratchet\Server\IoServer;
use Ratchet\WebSocket\WsServer;

$messageComponent = new class () implements MessageComponentInterface {
    public function onOpen(ConnectionInterface $conn)
    {
        var_dump('open');
    }

    public function onClose(ConnectionInterface $conn)
    {

        var_dump('close');
    }

    public function onError(ConnectionInterface $conn, \Exception $e)
    {
        var_dump($e);
    }

    public function onMessage(ConnectionInterface $from, $msg)
    {
        (new MysqlPostRepository())->findAll()->then(
            function ($result) use ($from) {
                foreach ($result as $post) {
                    $from->send((string)$post);
                }


            }
        );
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
