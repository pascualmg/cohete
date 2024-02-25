<?php

namespace pascualmg\reactor\ddd\Application\Post;

use pascualmg\reactor\ddd\Domain\Service\PostCreator;

class CreatePostCommandHandler
{
    public function __construct(PostCreator $postCreator)
    {
    }

}