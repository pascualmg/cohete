<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require "../../../../vendor/autoload.php";

use Pascualmg\Rx\ddd\Infrastructure\Repository\Post\MysqlPostRepository;
use Ratchet\Server\IoServer;
use Ratchet\WebSocket\WsServer;
use React\Mysql\MysqlResult;

$messageComponent = new class () implements \Ratchet\MessageComponentInterface {
    public function onOpen(\Ratchet\ConnectionInterface $conn)
    {
        var_dump('open');
    }

    public function onClose(\Ratchet\ConnectionInterface $conn)
    {

        var_dump('close');
    }

    public function onError(\Ratchet\ConnectionInterface $conn, \Exception $e)
    {
        var_dump($e);
    }

    public function onMessage(\Ratchet\ConnectionInterface $from, $msg)
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
