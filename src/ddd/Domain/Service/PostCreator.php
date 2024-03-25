<?php

namespace pascualmg\reactor\ddd\Domain\Service;

use pascualmg\reactor\ddd\Domain\Bus\Message;
use pascualmg\reactor\ddd\Domain\Bus\MessageBus;
use pascualmg\reactor\ddd\Domain\Entity\Post\Post;
use pascualmg\reactor\ddd\Domain\Entity\PostRepository;
use Psr\Log\LoggerInterface;

readonly class PostCreator
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
        string $datePublished
    ): void {
        $post = Post::fromPrimitives(
            $postId,
            $headline,
            $articleBody,
            $author,
            $datePublished
        );


        $this->postRepository->save($post)->then(
            function (Bool $_) use ($post) {
                $this->messageBus->publish(new Message('domain_event.post_created', [$post]));
            },
            fn (\Exception $exception) => $this->logger->error("Cant create the new post", [$post, $exception])
        );

        }

}
