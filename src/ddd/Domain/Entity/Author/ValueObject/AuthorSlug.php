<?php

namespace pascualmg\cohete\ddd\Domain\Entity\Author\ValueObject;

use Cohete\DDD\ValueObject\StringValueObject;
use pascualmg\cohete\ddd\Domain\Service\Slugifier;

/**
 * Identidad URL-safe de un autor (todo el nombre, no solo la primera
 * palabra). Unica fuente de verdad para /blog/{authorSlug}/{slug}: la usan
 * por igual el repositorio, los controllers HTML, el RSS y el publish desde
 * .org, asi que "Ambrosio del Santuario" siempre resuelve al mismo slug
 * "ambrosio-del-santuario" en todo el sitio.
 *
 * from() acepta tanto un nombre completo ("Ambrosio del Santuario") como un
 * slug que ya viene de una URL ("ambrosio-del-santuario"): slugificar un
 * slug ya normalizado es una operacion idempotente.
 */
class AuthorSlug extends StringValueObject
{
    public static function from(?string $value = null): static
    {
        self::assertNotEmpty($value);
        return parent::from(Slugifier::slugify($value));
    }

    public static function fromName(AuthorName $name): static
    {
        return self::from((string)$name);
    }
}
