<?php

namespace pascualmg\cohete\ddd\Application\Post;

use pascualmg\cohete\ddd\Domain\Entity\PostRepository;
use React\Promise\PromiseInterface;

class FindPostBySlugQueryHandler
{
    public function __construct(
        private readonly PostRepository $postRepository,
    ) {
    }

    public function __invoke(FindPostBySlugQuery $query): PromiseInterface
    {
        return $this->postRepository->findBySlug($query->slug);
    }
}
