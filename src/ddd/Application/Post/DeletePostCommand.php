<?php

namespace pascualmg\cohete\ddd\Application\Post;

readonly class DeletePostCommand
{
    public function __construct(
        public string $postId,
    ) {
    }
}
