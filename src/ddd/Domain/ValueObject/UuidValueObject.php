<?php

namespace pascualmg\cohete\ddd\Domain\ValueObject;

use Ramsey\Uuid\Uuid as RamseyUuid;

class UuidValueObject implements \Stringable
{
    private function __construct(
        public readonly string $value
    ) {
    }

    public static function from(string $maybeUuid): static
    {
        $validUuid = RamseyUuid::fromString($maybeUuid);
        return new static($validUuid->toString());
    }

    public static function v4(): static
    {
        return new static(RamseyUuid::uuid4()->toString());
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
