<?php

namespace pascualmg\cohete\ddd\Infrastructure\HttpServer\RequestHandler;

use pascualmg\cohete\ddd\Application\Post\CreatePostCommand;
use pascualmg\cohete\ddd\Application\Post\CreatePostCommandHandler;
use pascualmg\cohete\ddd\Domain\Entity\Author\Author;
use pascualmg\cohete\ddd\Domain\Entity\Author\ValueObject\AuthorName;
use pascualmg\cohete\ddd\Domain\Entity\AuthorRepository;
use pascualmg\cohete\ddd\Domain\ValueObject\UuidValueObject;
use pascualmg\cohete\ddd\Infrastructure\HttpServer\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use React\Promise\PromiseInterface;

class CreatePostController implements HttpRequestHandler
{
    public function __construct(
        private readonly CreatePostCommandHandler $createPostCommandHandler,
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

        $authorName = $payload['author'] ?? null;
        if (empty($authorName)) {
            return JsonResponse::create(400, ['error' => 'author is required']);
        }

        $authHeader = $request->getHeaderLine('Authorization');
        $bearerToken = (!empty($authHeader) && str_starts_with($authHeader, 'Bearer '))
            ? substr($authHeader, 7)
            : null;

        return $this->authorRepository->findByName(AuthorName::from($authorName))->then(
            function (?Author $existingAuthor) use ($payload, $bearerToken): ResponseInterface|PromiseInterface {
                if ($existingAuthor !== null) {
                    // Author exists: require valid token
                    if ($bearerToken === null) {
                        return JsonResponse::create(401, [
                            'error' => "Author '{$payload['author']}' already claimed. Provide your token via Authorization: Bearer <token>",
                        ]);
                    }
                    if (!$existingAuthor->verifyKey($bearerToken)) {
                        return JsonResponse::create(403, [
                            'error' => 'Invalid token for this author',
                        ]);
                    }

                    return $this->createPost($payload);
                }

                // New author: register and return token
                [$author, $plainKey] = Author::register($payload['author']);

                return $this->authorRepository->save($author)->then(
                    function () use ($payload, $plainKey): ResponseInterface {
                        $response = $this->createPost($payload);

                        // Decode to inject the token, re-encode
                        $data = json_decode((string) $response->getBody(), true);
                        $data['author_token'] = $plainKey;
                        $data['message'] = 'Welcome! Save this author_token - you will need it to publish as this author again.';

                        return JsonResponse::create(201, $data);
                    }
                );
            }
        );
    }

    private function createPost(array $payload): ResponseInterface
    {
        $postId = $payload['id'] ?? (string) UuidValueObject::v4();

        ($this->createPostCommandHandler)(
            new CreatePostCommand(
                $postId,
                $payload['headline'],
                $payload['articleBody'],
                $payload['author'],
                $payload['datePublished'] ?? (new \DateTimeImmutable())->format(\DateTimeInterface::ATOM),
            )
        );

        return JsonResponse::create(201, [
            'id' => $postId,
            'headline' => $payload['headline'],
            'author' => $payload['author'],
        ]);
    }
}
