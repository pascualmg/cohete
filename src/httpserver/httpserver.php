<?php

require '../../vendor/autoload.php';

use Passh\Rx\httpserver\FilePostRepository;
use React\EventLoop\Loop;
use React\Http\HttpServer;
use React\Http\Message\Response;

$loop = Loop::get();

$httpServer = new HttpServer(function (\Psr\Http\Message\ServerRequestInterface $request) use ($loop) {
    $getParamOrNull = function (string $maybeParam, array $queryParams): ?string {
        return $queryParams()[$maybeParam] ?? null;
    };
    $queryParams = $request->getQueryParams();

    $postRepository = new FilePostRepository();

    $allPosts = $postRepository->findAll();


    return new Response(
        200,
        ['Content-Type' => 'application/json'],
        json_encode($allPosts)
    );
});

$port8000 = new \React\Socket\SocketServer(
    '127.0.0.1:8000',
    [],
    $loop
);

$httpServer->listen($port8000);


$loop->run();