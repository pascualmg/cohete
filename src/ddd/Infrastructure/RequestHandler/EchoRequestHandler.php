<?php

namespace Pascualmg\Rx\ddd\Infrastructure\RequestHandler;

use Pascualmg\Rx\ddd\Domain\Bus\Bus;
use Pascualmg\Rx\ddd\Domain\Bus\Event;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use RingCentral\Psr7\Response;

class EchoRequestHandler implements Handler
{
    private Bus $bus;

    public function __construct(Bus $bus)
    {
        $this->bus = $bus;

    }

    public function __invoke(ServerRequestInterface $request): ResponseInterface
    {
        $this->bus->dispatch(
            new Event(
                'foo',
                ['some payload']
            )
        );

        return new Response(
            200,
            ['Content-Type' => 'Application/Text'],
            "echo from echoRequestHandler"
        );
    }
}
