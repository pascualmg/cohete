<?php

namespace Pascualmg\Rx\ddd\Infrastructure\HttpServer;

use InvalidArgumentException;
use PHPUnit\Framework\ProcessIsolationException;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use React\Http\Message\Response;
use React\Promise\Deferred;
use React\Promise\PromiseInterface;
use ReflectionMethod;
use ReflectionNamedType;
use RuntimeException;

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

        if (!$returnType instanceof ReflectionNamedType ||
            !is_a($returnType->getName(), PromiseInterface::class, true)
        ) {
            throw new InvalidArgumentException('Handler must return an instance of ' . PromiseInterface::class);
        }
        $params = $reflection->getParameters();
        if (empty($params) ||
            !$params[0]->hasType() ||
            !$params[0]->getType() instanceof ReflectionNamedType ||
            !is_a($params[0]->getType()->getName(), RequestInterface::class, true)
        ) {
            $handlerFQDN = sprintf("%s::%s", $handler[0]::class, $reflection->getName());
            throw new InvalidArgumentException(
                sprintf(
                    "Handler %s must accept a parameter of type %s",
                    $handlerFQDN,
                    RequestInterface::class
                )
            );
        }
    }

    public function loadFromJson(string $jsonRoutesFilePath): void
    {
        JsonRouterLoader::load($jsonRoutesFilePath, $this);
    }

    public function handleRequest(ServerRequestInterface $request): PromiseInterface
    {
        $deferred = new Deferred();

        $method = strtoupper($request->getMethod());
        $route = $request->getUri()->getPath();


        if (isset($this->routes[$method][$route])) {
            $handler = $this->routes[$method][$route];
            $response = $handler($request);
            if (!$response instanceof PromiseInterface) {
                $deferred->reject(
                    new RuntimeException('Handler must return an instance of PromiseInterface.')
                );
            }
            $deferred->resolve($response);
        }

        $deferred->resolve(
            new Response(404, ['Content-Type' => 'text/plain'], 'Route not found')
        );
        return $deferred->promise();
    }
}
