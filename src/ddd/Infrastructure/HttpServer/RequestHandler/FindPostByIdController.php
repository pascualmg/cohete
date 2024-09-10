<?php

namespace pascualmg\cohete\ddd\Infrastructure\HttpServer\RequestHandler;

use pascualmg\cohete\ddd\Application\Post\FindPostByIdQuery;
use pascualmg\cohete\ddd\Application\Post\FindPostByIdQueryHandler;
use pascualmg\cohete\ddd\Domain\Entity\Post\Post;
use pascualmg\cohete\ddd\Domain\Entity\Post\ValueObject\PostId;
use pascualmg\cohete\ddd\Domain\ValueObject\UuidValueObject;
use pascualmg\cohete\ddd\Infrastructure\HttpServer\JsonResponse;
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
            new FindPostByIdQuery(
                PostId::from($routeParams['id'])
            )
        )->then(
            onFulfilled: static fn ($result) => is_null($result) ?
                JsonResponse::notFound(Post::class) :
                JsonResponse::withPayload($result),
            onRejected: fn (\Throwable $e) => JsonResponse::withError($e)
        );
    }
}
