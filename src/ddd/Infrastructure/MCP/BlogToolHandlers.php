<?php

namespace pascualmg\cohete\ddd\Infrastructure\MCP;

use pascualmg\cohete\ddd\Domain\Entity\Post\Post;
use pascualmg\cohete\ddd\Domain\Entity\Post\ValueObject\PostId;
use pascualmg\cohete\ddd\Domain\Entity\PostRepository;
use pascualmg\cohete\ddd\Domain\ValueObject\UuidValueObject;
use pascualmg\cohete\ddd\Infrastructure\Service\OrgToHtmlConverter;
use PhpMcp\Server\Attributes\McpTool;

use function React\Async\await;

class BlogToolHandlers
{
    public function __construct(
        private readonly PostRepository $postRepository,
        private readonly OrgToHtmlConverter $orgToHtmlConverter,
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
     * Publish a blog post from org-mode content. Pandoc converts org to HTML automatically.
     */
    #[McpTool(name: 'publish_org')]
    public function publishOrg(string $orgContent): array
    {
        $metadata = $this->orgToHtmlConverter->extractMetadata($orgContent);
        $html = $this->orgToHtmlConverter->convert($orgContent);

        $postId = (string)UuidValueObject::v4();

        try {
            $dt = new \DateTimeImmutable($metadata['date']);
            $datePublished = $dt->format(\DateTimeInterface::ATOM);
        } catch (\Exception) {
            $datePublished = (new \DateTimeImmutable())->format(\DateTimeInterface::ATOM);
        }

        $post = Post::fromPrimitives(
            $postId,
            $metadata['title'],
            $html,
            $metadata['author'],
            $datePublished,
            $orgContent,
        );

        await($this->postRepository->save($post));

        return [
            'id' => $postId,
            'headline' => $metadata['title'],
            'author' => $metadata['author'],
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
}
