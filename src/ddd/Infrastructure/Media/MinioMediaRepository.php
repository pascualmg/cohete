<?php

declare(strict_types=1);

namespace pascualmg\cohete\ddd\Infrastructure\Media;

use Aws\S3\S3Client;
use pascualmg\cohete\ddd\Domain\Entity\Media\Media;
use pascualmg\cohete\ddd\Domain\Entity\Media\MediaId;
use pascualmg\cohete\ddd\Domain\Entity\Media\MediaRepository;
use pascualmg\cohete\ddd\Domain\Entity\Media\ValueObject\Bucket;
use pascualmg\cohete\ddd\Domain\Entity\Media\ValueObject\ContentType;
use pascualmg\cohete\ddd\Domain\Entity\Media\ValueObject\MediaKey;
use Psr\Http\Message\StreamInterface;

/**
 * Implementacion MinIO usando aws-sdk-php (compatible S3).
 *
 * La metadata Media (id->key, contentType, size) se guarda como user metadata
 * del propio objeto en MinIO via x-amz-meta-* headers. Asi NO necesitamos
 * tabla MySQL para metadata: el storage es la fuente de verdad.
 *
 * Trade-off: ListObjects por bucket es O(N). Si crece, anadir indice MySQL.
 */
final class MinioMediaRepository implements MediaRepository
{
    public function __construct(
        private readonly S3Client $client,
        private readonly Bucket $defaultBucket,
    ) {
    }

    public function put(Media $media, StreamInterface|string $body): void
    {
        $this->client->putObject([
            'Bucket'      => (string) $media->bucket,
            'Key'         => $this->keyFromMedia($media),
            'Body'        => $body,
            'ContentType' => (string) $media->contentType,
            'Metadata'    => [
                'media-id'    => (string) $media->id,
                'uploaded-at' => $media->uploadedAt->format(\DateTimeInterface::ATOM),
            ],
        ]);
    }

    public function find(MediaId $id): ?Media
    {
        try {
            $head = $this->client->headObject([
                'Bucket' => (string) $this->defaultBucket,
                'Key'    => $this->keyFromId($id),
            ]);
        } catch (\Aws\S3\Exception\S3Exception $e) {
            if ($e->getStatusCode() === 404) {
                return null;
            }
            throw $e;
        }

        return Media::reconstitute(
            id: $id,
            bucket: $this->defaultBucket,
            key: MediaKey::from($this->keyFromId($id)),
            contentType: ContentType::from($head['ContentType'] ?? 'application/octet-stream'),
            byteSize: (int) ($head['ContentLength'] ?? 0),
            uploadedAt: new \DateTimeImmutable($head['Metadata']['uploaded-at'] ?? 'now'),
        );
    }

    public function delete(MediaId $id): void
    {
        $this->client->deleteObject([
            'Bucket' => (string) $this->defaultBucket,
            'Key'    => $this->keyFromId($id),
        ]);
    }

    public function presignedUrl(MediaId $id, int $ttlSeconds = 3600): string
    {
        $cmd = $this->client->getCommand('GetObject', [
            'Bucket' => (string) $this->defaultBucket,
            'Key'    => $this->keyFromId($id),
        ]);
        $request = $this->client->createPresignedRequest($cmd, "+{$ttlSeconds} seconds");
        return (string) $request->getUri();
    }

    /**
     * Convencion: el key incluye el id como prefijo para deduplicacion natural.
     * Ejemplo: "media/<uuid>/<key-original>".
     * En esta version usamos directamente el id como key.
     */
    private function keyFromId(MediaId $id): string
    {
        return "media/{$id->value}";
    }

    private function keyFromMedia(Media $media): string
    {
        return $this->keyFromId($media->id);
    }
}
