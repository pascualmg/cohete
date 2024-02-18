<?php

namespace pascualmg\reactor\ddd\Domain\ValueObject;

use Ramsey\Uuid\Uuid as RamseyUuid;

class Uuid implements \Stringable
{

    private function __construct(
        public readonly string $value
    ) {
    }

    public static function from(string $maybeUuid): self
    {
        $validUuid = RamseyUuid::fromString($maybeUuid);
        return new self($validUuid->toString());
    }

    public function __toString(): string
    {
        return $this->value;
    }
}