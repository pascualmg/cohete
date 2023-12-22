<?php

namespace Pascualmg\Rx\ddd\Application\Post;

use Pascualmg\Rx\ddd\Domain\Entity\PostRepository;
use React\Promise\PromiseInterface;

class FindPostByIdQueryHandler
{
    private readonly PostRepository $postRepository;

    public function __construct(
        PostRepository $postRepository
    ) {
        $this->postRepository = $postRepository;
    }

    public function __invoke(FindPostByIdQuery $findPostByIdQuery): PromiseInterface
    {
        return $this->postRepository->findById($findPostByIdQuery->postId());
    }

}
