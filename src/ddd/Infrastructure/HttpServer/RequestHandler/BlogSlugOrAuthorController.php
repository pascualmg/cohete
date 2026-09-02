<?php

namespace pascualmg\cohete\ddd\Infrastructure\HttpServer\RequestHandler;
use Cohete\HttpServer\HttpRequestHandler;

use pascualmg\cohete\ddd\Domain\Entity\Author\ValueObject\AuthorSlug;
use pascualmg\cohete\ddd\Domain\Entity\Post\Post;
use pascualmg\cohete\ddd\Domain\Entity\Post\ValueObject\Slug;
use pascualmg\cohete\ddd\Domain\Entity\PostRepository;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use React\Http\Message\Response;
use React\Promise\PromiseInterface;

/**
 * GET /blog/{slug} es ambiguo a proposito: FastRoute no permite dos rutas
 * con la misma forma ("/blog/{slug}" y "/blog/{author}"), asi que un unico
 * segmento tras /blog puede significar dos cosas y las resolvemos aqui, en
 * ese orden:
 *   1. Es el slug de un post -> 301 a su URL canonica /blog/{authorSlug}/{slug}.
 *   2. Si no, es el slug de un autor -> listado de sus posts (pagina de autor).
 *   3. Si tampoco -> 404.
 */
class BlogSlugOrAuthorController implements HttpRequestHandler
{
    use BlogListPageTrait;

    public function __construct(
        private readonly PostRepository $postRepository,
    ) {
    }

    public function __invoke(ServerRequestInterface $request, ?array $routeParams): ResponseInterface|PromiseInterface
    {
        $slugParam = $routeParams['slug'] ?? '';
        $lang = $this->detectLang($request);

        return $this->postRepository->findBySlug(Slug::from($slugParam))->then(
            function (?Post $post) use ($slugParam, $lang): ResponseInterface|PromiseInterface {
                if ($post !== null) {
                    $authorSlug = (string) AuthorSlug::from((string)$post->author);
                    $slug = (string)$post->slug;
                    return new Response(301, ['Location' => "/blog/{$authorSlug}/{$slug}"]);
                }
                return $this->renderAuthorIndex($slugParam, $lang);
            },
            fn (\Throwable $e) => new Response(500, ['Content-Type' => 'text/plain'], $e->getMessage())
        );
    }

    private function renderAuthorIndex(string $authorParam, string $lang): PromiseInterface
    {
        $wanted = AuthorSlug::from($authorParam);

        return $this->postRepository->findAll()->then(
            function (array $posts) use ($wanted, $lang): ResponseInterface {
                $authorPosts = array_values(array_filter(
                    $posts,
                    fn (Post $post): bool => AuthorSlug::from((string)$post->author)->equals($wanted)
                ));

                if (empty($authorPosts)) {
                    $notFound = $lang === 'en' ? 'Post not found' : 'Post no encontrado';
                    $back = $lang === 'en' ? 'Back to blog' : 'Volver al blog';
                    return new Response(404, ['Content-Type' => 'text/html; charset=utf-8'],
                        "<!DOCTYPE html><html lang=\"{$lang}\"><body><h1>{$notFound}</h1><p><a href=\"/blog\">{$back}</a></p></body></html>"
                    );
                }

                $authorDisplayName = (string) $authorPosts[0]->author;
                return new Response(200, ['Content-Type' => 'text/html; charset=utf-8'],
                    $this->renderHtml($authorPosts, $lang, $authorDisplayName)
                );
            },
            fn (\Throwable $e) => new Response(500, ['Content-Type' => 'text/plain'], $e->getMessage())
        );
    }
}
