<?php

namespace pascualmg\reactor\ddd\Domain\Bus;

class Message
{
    public function __construct(
        public readonly string $name,
        public readonly string $type,
        public readonly mixed $payload,
    ) {
    }

}
