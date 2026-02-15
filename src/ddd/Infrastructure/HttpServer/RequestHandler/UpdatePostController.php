<?php

namespace pascualmg\cohete\ddd\Infrastructure\HttpServer\RequestHandler;

use pascualmg\cohete\ddd\Application\Post\UpdatePostCommand;
use pascualmg\cohete\ddd\Application\Post\UpdatePostCommandHandler;
use pascualmg\cohete\ddd\Infrastructure\HttpServer\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use React\Promise\PromiseInterface;

class UpdatePostController implements HttpRequestHandler
{
    public function __construct(
        private readonly UpdatePostCommandHandler $updatePostCommandHandler
    ) {
    }

    public function __invoke(ServerRequestInterface $request, ?array $routeParams): ResponseInterface|PromiseInterface
    {
        $body = $request->getBody()->getContents();
        try {
            $payload = json_decode($body, true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            return JsonResponse::withError($e);
        }

        ($this->updatePostCommandHandler)(
            new UpdatePostCommand(
                $routeParams['id'],
                $payload['headline'],
                $payload['articleBody'],
                $payload['author'],
                $payload['datePublished'],
                $payload['orgSource'] ?? null,
            )
        );

        return JsonResponse::accepted();
    }
}
