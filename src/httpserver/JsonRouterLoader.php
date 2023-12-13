<?php

namespace Passh\Rx\httpserver;

use RuntimeException;
use Throwable;

class JsonRouterLoader
{
    private function __construct()
    {
    }

    public static function load(string $jsonRoutesFilePath, Router $router)
    {
        foreach (self::parseRoutesFromJson($jsonRoutesFilePath) as $route) {
            $router->addRoute(
                $route['Method'],
                $route['Path'],
                self::transformHandlerToCallable($route['Handler'])
            );
        }
    }


    private static function parseRoutesFromJson(string $jsonRoutesFilePath): array
    {
        if (!file_exists($jsonRoutesFilePath)) {
            throw new RuntimeException("File {$jsonRoutesFilePath} does not exist.");
        }
        $routes = json_decode(
            file_get_contents($jsonRoutesFilePath),
            true,
            512,
            JSON_THROW_ON_ERROR
        );
        foreach ($routes as $route) {
            self::assertFormat($route);
        }
        return $routes;
    }

    public static function assertFormat(array $route): void
    {
        if (!isset($route['Method'], $route['Path'], $route['Handler'])) {
            throw new RuntimeException("Invalid JSON route format");
        }

        // Handler should be in format "Namespace\Class::method"
        if (!preg_match(
            '/^(\\\?[a-zA-Z_\x80-\xff][a-zA-Z0-9_\x80-\xff]*)+::[a-zA-Z_\x80-\xff][a-zA-Z0-9_\x80-\xff]*$/',
            $route['Handler']
        )) {
            throw new RuntimeException("Handler '{$route['Handler']}' format is not correct");
        }
    }

    private static function transformHandlerToCallable(string $handler): callable
    {
        // Assuming the handler string is a static method call in the "Class::method" format
        [$class, $method] = explode('::', $handler);

        try {
            $instance = new $class();
        } catch (Throwable $e) {
            throw new RuntimeException('Handler class does not exist or could not be instantiated', 0, $e);
        }

        if (!is_callable([$instance, $method])) {
            throw new RuntimeException('Handler method is not callable');
        }

        return [$instance, $method];//si si , esto es una callable xD
    }
}
