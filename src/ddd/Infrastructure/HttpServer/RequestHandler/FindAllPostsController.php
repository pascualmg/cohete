<?php

namespace pascualmg\cohete\ddd\Infrastructure\HttpServer\RequestHandler;

use pascualmg\cohete\ddd\Application\Post\FindAllPosts;
use pascualmg\cohete\ddd\Application\Post\FindAllPostsQuery;
use pascualmg\cohete\ddd\Infrastructure\HttpServer\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use React\Promise\PromiseInterface;

class FindAllPostsController implements HttpRequestHandler
{
    public function __construct(
        private readonly FindAllPosts $findAllPosts
    ) {
    }

    public function __invoke(ServerRequestInterface $request, ?array $routeParams): PromiseInterface | ResponseInterface
    {
        return ($this->findAllPosts)(
            new FindAllPostsQuery()
        )->then(
            onFulfilled: static fn (array $posts) => JsonResponse::withPayload($posts),
            onRejected: static fn (\Throwable $e) => JsonResponse::withError($e)
        );
    }
}
