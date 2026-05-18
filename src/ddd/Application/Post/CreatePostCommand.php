<?php

namespace pascualmg\cohete\ddd\Application\Post;

readonly class CreatePostCommand
{
    public function __construct(
        public string $postId,
        public string $headline,
        public string $articleBody,
        public string $author,
        public string $datePublished,
        public ?string $orgSource = null,
        public ?string $slug = null,
    ) {
    }
}
