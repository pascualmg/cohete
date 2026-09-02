<?php

namespace pascualmg\cohete\ddd\Domain\Entity\Post\ValueObject;

use Cohete\DDD\ValueObject\StringValueObject;
use pascualmg\cohete\ddd\Domain\Service\Slugifier;

class Slug extends StringValueObject
{
    public static function from(?string $value = null): static
    {
        self::assertNotNull($value);
        self::assertNotEmpty($value);

        $slug = self::generateSlug($value);

        return parent::from($slug);
    }

    /** @deprecated usar Slugifier::slugify() directamente; se mantiene por compatibilidad. */
    public static function generateSlug(string $string): string
    {
        return Slugifier::slugify($string);
    }
}
