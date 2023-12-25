<?php

namespace Pascualmg\Rx\ddd\Application\Post;

use Pascualmg\Rx\ddd\Domain\Bus\Message;
use Pascualmg\Rx\ddd\Domain\Bus\MessageBus;
use Pascualmg\Rx\ddd\Domain\Entity\PostRepository;
use React\Promise\PromiseInterface;

class FindPostByIdQueryHandler
{
    private readonly PostRepository $postRepository;
    private MessageBus $bus;

    public function __construct(
        PostRepository $postRepository,
        MessageBus $bus
    ) {
        $this->postRepository = $postRepository;
        $this->bus = $bus;
    }

    public function __invoke(FindPostByIdQuery $findPostByIdQuery): PromiseInterface //of a post
    {
        $this->bus->dispatch(
            new Message(
                'foo',
                "wee! desde FindBypost"
            )
        );
        return $this->postRepository->findById($findPostByIdQuery->postId());
    }

}
