<?php

namespace pascualmg\cohete\ddd\Infrastructure\MCP;

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
     * Create a new blog post from JSON fields.
     */
    #[McpTool(name: 'create_post')]
    public function createPost(string $headline, string $articleBody, string $author): array
    {
        $postId = (string)UuidValueObject::v4();
        $datePublished = (new \DateTimeImmutable())->format(\DateTimeInterface::ATOM);

        $post = Post::fromPrimitives($postId, $headline, $articleBody, $author, $datePublished);

        await($this->postRepository->save($post));

        return [
            'id' => $postId,
            'headline' => $headline,
            'author' => $author,
            'datePublished' => $datePublished,
        ];
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
     * Update an existing blog post. All fields are required.
     */
    #[McpTool(name: 'update_post')]
    public function updatePost(
        string $id,
        string $headline,
        string $articleBody,
        string $author,
        string $datePublished,
        ?string $orgSource = null,
    ): array {
        $post = Post::fromPrimitives($id, $headline, $articleBody, $author, $datePublished, $orgSource);

        $updated = await($this->postRepository->update($post));

        return $updated
            ? ['updated' => true, 'id' => $id]
            : ['updated' => false, 'error' => "Post not found: $id"];
    }

    /**
     * Delete a blog post by its UUID.
     */
    #[McpTool(name: 'delete_post')]
    public function deletePost(string $id): array
    {
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
