<?php

require '../../vendor/autoload.php';

use Passh\Rx\httpserver\FilePostRepository;
use Passh\Rx\httpserver\JsonRouterLoader;
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

$routerLoader = new JsonRouterLoader('routes.json');
$routerLoader->loadinto($router);

$router->addRoute('GET', 'foo', $manejeitor);
$echoAndModifyPathMiddleware = function (ServerRequestInterface $request, callable $next) {
    // Imprime la ruta actual
    echo 'Ruta actual: ' . $request->getUri()->getPath() . PHP_EOL;

    // Añade "/foo" al final de la ruta
    $newPath = $request->getUri()->getPath() . '/foo';
    $uri = $request->getUri()->withPath($newPath);
    $request = $request->withUri($uri);

    // Pasa la solicitud modificada al siguiente middleware
    return $next($request);
};
$httpServer = new HttpServer(
    $echoAndModifyPathMiddleware,
    function (ServerRequestInterface $request) use ($router) {
    try {
        return $router->handleRequest($request);
    } catch (\Throwable $exception) {
        return new Response(
            500,
            ['Content-Type' => 'application/json'],
            json_encode(['error' => $exception->getMessage()] )
        );
    }
});
$httpServer->listen($port8000);
$httpServer->on(
    'error',
    function (Throwable $error) {
        echo 'Error: ' . $error->getMessage() . PHP_EOL;
    }
);

$port8000->on('connection', function (\React\Socket\ConnectionInterface $connection) {
    $connection->on('data', 'var_dump');
});

$loop->run();