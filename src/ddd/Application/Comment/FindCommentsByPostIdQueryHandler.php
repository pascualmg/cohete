<?php

namespace pascualmg\cohete\ddd\Application\Comment;

use pascualmg\cohete\ddd\Domain\Entity\CommentRepository;
use pascualmg\cohete\ddd\Domain\Entity\Post\ValueObject\PostId;
use React\Promise\PromiseInterface;

class FindCommentsByPostIdQueryHandler
{
    public function __construct(
        private readonly CommentRepository $commentRepository,
    ) {
    }

    public function __invoke(FindCommentsByPostIdQuery $query): PromiseInterface
    {
        return $this->commentRepository->findByPostId(PostId::from($query->postId));
    }
}
