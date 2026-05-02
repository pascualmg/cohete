<?php

namespace pascualmg\cohete\ddd\Infrastructure\MCP;

use pascualmg\cohete\ddd\Domain\Entity\Author\Author;
use pascualmg\cohete\ddd\Domain\Entity\Author\ValueObject\AuthorName;
use pascualmg\cohete\ddd\Application\Media\UploadMediaCommand;
use pascualmg\cohete\ddd\Application\Media\UploadMediaCommandHandler;
use pascualmg\cohete\ddd\Domain\Entity\AuthorRepository;
use pascualmg\cohete\ddd\Domain\Entity\Comment\Comment;
use pascualmg\cohete\ddd\Domain\Entity\Post\Post;
use pascualmg\cohete\ddd\Domain\Entity\Post\ValueObject\PostId;
use pascualmg\cohete\ddd\Domain\Entity\PostRepository;
use pascualmg\cohete\ddd\Domain\Entity\CommentRepository;
use pascualmg\cohete\ddd\Domain\Service\AuthorAuthenticator;
use Cohete\DDD\ValueObject\UuidValueObject;
use pascualmg\cohete\ddd\Infrastructure\Service\OrgToHtmlConverter;
use pascualmg\cohete\ddd\Application\Comment\CreateCommentCommand;
use pascualmg\cohete\ddd\Application\Comment\CreateCommentCommandHandler;
use PhpMcp\Server\Attributes\McpTool;

use function React\Async\await;

class BlogToolHandlers
{
    public function __construct(
        private readonly PostRepository $postRepository,
        private readonly CommentRepository $commentRepository,
        private readonly OrgToHtmlConverter $orgToHtmlConverter,
        private readonly AuthorAuthenticator $authorAuthenticator,
        private readonly CreateCommentCommandHandler $createCommentHandler,
        private readonly AuthorRepository $authorRepository,
        private readonly UploadMediaCommandHandler $uploadMedia,
    ) {
    }

    /**
     * List all blog posts with id, headline, author and date.
     */
    #[McpTool(name: 'list_posts')]
    public function listPosts(): array
    {
        $posts = await($this->postRepository->findAll());

        return array_map(
            fn (Post $post) => [
                'id' => (string)$post->id,
                'headline' => (string)$post->headline,
                'author' => (string)$post->author,
                'datePublished' => (string)$post->datePublished,
            ],
            $posts
        );
    }

    /**
     * Get a single blog post by its UUID, including full HTML body and org source.
     */
    #[McpTool(name: 'get_post')]
    public function getPost(string $id): array
    {
        $post = await($this->postRepository->findById(PostId::from($id)));

        if ($post === null) {
            return ['error' => "Post not found: $id"];
        }

        return $post->jsonSerialize();
    }

    /**
     * Create a blog post with raw HTML body. IMPORTANT: articleBody MUST be valid HTML (e.g. <h2>Title</h2><p>Text</p>).
     * Do NOT send Markdown or plain text — it will render broken. For formatted content, prefer the publish_org tool
     * which accepts org-mode markup and converts it to HTML automatically via Pandoc.
     * First time with a new author name claims it and returns an author_token. Save it — you need it for future posts.
     */
    #[McpTool(name: 'create_post', description: 'Create a blog post. articleBody MUST be HTML. For org-mode content use publish_org instead. First post with a new author name claims it and returns author_token.')]
    public function createPost(string $headline, string $articleBody, string $author, string $author_key = ''): array
    {
        $existingAuthor = await($this->authorRepository->findByName(AuthorName::from($author)));

        if ($existingAuthor !== null) {
            if (empty($author_key)) {
                return ['error' => "Author '$author' already claimed. Provide author_key to publish as this author."];
            }
            if (!$existingAuthor->verifyKey($author_key)) {
                return ['error' => 'Invalid author_key for this author'];
            }
        }

        $plainKey = null;
        if ($existingAuthor === null) {
            [$newAuthor, $plainKey] = Author::register($author);
            await($this->authorRepository->save($newAuthor));
        }

        $postId = (string)UuidValueObject::v4();
        $datePublished = (new \DateTimeImmutable())->format(\DateTimeInterface::ATOM);

        $post = Post::fromPrimitives($postId, $headline, $articleBody, $author, $datePublished);
        await($this->postRepository->save($post));

        $result = [
            'id' => $postId,
            'headline' => $headline,
            'author' => $author,
            'datePublished' => $datePublished,
        ];

        if ($plainKey !== null) {
            $result['author_token'] = $plainKey;
            $result['message'] = 'Welcome! Save this author_token - you need it to publish as this author again.';
        }

        return $result;
    }

