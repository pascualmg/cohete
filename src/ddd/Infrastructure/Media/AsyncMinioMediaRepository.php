<?php

declare(strict_types=1);

namespace pascualmg\cohete\ddd\Infrastructure\Media;

use pascualmg\cohete\ddd\Domain\Entity\Media\Media;
use pascualmg\cohete\ddd\Domain\Entity\Media\MediaId;
use pascualmg\cohete\ddd\Domain\Entity\Media\MediaRepository;
use pascualmg\cohete\ddd\Domain\Entity\Media\ValueObject\Bucket;
use pascualmg\cohete\ddd\Domain\Entity\Media\ValueObject\ContentType;
use pascualmg\cohete\ddd\Domain\Entity\Media\ValueObject\MediaKey;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use React\Http\Browser;
use React\Promise\PromiseInterface;
use function React\Promise\reject;
use function React\Promise\resolve;

/**
 * Implementacion async de MediaRepository sobre MinIO/S3.
 *
 * Usa React\Http\Browser (NO bloqueante) + AWS Signature V4 manual.
 * NO usa aws-sdk-php porque su modelo Guzzle promises bloquea el event loop
 * de ReactPHP cuando se llama ->wait().
 */
final class AsyncMinioMediaRepository implements MediaRepository
{
    public function __construct(
        private readonly Browser $http,
        private readonly Aws4Signer $signer,
        private readonly string $endpoint,         // ej: http://100.64.0.4:9000
        private readonly Bucket $defaultBucket,
    ) {
    }

    public function put(Media $media, StreamInterface|string $body): PromiseInterface
    {
        $url = $this->urlForId($media->id);
        $bodyStr = is_string($body) ? $body : (string) $body;
        $bodyHash = hash('sha256', $bodyStr);

        $headers = $this->signer->signRequest(
            method: 'PUT',
            url: $url,
            headers: [
                'content-type' => (string) $media->contentType,
                'content-length' => (string) strlen($bodyStr),
                'x-amz-meta-media-id' => (string) $media->id,
                'x-amz-meta-uploaded-at' => $media->uploadedAt->format(\DateTimeInterface::ATOM),
            ],
            bodyHash: $bodyHash,
        );

        return $this->http->put($url, $headers, $bodyStr)->then(
            fn(ResponseInterface $r) => null,  // resolve void
        );
    }

    public function find(MediaId $id): PromiseInterface
    {
        $url = $this->urlForId($id);
        $headers = $this->signer->signRequest('HEAD', $url);

        return $this->http->head($url, $headers)->then(
            function (ResponseInterface $r) use ($id) {
                $contentType = $r->getHeaderLine('Content-Type') ?: 'application/octet-stream';
                $contentLength = (int) ($r->getHeaderLine('Content-Length') ?: 0);
                $uploadedAt = $r->getHeaderLine('x-amz-meta-uploaded-at') ?: 'now';

                return Media::reconstitute(
                    id: $id,
                    bucket: $this->defaultBucket,
                    key: MediaKey::from("media/{$id}"),
                    contentType: ContentType::from($contentType),
                    byteSize: $contentLength,
                    uploadedAt: new \DateTimeImmutable($uploadedAt),
                );
            },
            function (\Throwable $e) {
                // 404 = no existe (resolve null en lugar de propagar excepcion)
                if (str_contains($e->getMessage(), '404') || str_contains($e->getMessage(), 'Not Found')) {
                    return resolve(null);
                }
                return reject($e);
            },
        );
    }

    public function delete(MediaId $id): PromiseInterface
    {
        $url = $this->urlForId($id);
        $headers = $this->signer->signRequest('DELETE', $url);

        return $this->http->delete($url, $headers)->then(
            fn(ResponseInterface $r) => null,
        );
    }

    public function presignedUrl(MediaId $id, int $ttlSeconds = 3600): PromiseInterface
    {
        // Firma local sin IO -> resolve sync
        $url = $this->signer->presignUrl('GET', $this->urlForId($id), $ttlSeconds);
        return resolve($url);
    }

    private function urlForId(MediaId $id): string
    {
        return rtrim($this->endpoint, '/') . '/' . $this->defaultBucket . '/media/' . $id;
    }
}
