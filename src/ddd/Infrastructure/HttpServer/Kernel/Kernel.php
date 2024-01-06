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

class Kernel
{
    private ContainerInterface $container;
    private Dispatcher $dispatcher;
    private bool $isDevelopment;

    public function __construct(bool $isDevelopment = false)
    {
        //explicit no dependency injection.
        $this->container = ContainerFactory::create();
        $this->dispatcher = Router::fromJson(
            $this->container->get('routes.path')
        );
        $this->isDevelopment = $isDevelopment;
    }

    public function __invoke(ServerRequestInterface $request): PromiseInterface //of a ResponseInterface
    {
        //Este if se puede refactorizar wrapeando el fragmento principal en una func y quedaría mas "elegante"
        //pero haría la lectura mas complicada
        //todo: refactor this ?
        if ($this->isDevelopment) {
            try {
                return self::AsyncHandleRequest(
                    request: $request,
                    container: $this->container,
                    dispatcher: $this->dispatcher
                )->then(
                    onFulfilled: function (ResponseInterface $response): ResponseInterface {
                        return $response;
                    }
                )->catch(
                    onRejected: function (Throwable $exception): ResponseInterface {
                        return JsonResponse::withError($exception);
                    }
                );
            } catch (Throwable $exception) {
                // Este Try-Catch es importante , ya que elimina el  mensaje 500 sin info del servidor cuando
                // se produce una exception no controlada desde un repo , handler o cualquier otra clase interna.
                // Estas son las excepciones que se lanzan directamente throw , y no por ejemplo las que se hacen con un
                // $defered->reject(Throwable $e) , que son las que si son capturadas arriba.
                //todo: controlarlo con el .env , si es 'prod' inactivo maybe
                return self::wrapWithPromise(JsonResponse::withError($exception));
            }
        } else {
            return self::AsyncHandleRequest(
                request: $request,
                container: $this->container,
                dispatcher: $this->dispatcher
            )->then(
                onFulfilled: function (ResponseInterface $response): ResponseInterface {
                    return $response;
                }
            )->catch(
                onRejected: function (Throwable $exception): ResponseInterface {
                    return JsonResponse::withError($exception);
                }
            );
        }
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
