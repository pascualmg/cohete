<?php

namespace pascualmg\cohete\ddd\Infrastructure\HttpServer\Kernel;

use FastRoute\Dispatcher;
use pascualmg\cohete\ddd\Infrastructure\HttpServer\JsonResponse;
use pascualmg\cohete\ddd\Infrastructure\HttpServer\Router\Router;
use pascualmg\cohete\ddd\Infrastructure\PSR11\ContainerFactory;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use React\Http\Message\Response;
use React\Promise\Deferred;
use React\Promise\Promise;
use React\Promise\PromiseInterface;
use Throwable;

class Kernel
{
    private ContainerInterface $container;
    private Dispatcher $dispatcher;

    public function __construct()
    {
        //explicit no dependency injection.
        $this->container = ContainerFactory::create();
        $this->dispatcher = Router::fromJson(
            $this->container->get('routes.path')
        );
    }

    public function __invoke(ServerRequestInterface $request): PromiseInterface //of a ResponseInterface
    {

        return self::AsyncHandleRequest(
            request: $request,
            container: $this->container,
            dispatcher: $this->dispatcher
        )->then(
            onFulfilled: function (ResponseInterface $response): ResponseInterface {
                return $response;
            },
            onRejected: function (Throwable $exception): ResponseInterface {
                return JsonResponse::withError($exception);
            }
        );
    }

    public static function AsyncHandleRequest(
        ServerRequestInterface $request,
        ContainerInterface $container,
        Dispatcher $dispatcher
    ): PromiseInterface {
        $deferred = new Deferred();

        $method = strtoupper($request->getMethod());
        $uri = $request->getUri()->getPath( );

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
                try {
                    $response = $container->get($httpRequestHandlerName)($request, $params);
                } catch (Throwable $throwable) {
                    $deferred->reject($throwable);
                    break;
                }

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
