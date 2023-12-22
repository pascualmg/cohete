<?php

namespace Pascualmg\Rx\ddd\Infrastructure\RequestHandler;

use Pascualmg\Rx\ddd\Application\Post\FindPostByIdQuery;
use Pascualmg\Rx\ddd\Application\Post\FindPostByIdQueryHandler;
use Pascualmg\Rx\ddd\Application\Post\ReactJsonResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use React\Promise\Deferred;
use React\Promise\PromiseInterface;

class FindPostByIdHttpRequestHandler implements HttpRequestHandler
{
    private FindPostByIdQueryHandler $handler;

    public function __construct(FindPostByIdQueryHandler $findPostByIdQueryHandler)
    {
        $this->handler = $findPostByIdQueryHandler;
    }

    public function __invoke(ServerRequestInterface $request, ?array $routeParams): ResponseInterface|PromiseInterface
    {
        $deferred = new Deferred();

        ($this->handler)(
            new FindPostByIdQuery((int)$routeParams['id'])
        )->then(
            onFulfilled: function ($result) use ($deferred) {
                $deferred->resolve(
                    ReactJsonResponse::withPayload($result)
                );
            },
            onRejected: function (\Throwable $e) use ($deferred) {
                $deferred->resolve(
                    ReactJsonResponse::withError($e)
                );
            }
        );
        return $deferred->promise();
    }
}
