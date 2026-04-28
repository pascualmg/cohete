<?php

namespace pascualmg\cohete\ddd\Infrastructure\HttpServer\RequestHandler;
use Cohete\HttpServer\HttpRequestHandler;

use pascualmg\cohete\ddd\Application\Post\UpdatePostCommand;
use pascualmg\cohete\ddd\Application\Post\UpdatePostCommandHandler;
use pascualmg\cohete\ddd\Domain\Entity\Author\Author;
use pascualmg\cohete\ddd\Domain\Entity\Author\ValueObject\AuthorName;
use pascualmg\cohete\ddd\Domain\Entity\AuthorRepository;
use pascualmg\cohete\ddd\Domain\Entity\Post\Post;
use pascualmg\cohete\ddd\Domain\Entity\Post\ValueObject\PostId;
use pascualmg\cohete\ddd\Domain\Entity\PostRepository;
use Cohete\HttpServer\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use React\Promise\PromiseInterface;

class UpdatePostController implements HttpRequestHandler
{
    public function __construct(
        private readonly UpdatePostCommandHandler $updatePostCommandHandler,
        private readonly PostRepository $postRepository,
        private readonly AuthorRepository $authorRepository,
    ) {
    }

    public function __invoke(ServerRequestInterface $request, ?array $routeParams): ResponseInterface|PromiseInterface
    {
        $authHeader = $request->getHeaderLine('Authorization');
        if (empty($authHeader) || !str_starts_with($authHeader, 'Bearer ')) {
            return JsonResponse::create(401, ['error' => 'Authorization: Bearer <token> required']);
        }
        $bearerToken = substr($authHeader, 7);

        $body = (string) $request->getBody();
        try {
            $payload = json_decode($body, true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            return JsonResponse::withError($e);
        }

        $postId = $routeParams['id'];

        return $this->postRepository->findById(PostId::from($postId))->then(
            function (?Post $post) use ($payload, $postId, $bearerToken): ResponseInterface|PromiseInterface {
                if ($post === null) {
                    return JsonResponse::create(404, ['error' => "Post not found: $postId"]);
                }

                $authorName = (string) $post->author;

                return $this->authorRepository->findByName(AuthorName::from($authorName))->then(
                    function (?Author $author) use ($payload, $postId, $bearerToken, $authorName): ResponseInterface {
                        if ($author === null || !$author->verifyKey($bearerToken)) {
                            return JsonResponse::create(403, ['error' => "Invalid token for author '$authorName'"]);
                        }

                        // datePublished lo pone SIEMPRE el servidor al editar,
                        // igual que al crear. Asi la lista refleja la ultima edicion.
                        $datePublished = (new \DateTimeImmutable())->format(\DateTimeInterface::ATOM);

                        ($this->updatePostCommandHandler)(
                            new UpdatePostCommand(
                                $postId,
                                $payload['headline'],
                                $payload['articleBody'],
                                $payload['author'] ?? $authorName,
                                $datePublished,
                                $payload['orgSource'] ?? null,
                            )
                        );

                        return JsonResponse::accepted(['updated' => true, 'id' => $postId]);
                    }
                );
            }
        );
    }
}
