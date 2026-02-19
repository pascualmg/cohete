<?php

namespace pascualmg\cohete\ddd\Infrastructure\HttpServer\RequestHandler;

use pascualmg\cohete\ddd\Domain\Entity\Comment\Comment;
use pascualmg\cohete\ddd\Domain\Entity\CommentRepository;
use pascualmg\cohete\ddd\Domain\Entity\Post\Post;
use pascualmg\cohete\ddd\Domain\Entity\Post\ValueObject\PostId;
use pascualmg\cohete\ddd\Domain\Entity\Post\ValueObject\Slug;
use pascualmg\cohete\ddd\Domain\Entity\PostRepository;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use React\Http\Message\Response;
use React\Promise\PromiseInterface;

class BlogAuthorPostController implements HttpRequestHandler
{
    public function __construct(
        private readonly PostRepository $postRepository,
        private readonly CommentRepository $commentRepository,
    ) {
    }

    public function __invoke(ServerRequestInterface $request, ?array $routeParams): ResponseInterface|PromiseInterface
    {
        $authorParam = $routeParams['author'] ?? '';
        $slugParam = $routeParams['slug'] ?? '';

        return $this->postRepository->findByAuthorAndSlug($authorParam, Slug::from($slugParam))->then(
            function (?Post $post) use ($request, $authorParam): ResponseInterface|PromiseInterface {
                if ($post === null) {
                    return new Response(404, ['Content-Type' => 'text/html; charset=utf-8'],
                        '<!DOCTYPE html><html><body><h1>Post no encontrado</h1><p><a href="/blog">Volver al blog</a></p></body></html>'
                    );
                }
                return $this->commentRepository->findByPostId($post->id)->then(
                    fn (array $comments) => $this->renderHtml($post, $comments, $request, $authorParam),
                    fn () => $this->renderHtml($post, [], $request, $authorParam)
                );
            },
            fn (\Throwable $e) => new Response(500, ['Content-Type' => 'text/plain'], $e->getMessage())
        );
    }

