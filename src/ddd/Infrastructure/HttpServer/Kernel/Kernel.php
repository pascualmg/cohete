<?php

namespace pascualmg\reactor\ddd\Infrastructure\HttpServer\Kernel;

use FastRoute\Dispatcher;
use pascualmg\reactor\ddd\Infrastructure\HttpServer\JsonResponse;
use pascualmg\reactor\ddd\Infrastructure\HttpServer\Router\Router;
use pascualmg\reactor\ddd\Infrastructure\PSR11\ContainerFactory;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use React\Http\Message\Response;
use React\Promise\Deferred;
use React\Promise\Promise;
use React\Promise\PromiseInterface;
use Throwable;

/**
 * The Kernel class represents the core of the application and handles incoming HTTP requests.
 * It is responsible for processing the request and returning an appropriate response.
 */
class Kernel
{
    public function __invoke(ServerRequestInterface $request): PromiseInterface|ResponseInterface
    {

        $container = ContainerFactory::create();

        $dispatcher = Router::fromJson(
            $container->get('routes.path')
        );

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
}
