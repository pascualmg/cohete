<?php

namespace pascualmg\cohete\ddd\Domain\Entity;

use pascualmg\cohete\ddd\Domain\Entity\Comment\Comment;
use pascualmg\cohete\ddd\Domain\Entity\Post\ValueObject\PostId;
use React\Promise\PromiseInterface;

interface CommentRepository
{
    public function findByPostId(PostId $postId): PromiseInterface; //of Comment[]

    public function save(Comment $comment): PromiseInterface; //of bool

    public function countGroupedByPost(): PromiseInterface; //of array<string, int>
}
