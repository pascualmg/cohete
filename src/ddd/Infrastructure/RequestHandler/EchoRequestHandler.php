<?php

namespace Pascualmg\Rx\ddd\Infrastructure\RequestHandler;

use Pascualmg\Rx\ddd\Domain\Bus\Event;
use Pascualmg\Rx\ddd\Infrastructure\Bus\ReactEventBus;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use React\EventLoop\Loop;
use RingCentral\Psr7\Response;

class EchoRequestHandler implements Handler
{
    public function handle(ServerRequestInterface $request): ResponseInterface
    {

        return new Response(
            200,
            ['Content-Type' => 'Application/Text'],
            "tocame el nardo bernardo"
        );
    }
}
