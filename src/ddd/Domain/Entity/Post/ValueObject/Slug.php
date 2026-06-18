<?php

namespace pascualmg\cohete\ddd\Domain\Entity\Post\ValueObject;

use Cohete\DDD\ValueObject\StringValueObject;

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
        $slug = \str_replace("'", '', $string);
        $slug = \str_replace('&', ' and ', $slug);

        // Emojis y simbolos (no letra/digito) -> espacio, ANTES de transliterar.
        $slug = \preg_replace('~[^\\pL\\d]+~u', ' ', $slug);

        // Transliterar a ASCII. //IGNORE descarta lo intraducible (emoji) en vez
        // de hacer fallar a iconv: sin esto iconv devolvia false ante un emoji y
        // el slug quedaba roto/vacio -> reventaba la pagina de detalle por el slug.
        $ascii = @\iconv('utf-8', 'us-ascii//TRANSLIT//IGNORE', $slug);
        if (false !== $ascii) {
            $slug = $ascii;
        }

        // Solo alfanumerico + guion; cualquier resto superviviente, fuera.
        $slug = \preg_replace('~[^a-zA-Z0-9]+~', '-', $slug);
        $slug = \preg_replace('~-+~', '-', $slug);
        $slug = \strtolower(\trim($slug, '-'));

        // NUNCA devolver vacio (un slug vacio rompia el detalle del post).
        if ('' === $slug) {
            $slug = 'post-' . \substr(\md5($string), 0, 8);
        }

        return $slug;
    }

}
