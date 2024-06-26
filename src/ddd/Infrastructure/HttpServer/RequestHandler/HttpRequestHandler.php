<?php

namespace pascualmg\cohete\ddd\Infrastructure\HttpServer\RequestHandler;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use React\Promise\PromiseInterface;

interface HttpRequestHandler
{
    public function __invoke(ServerRequestInterface $request, ?array $routeParams): ResponseInterface | PromiseInterface;
}
