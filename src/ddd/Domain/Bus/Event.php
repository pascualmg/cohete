<?php

namespace Pascualmg\Rx\ddd\Domain\Bus;

class Event
{
    private array $payload = [];
    public function __construct(
        public readonly string $name,
        $payload
    ) {
    }
}
