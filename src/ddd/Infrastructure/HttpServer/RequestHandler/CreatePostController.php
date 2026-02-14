<?php

namespace pascualmg\cohete\ddd\Infrastructure\HttpServer\RequestHandler;

use Fig\Http\Message\StatusCodeInterface;
use pascualmg\cohete\ddd\Application\Post\CreatePostCommand;
use pascualmg\cohete\ddd\Application\Post\CreatePostCommandHandler;
use pascualmg\cohete\ddd\Infrastructure\HttpServer\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use React\Promise\PromiseInterface;

class CreatePostController implements HttpRequestHandler
{
    public function __construct(
        private readonly CreatePostCommandHandler $createPostCommandHandler
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
        ($this->createPostCommandHandler)(
            new CreatePostCommand(
                $payload['id'],
                $payload['headline'],
                $payload['articleBody'],
                $payload['author'],
                $payload['datePublished'],
            )
        );

        return JsonResponse::accepted();
    }

}
