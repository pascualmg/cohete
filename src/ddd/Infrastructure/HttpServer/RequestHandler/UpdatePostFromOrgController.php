<?php

namespace pascualmg\cohete\ddd\Infrastructure\HttpServer\RequestHandler;

use Cohete\HttpServer\HttpRequestHandler;
use Cohete\HttpServer\JsonResponse;
use pascualmg\cohete\ddd\Application\Post\UpdatePostCommand;
use pascualmg\cohete\ddd\Application\Post\UpdatePostCommandHandler;
use pascualmg\cohete\ddd\Domain\Entity\Author\Author;
use pascualmg\cohete\ddd\Domain\Entity\Author\ValueObject\AuthorName;
use pascualmg\cohete\ddd\Domain\Entity\AuthorRepository;
use pascualmg\cohete\ddd\Domain\Entity\Post\Post;
use pascualmg\cohete\ddd\Domain\Entity\Post\ValueObject\PostId;
use pascualmg\cohete\ddd\Domain\Entity\PostRepository;
use pascualmg\cohete\ddd\Infrastructure\Service\OrgToHtmlConverter;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use React\Promise\PromiseInterface;

/**
 * PUT /post/org/{id}
 *
 * Acepta raw .org en el body (igual que POST /post/org) pero actualiza un
 * post existente PRESERVANDO el UUID. El cliente firma con su Bearer
 * y el servidor verifica que ese Bearer corresponde al author actual
 * del post antes de aplicar.
 *
 * Util para el flujo de "post vivo": un mismo .org va creciendo y se
 * republica al mismo UUID sin que cambien permalinks.
 *
 * datePublished se actualiza a ahora (igual que en PUT /post/{id} JSON),
 * para que la lista refleje la ultima edicion. El orgSource original se
 * sustituye por el nuevo body. #+SLUG del frontmatter se respeta (igual
 * que en POST /post/org).
 */
class UpdatePostFromOrgController implements HttpRequestHandler
{
    public function __construct(
        private readonly UpdatePostCommandHandler $updatePostCommandHandler,
        private readonly OrgToHtmlConverter $orgToHtmlConverter,
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
        $orgContent = (string) $request->getBody();
        if (empty(trim($orgContent))) {
            return JsonResponse::create(400, ['error' => 'Empty org content']);
        }

        return $this->postRepository->findById(PostId::from($postId))->then(
            function (?Post $post) use ($orgContent, $postId, $bearerToken): ResponseInterface|PromiseInterface {
                if ($post === null) {
                    return JsonResponse::create(404, ['error' => "Post not found: $postId"]);
                }

                $authorName = (string) $post->author;

                return $this->authorRepository->findByName(AuthorName::from($authorName))->then(
                    function (?Author $author) use ($orgContent, $postId, $bearerToken, $authorName): ResponseInterface {
                        if ($author === null || !$author->verifyKey($bearerToken)) {
                            return JsonResponse::create(403, ['error' => "Invalid token for author '$authorName'"]);
                        }

                        try {
                            $metadata = $this->orgToHtmlConverter->extractMetadata($orgContent);
                            $html = $this->orgToHtmlConverter->convert($orgContent);
                        } catch (\Throwable $e) {
                            return JsonResponse::withError($e);
                        }

                        // datePublished a ahora (refleja ultima edicion).
                        $datePublished = (new \DateTimeImmutable())->format(\DateTimeInterface::ATOM);

                        // #+SLUG opcional. Si no esta, se deriva del nuevo headline.
                        $slug = $metadata['slug'] ?? null;

                        ($this->updatePostCommandHandler)(
                            new UpdatePostCommand(
                                $postId,
                                $metadata['title'],
                                $html,
                                $authorName,
                                $datePublished,
                                $orgContent,
                                $slug,
                            )
                        );

                        return JsonResponse::accepted([
                            'updated' => true,
                            'id' => $postId,
                            'headline' => $metadata['title'],
                            'author' => $authorName,
                            'datePublished' => $datePublished,
                        ]);
                    }
                );
            }
        );
    }
}
