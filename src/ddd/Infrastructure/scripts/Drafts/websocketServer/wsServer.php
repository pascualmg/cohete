<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL & ~E_DEPRECATED & ~E_USER_DEPRECATED);

require dirname(__DIR__, 6) . '/vendor/autoload.php';

use pascualmg\cohete\ddd\Infrastructure\scripts\Drafts\websocketServer\Chat;
use pascualmg\cohete\ddd\Infrastructure\scripts\Drafts\websocketServer\TestMessageComponent;
use Ratchet\Server\IoServer;
use Ratchet\WebSocket\WsServer;

$wsServer = new WsServer(
    new Chat()
);

$httpServer = new Ratchet\Http\HttpServer($wsServer);

$ioServer = IoServer::factory(
    $httpServer,
    8001,
    '0.0.0.0'
);

$ioServer->run();
