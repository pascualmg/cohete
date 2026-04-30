<?php

declare(strict_types=1);

namespace pascualmg\cohete\ddd\Domain\Entity\Media\ValueObject;

final readonly class MediaKey
{
    private function __construct(public string $value)
    {
    }

    public static function from(string $value): self
    {
        $trimmed = ltrim($value, '/');
        if ($trimmed === '' || strlen($trimmed) > 1024) {
            throw new \InvalidArgumentException("invalid media key length: '$value'");
        }
        return new self($trimmed);
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
