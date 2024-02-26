<?php

namespace pascualmg\reactor\ddd\Application\Post;

class CreatePostCommand
{
    public function __construct(
        public readonly string $postId,
        public readonly string $headline,
        public readonly string $articleBody,
        public readonly string $image,
        public readonly string $author,
        public readonly string $datePublished,
    ) {
    }
}