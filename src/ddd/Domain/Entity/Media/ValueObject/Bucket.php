<?php

declare(strict_types=1);

namespace pascualmg\cohete\ddd\Domain\Entity\Media\ValueObject;

final readonly class Bucket
{
    private function __construct(public string $value)
    {
    }

    public static function from(string $value): self
    {
        if ($value === '' || strlen($value) > 63) {
            throw new \InvalidArgumentException("invalid bucket name length: '$value'");
        }
        if (!preg_match('/^[a-z0-9][a-z0-9.\-]*[a-z0-9]$/', $value)) {
            throw new \InvalidArgumentException("invalid bucket name format: '$value'");
        }
        return new self($value);
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
