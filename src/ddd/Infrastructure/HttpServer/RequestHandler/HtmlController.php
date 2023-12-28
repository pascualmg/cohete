<?php

namespace pascualmg\reactor\ddd\Infrastructure\HttpServer\RequestHandler;

use Fig\Http\Message\StatusCodeInterface;
use pascualmg\reactor\ddd\Infrastructure\HttpServer\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use React\Http\Message\Response;
use React\Promise\PromiseInterface;
use React\Stream\ReadableResourceStream;
use React\Stream\ThroughStream;

class HtmlController implements HttpRequestHandler, StatusCodeInterface
{
    public function __invoke(ServerRequestInterface $request, ?array $routeParams): ResponseInterface|PromiseInterface
    {
        //omg :) nice
        $foo = new ThroughStream(static fn($id) => $id);

        $uri = __DIR__ . '/../HttpServer/html' . $routeParams['params'];

        if(!file_exists($uri)){
           return JsonResponse::notFound($uri);
        }
        $html = new ReadableResourceStream(
            fopen(
                //'/Users/passh/src/reactphp/rxphp/src/ddd/Infrastructure/HttpServer/html/websocketTest.html',
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
