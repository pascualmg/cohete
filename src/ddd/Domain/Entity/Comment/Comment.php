<?php

namespace pascualmg\cohete\ddd\Domain\Entity\Comment;

use pascualmg\cohete\ddd\Domain\Entity\Comment\Event\CommentWasPublished;
use pascualmg\cohete\ddd\Domain\Entity\Comment\ValueObject\CommentAuthorName;
use pascualmg\cohete\ddd\Domain\Entity\Comment\ValueObject\CommentBody;
use pascualmg\cohete\ddd\Domain\Entity\Comment\ValueObject\CommentId;
use pascualmg\cohete\ddd\Domain\Entity\Post\ValueObject\PostId;

class Comment implements \JsonSerializable
{
    private array $domainEvents = [];

    public function __construct(
        public readonly CommentId $id,
        public readonly PostId $postId,
        public readonly CommentAuthorName $authorName,
        public readonly CommentBody $body,
        public readonly \DateTimeImmutable $createdAt,
    ) {
    }

    public static function publish(
        string $postId,
        string $authorName,
        string $body,
    ): self {
        $commentId = CommentId::v4();

        $comment = new self(
            $commentId,
            PostId::from($postId),
            CommentAuthorName::from($authorName),
            CommentBody::from($body),
            new \DateTimeImmutable(),
        );

        $comment->domainEvents[] = new CommentWasPublished(
            (string)$commentId,
            $postId,
            $authorName,
        );

        return $comment;
    }

    public static function fromPrimitives(
        string $id,
        string $postId,
        string $authorName,
        string $body,
        string $createdAt,
    ): self {
        return new self(
            CommentId::from($id),
            PostId::from($postId),
            CommentAuthorName::from($authorName),
            CommentBody::from($body),
            new \DateTimeImmutable($createdAt),
        );
    }

    public function pullDomainEvents(): array
    {
        $events = $this->domainEvents;
        $this->domainEvents = [];
        return $events;
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => (string)$this->id,
            'postId' => (string)$this->postId,
            'authorName' => (string)$this->authorName,
            'body' => (string)$this->body,
            'createdAt' => $this->createdAt->format(\DateTimeInterface::ATOM),
        ];
    }
}
