<?php

namespace pascualmg\cohete\ddd\Domain\Entity\Post\ValueObject;

use pascualmg\cohete\ddd\Domain\ValueObject\StringValueObject;

class ArticleBody extends StringValueObject
{
    public static function from(?string $value = null): static
    {
        parent::assertNotNull($value);
        parent::assertNotEmpty($value);
        return parent::from($value);
    }

}
