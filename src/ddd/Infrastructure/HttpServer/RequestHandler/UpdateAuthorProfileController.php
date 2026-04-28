<?php

namespace pascualmg\cohete\ddd\Infrastructure\HttpServer\RequestHandler;

use Cohete\HttpServer\HttpRequestHandler;
use Cohete\HttpServer\JsonResponse;
use pascualmg\cohete\ddd\Application\Author\UpdateAuthorProfileCommand;
use pascualmg\cohete\ddd\Application\Author\UpdateAuthorProfileCommandHandler;
use pascualmg\cohete\ddd\Domain\Entity\Author\Author;
use pascualmg\cohete\ddd\Domain\Entity\Author\ValueObject\AuthorId;
use pascualmg\cohete\ddd\Domain\Entity\AuthorRepository;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use React\Promise\PromiseInterface;

/**
 * PUT /author/{id}
 *
 * Edit own profile (bio, links). Requires Bearer with the author token.
 * Body: { "bio": "...", "links": [{"label":"github","url":"..."}, ...] }
 */
class UpdateAuthorProfileController implements HttpRequestHandler
{
    public function __construct(
        private readonly UpdateAuthorProfileCommandHandler $updateProfile,
        private readonly AuthorRepository $authorRepository,
    ) {
    }

    public function __invoke(ServerRequestInterface $request, ?array $routeParams): ResponseInterface|PromiseInterface
    {
        $id = $routeParams['id'] ?? null;
        if ($id === null) {
            return JsonResponse::create(400, ['error' => 'Missing author id']);
        }

        $authHeader = $request->getHeaderLine('Authorization');
        if (empty($authHeader) || !str_starts_with($authHeader, 'Bearer ')) {
            return JsonResponse::create(401, ['error' => 'Authorization: Bearer <token> required']);
        }
        $bearerToken = substr($authHeader, 7);

        $raw = (string)$request->getBody();
        try {
            $payload = json_decode($raw, true, 32, JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            return JsonResponse::create(400, ['error' => 'Invalid JSON: ' . $e->getMessage()]);
        }

        $bio = isset($payload['bio']) ? (string)$payload['bio'] : null;
        if ($bio !== null && mb_strlen($bio) > 4000) {
            return JsonResponse::create(400, ['error' => 'bio too long (max 4000 chars)']);
        }

        $links = $payload['links'] ?? null;
        if ($links !== null && !is_array($links)) {
            return JsonResponse::create(400, ['error' => '"links" must be an array']);
        }

        return $this->authorRepository->findById(AuthorId::from($id))
            ->then(function (?Author $author) use ($bearerToken, $id, $bio, $links): ResponseInterface|PromiseInterface {
                if ($author === null) {
                    return JsonResponse::create(404, ['error' => 'Author not found']);
                }
                if (!$author->verifyKey($bearerToken)) {
                    return JsonResponse::create(403, ['error' => 'Invalid token for this author']);
                }

                return ($this->updateProfile)(new UpdateAuthorProfileCommand($id, $bio, $links))
                    ->then(fn (?Author $updated): ResponseInterface => $updated === null
                        ? JsonResponse::create(500, ['error' => 'Update failed'])
                        : JsonResponse::create(200, $updated->jsonSerialize()));
            });
    }
}
