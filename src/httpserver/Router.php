<?php

namespace Passh\Rx\httpserver;


use InvalidArgumentException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use React\Http\Message\Response;
use ReflectionFunction;
use ReflectionMethod;
use ReflectionNamedType;
use RuntimeException;

class Router
{
    private array $routes = [];

    public function addRoute(string $method, string $route, callable $handler): void
    {
//        $this->assertHandlerReturnsResponse($handler);

        $this->routes[strtoupper($method)][$route] = $handler;
    }

    public function loadFromJson(string $jsonRoutesFilePath): void
    {
        JsonRouterLoader::load($jsonRoutesFilePath, $this);
    }

    private function assertHandlerReturnsResponse(callable $handler): void
    {
        $reflection = is_array($handler) && count($handler) === 2
            ? new ReflectionMethod(...$handler)
            : new ReflectionFunction($handler);

        $returnType = $reflection->getReturnType();

        if (!$returnType instanceof ReflectionNamedType ||
            !is_a($returnType->getName(), ResponseInterface::class, true)
        ) {
            throw new InvalidArgumentException('Handler must return an instance of ' . ResponseInterface::class);
        }
    }

    public function handleRequest(ServerRequestInterface $request): ResponseInterface
    {
        $method = strtoupper($request->getMethod());
        $route = $request->getUri()->getPath();


        if (isset($this->routes[$method][$route])) {
            $handler = $this->routes[$method][$route];
            $response = $handler($request);
            if (!$response instanceof ResponseInterface) {
                throw new RuntimeException('Handler must return an instance of ResponseInterface.');
            }
            return $response;
        }

        return new Response(404, ['Content-Type' => 'text/plain'], 'Route not found');
    }
}