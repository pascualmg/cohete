<?php

namespace Pascualmg\Rx\ddd\Infrastructure\HttpServer;

use InvalidArgumentException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use React\Http\Message\Response;
use React\Promise\Deferred;
use React\Promise\PromiseInterface;
use ReflectionMethod;
use ReflectionNamedType;
use Rx\React\Promise;

class Router
{
    private array $routes = [];

    public function addRoute(string $method, string $route, callable $handler): void
    {
        $this->assertHandlerReturnsResponse($handler);

        $this->routes[strtoupper($method)][$route] = $handler;
    }

    private function assertHandlerReturnsResponse(callable $handler): void
    {
        $reflection = new ReflectionMethod(...$handler);

        $returnType = $reflection->getReturnType();

        //this is for the handler tha implement the interface with two return types.
        $isPromiseOrResponse = false;
        if ($returnType instanceof \ReflectionUnionType) {
            $returnedTypes = $returnType->getTypes();
            $nameOfTheTypes = array_map(static fn (ReflectionNamedType $type) => $type->getName(), $returnedTypes);
            $isPromiseOrResponse = array_reduce(
                $nameOfTheTypes,
                static fn ($acc, $curr) => ($acc ||
                        is_a($curr, PromiseInterface::class, true)) ||
                    is_a($curr, ResponseInterface::class, true),
                false
            );
        }
        if (!$isPromiseOrResponse
            && (!$returnType instanceof ReflectionNamedType
                || (!is_a($returnType->getName(), PromiseInterface::class, true)
                    && !is_a($returnType->getName(), ResponseInterface::class, true)))) {
            throw new InvalidArgumentException(
                sprintf(
                    "Handler must return an instance of %s or %s, but this handler returns %s",
                    PromiseInterface::class,
                    ResponseInterface::class,
                    $returnType->getName()
                )
            );
        }


        $params = $reflection->getParameters();
        if (empty($params) ||
            !$params[0]->hasType() ||
            !$params[0]->getType() instanceof ReflectionNamedType ||
            !is_a($params[0]->getType()->getName(), ServerRequestInterface::class, true)
        ) {
            $handlerFQDN = sprintf("%s::%s", $handler[0]::class, $reflection->getName());
            throw new InvalidArgumentException(
                sprintf(
                    "Handler %s must accept a parameter of type %s",
                    $handlerFQDN,
                    ServerRequestInterface::class
                )
            );
        }
    }

    public function loadFromJson(
        string $jsonRoutesFilePath
    ): void {
        JsonRouterLoader::load($jsonRoutesFilePath, $this);
    }

    public function handleRequest(
        ServerRequestInterface $request
    ): PromiseInterface {
        $deferred = new Deferred();

        $method = strtoupper($request->getMethod());
        $route = $request->getUri()->getPath();


        if (isset($this->routes[$method][$route])) {
            $handler = $this->routes[$method][$route];
            $response = $handler($request);
            if (!$response instanceof PromiseInterface) {
                $wrappedResponse = Promise::resolved($response);
                $deferred->resolve($wrappedResponse);
            }
            $deferred->resolve($response);
        }

        $deferred->resolve(
            new Response(404, ['Content-Type' => 'text/plain'], 'Route not found')
        );
        return $deferred->promise();
    }
}
