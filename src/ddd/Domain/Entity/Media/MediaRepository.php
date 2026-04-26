<?php

declare(strict_types=1);

namespace pascualmg\cohete\ddd\Domain\Entity\Media;

use Psr\Http\Message\StreamInterface;

/**
 * Interface de dominio. NO sabe si los bytes viven en MinIO, S3, filesystem.
 *
 * Implementaciones:
 *   Infrastructure/Media/MinioMediaRepository.php   (prod)
 *   Infrastructure/Media/InMemoryMediaRepository.php (tests)
 */
interface MediaRepository
{
    /** Sube los bytes y persiste la metadata. */
    public function put(Media $media, StreamInterface|string $body): void;

    /** Devuelve la metadata si existe. */
    public function find(MediaId $id): ?Media;

    /** Borra metadata + bytes. */
    public function delete(MediaId $id): void;

    /** URL firmada con expiracion (para servir directo al cliente). */
    public function presignedUrl(MediaId $id, int $ttlSeconds = 3600): string;
}
