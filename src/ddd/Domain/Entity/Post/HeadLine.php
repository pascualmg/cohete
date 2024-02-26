<?php

namespace pascualmg\reactor\ddd\Domain\Entity\Post;

use pascualmg\reactor\ddd\Domain\ValueObject\StringValueObject;

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