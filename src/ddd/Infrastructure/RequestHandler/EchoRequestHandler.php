<?php

namespace Pascualmg\Rx\ddd\Infrastructure\RequestHandler;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use React\Promise\PromiseInterface;
use RingCentral\Psr7\Response;

class EchoRequestHandler implements Handler
{
    public function handle(ServerRequestInterface $request): ResponseInterface|PromiseInterface
    {
        return new Response(
            200,
            ['Content-Type' => 'Application/Text'],
            "tocame el nardo bernardo"
        );
    }
}
