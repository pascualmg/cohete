<?php

namespace pascualmg\cohete\ddd\Infrastructure\HttpServer\RequestHandler;
use Cohete\HttpServer\HttpRequestHandler;

use Cohete\Bus\Message;
use Cohete\Bus\MessageBus;
use Cohete\HttpServer\JsonResponse;
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
        $this->bus->publish(
            new Message(
                'foo',
                ['some payload']
            )
        );

        return JsonResponse::withPayload(serialize($request));
    }
}
