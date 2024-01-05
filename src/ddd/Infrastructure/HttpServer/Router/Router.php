<?php

namespace pascualmg\reactor\ddd\Infrastructure\HttpServer\Router;

use FastRoute\Dispatcher;
use FastRoute\RouteCollector;

use function FastRoute\simpleDispatcher;

class Router
{
    public static function fromJson(string $path): Dispatcher
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
    public static function assertNotEmpty(string $path): void
    {
        if (empty($path)) {
            throw new \RuntimeException(
                "The path of the json File to load the routes is empty, maybe you have no .env or this variable is undefined? "
            );
        }
    }
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
