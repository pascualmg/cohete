<?php

namespace Pascualmg\Rx\ddd\Infrastructure\RequestHandler;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use React\Promise\PromiseInterface;
use Rx\React\Promise;

interface Handler
{
    public function handle(ServerRequestInterface $request): ResponseInterface | PromiseInterface;
}
