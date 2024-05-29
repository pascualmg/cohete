<?php

namespace pascualmg\cohete\ddd\Infrastructure\HttpServer\RequestHandler;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use React\Http\Message\Response;
use React\Promise\PromiseInterface;

class RedirectController implements HttpRequestHandler
{

    public function __invoke(ServerRequestInterface $request, ?array $routeParams): ResponseInterface|PromiseInterface
    {
        return new Response(
            301,
            [
                'Location' => '/html/pascualmgPorfolio.html'
            ],
            'Redirecting to my portfolio...'
        );
    }
}