<?php

namespace Pascualmg\Rx\ddd\Infrastructure\RequestHandler;

use Fig\Http\Message\StatusCodeInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use React\Http\Message\Response;
use React\Promise\PromiseInterface;
use React\Stream\ReadableResourceStream;
use React\Stream\ThroughStream;

class HtmlController implements HttpRequestHandler
{
    public function __invoke(ServerRequestInterface $request, ?array $routeParams): ResponseInterface|PromiseInterface
    {
        $foo = new ThroughStream('strtoupper');

        //en vez de usar un file_get_contents usamos un stream
        $html = new ReadableResourceStream(
            fopen(
                __DIR__ . '/../../../scripts/Drafts/websocketServer/websocketTest.html',
                'rb'
            )
        );

        return new Response(
            StatusCodeInterface::STATUS_OK,
            ['Content-Type' => 'text/html'],
            $html->pipe($foo)
        );
    }
}
