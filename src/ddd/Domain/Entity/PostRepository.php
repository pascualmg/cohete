<?php

namespace Pascualmg\Rx\ddd\Domain\Entity;

use React\Promise\PromiseInterface;

interface PostRepository
{
    public function findAll(): PromiseInterface; //of an array of posts

    public function findById(int $postId) : PromiseInterface; //of a post
}
