<?php

namespace pascualmg\cohete\ddd\Infrastructure\HttpServer\RequestHandler;

use pascualmg\cohete\ddd\Application\Comment\FindCommentsByPostIdQuery;
use pascualmg\cohete\ddd\Application\Comment\FindCommentsByPostIdQueryHandler;
use pascualmg\cohete\ddd\Infrastructure\HttpServer\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use React\Promise\PromiseInterface;

class ListCommentsController implements HttpRequestHandler
{
    public function __construct(
        private readonly FindCommentsByPostIdQueryHandler $handler,
    ) {
    }

    public function __invoke(ServerRequestInterface $request, ?array $routeParams): ResponseInterface|PromiseInterface
    {
        $postId = $routeParams['id'] ?? '';

        return ($this->handler)(new FindCommentsByPostIdQuery($postId))->then(
            fn (array $comments) => JsonResponse::create(200, array_map(
                fn ($c) => $c->jsonSerialize(),
                $comments
            )),
            fn (\Throwable $e) => JsonResponse::create(500, ['error' => $e->getMessage()])
        );
    }
}
