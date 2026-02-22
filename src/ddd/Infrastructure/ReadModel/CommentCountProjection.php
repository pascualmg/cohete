<?php

namespace pascualmg\cohete\ddd\Infrastructure\ReadModel;

use pascualmg\cohete\ddd\Domain\Entity\CommentRepository;
use React\Promise\PromiseInterface;

class CommentCountProjection
{
    /** @var array<string, int> postId => count */
    private array $counts = [];

    public function __construct(
        private readonly CommentRepository $commentRepository,
    ) {
    }

    public function boot(): PromiseInterface
    {
        return $this->commentRepository->countGroupedByPost()
            ->then(fn (array $counts) => $this->counts = $counts);
    }

    public function onCommentPublished(array $payload): void
    {
        $postId = $payload['postId'];
        $this->counts[$postId] = ($this->counts[$postId] ?? 0) + 1;
    }

    public function getCount(string $postId): int
    {
        return $this->counts[$postId] ?? 0;
    }

    /** @return array<string, int> */
    public function getAllCounts(): array
    {
        return $this->counts;
    }
}
