<?php

namespace Pascualmg\Rx\ddd\Infrastructure\HttpServer;

use FastRoute\Dispatcher;
use FastRoute\RouteCollector;
use FriendsOfReact\Http\Middleware\Psr15Adapter\PSR15Middleware;
use Middlewares\ClientIp;
use Pascualmg\Rx\ddd\Infrastructure\PSR11\ContainerFactory;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use React\EventLoop\Loop;
use React\Http\HttpServer;
use React\Http\Message\Response;
use React\Promise\Deferred;
use React\Promise\Promise;
use React\Promise\PromiseInterface;
use React\Socket\SocketServer;
use Throwable;

use function FastRoute\simpleDispatcher;

class ReactHttpServer
{
    public static function init(
        string $jsonRoutesPath,
        string $host = '0.0.0.0',
        string $port = '8000',
    ): void {
        $loop = Loop::get();
        $container = ContainerFactory::create();

        $port8000 = new SocketServer(
            sprintf("%s:%s", $host, $port),
            [],
            $loop
        );

        //https://github.com/friends-of-reactphp/http-middleware-psr15-adapter
        $clientIPMiddleware = new PSR15Middleware(
            (new ClientIp())
        );

        $dispatcher = self::loadRoutesFromJson($jsonRoutesPath);

        $httpServer = new HttpServer(
            $clientIPMiddleware,
            function (ServerRequestInterface $request) use (
                $container,
                $dispatcher
            ): PromiseInterface|ResponseInterface {
                try {
                    return self::AsyncHandleRequest(
                        request: $request,
                        container: $container,
                        dispatcher: $dispatcher
                    )->then(
                        onFulfilled: function (ResponseInterface $response): ResponseInterface {
                            return $response;
                        }
                    )->catch(
                        onRejected: function (Throwable $exception): ResponseInterface {
                            return new Response(
                                409,
                                ['Content-Type' => 'application/json'],
                                self::toJson($exception)
                            );
                        }
                    );
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

        //        $port8000->on('connection', function (ConnectionInterface $connection) {
        //            $connection->on('data', 'var_dump');
        //        });
    }

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
                    new Response(405, ['Content-Type' => 'text/plain'], "Method not allowed, use  $allowedMethods ")
                );
                break;
            case Dispatcher::FOUND:
                [$_, $httpRequestHandlerName, $params] = $routeInfo;

                //THE CORE, with autowiring in the __construct and a bit of magic
                $response = $container->get($httpRequestHandlerName)($request, $params);

                $deferred->resolve(
                    $response instanceof PromiseInterface ? $response : self::wrapWithPromise($response)
                );
                break;
        }

        return $deferred->promise();
    }


    private static function wrapWithPromise($response): PromiseInterface
    {
        return new Promise(function ($resolve, $_) use ($response) {
            $resolve($response);
        });
    }

    public static function loadRoutesFromJson(string $path): Dispatcher
    {
        self::assertNotEmpty($path);

        $routesFromJsonFile = self::parseJsonToArray($path);

        return simpleDispatcher(
            function (RouteCollector $r) use ($routesFromJsonFile) {
                // "foo, bar baz " => ["FOO", "BAR", "BAZ"]
                $toUpperWords = static fn (string $text): array => array_values(
                    array_filter(
                        preg_split("/[ ,]/", strtoupper($text)),
                        'strlen'
                    )
                );

                foreach ($routesFromJsonFile as $routeFromJsonFile) {
                    $r->addRoute(
                        $toUpperWords($routeFromJsonFile['method']),
                        $routeFromJsonFile['path'],
                        $routeFromJsonFile['handler']
                    );
                }
            }
        );
    }

    /**
     * @param string $path
     * @return void
     */
    public static function assertNotEmpty(string $path): void
    {
        if (empty($path)) {
            throw new \RuntimeException(
                "The path of the json File to load the routes is empty, maybe you have no .env or this variable is undefined? "
            );
        }
    }

    /**
     * @param string $path
     * @return mixed
     */
    public static function parseJsonToArray(string $path): array
    {
        $file = file_get_contents($path);
        try {
            $routesFromJsonFile = json_decode(
                $file,
                true,
                512,
                JSON_THROW_ON_ERROR
            );
        } catch (\JsonException $e) {
            throw new \RuntimeException(
                sprintf(
                    "Invalid JSON format while loading routes from file %s \n Error: %s",
                    $path,
                    $e->getMessage()
                )
            );
        }
        return $routesFromJsonFile;
    }
}