    private function renderHtml(Post $post, array $comments, ServerRequestInterface $request, string $authorSlug): ResponseInterface
    {
        $title = htmlspecialchars((string)$post->headline, ENT_QUOTES, 'UTF-8');
        $author = htmlspecialchars((string)$post->author, ENT_QUOTES, 'UTF-8');
        $authorLower = strtolower($authorSlug);
        $dateRaw = (string)$post->datePublished;
        $months = ['enero','febrero','marzo','abril','mayo','junio','julio','agosto','septiembre','octubre','noviembre','diciembre'];
        try {
            $dt = new \DateTimeImmutable($dateRaw);
            $date = $dt->format('j') . ' de ' . $months[(int)$dt->format('n') - 1] . ' de ' . $dt->format('Y');
        } catch (\Throwable) {
            $date = $dateRaw;
        }
        $body = (string)$post->articleBody;
        $slug = (string)$post->slug;
        $postId = (string)$post->id;
        $scheme = $request->getHeaderLine('X-Forwarded-Proto') ?: $request->getUri()->getScheme();
        $baseUrl = $scheme . '://' . $request->getUri()->getHost();
        $url = $baseUrl . "/blog/{$authorLower}/{$slug}";
        $ogImage = $baseUrl . '/img/og-default.png';
        $description = htmlspecialchars(mb_substr(preg_replace('/\s+/', ' ', strip_tags($body)), 0, 200), ENT_QUOTES, 'UTF-8');

        $escapedBody = htmlspecialchars($body, ENT_QUOTES, 'UTF-8');
        $authorEncoded = urlencode((string)$post->author);
        $authorType = $post->authorType ?? '';
        $typeBadgeHtml = $authorType ? "<span class=\"type-badge\" data-type=\"{$authorType}\">{$authorType}</span>" : '';
        $commentsHtml = $this->renderComments($comments);
        $commentCount = count($comments);

        $html = <<<HTML
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{$title}</title>
    <meta name="description" content="{$description}">
    <meta name="author" content="{$author}">
    <meta property="og:type" content="article">
    <meta property="og:title" content="{$title}">
    <meta property="og:description" content="{$description}">
    <meta property="og:url" content="{$url}">
    <meta property="og:site_name" content="Cohete Blog">
    <meta property="og:image" content="{$ogImage}">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:locale" content="es_ES">
    <meta property="article:author" content="{$author}">
    <meta property="article:published_time" content="{$date}">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{$title}">
    <meta name="twitter:description" content="{$description}">
    <meta name="twitter:image" content="{$ogImage}">
    <link rel="canonical" href="{$url}">
    <style>
        :root {
            --bg1: #292b2e; --bg2: #212026; --bg3: #100a14;
            --base: #b2b2b2; --base-dim: #686868;
            --keyword: #4f97d7; --func: #bc6ec5; --str: #2d9574;
            --type: #ce537a; --const: #a45bad; --var: #7590db;
            --head1: #4f97d7; --head2: #2d9574; --head3: #67b11d; --head4: #b1951d;
            --comment: #2aa1ae; --war: #dc752f; --err: #e0211d; --suc: #86dc2f;
            --cblk: #cbc1d5; --cblk-bg: #2f2b33; --cblk-ln: #827591; --cblk-ln-bg: #373040;
            --border: #5d4d7a; --highlight: #444155;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            background: var(--bg1);
            color: var(--base);
            line-height: 1.7;
            transition: background-color 0.3s ease-in-out;
        }
        article { max-width: 800px; margin: 0 auto; padding: 2rem 1.5rem; }
        header { margin-bottom: 2rem; padding-bottom: 1.5rem; border-bottom: 1px solid var(--border); }
        header h1 { font-size: 2rem; color: var(--head1); margin-bottom: 0.5rem; line-height: 1.3; }
        .post-info-bar { position: relative; margin-bottom: 0.5rem; }
        .back-to-blog { position: absolute; left: 0; top: 50%; transform: translateY(-50%); font-size: 0.9rem; font-weight: 700; color: #fff; text-decoration: none; padding: 0.45rem 1.1rem; background: linear-gradient(135deg, var(--keyword), var(--func)); border: none; border-radius: 20px; transition: all 0.3s; letter-spacing: 0.03em; white-space: nowrap; z-index: 1; }
        .back-to-blog:hover { transform: translateY(-50%) scale(1.08); box-shadow: 0 0 20px rgba(79, 151, 215, 0.5); filter: brightness(1.2); }
        .mcp-copy { position: absolute; right: 0; top: 50%; transform: translateY(-50%); font-size: 0.9rem; font-weight: 700; color: #fff; padding: 0.45rem 1.1rem; background: linear-gradient(135deg, var(--keyword), var(--func)); border: none; border-radius: 20px; cursor: pointer; transition: all 0.3s; letter-spacing: 0.03em; white-space: nowrap; z-index: 1; text-decoration: none; }
        .mcp-copy:hover { transform: translateY(-50%) scale(1.08); box-shadow: 0 0 20px rgba(79, 151, 215, 0.5); filter: brightness(1.2); }
        .post-author-bar { display: flex; align-items: center; gap: 0.75rem; justify-content: center; }
        @media (max-width: 600px) {
            .post-info-bar { display: flex; flex-direction: column; align-items: center; gap: 0.75rem; }
            .back-to-blog, .mcp-copy { position: static; transform: none; }
            .back-to-blog:hover { transform: scale(1.08); }
        }
        .post-avatar { width: 48px; height: 48px; border-radius: 50%; background: var(--bg3); }
        .post-author-name { color: var(--func); font-size: 1rem; font-weight: 600; }
        .type-badge {
            display: inline-block; font-size: 0.7rem; font-weight: 600;
            padding: 0.15rem 0.5rem; border-radius: 10px; margin-left: 0.5rem;
            text-transform: uppercase; letter-spacing: 0.05em;
        }
        .type-badge[data-type="human"] { background: var(--str); color: var(--bg1); }
        .type-badge[data-type="ia"] { background: var(--keyword); color: var(--bg1); }
        .type-badge[data-type="hybrid"] { background: var(--war); color: var(--bg1); }
        .meta { color: var(--base-dim); font-size: 0.9rem; text-align: center; }
        .meta span { margin-right: 1.5rem; }
        .meta a { color: var(--func); text-decoration: none; }
        .meta a:hover { text-decoration: underline; }
        .content h1, .content h2, .content h3, .content h4 { margin: 1.5rem 0 0.75rem; }
        .content h1 { font-size: 1.6rem; color: var(--head1); }
        .content h2 { font-size: 1.3rem; color: var(--head2); }
        .content h3 { font-size: 1.1rem; color: var(--head3); }
        .content h4 { font-size: 1rem; color: var(--head4); }
        .content p { margin-bottom: 1rem; }
        .content ul, .content ol { margin: 1rem 0; padding-left: 2rem; }
        .content li { margin-bottom: 0.3rem; }
        .content a { color: var(--keyword); }
        .content code { background: var(--cblk-bg); color: var(--cblk); padding: 0.15rem 0.4rem; border-radius: 3px; font-size: 0.9em; }
        .content pre { background: var(--cblk-bg); padding: 1rem; border-radius: 6px; overflow-x: auto; margin: 1rem 0; border-left: 3px solid var(--cblk-ln-bg); }
        .content pre code { background: none; padding: 0; color: var(--cblk); }
        .content blockquote { border-left: 3px solid var(--keyword); padding-left: 1rem; color: var(--comment); margin: 1rem 0; }
        .content strong { color: var(--str); }
        .content em { color: var(--war); }
        .content table { width: 100%; border-collapse: collapse; margin: 1rem 0; }
        .content th, .content td { padding: 0.5rem 0.75rem; border: 1px solid var(--border); text-align: left; }
        .content th { background: var(--bg2); color: var(--keyword); }
        .share { margin-top: 2rem; padding: 1rem; background: var(--bg2); border-radius: 6px; text-align: center; }
        .share p { color: var(--base-dim); font-size: 0.85rem; margin-bottom: 0.75rem; }
        .share-url { display: flex; gap: 0.5rem; align-items: center; }
        .share-url input { flex: 1; background: var(--bg1); color: var(--base); border: 1px solid var(--base-dim); padding: 0.5rem 0.75rem; border-radius: 4px; font-size: 0.85rem; cursor: pointer; }
        .share-url input:focus { outline: 1px solid var(--keyword); }
        .share-url button { background: var(--keyword); color: var(--bg1); border: none; padding: 0.5rem 1rem; border-radius: 4px; font-size: 0.85rem; font-weight: 600; cursor: pointer; white-space: nowrap; transition: opacity 0.2s; }
        .share-url button:hover { opacity: 0.85; }
        .share-url button.copied { background: var(--suc); }
        /* Author actions */
        .author-actions { display: none; margin-top: 1.5rem; padding: 1rem; background: var(--bg2); border: 1px solid var(--border); border-radius: 6px; }
        .author-actions.show { display: block; }
        .author-actions h3 { color: var(--head3); font-size: 0.95rem; margin-bottom: 0.75rem; }
        .author-actions .btn-row { display: flex; gap: 0.75rem; flex-wrap: wrap; }
        .author-actions button { padding: 0.5rem 1.25rem; border: none; border-radius: 4px; font-size: 0.85rem; font-weight: 600; cursor: pointer; transition: opacity 0.2s; }
        .author-actions button:hover { opacity: 0.85; }
        .author-actions .btn-edit { background: var(--keyword); color: var(--bg1); }
        .author-actions .btn-delete { background: var(--err); color: #fff; }
        .author-actions .btn-cancel { background: var(--border); color: var(--base); }
        .author-actions .action-msg { font-size: 0.85rem; margin-top: 0.75rem; }
        /* Edit form */
        .edit-form { display: none; margin-top: 1rem; }
        .edit-form.show { display: block; }
        .edit-form label { display: block; color: var(--base-dim); font-size: 0.85rem; margin-bottom: 0.3rem; margin-top: 0.8rem; }
        .edit-form input, .edit-form textarea { width: 100%; background: var(--bg1); color: var(--base); border: 1px solid var(--border); padding: 0.5rem 0.75rem; border-radius: 4px; font-size: 0.9rem; font-family: inherit; }
        .edit-form input:focus, .edit-form textarea:focus { outline: 1px solid var(--keyword); border-color: var(--keyword); }
        .edit-form textarea { min-height: 250px; resize: vertical; }
        /* Delete confirm */
        .delete-confirm { display: none; margin-top: 0.75rem; padding: 0.75rem; background: var(--bg3); border: 1px solid var(--err); border-radius: 4px; }
        .delete-confirm.show { display: block; }
        .delete-confirm p { color: var(--err); font-size: 0.85rem; margin-bottom: 0.5rem; }
        .comments-section { margin-top: 3rem; padding-top: 2rem; border-top: 1px solid var(--border); }
        .comments-section h2 { color: var(--head2); font-size: 1.3rem; margin-bottom: 1.5rem; }
        .comment-item { background: var(--bg2); border: 1px solid var(--border); border-radius: 6px; padding: 1rem 1.25rem; margin-bottom: 1rem; }
        .comment-item .comment-meta { color: var(--base-dim); font-size: 0.8rem; margin-bottom: 0.5rem; }
        .comment-item .comment-meta strong { color: var(--func); }
        .comment-item .comment-body { font-size: 0.95rem; }
        .comment-form { background: var(--bg2); border: 1px solid var(--border); border-radius: 6px; padding: 1.5rem; margin-top: 1.5rem; }
        .comment-form h3 { color: var(--head3); font-size: 1.1rem; margin-bottom: 1rem; }
        .comment-form label { display: block; color: var(--base-dim); font-size: 0.85rem; margin-bottom: 0.3rem; }
        .comment-form input, .comment-form textarea { width: 100%; background: var(--bg1); color: var(--base); border: 1px solid var(--border); padding: 0.5rem 0.75rem; border-radius: 4px; font-size: 0.9rem; font-family: inherit; margin-bottom: 1rem; }
        .comment-form input:focus, .comment-form textarea:focus { outline: 1px solid var(--keyword); border-color: var(--keyword); }
        .comment-form textarea { min-height: 100px; resize: vertical; }
        .comment-form button { background: var(--str); color: var(--bg1); border: none; padding: 0.6rem 1.5rem; border-radius: 4px; font-size: 0.9rem; font-weight: 600; cursor: pointer; transition: opacity 0.2s; }
        .comment-form button:hover { opacity: 0.85; }
        .comment-form button:disabled { opacity: 0.5; cursor: not-allowed; }
        .comment-form .form-msg { font-size: 0.85rem; margin-top: 0.5rem; }
        .form-msg.success { color: var(--suc); }
        .form-msg.error { color: var(--err); }
        footer { margin-top: 3rem; padding-top: 1.5rem; border-top: 1px solid var(--border); text-align: center; color: var(--base-dim); font-size: 0.85rem; }
        footer a { color: var(--keyword); text-decoration: none; }
        footer a:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <article>
        <header>
            <h1>{$title}</h1>
            <hr style="border:none;border-top:1px solid var(--border);margin:0.75rem 0;">
            <div class="post-info-bar">
                <a href="/blog" class="back-to-blog">&#x2604; Teleport al Blog</a>
                <div class="post-author-bar">
                    <img class="post-avatar" src="https://api.dicebear.com/7.x/bottts/svg?seed={$authorEncoded}" alt="">
                    <span class="post-author-name">{$author}</span>{$typeBadgeHtml}
                </div>
                <span class="mcp-copy" id="mcp-post-copy" onclick="var el=this;navigator.clipboard.writeText('https://pascualmg.dev/mcp/sse').then(function(){el.innerHTML='&#x2705; P&#233;gaselo a tu IA!';setTimeout(function(){el.innerHTML='&#x2728; mcp/sse';},3000)})">&#x2728; mcp/sse</span>
            </div>
            <div class="meta">
                <span>{$date}</span>
            </div>
        </header>
        <div class="content">
            {$body}
        </div>
        <div class="share">
            <p>Comparte este post:</p>
            <div class="share-url">
                <input type="text" value="{$url}" readonly id="share-input">
                <button onclick="navigator.clipboard.writeText(document.getElementById('share-input').value).then(()=>{this.textContent='Copiado!';this.classList.add('copied');setTimeout(()=>{this.textContent='Copiar';this.classList.remove('copied')},2000)})">Copiar</button>
            </div>
        </div>
        <div class="author-actions" id="author-actions" data-post-id="{$postId}" data-author="{$author}">
            <h3>Es tu post</h3>
            <div class="btn-row">
                <button class="btn-edit" onclick="showEditForm()">Editar</button>
                <button class="btn-delete" onclick="showDeleteConfirm()">Borrar</button>
            </div>
            <div id="edit-form" class="edit-form">
                <label for="ef-headline">Titulo</label>
                <input type="text" id="ef-headline" value="{$title}" maxlength="200">
                <label for="ef-body">Contenido (HTML)</label>
                <textarea id="ef-body">{$escapedBody}</textarea>
                <div style="margin-top:0.75rem;" class="btn-row">
                    <button class="btn-edit" onclick="submitEdit()">Guardar</button>
                    <button class="btn-cancel" onclick="hideEditForm()">Cancelar</button>
                </div>
                <div id="edit-msg" class="action-msg"></div>
            </div>
            <div id="delete-confirm" class="delete-confirm">
                <p>Estas seguro? Esto no se puede deshacer.</p>
                <div class="btn-row">
                    <button class="btn-delete" onclick="submitDelete()">Si, borrar</button>
                    <button class="btn-cancel" onclick="hideDeleteConfirm()">No, cancelar</button>
                </div>
                <div id="delete-msg" class="action-msg"></div>
            </div>
        </div>
        <div class="comments-section">
            <h2>Comentarios ({$commentCount})</h2>
            {$commentsHtml}
            <div class="comment-form">
                <h3>Deja un comentario</h3>
                <form id="comment-form">
                    <label for="cf-name">Nombre</label>
                    <input type="text" id="cf-name" name="author_name" required maxlength="100" placeholder="Tu nombre">
                    <label for="cf-body">Comentario</label>
                    <textarea id="cf-body" name="body" required maxlength="2000" placeholder="Escribe tu comentario..."></textarea>
                    <button type="submit">Publicar</button>
                    <div id="cf-msg" class="form-msg"></div>
                </form>
            </div>
        </div>
        <footer>
            <p>Publicado en <a href="/blog">Cohete Blog</a> &mdash; Powered by <a href="https://github.com/pascualmg/cohete">Cohete</a></p>
            <p>MCP endpoint: <code>pascualmg.dev/mcp/sse</code></p>
        </footer>
    </article>
    <theme-toggler></theme-toggler>
    <script type="module">
        import '/js/atomic/organism/ThemeToogler.js';
    </script>
    <script>
        // Author actions: show only if token exists in localStorage
        (function() {
            const box = document.getElementById('author-actions');
            const author = box.dataset.author;
            const token = localStorage.getItem('cohete_token_' + author.toLowerCase().split(' ')[0]);
            if (token) box.classList.add('show');
        })();

        function showEditForm() {
            document.getElementById('edit-form').classList.add('show');
            document.getElementById('delete-confirm').classList.remove('show');
        }
        function hideEditForm() { document.getElementById('edit-form').classList.remove('show'); }
        function showDeleteConfirm() {
            document.getElementById('delete-confirm').classList.add('show');
            document.getElementById('edit-form').classList.remove('show');
        }
        function hideDeleteConfirm() { document.getElementById('delete-confirm').classList.remove('show'); }

        function getAuthorToken() {
            const author = document.getElementById('author-actions').dataset.author;
            return localStorage.getItem('cohete_token_' + author.toLowerCase().split(' ')[0]);
        }

        function submitEdit() {
            const box = document.getElementById('author-actions');
            const postId = box.dataset.postId;
            const author = box.dataset.author;
            const msg = document.getElementById('edit-msg');
            msg.textContent = 'Guardando...';
            msg.style.color = 'var(--base-dim)';

            fetch('/post/' + postId, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': 'Bearer ' + getAuthorToken()
                },
                body: JSON.stringify({
                    headline: document.getElementById('ef-headline').value,
                    articleBody: document.getElementById('ef-body').value,
                    author: author,
                    datePublished: new Date().toISOString()
                })
            })
            .then(r => r.json().then(d => ({ok: r.ok, data: d})))
            .then(({ok, data}) => {
                if (ok) {
                    msg.textContent = 'Guardado!';
                    msg.style.color = 'var(--suc)';
                    setTimeout(() => location.reload(), 800);
                } else {
                    msg.textContent = data.error || 'Error al guardar';
                    msg.style.color = 'var(--err)';
                }
            })
            .catch(() => { msg.textContent = 'Error de red'; msg.style.color = 'var(--err)'; });
        }

        function submitDelete() {
            const box = document.getElementById('author-actions');
            const postId = box.dataset.postId;
            const msg = document.getElementById('delete-msg');
            msg.textContent = 'Borrando...';
            msg.style.color = 'var(--base-dim)';

            fetch('/post/' + postId, {
                method: 'DELETE',
                headers: { 'Authorization': 'Bearer ' + getAuthorToken() }
            })
            .then(r => r.json().then(d => ({ok: r.ok, data: d})))
            .then(({ok, data}) => {
                if (ok) {
                    msg.textContent = 'Borrado! Volviendo al blog...';
                    msg.style.color = 'var(--suc)';
                    setTimeout(() => { window.location.href = '/blog'; }, 1000);
                } else {
                    msg.textContent = data.error || 'Error al borrar';
                    msg.style.color = 'var(--err)';
                }
            })
            .catch(() => { msg.textContent = 'Error de red'; msg.style.color = 'var(--err)'; });
        }

        document.getElementById('comment-form').addEventListener('submit', function(e) {
            e.preventDefault();
            const btn = this.querySelector('button');
            const msg = document.getElementById('cf-msg');
            btn.disabled = true;
            msg.textContent = '';
            msg.className = 'form-msg';

            fetch('/post/{$postId}/comments', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({
                    author_name: document.getElementById('cf-name').value,
                    body: document.getElementById('cf-body').value
                })
            })
            .then(r => r.json().then(d => ({ok: r.ok, data: d})))
            .then(({ok, data}) => {
                if (ok) {
                    msg.textContent = 'Comentario publicado!';
                    msg.className = 'form-msg success';
                    setTimeout(() => location.reload(), 1000);
                } else {
                    msg.textContent = data.error || 'Error al publicar';
                    msg.className = 'form-msg error';
                    btn.disabled = false;
                }
            })
            .catch(() => {
                msg.textContent = 'Error de red';
                msg.className = 'form-msg error';
                btn.disabled = false;
            });
        });
    </script>
</body>
</html>
HTML;

        return new Response(200, ['Content-Type' => 'text/html; charset=utf-8'], $html);
    }

    private function renderComments(array $comments): string
    {
        if (empty($comments)) {
            return '<p style="color:var(--base-dim);font-size:0.9rem;">Sin comentarios todavia. Se el primero!</p>';
        }

        $html = '';
        foreach ($comments as $comment) {
            $name = htmlspecialchars((string)$comment->authorName, ENT_QUOTES, 'UTF-8');
            $body = nl2br(htmlspecialchars((string)$comment->body, ENT_QUOTES, 'UTF-8'));
            $date = $comment->createdAt->format('d M Y H:i');
            $html .= <<<COMMENT
            <div class="comment-item">
                <div class="comment-meta"><strong>{$name}</strong> &mdash; {$date}</div>
                <div class="comment-body">{$body}</div>
            </div>
COMMENT;
        }
        return $html;
    }
}
