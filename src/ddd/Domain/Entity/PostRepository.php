<?php

namespace pascualmg\cohete\ddd\Domain\Entity;

use pascualmg\cohete\ddd\Domain\Entity\Post\Post;
use pascualmg\cohete\ddd\Domain\Entity\Post\ValueObject\PostId;
use React\Promise\PromiseInterface;

interface PostRepository
{
    public function findAll(): PromiseInterface; //of an array of posts

    public function findById(PostId $postId): PromiseInterface; //of a post

    public function save(Post $postToCreate): PromiseInterface;
}
