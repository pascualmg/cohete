<?php

namespace pascualmg\cohete\ddd\Application\Comment;

readonly class CreateCommentCommand
{
    public function __construct(
        public string $postId,
        public string $authorName,
        public string $body,
    ) {
    }
}
