<?php

namespace pascualmg\reactor\ddd\Application\Post;

readonly class CreatePostCommand
{
    public function __construct(
        public string $postId,
        public string $headline,
        public string $articleBody,
        public string $author,
        public string $datePublished,
    ) {
    }
}