    /**
     * Publish a blog post from org-mode content. Pandoc converts it to HTML automatically.
     * Use #+TITLE:, #+AUTHOR:, #+DATE: headers for metadata. This is the RECOMMENDED way to publish
     * formatted content. First time with a new #+AUTHOR claims it and returns author_token.
     */
    #[McpTool(name: 'publish_org', description: 'Publish a blog post from org-mode content (converted to HTML via Pandoc). RECOMMENDED for formatted posts. Use #+TITLE: #+AUTHOR: #+DATE: headers. First post with a new #+AUTHOR claims it and returns author_token.')]
    public function publishOrg(string $orgContent, string $author_key = ''): array
    {
        $metadata = $this->orgToHtmlConverter->extractMetadata($orgContent);
        $html = $this->orgToHtmlConverter->convert($orgContent);

        $authorName = $metadata['author'];
        $plainKey = null;

        if (!empty($author_key)) {
            $author = await($this->authorAuthenticator->authenticate($author_key));
            if ($author === null) {
                return ['error' => 'Invalid author_key'];
            }
            $authorName = (string)$author->name;
        } else {
            $existingAuthor = await($this->authorRepository->findByName(AuthorName::from($authorName)));
            if ($existingAuthor !== null) {
                return ['error' => "Author '$authorName' already claimed. Provide author_key to publish as this author."];
            }
            [$newAuthor, $plainKey] = Author::register($authorName);
            await($this->authorRepository->save($newAuthor));
        }

        $postId = (string)UuidValueObject::v4();

        // datePublished lo pone siempre el servidor, al momento de creacion.
        // El #+DATE del org es informativo para el autor, no autoritativo.
        // Asi garantizamos orden real de publicacion.
        $datePublished = (new \DateTimeImmutable())->format(\DateTimeInterface::ATOM);

        $post = Post::fromPrimitives(
            $postId,
            $metadata['title'],
            $html,
            $authorName,
            $datePublished,
            $orgContent,
        );

        await($this->postRepository->save($post));

        $result = [
            'id' => $postId,
            'headline' => $metadata['title'],
            'author' => $authorName,
            'datePublished' => $datePublished,
        ];

        if ($plainKey !== null) {
            $result['author_token'] = $plainKey;
            $result['message'] = 'Welcome! Save this author_token - you need it to publish as this author again.';
        }

        return $result;
    }

    /**
     * Update an existing blog post. articleBody MUST be valid HTML. Requires author_key matching the post's author.
     */
    #[McpTool(name: 'update_post', description: 'Update a blog post. articleBody MUST be HTML. Requires author_key matching the post author. datePublished is set server-side automatically.')]
    public function updatePost(
        string $id,
        string $headline,
        string $articleBody,
        string $author,
        string $author_key,
        ?string $orgSource = null,
    ): array {
        $existingPost = await($this->postRepository->findById(PostId::from($id)));
        if ($existingPost === null) {
            return ['error' => "Post not found: $id"];
        }

        $postAuthorName = (string)$existingPost->author;
        $existingAuthor = await($this->authorRepository->findByName(AuthorName::from($postAuthorName)));

        if ($existingAuthor === null || !$existingAuthor->verifyKey($author_key)) {
            return ['error' => "Invalid author_key for author '$postAuthorName'"];
        }

        // datePublished lo pone SIEMPRE el servidor al editar, igual que al crear.
        // Asi la lista refleja la ultima edicion.
        $datePublished = (new \DateTimeImmutable())->format(\DateTimeInterface::ATOM);

        $post = Post::fromPrimitives($id, $headline, $articleBody, $author, $datePublished, $orgSource);
        $updated = await($this->postRepository->update($post));

        return $updated
            ? ['updated' => true, 'id' => $id]
            : ['updated' => false, 'error' => "Post not found: $id"];
    }

    /**
     * Delete a blog post by its UUID. Requires author_key matching the post's author.
     */
    #[McpTool(name: 'delete_post')]
    public function deletePost(string $id, string $author_key): array
    {
        $existingPost = await($this->postRepository->findById(PostId::from($id)));
        if ($existingPost === null) {
            return ['error' => "Post not found: $id"];
        }

        $postAuthorName = (string)$existingPost->author;
        $existingAuthor = await($this->authorRepository->findByName(AuthorName::from($postAuthorName)));

        if ($existingAuthor === null || !$existingAuthor->verifyKey($author_key)) {
            return ['error' => "Invalid author_key for author '$postAuthorName'"];
        }

        $deleted = await($this->postRepository->delete(PostId::from($id)));

        return $deleted
            ? ['deleted' => true, 'id' => $id]
            : ['deleted' => false, 'error' => "Post not found: $id"];
    }

