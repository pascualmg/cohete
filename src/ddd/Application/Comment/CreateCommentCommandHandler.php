<?php

namespace pascualmg\cohete\ddd\Application\Comment;

use pascualmg\cohete\ddd\Domain\Bus\MessageBus;
use pascualmg\cohete\ddd\Domain\Entity\Comment\Comment;
use pascualmg\cohete\ddd\Domain\Entity\CommentRepository;
use pascualmg\cohete\ddd\Domain\Entity\Post\ValueObject\PostId;
use pascualmg\cohete\ddd\Domain\Entity\PostRepository;
use Psr\Log\LoggerInterface;
use React\Promise\PromiseInterface;

class CreateCommentCommandHandler
{
    public function __construct(
        private readonly CommentRepository $commentRepository,
        private readonly PostRepository $postRepository,
        private readonly MessageBus $messageBus,
        private readonly LoggerInterface $logger,
    ) {
    }

    public function __invoke(CreateCommentCommand $command): PromiseInterface
    {
        return $this->postRepository->findById(PostId::from($command->postId))->then(
            function ($post) use ($command): PromiseInterface {
                if ($post === null) {
                    return \React\Promise\reject(
                        new \InvalidArgumentException("Post not found: {$command->postId}")
                    );
                }

                $comment = Comment::publish(
                    $command->postId,
                    $command->authorName,
                    $command->body,
                );

                return $this->commentRepository->save($comment)->then(
                    function (bool $saved) use ($comment): array {
                        foreach ($comment->pullDomainEvents() as $event) {
                            $this->messageBus->publish($event);
                        }
                        return $comment->jsonSerialize();
                    },
                    function (\Exception $e) {
                        $this->logger->error("Failed to save comment", ['exception' => $e->getMessage()]);
                        throw $e;
                    }
                );
            }
        );
    }
}
