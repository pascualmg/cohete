<?php

namespace pascualmg\cohete\ddd\Domain\Service;

use pascualmg\cohete\ddd\Domain\Bus\Message;
use pascualmg\cohete\ddd\Domain\Bus\MessageBus;
use pascualmg\cohete\ddd\Domain\Entity\Post\Post;
use pascualmg\cohete\ddd\Domain\Entity\PostRepository;
use Psr\Log\LoggerInterface;

class PostCreator
{
    public function __construct(
        private PostRepository $postRepository,
        private MessageBus $messageBus,
        private LoggerInterface $logger
    ) {
    }

    public function __invoke(
        string $postId,
        string $headline,
        string $articleBody,
        string $author,
        string $datePublished,
        ?string $orgSource = null,
    ): void {
        $post = Post::fromPrimitives(
            $postId,
            $headline,
            $articleBody,
            $author,
            $datePublished,
            $orgSource,
        );

        $this->postRepository->save($post)->then(
            fn(bool $_) => $this->messageBus->publish(new Message('domain_event.post_created', [$post])),
            fn(\Exception $exception) => $this->logger->error("Cant create the new post", [$post, $exception])
        );
    }

}
