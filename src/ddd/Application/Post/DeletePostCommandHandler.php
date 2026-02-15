<?php

namespace pascualmg\cohete\ddd\Application\Post;

use pascualmg\cohete\ddd\Domain\Service\PostDeleter;

class DeletePostCommandHandler
{
    public function __construct(
        private readonly PostDeleter $postDeleter
    ) {
    }

    public function __invoke(DeletePostCommand $deletePostCommand): void
    {
        ($this->postDeleter)(
            $deletePostCommand->postId,
        );
    }
}
