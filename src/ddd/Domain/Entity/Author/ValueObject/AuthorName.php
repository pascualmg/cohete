<?php

namespace pascualmg\cohete\ddd\Domain\Entity\Author\ValueObject;

use Cohete\DDD\ValueObject\StringValueObject;

class AuthorName extends StringValueObject
{
    private const MAXLENGTH = 100;

    public static function from(?string $value = null): static
    {
        self::assertNotEmpty($value);
        self::assertMaxLength(self::MAXLENGTH, $value);
        return new static($value);
    }
}
