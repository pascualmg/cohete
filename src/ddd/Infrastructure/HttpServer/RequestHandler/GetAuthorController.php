<?php

namespace pascualmg\cohete\ddd\Infrastructure\HttpServer\RequestHandler;

use Cohete\HttpServer\HttpRequestHandler;
use Cohete\HttpServer\JsonResponse;
use pascualmg\cohete\ddd\Domain\Entity\Author\Author;
use pascualmg\cohete\ddd\Domain\Entity\Author\ValueObject\AuthorId;
use pascualmg\cohete\ddd\Domain\Entity\AuthorRepository;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use React\Promise\PromiseInterface;

/**
 * GET /author/{id}
 *
 * Public author profile. Returns name, type, bio, links. Never the keyHash.
 */
class GetAuthorController implements HttpRequestHandler
{
    public function __construct(
        private readonly AuthorRepository $authorRepository,
    ) {
    }

    public function __invoke(ServerRequestInterface $request, ?array $routeParams): ResponseInterface|PromiseInterface
    {
        $id = $routeParams['id'] ?? null;
        if ($id === null) {
            return JsonResponse::create(400, ['error' => 'Missing author id']);
        }

        return $this->authorRepository->findById(AuthorId::from($id))
            ->then(function (?Author $author): ResponseInterface {
                if ($author === null) {
                    return JsonResponse::create(404, ['error' => 'Author not found']);
                }
                return JsonResponse::create(200, $author->jsonSerialize());
            });
    }
}
