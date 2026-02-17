<?php

namespace pascualmg\cohete\ddd\Infrastructure\HttpServer\RequestHandler;

use pascualmg\cohete\ddd\Domain\Entity\Author\Author;
use pascualmg\cohete\ddd\Domain\Entity\Author\ValueObject\AuthorName;
use pascualmg\cohete\ddd\Domain\Entity\AuthorRepository;
use pascualmg\cohete\ddd\Domain\Entity\Post\Post;
use pascualmg\cohete\ddd\Domain\Entity\Post\ValueObject\PostId;
use pascualmg\cohete\ddd\Domain\Entity\PostRepository;
use pascualmg\cohete\ddd\Infrastructure\HttpServer\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use React\Promise\PromiseInterface;

class DeletePostController implements HttpRequestHandler
{
    public function __construct(
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

        $postId = $routeParams['id'];

        return $this->postRepository->findById(PostId::from($postId))->then(
            function (?Post $post) use ($postId, $bearerToken): ResponseInterface|PromiseInterface {
                if ($post === null) {
                    return JsonResponse::create(404, ['error' => "Post not found: $postId"]);
                }

                $authorName = (string) $post->author;

                return $this->authorRepository->findByName(AuthorName::from($authorName))->then(
                    function (?Author $author) use ($postId, $bearerToken, $authorName): ResponseInterface|PromiseInterface {
                        if ($author === null || !$author->verifyKey($bearerToken)) {
                            return JsonResponse::create(403, ['error' => "Invalid token for author '$authorName'"]);
                        }

                        return $this->postRepository->delete(PostId::from($postId))->then(
                            fn (bool $deleted) => $deleted
                                ? JsonResponse::create(200, ['deleted' => true, 'id' => $postId])
                                : JsonResponse::create(500, ['error' => 'Delete failed'])
                        );
                    }
                );
            }
        );
    }
}
