<?php

namespace pascualmg\cohete\ddd\Domain\Entity\Author\ValueObject;

use pascualmg\cohete\ddd\Domain\ValueObject\StringValueObject;

class AuthorKeyHash extends StringValueObject
{
    public static function from(?string $value = null): static
    {
        self::assertNotEmpty($value);
        return new static($value);
    }
}
