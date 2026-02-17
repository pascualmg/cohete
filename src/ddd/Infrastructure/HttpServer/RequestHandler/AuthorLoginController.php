<?php

namespace pascualmg\cohete\ddd\Infrastructure\HttpServer\RequestHandler;

use pascualmg\cohete\ddd\Domain\Entity\Author\Author;
use pascualmg\cohete\ddd\Domain\Entity\Author\ValueObject\AuthorName;
use pascualmg\cohete\ddd\Domain\Entity\AuthorRepository;
use pascualmg\cohete\ddd\Infrastructure\HttpServer\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use React\Promise\PromiseInterface;

class AuthorLoginController implements HttpRequestHandler
{
    public function __construct(
        private readonly AuthorRepository $authorRepository,
    ) {
    }

    public function __invoke(ServerRequestInterface $request, ?array $routeParams): ResponseInterface|PromiseInterface
    {
        $body = (string) $request->getBody();
        try {
            $payload = json_decode($body, true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            return JsonResponse::withError($e);
        }

        $name = $payload['name'] ?? null;
        $key = $payload['key'] ?? null;

        if (empty($name) || empty($key)) {
            return JsonResponse::create(400, ['error' => 'name and key are required']);
        }

        return $this->authorRepository->findByName(AuthorName::from($name))->then(
            function (?Author $author) use ($key): ResponseInterface {
                if ($author === null) {
                    return JsonResponse::create(404, ['error' => 'Author not found']);
                }

                if (!$author->verifyKey($key)) {
                    return JsonResponse::create(403, ['error' => 'Wrong key']);
                }

                return JsonResponse::create(200, [
                    'ok' => true,
                    'author' => [
                        'id' => (string)$author->id,
                        'name' => (string)$author->name,
                        'type' => $author->type,
                    ],
                ]);
            }
        );
    }
}
