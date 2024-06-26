<?php

use pascualmg\cohete\ddd\Infrastructure\scripts\chat\ConnectionPool;
use React\EventLoop\Loop;
use React\Socket\ConnectionInterface;
use React\Socket\SocketServer;

require dirname(__DIR__, 5) . '/vendor/autoload.php';


$loop = Loop::get();

$socket = new SocketServer('0.0.0.0:11334', [], $loop);

$connectionsPool = new ConnectionPool();
$socket->on('connection', function (ConnectionInterface $connection) use ($connectionsPool) {
    $connectionsPool->add($connection);
});


$loop->run();
