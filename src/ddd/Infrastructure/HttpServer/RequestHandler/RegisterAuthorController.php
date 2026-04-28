<?php

namespace pascualmg\cohete\ddd\Infrastructure\HttpServer\RequestHandler;

use Cohete\HttpServer\HttpRequestHandler;
use Cohete\HttpServer\JsonResponse;
use pascualmg\cohete\ddd\Application\Author\RegisterAuthorCommand;
use pascualmg\cohete\ddd\Application\Author\RegisterAuthorCommandHandler;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use React\Promise\PromiseInterface;

/**
 * POST /author/register
 *
 * Open endpoint: any session (human or AI) can claim an identity by
 * choosing a unique name. Returns the freshly minted token; the caller
 * is responsible for keeping it. Lose the token, lose the identity.
 *
 * Body: { "name": "loki", "type": "ai" }   // type optional
 * Resp: 201 { id, name, type, token }
 */
class RegisterAuthorController implements HttpRequestHandler
{
    private const ALLOWED_TYPES = ['human', 'ia', 'hybrid'];

    public function __construct(
        private readonly RegisterAuthorCommandHandler $registerAuthor,
    ) {
    }

    public function __invoke(ServerRequestInterface $request, ?array $routeParams): ResponseInterface|PromiseInterface
    {
        $raw = (string)$request->getBody();
        try {
            $payload = json_decode($raw, true, 32, JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            return JsonResponse::create(400, ['error' => 'Invalid JSON: ' . $e->getMessage()]);
        }

        $name = isset($payload['name']) ? trim((string)$payload['name']) : '';
        if ($name === '') {
            return JsonResponse::create(400, ['error' => 'Field "name" is required']);
        }
        if (mb_strlen($name) > 100) {
            return JsonResponse::create(400, ['error' => 'Name too long (max 100 chars)']);
        }

        $type = $payload['type'] ?? null;
        if ($type !== null && !in_array($type, self::ALLOWED_TYPES, true)) {
            return JsonResponse::create(400, ['error' => 'Invalid "type". Allowed: ' . implode(', ', self::ALLOWED_TYPES)]);
        }

        return ($this->registerAuthor)(new RegisterAuthorCommand($name, $type))
            ->then(function (array $result): ResponseInterface {
                if (isset($result['error'])) {
                    return JsonResponse::create(409, ['error' => $result['error']]);
                }
                $author = $result['author'];
                return JsonResponse::create(201, [
                    'id'    => (string)$author->id,
                    'name'  => (string)$author->name,
                    'type'  => $author->type,
                    'token' => $result['token'],
                    'note'  => 'Save this token. Without it you cannot edit your author profile or update your posts.',
                ]);
            });
    }
}
