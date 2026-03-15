<?php

namespace pascualmg\cohete\ddd\Domain\Entity\Author\ValueObject;

use Cohete\DDD\ValueObject\StringValueObject;

class AuthorKeyHash extends StringValueObject
{
    public static function from(?string $value = null): static
    {
        self::assertNotEmpty($value);
        return new static($value);
    }
}
