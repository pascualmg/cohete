<?php

namespace pascualmg\reactor\ddd\Infrastructure\HttpServer\RequestHandler;

use Fig\Http\Message\StatusCodeInterface;
use pascualmg\reactor\ddd\Infrastructure\HttpServer\JsonResponse;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use React\Http\Message\Response;
use React\Promise\PromiseInterface;
use React\Stream\ReadableResourceStream;
use React\Stream\ThroughStream;

class HtmlController implements HttpRequestHandler, StatusCodeInterface
{
    public function __construct(ContainerInterface $container)
    {
    }

    public function __invoke(
        ServerRequestInterface $request,
        ?array $routeParams
    ): ResponseInterface|PromiseInterface
    {
        //omg :) nice
        $foo = new ThroughStream(static fn($id) => $id);

        $uri = dirname(__DIR__,1) . '/html' . $routeParams['params'] ?? "";

        if(
            !file_exists($uri) ||
            is_dir($uri)
        ){
           return JsonResponse::notFound($uri);
        }
        $html = new ReadableResourceStream(
            fopen(
            $uri,
                'rb'
            )
        );

        return new Response(
            self::STATUS_OK,
            ['Content-Type' => 'text/html'],
            $html->pipe($foo)
        );
    }
}
