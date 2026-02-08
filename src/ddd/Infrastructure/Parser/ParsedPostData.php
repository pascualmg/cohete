<?php

declare(strict_types=1);

namespace pascualmg\cohete\ddd\Infrastructure\Parser;

readonly class ParsedPostData
{
    public function __construct(
        public string $headline,
        public string $articleBody,
        public ?string $author = null,
        public ?\DateTimeImmutable $datePublished = null,
    ) {
    }
}
