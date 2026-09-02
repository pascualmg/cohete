<?php

namespace pascualmg\cohete\ddd\Infrastructure\HttpServer\RequestHandler;
use Cohete\HttpServer\HttpRequestHandler;

use pascualmg\cohete\ddd\Domain\Entity\PostRepository;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use React\Http\Message\Response;
use React\Promise\PromiseInterface;

class BlogIndexController implements HttpRequestHandler
{
    use BlogListPageTrait;

    public function __construct(
        private readonly PostRepository $postRepository,
    ) {
    }

    public function __invoke(ServerRequestInterface $request, ?array $routeParams): ResponseInterface|PromiseInterface
    {
        $lang = $this->detectLang($request);
        return $this->postRepository->findAll()->then(
            function (array $posts) use ($lang): ResponseInterface {
                return new Response(200, ['Content-Type' => 'text/html; charset=utf-8'], $this->renderHtml($posts, $lang));
            },
            fn (\Throwable $e) => new Response(500, ['Content-Type' => 'text/plain'], $e->getMessage())
        );
    }
}
