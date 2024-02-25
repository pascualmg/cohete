<?php

namespace pascualmg\reactor\ddd\Infrastructure\HttpServer\RequestHandler;

use pascualmg\reactor\ddd\Application\Post\FindPostByIdQuery;
use pascualmg\reactor\ddd\Application\Post\FindPostByIdQueryHandler;
use pascualmg\reactor\ddd\Domain\Entity\Post\Post;
use pascualmg\reactor\ddd\Infrastructure\HttpServer\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use React\Promise\PromiseInterface;

class FindPostByIdController implements HttpRequestHandler
{
    public function __construct(
        private readonly FindPostByIdQueryHandler $findPostByIdQueryHandler
    ) {
    }

    public function __invoke(ServerRequestInterface $request, ?array $routeParams): ResponseInterface|PromiseInterface
    {
        return ($this->findPostByIdQueryHandler)(
            new FindPostByIdQuery((int)$routeParams['id'])
        )->then(
            onFulfilled: static fn ($result) => is_null($result) ?
                JsonResponse::notFound(Post::class) :
                JsonResponse::withPayload($result),
            onRejected: fn (\Throwable $e) => JsonResponse::withError($e)
        );
    }
}
