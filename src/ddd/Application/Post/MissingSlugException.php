<?php

namespace pascualmg\cohete\ddd\Application\Post;

/**
 * Falta el #+SLUG en el frontmatter org. Es obligatorio para el flujo
 * idempotente: el slug es la identidad del post (clave del upsert por autor).
 * Sin el, no podriamos distinguir "republicar" de "crear otro". 400.
 */
class MissingSlugException extends \RuntimeException
{
}
