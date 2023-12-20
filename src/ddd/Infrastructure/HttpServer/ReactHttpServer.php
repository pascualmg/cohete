<?php

namespace Pascualmg\Rx\ddd\Infrastructure\HttpServer;

use FastRoute\Dispatcher;
use FastRoute\RouteCollector;
use FriendsOfReact\Http\Middleware\Psr15Adapter\PSR15Middleware;
use Middlewares\ClientIp;
use Pascualmg\Rx\ddd\Domain\Bus\Bus;
use Pascualmg\Rx\ddd\Domain\Entity\PostRepository;
use Pascualmg\Rx\ddd\Infrastructure\Bus\ReactEventBus;
use Pascualmg\Rx\ddd\Infrastructure\Repository\Post\MysqlPostRepository;
use Pascualmg\Rx\ddd\Infrastructure\RequestHandler\TestController;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use React\EventLoop\Loop;
use React\EventLoop\LoopInterface;
use React\Http\HttpServer;
use React\Http\Message\Response;
use React\Promise\Deferred;
use React\Promise\Promise;
use React\Promise\PromiseInterface;
use React\Socket\ConnectionInterface;
use React\Socket\SocketServer;
use Throwable;

use function FastRoute\simpleDispatcher;

class ReactHttpServer
{
    public static function init(LoopInterface $loop, ContainerInterface $container): void
    {
        $port8000 = new SocketServer(
            '127.0.0.1:8000',
            [],
            $loop
        );


        self::configureContainer($container, $loop);

        $clientIPMiddleware = new PSR15Middleware(
            (new ClientIp())
        );

        $dispatcher = simpleDispatcher(function (RouteCollector $r) {
            $r->addRoute('GET', '/foo', TestController::class);
        });


        $httpServer = new HttpServer(
            $clientIPMiddleware,
            function (ServerRequestInterface $request) use ($container, $dispatcher) {
                try {
                    return self::AsyncHandleRequest(
                        $request,
                        $container,
                        $dispatcher
                    )
                        ->then(function (ResponseInterface $response) {
                            return $response;
                        })
                        ->catch(function (Throwable $exception) {
                            return new Response(
                                409,
                                ['Content-Type' => 'application/json'],
                                self::toJson($exception)
                            );
                        });
                } catch (Throwable $exception) {
                    // Capture only router configuration errors &
                    // other exceptions not related to request handling
                    return new Response(
                        500,
                        ['Content-Type' => 'application/json'],
                        self::toJson($exception)
                    );
                }
            }
        );

        $httpServer->listen($port8000);
        echo "server listening on " . $port8000->getAddress();

        $httpServer->on(
            'error',
            function (Throwable $error) {
                echo 'Error: ' . $error->getMessage() . PHP_EOL;
            }
        );

        $port8000->on('connection', function (ConnectionInterface $connection) {
            $connection->on('data', 'var_dump');
        });
    }

    /**
     * @throws \JsonException
     */
    private static function toJson(Throwable $exception): string
    {
        return json_encode([
            'name' => $exception::class,
            'message' => $exception->getMessage(),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'trace' => array_map('json_encode', $exception->getTrace())
        ], JSON_THROW_ON_ERROR);
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public static function AsyncHandleRequest(
        ServerRequestInterface $request,
        ContainerInterface $container,
        Dispatcher $dispatcher
    ): PromiseInterface {
        $deferred = new Deferred();

        $method = strtoupper($request->getMethod());
        $uri = $request->getUri()->getPath();

        //https://github.com/nikic/FastRoute
        $routeInfo = $dispatcher->dispatch($method, $uri);

        switch ($routeInfo[0]) {
            case Dispatcher::NOT_FOUND:
                // ... 404 Not Found
                $deferred->resolve(
                    new Response(404, ['Content-Type' => 'text/plain'], 'Route not found')
                );
                break;
            case Dispatcher::METHOD_NOT_ALLOWED:
                // ... 405 Method Not Allowed
                $allowedMethods = json_encode($routeInfo[1], JSON_THROW_ON_ERROR);
                $deferred->resolve(
                    new Response(405, ['Content-Type' => 'text/plain'], "Method not allowed, use  $allowedMethods " )
                );
                break;
            case Dispatcher::FOUND:
                [$_, $handlerName, $params] = $routeInfo;

                //core
                $response = $container->get($handlerName)($request, ...$params);

                $deferred->resolve(
                    $response instanceof PromiseInterface ? $response : self::wrapWithPromise($response)
                );
                break;
        }

        return $deferred->promise();
    }

    private static function configureContainer(ContainerInterface $container, LoopInterface $loop): void
    {
        $container->set(LoopInterface::class, fn() => Loop::get());

        $container->set(
            Bus::class,
            fn ()  => new ReactEventBus(
                $container->get(LoopInterface::class)
            )
        );

        $container->set(PostRepository::class , MysqlPostRepository::class);


    }

    private static function wrapWithPromise($response): PromiseInterface
    {
        return new Promise(function ($resolve, $_) use ($response) {
            $resolve($response);
        });
    }
}
