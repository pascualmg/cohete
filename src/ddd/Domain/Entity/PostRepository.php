<?php

namespace pascualmg\cohete\ddd\Domain\Entity;

use pascualmg\cohete\ddd\Domain\Entity\Post\Post;
use React\Promise\PromiseInterface;

interface PostRepository
{
    public function findAll(): PromiseInterface; //of an array of posts

    public function findById(int $postId): PromiseInterface; //of a post

    public function save(Post $postToCreate): PromiseInterface;
}
