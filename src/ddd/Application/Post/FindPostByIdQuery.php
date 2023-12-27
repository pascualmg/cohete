<?php

namespace pascualmg\reactor\ddd\Application\Post;

class FindPostByIdQuery
{
    public function __construct(
        private readonly int $postId
    ) {
    }

    public function postId(): int
    {
        return $this->postId;
    }

}
