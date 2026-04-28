<?php

namespace pascualmg\cohete\ddd\Application\Author;

readonly class RegisterAuthorCommand
{
    public function __construct(
        public string $name,
        public ?string $type = null,
    ) {
    }
}
