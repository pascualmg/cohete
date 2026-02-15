<?php

namespace pascualmg\cohete\ddd\Domain\Service;

use pascualmg\cohete\ddd\Domain\Bus\Message;
use pascualmg\cohete\ddd\Domain\Bus\MessageBus;
use pascualmg\cohete\ddd\Domain\Entity\Post\ValueObject\PostId;
use pascualmg\cohete\ddd\Domain\Entity\PostRepository;
use Psr\Log\LoggerInterface;

readonly class PostDeleter
{
    public function __construct(
        private PostRepository $postRepository,
        private MessageBus $messageBus,
        private LoggerInterface $logger
    ) {
    }

    public function __invoke(string $postId): void
    {
        $id = PostId::from($postId);

        $this->postRepository->delete($id)->then(
            fn (bool $_) => $this->messageBus->publish(new Message('domain_event.post_deleted', [$id])),
            fn (\Exception $exception) => $this->logger->error("Cant delete the post", [$id, $exception])
        );
    }
}
