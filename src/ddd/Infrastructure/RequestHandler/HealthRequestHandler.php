<?php

namespace Pascualmg\Rx\ddd\Infrastructure\RequestHandler;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use React\Http\Message\Response;
use React\Promise\PromiseInterface;

class HealthRequestHandler implements Handler
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
