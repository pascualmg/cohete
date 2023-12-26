<?php

namespace Pascualmg\Rx\ddd\Application\Post;

use Pascualmg\Rx\ddd\Domain\Entity\PostRepository;

class FindAllPosts
{
    public function __construct(
        private readonly PostRepository $postRepository
    ) {
    }


    public function __invoke(FindAllPostsQuery $findAllPostsQuery)
    {
        return $this->postRepository->findAll();
    }

}
