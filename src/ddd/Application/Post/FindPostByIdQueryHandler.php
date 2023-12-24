<?php

namespace Pascualmg\Rx\ddd\Application\Post;

use Pascualmg\Rx\ddd\Domain\Bus\Bus;
use Pascualmg\Rx\ddd\Domain\Bus\Event;
use Pascualmg\Rx\ddd\Domain\Entity\PostRepository;
use React\Promise\PromiseInterface;

class FindPostByIdQueryHandler
{
    private readonly PostRepository $postRepository;
    private Bus $bus;

    public function __construct(
        PostRepository $postRepository,
        Bus $bus
    ) {
        $this->postRepository = $postRepository;
        $this->bus = $bus;
    }

    public function __invoke(FindPostByIdQuery $findPostByIdQuery): PromiseInterface
    {
        $this->bus->dispatch(
            new Event(
                'foo',
                "wee! desde FindBypost"
            )
        );
        return $this->postRepository->findById($findPostByIdQuery->postId());
    }

}
