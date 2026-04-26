<?php

declare(strict_types=1);

namespace pascualmg\cohete\ddd\Infrastructure\Media;

use pascualmg\cohete\ddd\Domain\Entity\Media\Media;
use pascualmg\cohete\ddd\Domain\Entity\Media\MediaId;
use pascualmg\cohete\ddd\Domain\Entity\Media\MediaRepository;
use Psr\Http\Message\StreamInterface;
use React\Promise\PromiseInterface;
use function React\Promise\resolve;

/**
 * Implementacion in-memory para tests. Resuelve promises sincronas
 * (resolve() inmediato) pero respetando la firma async del interface.
 */
final class InMemoryMediaRepository implements MediaRepository
{
    /** @var array<string, Media> */
    private array $metadata = [];

    /** @var array<string, string> */
    private array $bytes = [];

    public function put(Media $media, StreamInterface|string $body): PromiseInterface
    {
        $id = (string) $media->id;
        $this->metadata[$id] = $media;
        $this->bytes[$id] = is_string($body) ? $body : (string) $body;
        return resolve(null);
    }

    public function find(MediaId $id): PromiseInterface
    {
        return resolve($this->metadata[(string) $id] ?? null);
    }

    public function delete(MediaId $id): PromiseInterface
    {
        $key = (string) $id;
        unset($this->metadata[$key], $this->bytes[$key]);
        return resolve(null);
    }

    public function presignedUrl(MediaId $id, int $ttlSeconds = 3600): PromiseInterface
    {
        if (!isset($this->metadata[(string) $id])) {
            return resolve("memory://test/missing/{$id}");
        }
        return resolve("memory://test/{$id}?expires={$ttlSeconds}");
    }

    /** Helper de tests (sync). */
    public function bytesOf(MediaId $id): ?string
    {
        return $this->bytes[(string) $id] ?? null;
    }
}
