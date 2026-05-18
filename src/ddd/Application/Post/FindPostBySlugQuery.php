<?php

namespace pascualmg\cohete\ddd\Application\Post;

use pascualmg\cohete\ddd\Domain\Entity\Post\ValueObject\Slug;

class FindPostBySlugQuery
{
    public function __construct(
        public readonly Slug $slug
    ) {
    }
}
