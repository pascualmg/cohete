<?php

namespace pascualmg\cohete\ddd\Application\Post;

/**
 * Resultado de publicar un .org: dice si se CREO (post nuevo) o se ACTUALIZO
 * (mismo UUID, upsert por slug) y devuelve los datos para que el controller
 * los mapee a HTTP (201 vs 200). Sin saber nada de HTTP: esto es Application.
 */
readonly class PublishOrgPostResult
{
    public function __construct(
        public bool $created,
        public string $id,
        public string $headline,
        public string $author,
        public string $datePublished,
        public string $slug,
    ) {
    }
}
