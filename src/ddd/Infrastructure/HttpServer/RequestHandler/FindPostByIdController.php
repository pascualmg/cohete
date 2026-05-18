<?php

namespace pascualmg\cohete\ddd\Infrastructure\HttpServer\RequestHandler;
use Cohete\HttpServer\HttpRequestHandler;

use pascualmg\cohete\ddd\Application\Post\FindPostByIdQuery;
use pascualmg\cohete\ddd\Application\Post\FindPostByIdQueryHandler;
use pascualmg\cohete\ddd\Application\Post\FindPostBySlugQuery;
use pascualmg\cohete\ddd\Application\Post\FindPostBySlugQueryHandler;
use pascualmg\cohete\ddd\Domain\Entity\Post\Post;
use pascualmg\cohete\ddd\Domain\Entity\Post\ValueObject\PostId;
use pascualmg\cohete\ddd\Domain\Entity\Post\ValueObject\Slug;
use Cohete\HttpServer\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use React\Promise\PromiseInterface;

/**
 * GET /post/{id}
 *
 * Acepta UUID o slug en el path param. Si parece UUID lo busca por id, en otro
 * caso lo trata como slug. El uso clasico (UUID) sigue funcionando intacto,
 * y los slugs bonitos (ej /post/hydra-del-pobre-era-pobre-por-algo) tambien.
 */
class FindPostByIdController implements HttpRequestHandler
{
    private const UUID_PATTERN = '/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i';

    public function __construct(
        private readonly FindPostByIdQueryHandler $findPostByIdQueryHandler,
        private readonly FindPostBySlugQueryHandler $findPostBySlugQueryHandler,
    ) {
    }

    public function __invoke(ServerRequestInterface $request, ?array $routeParams): ResponseInterface|PromiseInterface
    {
        $key = $routeParams['id'];

        $promise = preg_match(self::UUID_PATTERN, $key) === 1
            ? ($this->findPostByIdQueryHandler)(new FindPostByIdQuery(PostId::from($key)))
            : ($this->findPostBySlugQueryHandler)(new FindPostBySlugQuery(Slug::from($key)));

        return $promise->then(
            onFulfilled: static fn ($result) => is_null($result) ?
                JsonResponse::notFound(Post::class) :
                JsonResponse::withPayload($result),
            onRejected: fn (\Throwable $e) => JsonResponse::withError($e)
        );
    }
}
