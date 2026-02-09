<?php

namespace pascualmg\cohete\ddd\Application\Post;

use pascualmg\cohete\ddd\Domain\Entity\PostRepository;
use React\Promise\PromiseInterface;

readonly class FindAllPosts
{
    public function __construct(
        private PostRepository $postRepository
    ) {
    }


    public function __invoke(FindAllPostsQuery $_): PromiseInterface
    {
        return $this->postRepository->findAll();
    }

}
