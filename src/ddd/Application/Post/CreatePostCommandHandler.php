<?php

namespace pascualmg\reactor\ddd\Application\Post;

use pascualmg\reactor\ddd\Domain\Service\PostCreator;

class CreatePostCommandHandler
{
    public function __construct(
        private readonly PostCreator $postCreator
    ) {
    }

    public function __invoke(CreatePostCommand $createPostCommand)
    {
    }
}