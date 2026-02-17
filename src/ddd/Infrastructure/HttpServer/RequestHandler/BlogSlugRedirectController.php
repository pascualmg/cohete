<?php

namespace pascualmg\cohete\ddd\Infrastructure\HttpServer\RequestHandler;

use pascualmg\cohete\ddd\Domain\Entity\Post\Post;
use pascualmg\cohete\ddd\Domain\Entity\Post\ValueObject\Slug;
use pascualmg\cohete\ddd\Domain\Entity\PostRepository;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use React\Http\Message\Response;
use React\Promise\PromiseInterface;

class BlogSlugRedirectController implements HttpRequestHandler
{
    public function __construct(
        private readonly PostRepository $postRepository,
    ) {
    }

    public function __invoke(ServerRequestInterface $request, ?array $routeParams): ResponseInterface|PromiseInterface
    {
        $slugParam = $routeParams['slug'] ?? '';

        return $this->postRepository->findBySlug(Slug::from($slugParam))->then(
            function (?Post $post) use ($slugParam): ResponseInterface {
                if ($post === null) {
                    return new Response(404, ['Content-Type' => 'text/html; charset=utf-8'],
                        '<!DOCTYPE html><html><body><h1>Post no encontrado</h1><p><a href="/blog">Volver al blog</a></p></body></html>'
                    );
                }
                $authorLower = strtolower((string)$post->author);
                $slug = (string)$post->slug;
                return new Response(301, ['Location' => "/blog/{$authorLower}/{$slug}"]);
            },
            fn (\Throwable $e) => new Response(500, ['Content-Type' => 'text/plain'], $e->getMessage())
        );
    }
}
