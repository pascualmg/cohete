<?php

namespace Pascualmg\Rx\ddd\Domain\Bus;

class Message
{
    public function __construct(
        public readonly string $name,
        public readonly mixed $payload,
    ) {
    }

}
