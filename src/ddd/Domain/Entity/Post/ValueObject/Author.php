<?php

namespace pascualmg\cohete\ddd\Domain\Entity\Post\ValueObject;

use Cohete\DDD\ValueObject\StringValueObject;

class Author extends StringValueObject
{
    public const MAXLENGTH = 100;

    public static function from(?string $value = null): static
    {
        $value = $value ?? "";
        self::assertMaxLength(self::MAXLENGTH, $value);
        return new static($value);
    }
}
