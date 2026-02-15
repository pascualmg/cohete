<?php

namespace pascualmg\cohete\ddd\Infrastructure\HttpServer\RequestHandler;

use pascualmg\cohete\ddd\Domain\Entity\Post\Post;
use pascualmg\cohete\ddd\Domain\Entity\Post\ValueObject\Slug;
use pascualmg\cohete\ddd\Domain\Entity\PostRepository;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use React\Http\Message\Response;
use React\Promise\PromiseInterface;

class BlogPostController implements HttpRequestHandler
{
    public function __construct(
        private readonly PostRepository $postRepository,
    ) {
    }

    public function __invoke(ServerRequestInterface $request, ?array $routeParams): ResponseInterface|PromiseInterface
    {
        $slugParam = $routeParams['slug'] ?? '';

        return $this->postRepository->findBySlug(Slug::from($slugParam))->then(
            function (?Post $post) use ($request): ResponseInterface {
                if ($post === null) {
                    return new Response(404, ['Content-Type' => 'text/html; charset=utf-8'],
                        '<!DOCTYPE html><html><body><h1>Post no encontrado</h1><p><a href="/">Volver</a></p></body></html>'
                    );
                }
                return $this->renderHtml($post, $request);
            },
            fn (\Throwable $e) => new Response(500, ['Content-Type' => 'text/plain'], $e->getMessage())
        );
    }

    private function renderHtml(Post $post, ServerRequestInterface $request): ResponseInterface
    {
        $title = htmlspecialchars((string)$post->headline, ENT_QUOTES, 'UTF-8');
        $author = htmlspecialchars((string)$post->author, ENT_QUOTES, 'UTF-8');
        $date = (string)$post->datePublished;
        $body = (string)$post->articleBody;
        $slug = (string)$post->slug;
        $url = $request->getUri()->getScheme() . '://' . $request->getUri()->getHost() . '/blog/' . $slug;
        $description = htmlspecialchars(mb_substr(preg_replace('/\s+/', ' ', strip_tags($body)), 0, 200), ENT_QUOTES, 'UTF-8');

        $html = <<<HTML
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{$title}</title>
    <meta name="description" content="{$description}">
    <meta name="author" content="{$author}">

    <!-- Open Graph / LinkedIn -->
    <meta property="og:type" content="article">
    <meta property="og:title" content="{$title}">
    <meta property="og:description" content="{$description}">
    <meta property="og:url" content="{$url}">
    <meta property="og:site_name" content="Cohete Blog">
    <meta property="article:author" content="{$author}">
    <meta property="article:published_time" content="{$date}">

    <!-- Twitter -->
    <meta name="twitter:card" content="summary">
    <meta name="twitter:title" content="{$title}">
    <meta name="twitter:description" content="{$description}">

    <style>
        :root {
            --bg: #1a1b26;
            --bg2: #24283b;
            --fg: #c0caf5;
            --fg-dim: #565f89;
            --accent: #7aa2f7;
            --green: #9ece6a;
            --orange: #ff9e64;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            background: var(--bg);
            color: var(--fg);
            line-height: 1.7;
        }
        article {
            max-width: 800px;
            margin: 0 auto;
            padding: 2rem 1.5rem;
        }
        header {
            margin-bottom: 2rem;
            padding-bottom: 1.5rem;
            border-bottom: 1px solid var(--bg2);
        }
        header h1 {
            font-size: 2rem;
            color: var(--accent);
            margin-bottom: 0.5rem;
            line-height: 1.3;
        }
        .meta {
            color: var(--fg-dim);
            font-size: 0.9rem;
        }
        .meta span { margin-right: 1.5rem; }
        .content h1, .content h2, .content h3 {
            color: var(--accent);
            margin: 1.5rem 0 0.75rem;
        }
        .content h1 { font-size: 1.6rem; }
        .content h2 { font-size: 1.3rem; color: var(--green); }
        .content h3 { font-size: 1.1rem; color: var(--orange); }
        .content p { margin-bottom: 1rem; }
        .content ul, .content ol {
            margin: 1rem 0;
            padding-left: 2rem;
        }
        .content li { margin-bottom: 0.3rem; }
        .content a { color: var(--accent); }
        .content code {
            background: var(--bg2);
            padding: 0.15rem 0.4rem;
            border-radius: 3px;
            font-size: 0.9em;
        }
        .content pre {
            background: var(--bg2);
            padding: 1rem;
            border-radius: 6px;
            overflow-x: auto;
            margin: 1rem 0;
        }
        .content pre code {
            background: none;
            padding: 0;
        }
        .content blockquote {
            border-left: 3px solid var(--accent);
            padding-left: 1rem;
            color: var(--fg-dim);
            margin: 1rem 0;
        }
        .content strong { color: var(--green); }
        .content em { color: var(--orange); }
        .content table {
            width: 100%;
            border-collapse: collapse;
            margin: 1rem 0;
        }
        .content th, .content td {
            padding: 0.5rem 0.75rem;
            border: 1px solid var(--bg2);
            text-align: left;
        }
        .content th { background: var(--bg2); color: var(--accent); }
        footer {
            margin-top: 3rem;
            padding-top: 1.5rem;
            border-top: 1px solid var(--bg2);
            text-align: center;
            color: var(--fg-dim);
            font-size: 0.85rem;
        }
        .share {
            margin-top: 2rem;
            padding: 1rem;
            background: var(--bg2);
            border-radius: 6px;
            text-align: center;
        }
        .share p { color: var(--fg-dim); font-size: 0.85rem; margin-bottom: 0.5rem; }
        .share input {
            width: 100%;
            background: var(--bg);
            color: var(--accent);
            border: 1px solid var(--fg-dim);
            padding: 0.5rem;
            border-radius: 4px;
            font-size: 0.85rem;
            text-align: center;
            cursor: pointer;
        }
        .share input:focus { outline: 1px solid var(--accent); }
        footer a { color: var(--accent); text-decoration: none; }
        footer a:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <article>
        <header>
            <h1>{$title}</h1>
            <div class="meta">
                <span>{$author}</span>
                <span>{$date}</span>
            </div>
        </header>
        <div class="content">
            {$body}
        </div>
        <div class="share">
            <p>Comparte este post:</p>
            <input type="text" value="https://pascualmg.dev/blog/{$slug}" readonly onclick="this.select();navigator.clipboard.writeText(this.value)">
        </div>
        <footer>
            <p>Publicado en <a href="/">Cohete Blog</a> &mdash; Powered by <a href="https://github.com/pascualmg/cohete">Cohete</a></p>
            <p>MCP endpoint: <code>pascualmg.dev/mcp/sse</code></p>
        </footer>
    </article>
</body>
</html>
HTML;

        return new Response(200, ['Content-Type' => 'text/html; charset=utf-8'], $html);
    }
}
