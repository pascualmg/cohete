<?php

namespace pascualmg\reactor\ddd\Application\Post;

use pascualmg\reactor\ddd\Domain\Service\PostCreator;

class CreatePostCommandHandler
{
    public function __construct(
        private readonly PostCreator $postCreator
    ) {
    }

    public function __invoke(CreatePostCommand $createPostCommand): void
    {
        ($this->postCreator)(
            $createPostCommand->postId,
            $createPostCommand->headline,
            $createPostCommand->image,
            $createPostCommand->articleBody,
            $createPostCommand->author,
            $createPostCommand->datePublished,
        );
    }
}