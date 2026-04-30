<?php

declare(strict_types=1);

namespace pascualmg\cohete\ddd\Domain\Entity\Media;

use pascualmg\cohete\ddd\Domain\Entity\Media\ValueObject\Bucket;
use pascualmg\cohete\ddd\Domain\Entity\Media\ValueObject\ContentType;
use pascualmg\cohete\ddd\Domain\Entity\Media\ValueObject\MediaKey;

/**
 * Aggregate root para un objeto en el storage (imagen, PDF, audio...).
 *
 * No almacena los bytes en memoria — los gestiona MediaRepository::save().
 * Esta entidad solo lleva la metadata (id, bucket, key, contentType, size).
 */
final readonly class Media
{
    private function __construct(
        public MediaId      $id,
        public Bucket       $bucket,
        public MediaKey     $key,
        public ContentType  $contentType,
        public int          $byteSize,
        public \DateTimeImmutable $uploadedAt,
    ) {
    }

    public static function upload(
        MediaId $id,
        Bucket $bucket,
        MediaKey $key,
        ContentType $contentType,
        int $byteSize,
    ): self {
        if ($byteSize < 0) {
            throw new \InvalidArgumentException("byteSize must be >= 0");
        }
        return new self(
            id: $id,
            bucket: $bucket,
            key: $key,
            contentType: $contentType,
            byteSize: $byteSize,
            uploadedAt: new \DateTimeImmutable(),
        );
    }

    public static function reconstitute(
        MediaId $id,
        Bucket $bucket,
        MediaKey $key,
        ContentType $contentType,
        int $byteSize,
        \DateTimeImmutable $uploadedAt,
    ): self {
        return new self($id, $bucket, $key, $contentType, $byteSize, $uploadedAt);
    }
}
