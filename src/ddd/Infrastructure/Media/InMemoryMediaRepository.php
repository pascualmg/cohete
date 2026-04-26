<?php

declare(strict_types=1);

namespace pascualmg\cohete\ddd\Infrastructure\Media;

use pascualmg\cohete\ddd\Domain\Entity\Media\Media;
use pascualmg\cohete\ddd\Domain\Entity\Media\MediaId;
use pascualmg\cohete\ddd\Domain\Entity\Media\MediaRepository;
use Psr\Http\Message\StreamInterface;

/**
 * Implementacion in-memory para tests. NO persiste entre instancias.
 */
final class InMemoryMediaRepository implements MediaRepository
{
    /** @var array<string, Media> */
    private array $metadata = [];

    /** @var array<string, string> bytes en memoria */
    private array $bytes = [];

    public function put(Media $media, StreamInterface|string $body): void
    {
        $id = (string) $media->id;
        $this->metadata[$id] = $media;
        $this->bytes[$id] = is_string($body) ? $body : (string) $body;
    }

    public function find(MediaId $id): ?Media
    {
        return $this->metadata[(string) $id] ?? null;
    }

    public function delete(MediaId $id): void
    {
        $key = (string) $id;
        unset($this->metadata[$key], $this->bytes[$key]);
    }

    public function presignedUrl(MediaId $id, int $ttlSeconds = 3600): string
    {
        if (!isset($this->metadata[(string) $id])) {
            throw new \RuntimeException("media not found: $id");
        }
        return "memory://test/{$id}?expires={$ttlSeconds}";
    }

    /** Helper de tests: leer los bytes guardados. */
    public function bytesOf(MediaId $id): ?string
    {
        return $this->bytes[(string) $id] ?? null;
    }
}
