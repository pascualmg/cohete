<?php

namespace Pascualmg\Rx\ddd\Infrastructure\RequestHandler;

use Pascualmg\Rx\ddd\Domain\Bus\Message;
use Pascualmg\Rx\ddd\Domain\Bus\MessageBus;
use Pascualmg\Rx\ddd\Infrastructure\HttpServer\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class EchoController implements HttpRequestHandler
{
    private MessageBus $bus;

    public function __construct(MessageBus $bus)
    {
        $this->bus = $bus;

    }

    public function __invoke(ServerRequestInterface $request, ?array $routeParams): ResponseInterface
    {
        $this->bus->dispatch(
            new Message(
                'foo',
                ['some payload']
            )
        );

        return JsonResponse::withPayload(serialize($request));
    }
}
