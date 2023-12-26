<?php

namespace Pascualmg\Rx\ddd\Infrastructure\RequestHandler;

use Pascualmg\Rx\ddd\Application\Post\FindAllPosts;
use Pascualmg\Rx\ddd\Application\Post\FindAllPostsQuery;
use Pascualmg\Rx\ddd\Infrastructure\HttpServer\JsonResponse;
use Psr\Http\Message\ServerRequestInterface;
use React\Promise\PromiseInterface;

class FindAllPostsController implements HttpRequestHandler
{
    public function __construct(
        private readonly FindAllPosts $findAllPosts
    ) {
    }

    public function __invoke(ServerRequestInterface $request, ?array $routeParams): PromiseInterface
    {
        return ($this->findAllPosts)(
            new FindAllPostsQuery()
        )->then(
            onFulfilled: static fn (array $posts) => JsonResponse::withPayload($posts)
        )->catch(
            onRejected: static fn (\Throwable $e) => JsonResponse::withError($e)
        );
    }
}
