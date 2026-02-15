<?php

namespace pascualmg\cohete\ddd\Application\Post;

use pascualmg\cohete\ddd\Domain\Service\PostUpdater;

class UpdatePostCommandHandler
{
    public function __construct(
        private readonly PostUpdater $postUpdater
    ) {
    }

    public function __invoke(UpdatePostCommand $updatePostCommand): void
    {
        ($this->postUpdater)(
            $updatePostCommand->postId,
            $updatePostCommand->headline,
            $updatePostCommand->articleBody,
            $updatePostCommand->author,
            $updatePostCommand->datePublished,
            $updatePostCommand->orgSource,
        );
    }
}
