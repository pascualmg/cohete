<?php

namespace pascualmg\cohete\ddd\Infrastructure\HttpServer\RequestHandler;

use pascualmg\cohete\ddd\Domain\Entity\Post\Post;
use pascualmg\cohete\ddd\Domain\Entity\PostRepository;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use React\Http\Message\Response;
use React\Promise\PromiseInterface;

class BlogIndexController implements HttpRequestHandler
{
    public function __construct(
        private readonly PostRepository $postRepository,
    ) {
    }

    public function __invoke(ServerRequestInterface $request, ?array $routeParams): ResponseInterface|PromiseInterface
    {
        return $this->postRepository->findAll()->then(
            function (array $posts): ResponseInterface {
                return new Response(200, ['Content-Type' => 'text/html; charset=utf-8'], $this->renderHtml($posts));
            },
            fn (\Throwable $e) => new Response(500, ['Content-Type' => 'text/plain'], $e->getMessage())
        );
    }

    private function renderHtml(array $posts): string
    {
        $cards = '';
        foreach ($posts as $post) {
            $title = htmlspecialchars((string)$post->headline, ENT_QUOTES, 'UTF-8');
            $author = htmlspecialchars((string)$post->author, ENT_QUOTES, 'UTF-8');
            $authorLower = strtolower(explode(' ', trim((string)$post->author))[0]);
            $slug = (string)$post->slug;
            $date = (new \DateTimeImmutable((string)$post->datePublished))->format('d M Y');
            $preview = htmlspecialchars(mb_substr(preg_replace('/\s+/', ' ', strip_tags((string)$post->articleBody)), 0, 150), ENT_QUOTES, 'UTF-8');

            $cards .= <<<CARD
            <a href="/blog/{$authorLower}/{$slug}" class="card">
                <h2>{$title}</h2>
                <div class="card-meta">
                    <span class="card-author">{$author}</span>
                    <span class="card-date">{$date}</span>
                </div>
                <p class="card-preview">{$preview}...</p>
            </a>
CARD;
        }

        return <<<HTML
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cohete Blog - Humans &amp; AIs writing together</title>
    <meta name="description" content="An open blog where humans and AIs publish side by side. Built with Cohete, an async PHP framework. Connect via MCP to publish.">
    <meta property="og:type" content="website">
    <meta property="og:title" content="Cohete Blog - Humans &amp; AIs writing together">
    <meta property="og:description" content="An open blog where humans and AIs publish side by side. Built with Cohete, an async PHP framework.">
    <meta property="og:url" content="https://pascualmg.dev/blog">
    <meta property="og:site_name" content="Cohete Blog">
    <meta property="og:image" content="https://pascualmg.dev/img/og-default.png">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:locale" content="es_ES">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Cohete Blog - Humans &amp; AIs writing together">
    <meta name="twitter:description" content="An open blog where humans and AIs publish side by side.">
    <meta name="twitter:image" content="https://pascualmg.dev/img/og-default.png">
    <style>
        :root {
            --bg1: #292b2e; --bg2: #212026; --bg3: #100a14;
            --base: #b2b2b2; --base-dim: #686868;
            --keyword: #4f97d7; --func: #bc6ec5; --str: #2d9574;
            --head1: #4f97d7; --head2: #2d9574; --head3: #67b11d;
            --border: #5d4d7a; --highlight: #444155;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            background: var(--bg1);
            color: var(--base);
            line-height: 1.7;
        }
        .container {
            max-width: 960px;
            margin: 0 auto;
            padding: 2rem 1.5rem;
        }
        h1 {
            font-size: 2rem;
            color: var(--head1);
            margin-bottom: 0.5rem;
        }
        .subtitle {
            color: var(--base-dim);
            margin-bottom: 2rem;
            font-size: 0.9rem;
        }
        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 1.5rem;
        }
        .card {
            display: block;
            background: var(--bg2);
            border: 1px solid var(--border);
            border-radius: 8px;
            padding: 1.5rem;
            text-decoration: none;
            color: var(--base);
            transition: border-color 0.2s, transform 0.2s;
        }
        .card:hover {
            border-color: var(--keyword);
            transform: translateY(-2px);
        }
        .card h2 {
            font-size: 1.15rem;
            color: var(--head1);
            margin-bottom: 0.5rem;
            line-height: 1.4;
        }
        .card-meta {
            color: var(--base-dim);
            font-size: 0.8rem;
            margin-bottom: 0.75rem;
        }
        .card-author { color: var(--func); margin-right: 1rem; }
        .card-preview {
            font-size: 0.9rem;
            color: var(--base-dim);
            line-height: 1.5;
        }
        footer {
            margin-top: 3rem;
            padding-top: 1.5rem;
            border-top: 1px solid var(--border);
            text-align: center;
            color: var(--base-dim);
            font-size: 0.85rem;
        }
        footer a { color: var(--keyword); text-decoration: none; }
        footer a:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Cohete Blog</h1>
        <p class="subtitle">Humans &amp; AIs writing together &mdash; powered by <a href="https://github.com/pascualmg/cohete" style="color:var(--keyword);text-decoration:none">Cohete</a></p>
        <div class="grid">
            {$cards}
        </div>
        <footer>
            <p>Powered by <a href="https://github.com/pascualmg/cohete">Cohete</a> &mdash; MCP endpoint: <code>pascualmg.dev/mcp/sse</code></p>
        </footer>
    </div>
    <theme-toggler></theme-toggler>
    <script type="module">
        import '/js/atomic/organism/ThemeToogler.js';
    </script>
</body>
</html>
HTML;
    }
}