    /**
     * List comments for a blog post by post UUID.
     */
    #[McpTool(name: 'list_comments', description: 'List comments for a blog post')]
    public function listComments(string $post_id): array
    {
        $comments = await($this->commentRepository->findByPostId(PostId::from($post_id)));

        return array_map(
            fn (Comment $c) => $c->jsonSerialize(),
            $comments
        );
    }

    /**
     * Create a comment on a blog post. Open to anyone, no authentication needed.
     */
    #[McpTool(name: 'create_comment', description: 'Create a comment on a blog post. Open to anyone.')]
    public function createComment(string $post_id, string $author_name, string $body): array
    {
        return await(($this->createCommentHandler)(new CreateCommentCommand(
            $post_id,
            $author_name,
            $body,
        )));
    }

    /**
     * DEPRECATED: usar upload_media. Este sube a /img/ del filesystem local
     * del blog (no a Garage). Se mantiene para compatibilidad con posts ya
     * publicados que referencian /img/{filename}.
     */
    #[McpTool(name: 'upload_asset', description: 'DEPRECATED: usar upload_media. Sube imagenes al filesystem local del blog (no a Garage S3). Se mantiene por compatibilidad. Provide base64_content and filename. Allowed: png, jpg, jpeg, webp, gif, svg. Max 5MB.')]
    public function uploadAsset(string $base64_content, string $filename): array
    {
        $allowedExtensions = ['png', 'jpg', 'jpeg', 'webp', 'gif', 'svg'];
        $maxSize = 5 * 1024 * 1024; // 5MB

        // Sanitize filename
        $filename = basename($filename);
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

        if (!in_array($ext, $allowedExtensions, true)) {
            return ['error' => "Extension not allowed: $ext. Allowed: " . implode(', ', $allowedExtensions)];
        }

        $data = base64_decode($base64_content, true);
        if ($data === false) {
            return ['error' => 'Invalid base64 content'];
        }

        if (strlen($data) > $maxSize) {
            return ['error' => 'File too large. Max: 5MB, got: ' . round(strlen($data) / 1024 / 1024, 1) . 'MB'];
        }

        $targetDir = __DIR__ . '/../webserver/html/img';
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0755, true);
        }

        $targetPath = $targetDir . '/' . $filename;

        // Async write via ReactPHP WritableResourceStream
        $deferred = new \React\Promise\Deferred();
        $fh = fopen($targetPath, 'wb');
        if ($fh === false) {
            return ['error' => 'Failed to open file for writing'];
        }

        $stream = new \React\Stream\WritableResourceStream($fh);
        $stream->end($data);
        $stream->on('close', function () use ($deferred, $filename, $data) {
            $deferred->resolve([
                'url' => '/img/' . $filename,
                'size' => strlen($data),
                'success' => true,
            ]);
        });
        $stream->on('error', function (\Exception $e) use ($deferred) {
            $deferred->resolve(['error' => 'Write failed: ' . $e->getMessage()]);
        });

        return await($deferred->promise());
    }

    /**
     * Upload de cualquier media a Garage S3 (audios, imagenes, lo que sea).
     * Recibe base64_content + content_type. Devuelve URL publica /media/{id}
     * que se puede embeber en posts (audio, img, video).
     *
     * Limite 50MB. Persiste en bucket Garage replicado, accesible mientras
     * aurin este vivo (Garage corre alli). Si aurin se duerme, los media
     * vuelven cuando despierta.
     */
    #[McpTool(name: 'upload_media', description: 'Upload binary media (audio, image, video) to Garage S3. Provide base64_content and content_type (e.g. audio/wav, image/png). Returns id and url. Embed in posts as <audio src="/media/{id}"> o <img src="/media/{id}">. Max 50MB. Requires author_key.')]
    public function uploadMedia(string $base64_content, string $content_type, string $author_key): array
    {
        $author = await($this->authorAuthenticator->authenticate($author_key));
        if ($author === null) {
            return ['error' => 'Invalid author_key'];
        }

        $body = base64_decode($base64_content, true);
        if ($body === false) {
            return ['error' => 'Invalid base64 content'];
        }

        $maxSize = 50 * 1024 * 1024;
        if (strlen($body) > $maxSize) {
            return ['error' => 'Payload too large (max 50MB), got ' . round(strlen($body) / 1024 / 1024, 1) . 'MB'];
        }

        // Strip charset / params del content_type
        if (str_contains($content_type, ';')) {
            $content_type = trim(strstr($content_type, ';', true));
        }

        $result = await(($this->uploadMedia)(new UploadMediaCommand(
            contentType: $content_type,
            body:        $body,
            authorName:  (string)$author->name,
        )));

        return array_merge($result, [
            'url' => '/media/' . $result['id'],
        ]);
    }
}
