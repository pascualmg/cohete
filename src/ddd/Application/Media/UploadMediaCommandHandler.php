<?php

declare(strict_types=1);

namespace pascualmg\cohete\ddd\Application\Media;

use pascualmg\cohete\ddd\Domain\Entity\Media\Media;
use pascualmg\cohete\ddd\Domain\Entity\Media\MediaId;
use pascualmg\cohete\ddd\Domain\Entity\Media\MediaRepository;
use pascualmg\cohete\ddd\Domain\Entity\Media\ValueObject\Bucket;
use pascualmg\cohete\ddd\Domain\Entity\Media\ValueObject\ContentType;
use pascualmg\cohete\ddd\Domain\Entity\Media\ValueObject\MediaKey;
use React\Promise\PromiseInterface;
use Rx\Observable;

class UploadMediaCommandHandler
{
    public function __construct(
        private readonly MediaRepository $mediaRepository,
        private readonly string $defaultBucket,
    ) {
    }

    /**
     * @return PromiseInterface of array{id: string, key: string, byteSize: int}
     */
    public function __invoke(UploadMediaCommand $command): PromiseInterface
    {
        $id = MediaId::v4();
        $byteSize = strlen($command->body);

        $media = Media::upload(
            id: $id,
            bucket: Bucket::from($this->defaultBucket),
            key: MediaKey::from('media/' . (string)$id),
            contentType: ContentType::from($command->contentType),
            byteSize: $byteSize,
        );

        return Observable::fromPromise(
            $this->mediaRepository->put($media, $command->body)
        )
            ->map(fn () => [
                'id'       => (string)$id,
                'key'      => (string)$media->key,
                'byteSize' => $byteSize,
                'contentType' => (string)$media->contentType,
            ])
            ->toPromise();
    }
}
