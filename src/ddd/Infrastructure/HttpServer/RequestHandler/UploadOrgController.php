<?php

namespace pascualmg\cohete\ddd\Infrastructure\HttpServer\RequestHandler;

use Cohete\HttpServer\HttpRequestHandler;
use Cohete\HttpServer\JsonResponse;
use pascualmg\cohete\ddd\Application\Post\InvalidBearerException;
use pascualmg\cohete\ddd\Application\Post\MissingSlugException;
use pascualmg\cohete\ddd\Application\Post\PublishOrgPostCommandHandler;
use pascualmg\cohete\ddd\Application\Post\PublishOrgPostResult;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use React\Promise\PromiseInterface;

/**
 * POST /post/org — publica un post desde .org (raw en el body, requiere Bearer).
 *
 * IDEMPOTENTE por (autor, #+SLUG): si ya tienes un post con ese slug lo
 * ACTUALIZA preservando el UUID; si no, lo CREA. El cliente no decide
 * crear-vs-actualizar: publica el .org y el servidor hace lo correcto. Toda la
 * orquestacion vive en PublishOrgPostCommandHandler (compartido con el MCP).
 * Este controller solo adapta HTTP <-> caso de uso.
 */
class UploadOrgController implements HttpRequestHandler
{
    public function __construct(
        private readonly PublishOrgPostCommandHandler $publishOrgPost,
    ) {
    }

    public function __invoke(ServerRequestInterface $request, ?array $routeParams): ResponseInterface|PromiseInterface
    {
        $authHeader = $request->getHeaderLine('Authorization');
        if (empty($authHeader) || !str_starts_with($authHeader, 'Bearer ')) {
            return JsonResponse::create(401, ['error' => 'Missing Authorization: Bearer <key>']);
        }
        $token = substr($authHeader, 7);

        $orgContent = (string) $request->getBody();
        if (empty(trim($orgContent))) {
            return JsonResponse::create(400, ['error' => 'Empty org content']);
        }

        return ($this->publishOrgPost)($token, $orgContent)->then(
            fn (PublishOrgPostResult $result): ResponseInterface => JsonResponse::create(202, [
                'created' => $result->created,
                'updated' => !$result->created,
                'id' => $result->id,
                'headline' => $result->headline,
                'author' => $result->author,
                'datePublished' => $result->datePublished,
                'slug' => $result->slug,
            ]),
            fn (\Throwable $e): ResponseInterface => match (true) {
                $e instanceof InvalidBearerException => JsonResponse::create(403, ['error' => $e->getMessage()]),
                $e instanceof MissingSlugException => JsonResponse::create(400, ['error' => $e->getMessage()]),
                default => JsonResponse::withError($e),
            },
        );
    }
}
