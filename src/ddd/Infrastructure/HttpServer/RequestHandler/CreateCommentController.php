<?php

namespace pascualmg\cohete\ddd\Infrastructure\HttpServer\RequestHandler;

use pascualmg\cohete\ddd\Application\Comment\CreateCommentCommand;
use pascualmg\cohete\ddd\Application\Comment\CreateCommentCommandHandler;
use pascualmg\cohete\ddd\Infrastructure\HttpServer\JsonResponse;
use pascualmg\cohete\ddd\Infrastructure\Service\InMemoryRateLimiter;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use React\Promise\PromiseInterface;

class CreateCommentController implements HttpRequestHandler
{
    public function __construct(
        private readonly CreateCommentCommandHandler $handler,
        private readonly InMemoryRateLimiter $rateLimiter,
    ) {
    }

    public function __invoke(ServerRequestInterface $request, ?array $routeParams): ResponseInterface|PromiseInterface
    {
        $ip = $request->getServerParams()['REMOTE_ADDR'] ?? '0.0.0.0';

        if (!$this->rateLimiter->allow($ip)) {
            return JsonResponse::create(429, ['error' => 'Too many comments. Try again later.']);
        }

        $postId = $routeParams['id'] ?? '';
        $body = json_decode((string)$request->getBody(), true);

        if (!is_array($body) || empty($body['author_name']) || empty($body['body'])) {
            return JsonResponse::create(400, ['error' => 'Missing author_name or body']);
        }

        return ($this->handler)(new CreateCommentCommand(
            $postId,
            $body['author_name'],
            $body['body'],
        ))->then(
            fn (array $comment) => JsonResponse::create(201, $comment),
            fn (\Throwable $e) => JsonResponse::create(
                $e instanceof \InvalidArgumentException ? 404 : 500,
                ['error' => $e->getMessage()]
            )
        );
    }
}
