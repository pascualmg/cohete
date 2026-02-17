<?php

namespace pascualmg\cohete\ddd\Infrastructure\HttpServer\RequestHandler;

use pascualmg\cohete\ddd\Domain\Entity\Author\Author;
use pascualmg\cohete\ddd\Domain\Entity\Author\ValueObject\AuthorName;
use pascualmg\cohete\ddd\Domain\Entity\AuthorRepository;
use pascualmg\cohete\ddd\Infrastructure\HttpServer\JsonResponse;
use pascualmg\cohete\ddd\Infrastructure\Repository\Author\ObservableMysqlAuthorRepository;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use React\Promise\PromiseInterface;

class AuthorUpdateTypeController implements HttpRequestHandler
{
    public function __construct(
        private readonly AuthorRepository $authorRepository,
    ) {
    }

    public function __invoke(ServerRequestInterface $request, ?array $routeParams): ResponseInterface|PromiseInterface
    {
        $authHeader = $request->getHeaderLine('Authorization');
        if (empty($authHeader) || !str_starts_with($authHeader, 'Bearer ')) {
            return JsonResponse::create(401, ['error' => 'Authorization: Bearer <key> required']);
        }
        $bearerKey = substr($authHeader, 7);

        $body = (string) $request->getBody();
        try {
            $payload = json_decode($body, true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            return JsonResponse::withError($e);
        }

        $name = $payload['name'] ?? null;
        $type = $payload['type'] ?? null;
        $validTypes = ['human', 'ia', 'hybrid'];

        if (empty($name) || !in_array($type, $validTypes, true)) {
            return JsonResponse::create(400, ['error' => "name required, type must be one of: " . implode(', ', $validTypes)]);
        }

        return $this->authorRepository->findByName(AuthorName::from($name))->then(
            function (?Author $author) use ($bearerKey, $type, $name): ResponseInterface|PromiseInterface {
                if ($author === null) {
                    return JsonResponse::create(404, ['error' => 'Author not found']);
                }

                if (!$author->verifyKey($bearerKey)) {
                    return JsonResponse::create(403, ['error' => 'Wrong key']);
                }

                /** @var ObservableMysqlAuthorRepository $repo */
                $repo = $this->authorRepository;

                return $repo->updateType((string)$author->id, $type)->then(
                    fn () => JsonResponse::create(200, ['ok' => true, 'name' => $name, 'type' => $type])
                );
            }
        );
    }
}
