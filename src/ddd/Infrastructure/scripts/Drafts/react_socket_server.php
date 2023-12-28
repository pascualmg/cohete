<?php

require dirname(__DIR__, 5) . '/vendor/autoload.php';

use React\EventLoop\Loop;
use React\Socket\ConnectionInterface;

$uri = '0.0.0.0:11334';
$loop = Loop::get();

$socket = new React\Socket\SocketServer(
    $uri,
    [],
    $loop
);

$socket->on(
    'connection',
    function (ConnectionInterface $conn) {
        $conn->on('data', function ($data) {
            echo $data;
        });
    }
);
