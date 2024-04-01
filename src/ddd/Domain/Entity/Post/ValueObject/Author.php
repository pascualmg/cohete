<?php

namespace pascualmg\cohete\ddd\Domain\Entity\Post\ValueObject;

use pascualmg\cohete\ddd\Domain\ValueObject\StringValueObject;

class Author extends StringValueObject
{
    public const MAXLENGTH = 100;

    public function __construct(string $value)
    {
        self::assertMaxLength(self::MAXLENGTH, $value);
        parent::__construct($value);
    }

}
