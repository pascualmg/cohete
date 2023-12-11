<?php

require '../../vendor/autoload.php';

use Passh\Rx\httpserver\FilePostRepository;
use Psr\Http\Message\ServerRequestInterface;
use React\EventLoop\Loop;
use React\Http\HttpServer;
use React\Http\Message\Response;
use React\Socket\SocketServer;

$loop = Loop::get();

$manejeitor = function (ServerRequestInterface $request): Response
{
    $jsonResponse = function ($code, $body) {
        return new Response(
            $code,
            ['Content-Type' => 'application/json'],
            json_encode($body)
        );
    };


    if ($request->getMethod() === 'POST') {
        $bodyContents = json_decode($request->getBody()->getContents(), true);
        $bufferedBody = $request->getBody();
    }
    $getParamOrNull = function (string $maybeParam, array $queryParams): ?string {
        return $queryParams()[$maybeParam] ?? null;
    };
    $queryParams = $request->getQueryParams();

    $postRepository = new FilePostRepository();

    $allPosts = $postRepository->findAll();


    return $jsonResponse(
        200,
        $allPosts
    );
};

$port8000 = new SocketServer(
    '127.0.0.1:8000',
    [],
    $loop
);
echo "server listening on " . $port8000->getAddress();

$router = new \Passh\Rx\httpserver\Router();
$router->addRoute('GET', 'foo', $manejeitor);
$httpServer = new HttpServer(function (ServerRequestInterface $request) use ($router) {
    return $router->handleRequest($request);
});
$httpServer->listen($port8000);

$port8000->on('connection', function (\React\Socket\ConnectionInterface $connection) {
    $connection->on('data', 'var_dump');
});

$loop->run();