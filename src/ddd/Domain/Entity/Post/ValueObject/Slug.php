<?php

namespace pascualmg\reactor\ddd\Domain\Entity\Post\ValueObject;

use pascualmg\reactor\ddd\Domain\ValueObject\StringValueObject;

class Slug extends StringValueObject
{
    public static function from(?string $value = null): static
    {
        self::assertNotNull($value);
        self::assertNotEmpty($value);

        $slug = self::generateSlug($value);

        return parent::from($slug);
    }


    public static function generateSlug(string $string): string
    {

        $slug = preg_replace("/'/", "", $string);
        $slug = preg_replace("/&/", " and ", $slug);
        $slug = preg_replace('~[^\\pL\\d]+~u', '-', $slug);
        // translate string
        $slug = iconv('utf-8', 'us-ascii//TRANSLIT', $slug);

        // Remove unwanted characters
        $slug = preg_replace('~[^-\\w]+~', '', $slug);

        // remove duplicate -
        $slug = preg_replace('~-+~', '-', $slug);
        $slug = trim($slug, '-');

        // lowercase
        $slug = strtolower($slug);


        return $slug;
    }

}
