<?php

declare(strict_types=1);

namespace pascualmg\cohete\ddd\Domain\Entity\Media;

use Cohete\DDD\ValueObject\UuidValueObject;

final readonly class MediaId
{
    private function __construct(public string $value)
    {
    }

    public static function v4(): self
    {
        return new self((string) UuidValueObject::v4());
    }

    public static function from(string $value): self
    {
        if (!preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i', $value)) {
            throw new \InvalidArgumentException("invalid MediaId UUID: '$value'");
        }
        return new self(strtolower($value));
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
