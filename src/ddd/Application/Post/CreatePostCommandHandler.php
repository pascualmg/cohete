<?php

namespace pascualmg\cohete\ddd\Application\Post;

use pascualmg\cohete\ddd\Domain\Service\PostCreator;

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
            $createPostCommand->articleBody,
            $createPostCommand->author,
            $createPostCommand->datePublished,
            $createPostCommand->orgSource,
        );
    }
}
