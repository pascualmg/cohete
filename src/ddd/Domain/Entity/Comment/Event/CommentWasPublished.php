<?php

namespace pascualmg\cohete\ddd\Domain\Entity\Comment\Event;

use pascualmg\cohete\ddd\Domain\Bus\Message;

readonly class CommentWasPublished extends Message
{
    public function __construct(
        public readonly string $commentId,
        public readonly string $postId,
        public readonly string $authorName,
    ) {
        parent::__construct('domain_event.comment_published', [
            'commentId' => $commentId,
            'postId' => $postId,
            'authorName' => $authorName,
        ]);
    }
}
