<?php

namespace pascualmg\cohete\ddd\Application\Post;

use pascualmg\cohete\ddd\Domain\Entity\PostRepository;
use React\Promise\PromiseInterface;

class FindAllPosts
{
    public function __construct(
        private readonly PostRepository $postRepository
    ) {
    }


    public function __invoke(FindAllPostsQuery $findAllPostsQuery): PromiseInterface
    {
        return $this->postRepository->findAll();
    }

}
