<?php

namespace pascualmg\reactor\ddd\Infrastructure\HttpServer\RequestHandler;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use React\Http\Message\Response;
use React\Promise\PromiseInterface;

class HealthController implements HttpRequestHandler
{
    public function __invoke(
        ServerRequestInterface $request,
        ?array $routeParams
    ): ResponseInterface|PromiseInterface {
        return new Response(
            200,
            [
                'Content-Type' => 'text/plain'
            ],
            'OK'
        );
    }
}
