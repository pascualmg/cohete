<?php

require '../../vendor/autoload.php';

use Passh\Rx\httpserver\FilePostRepository;
use React\EventLoop\Loop;
use React\Http\HttpServer;
use React\Http\Message\Response;
use React\Socket\SocketServer;

$loop = Loop::get();

/**
 * @param \Psr\Http\Message\ServerRequestInterface $request
 * @return Response
 */
function handle(\Psr\Http\Message\ServerRequestInterface $request): Response
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


    return $jsonResponse(200, $allPosts);
}

$httpServer = new HttpServer(function (\Psr\Http\Message\ServerRequestInterface $request) use ($loop) {
    return handle($request);
});

$port8000 = new SocketServer(
    '127.0.0.1:8000',
    [],
    $loop
);


$port8000->on('connection', function (\React\Socket\ConnectionInterface $connection) {
    $connection->on('data', 'var_dump');
});

$httpServer->listen($port8000);


$loop->run();