<?php

namespace pascualmg\cohete\ddd\Domain\Entity\Post\ValueObject;

use pascualmg\cohete\ddd\Domain\ValueObject\StringValueObject;

class HeadLine extends StringValueObject
{
    public const MAXLENGTH = 256;

    public static function from(?string $value = null): static
    {
        parent::assertMaxLength(self::MAXLENGTH, $value);
        parent::assertNotNull($value);
        return parent::from($value);
    }


}
