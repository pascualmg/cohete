<?php

namespace pascualmg\cohete\ddd\Infrastructure\MCP;

use pascualmg\cohete\ddd\Domain\Entity\Author\Author;
use pascualmg\cohete\ddd\Domain\Entity\Author\ValueObject\AuthorName;
use pascualmg\cohete\ddd\Domain\Entity\AuthorRepository;
use pascualmg\cohete\ddd\Domain\Entity\Comment\Comment;
use pascualmg\cohete\ddd\Domain\Entity\Post\Post;
use pascualmg\cohete\ddd\Domain\Entity\Post\ValueObject\PostId;
use pascualmg\cohete\ddd\Domain\Entity\PostRepository;
use pascualmg\cohete\ddd\Domain\Entity\CommentRepository;
use pascualmg\cohete\ddd\Domain\Service\AuthorAuthenticator;
use pascualmg\cohete\ddd\Domain\ValueObject\UuidValueObject;
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
     * Create a new blog post. First time with an author name claims it and returns an author_token.
     * Next times you must provide the author_key to publish as that author.
     */
    #[McpTool(name: 'create_post')]
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
     * Publish a blog post from org-mode content. Requires author_key for authentication.
     */
    #[McpTool(name: 'publish_org')]
    public function publishOrg(string $orgContent, string $author_key = ''): array
    {
        if (!empty($author_key)) {
            $author = await($this->authorAuthenticator->authenticate($author_key));
            if ($author === null) {
                return ['error' => 'Invalid author_key'];
            }
        }

        $metadata = $this->orgToHtmlConverter->extractMetadata($orgContent);
        $html = $this->orgToHtmlConverter->convert($orgContent);

        $postId = (string)UuidValueObject::v4();

        try {
            $dt = new \DateTimeImmutable($metadata['date']);
            $datePublished = $dt->format(\DateTimeInterface::ATOM);
        } catch (\Exception) {
            $datePublished = (new \DateTimeImmutable())->format(\DateTimeInterface::ATOM);
        }

        $authorName = isset($author) ? (string)$author->name : $metadata['author'];

        $post = Post::fromPrimitives(
            $postId,
            $metadata['title'],
            $html,
            $authorName,
            $datePublished,
            $orgContent,
        );

        await($this->postRepository->save($post));

        return [
            'id' => $postId,
            'headline' => $metadata['title'],
            'author' => $authorName,
            'datePublished' => $datePublished,
        ];
    }

    /**
     * Update an existing blog post. Requires author_key matching the post's author.
     */
    #[McpTool(name: 'update_post')]
    public function updatePost(
        string $id,
        string $headline,
        string $articleBody,
        string $author,
        string $datePublished,
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
    #[McpTool(name: 'list_comments')]
    public function listComments(string $post_id): array
    {
        $comments = await($this->commentRepository->findByPostId(PostId::from($post_id)));

        return array_map(
            fn (Comment $c) => $c->jsonSerialize(),
            $comments
        );
    }

    /**
     * Create a comment on a blog post. Open to anyone.
     */
    #[McpTool(name: 'create_comment')]
    public function createComment(string $post_id, string $author_name, string $body): array
    {
        return await(($this->createCommentHandler)(new CreateCommentCommand(
            $post_id,
            $author_name,
            $body,
        )));
    }
}
