<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL & ~E_DEPRECATED & ~E_USER_DEPRECATED);

require dirname(__DIR__, 6) . '/vendor/autoload.php';

use pascualmg\reactor\ddd\Infrastructure\scripts\Drafts\websocketServer\ChatMessageComponent;
use pascualmg\reactor\ddd\Infrastructure\scripts\Drafts\websocketServer\TestMessageComponent;
use Ratchet\Server\IoServer;
use Ratchet\WebSocket\WsServer;

$wsServer = new WsServer(new ChatMessageComponent());
$httpServer = new Ratchet\Http\HttpServer($wsServer);
$ioServer = IoServer::factory(
    $httpServer,
    8001,
    '127.0.0.1'
);


$ioServer->run();
