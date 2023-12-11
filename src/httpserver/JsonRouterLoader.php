<?php

namespace Passh\Rx\httpserver;

class JsonRouterLoader
{
    private array $routes = [];

    public function __construct(string $jsonRoutesFilePath)
    {
        $this->loadRoutesFromJson($jsonRoutesFilePath);
    }

    public function loadInto(Router $router): void
    {
        foreach ($this->routes as $route) {
            $callableHandler = $this->transformHandlerToCallable($route['Handler']);
            $router->addRoute(
                $route['Method'],
                $route['Path'],
                $callableHandler
            );
        }
    }

    private function loadRoutesFromJson(string $jsonRoutesFilePath): void
    {
        if (!file_exists($jsonRoutesFilePath)) {
            throw new \RuntimeException("File {$jsonRoutesFilePath} does not exist.");
        }

        $jsonContent = file_get_contents($jsonRoutesFilePath);
        $routes = json_decode($jsonContent, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \RuntimeException('Error decoding JSON: ' . json_last_error_msg());
        }

        $this->routes = $routes;
    }

    private function transformHandlerToCallable(string $handler): callable
    {

        if (is_callable($handler, true)) {
            return function (...$args) use ($handler) {
                return call_user_func_array($handler, $args);
            };
        }

        // Assuming the handler string is a static method call in the "Class::method" format
        list($class, $method) = explode('::', $handler);
        if (!class_exists($class) || !method_exists($class, $method)) {
            throw new \RuntimeException('Handler class or method does not exist');
        }
        return [$class, $method];
    }

    /* to get all routes */
    public function getRoutes(): array
    {
        return $this->routes;
    }
}