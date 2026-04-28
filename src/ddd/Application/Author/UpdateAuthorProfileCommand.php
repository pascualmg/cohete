<?php

namespace pascualmg\cohete\ddd\Application\Author;

readonly class UpdateAuthorProfileCommand
{
    public function __construct(
        public string $authorId,
        public ?string $bio = null,
        public ?array $links = null,
    ) {
    }
}
