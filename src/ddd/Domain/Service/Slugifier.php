<?php

namespace pascualmg\cohete\ddd\Domain\Service;

/**
 * Unica fuente de verdad para convertir texto libre (titulos, nombres de
 * autor...) en un slug URL-safe. Antes de esto habia TRES reglas distintas
 * repartidas por el codigo (JS del index, RSS, SQL del repositorio) y
 * autores de mas de una palabra rompian: "Ambrosio del Santuario" enlazaba
 * mal con "Ambrosio" y sus URLs daban 404.
 *
 * Slug (post) y AuthorSlug (autor) delegan aqui: mismo algoritmo, dos VOs.
 */
final class Slugifier
{
    public static function slugify(string $value): string
    {
        $slug = \str_replace("'", '', $value);
        $slug = \str_replace('&', ' and ', $slug);

        // Emojis y simbolos (no letra/digito) -> espacio, ANTES de transliterar.
        $slug = \preg_replace('~[^\\pL\\d]+~u', ' ', $slug);

        // Transliterar a ASCII. //IGNORE descarta lo intraducible (emoji) en vez
        // de hacer fallar a iconv: sin esto iconv devolvia false ante un emoji y
        // el slug quedaba roto/vacio.
        $ascii = @\iconv('utf-8', 'us-ascii//TRANSLIT//IGNORE', $slug);
        if (false !== $ascii) {
            $slug = $ascii;
        }

        // Solo alfanumerico + guion; cualquier resto superviviente, fuera.
        $slug = \preg_replace('~[^a-zA-Z0-9]+~', '-', $slug);
        $slug = \preg_replace('~-+~', '-', $slug);
        $slug = \strtolower(\trim($slug, '-'));

        // NUNCA vacio (un slug vacio rompia el detalle del post/autor).
        if ('' === $slug) {
            $slug = 'x-' . \substr(\md5($value), 0, 8);
        }

        return $slug;
    }
}
