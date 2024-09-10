<?php

namespace pascualmg\cohete\ddd\Application\Post;

use pascualmg\cohete\ddd\Domain\Entity\Post\ValueObject\PostId;

class FindPostByIdQuery
{
    public function __construct(
        public readonly PostId $postId
    ) {
    }
}
