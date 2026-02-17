<?php

namespace pascualmg\cohete\ddd\Application\Comment;

readonly class FindCommentsByPostIdQuery
{
    public function __construct(
        public string $postId,
    ) {
    }
}
