<?php

namespace pascualmg\reactor\ddd\Application\Post;

use pascualmg\reactor\ddd\Domain\Entity\PostRepository;
use React\Promise\PromiseInterface;

class FindPostByIdQueryHandler
{
    private readonly PostRepository $postRepository;
    public function __construct(
        PostRepository $postRepository,
    ) {
        $this->postRepository = $postRepository;
    }

    public function __invoke(FindPostByIdQuery $findPostByIdQuery): PromiseInterface //of a post
    {
        return $this->postRepository->findById($findPostByIdQuery->postId());
    }

}
