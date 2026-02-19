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
        $scheme = $request->getHeaderLine('X-Forwarded-Proto') ?: $request->getUri()->getScheme();
        $url = $scheme . '://' . $request->getUri()->getHost() . '/blog/' . $slug;
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
        /* Spacemacs-dark defaults (ThemeSwitcher overrides these via JS) */
        :root {
            --bg1: #292b2e; --bg2: #212026; --bg3: #100a14;
            --base: #b2b2b2; --base-dim: #686868;
            --keyword: #4f97d7; --func: #bc6ec5; --str: #2d9574;
            --type: #ce537a; --const: #a45bad; --var: #7590db;
            --head1: #4f97d7; --head2: #2d9574; --head3: #67b11d; --head4: #b1951d;
            --comment: #2aa1ae; --war: #dc752f; --err: #e0211d; --suc: #86dc2f;
            --cblk: #cbc1d5; --cblk-bg: #2f2b33; --cblk-ln: #827591; --cblk-ln-bg: #373040;
            --border: #5d4d7a; --highlight: #444155;
            --aqua: #2d9574; --green: #67b11d; --cyan: #28def0;
            --magenta: #a31db1; --blue: #4f97d7; --yellow: #b1951d; --red: #f2241f;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            background: var(--bg1);
            color: var(--base);
            line-height: 1.7;
            transition: background-color 0.3s ease-in-out;
        }
        article {
            max-width: 800px;
            margin: 0 auto;
            padding: 2rem 1.5rem;
        }
        header {
            margin-bottom: 2rem;
            padding-bottom: 1.5rem;
            border-bottom: 1px solid var(--border);
        }
        header h1 {
            font-size: 2rem;
            color: var(--head1);
            margin-bottom: 0.5rem;
            line-height: 1.3;
        }
        .meta {
            color: var(--base-dim);
            font-size: 0.9rem;
        }
        .meta span { margin-right: 1.5rem; }
        .content h1, .content h2, .content h3, .content h4 {
            margin: 1.5rem 0 0.75rem;
        }
        .content h1 { font-size: 1.6rem; color: var(--head1); }
        .content h2 { font-size: 1.3rem; color: var(--head2); }
        .content h3 { font-size: 1.1rem; color: var(--head3); }
        .content h4 { font-size: 1rem; color: var(--head4); }
        .content p { margin-bottom: 1rem; }
        .content ul, .content ol {
            margin: 1rem 0;
            padding-left: 2rem;
        }
        .content li { margin-bottom: 0.3rem; }
        .content a { color: var(--keyword); }
        .content code {
            background: var(--cblk-bg);
            color: var(--cblk);
            padding: 0.15rem 0.4rem;
            border-radius: 3px;
            font-size: 0.9em;
        }
        .content pre {
            background: var(--cblk-bg);
            padding: 1rem;
            border-radius: 6px;
            overflow-x: auto;
            margin: 1rem 0;
            border-left: 3px solid var(--cblk-ln-bg);
        }
        .content pre code {
            background: none;
            padding: 0;
            color: var(--cblk);
        }
        .content blockquote {
            border-left: 3px solid var(--keyword);
            padding-left: 1rem;
            color: var(--comment);
            margin: 1rem 0;
        }
        .content strong { color: var(--str); }
        .content em { color: var(--war); }
        .content table {
            width: 100%;
            border-collapse: collapse;
            margin: 1rem 0;
        }
        .content th, .content td {
            padding: 0.5rem 0.75rem;
            border: 1px solid var(--border);
            text-align: left;
        }
        .content th { background: var(--bg2); color: var(--keyword); }
        footer {
            margin-top: 3rem;
            padding-top: 1.5rem;
            border-top: 1px solid var(--border);
            text-align: center;
            color: var(--base-dim);
            font-size: 0.85rem;
        }
        .share {
            margin-top: 2rem;
            padding: 1rem;
            background: var(--bg2);
            border-radius: 6px;
            text-align: center;
        }
        .share p { color: var(--base-dim); font-size: 0.85rem; margin-bottom: 0.75rem; }
        .share-url {
            display: flex;
            gap: 0.5rem;
            align-items: center;
        }
        .share-url input {
            flex: 1;
            background: var(--bg1);
            color: var(--base);
            border: 1px solid var(--base-dim);
            padding: 0.5rem 0.75rem;
            border-radius: 4px;
            font-size: 0.85rem;
            cursor: pointer;
        }
        .share-url input:focus { outline: 1px solid var(--keyword); }
        .share-url button {
            background: var(--keyword);
            color: var(--bg1);
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 4px;
            font-size: 0.85rem;
            font-weight: 600;
            cursor: pointer;
            white-space: nowrap;
            transition: opacity 0.2s;
        }
        .share-url button:hover { opacity: 0.85; }
        .share-url button.copied {
            background: var(--suc);
        }
        footer a { color: var(--keyword); text-decoration: none; }
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
            <div class="share-url">
                <input type="text" value="https://pascualmg.dev/blog/{$slug}" readonly id="share-input">
                <button onclick="navigator.clipboard.writeText(document.getElementById('share-input').value).then(()=>{this.textContent='Copiado!';this.classList.add('copied');setTimeout(()=>{this.textContent='Copiar';this.classList.remove('copied')},2000)})">Copiar</button>
            </div>
        </div>
        <footer>
            <p>Publicado en <a href="/">Cohete Blog</a> &mdash; Powered by <a href="https://github.com/pascualmg/cohete">Cohete</a></p>
            <p>MCP endpoint: <code>pascualmg.dev/mcp/sse</code></p>
        </footer>
    </article>
    <theme-toggler></theme-toggler>
    <script type="module">
        import '/js/atomic/organism/ThemeToogler.js';
    </script>
</body>
</html>
HTML;

        return new Response(200, ['Content-Type' => 'text/html; charset=utf-8'], $html);
    }
}
