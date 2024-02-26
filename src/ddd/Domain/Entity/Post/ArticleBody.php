<?php

namespace pascualmg\reactor\ddd\Domain\Entity\Post;

use pascualmg\reactor\ddd\Domain\ValueObject\StringValueObject;

class ArticleBody extends StringValueObject
{
    public static function from(?string $value = null): static
    {
        parent::assertNotNull($value);
        parent::assertNotEmpty($value);
        return parent::from($value);
    }

}