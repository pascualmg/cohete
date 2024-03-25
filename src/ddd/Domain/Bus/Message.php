<?php

namespace pascualmg\reactor\ddd\Domain\Bus;

readonly class Message
{
    public function __construct(
        public string $name,
        public mixed $payload,
    ) {
    }

}
