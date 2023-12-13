<?php

require '../../vendor/autoload.php';

use Passh\Rx\httpserver\Router;
use Psr\Http\Message\ServerRequestInterface;
use React\EventLoop\Loop;
use React\Http\HttpServer;
use React\Http\Message\Response;
use React\Socket\ConnectionInterface;
use React\Socket\SocketServer;

$loop = Loop::get();

$port8000 = new SocketServer(
    '127.0.0.1:8000',
    [],
    $loop
);
echo "server listening on " . $port8000->getAddress();

$router = new Router();
$router->loadFromJson('routes.json');


function toJson(Throwable $exception): string
{
        return json_encode([
            'name' => $exception::class,
            'message' => $exception->getMessage(),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'trace' => array_map('json_encode', $exception->getTrace())
        ], JSON_THROW_ON_ERROR);
}

$httpServer = new HttpServer(
    function (ServerRequestInterface $request) use ($router) {
        try {
            return $router->handleRequest($request);
        } catch (Throwable $exception) {
            return new Response(
                409,
                ['Content-Type' => 'application/json'],
                toJson($exception)
            );
        }
    }
);
$httpServer->listen($port8000);
$httpServer->on(
    'error',
    function (Throwable $error) {
        echo 'Error: ' . $error->getMessage() . PHP_EOL;
    }
);

$port8000->on('connection', function (ConnectionInterface $connection) {
    $connection->on('data', 'var_dump');
});

$loop->run();
