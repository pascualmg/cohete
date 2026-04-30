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
use Rx\Observable;

/**
 * Tercera version: implementacion async con RxPHP Observables.
 *
 * Mismo interface PromiseInterface (consistente con el resto del codebase).
 * La diferencia esta DENTRO: en lugar de encadenar `->then()->then()...`,
 * usamos `Observable::fromPromise()->flatMap()->...->toPromise()`.
 *
 * Ventajas:
 *   - flatMap es mas expresivo cuando encadenas N operaciones IO
 *   - retry, retryWhen, timeout, throttle... vienen gratis con Rx
 *   - misma sintaxis que ObservableMysqlPostRepository, AsyncFilePostRepository
 *
 * Cuando elegir cual:
 */
final class ObservableS3MediaRepository implements MediaRepository
{
    public function __construct(
        private readonly Browser $http,
        private readonly Aws4Signer $signer,
        private readonly string $endpoint,
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
            ],
            bodyHash: $bodyHash,
        );

        return Observable::fromPromise($this->http->put($url, $headers, $bodyStr))
            ->map(fn(ResponseInterface $_) => null)
            ->toPromise();
    }

    public function find(MediaId $id): PromiseInterface
    {
        $url = $this->urlForId($id);
        $headers = $this->signer->signRequest('HEAD', $url);

        return Observable::fromPromise($this->http->head($url, $headers))
            ->map(function (ResponseInterface $r) use ($id) {
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
            })
            ->catch(function (\Throwable $e) {
                // 404 -> emit null (no existe)
                if (str_contains($e->getMessage(), '404') || str_contains($e->getMessage(), 'Not Found')) {
                    return Observable::of(null);
                }
                return Observable::error($e);
            })
            ->toPromise();
    }

    public function delete(MediaId $id): PromiseInterface
    {
        $url = $this->urlForId($id);
        $headers = $this->signer->signRequest('DELETE', $url);

        return Observable::fromPromise($this->http->delete($url, $headers))
            ->map(fn(ResponseInterface $_) => null)
            ->toPromise();
    }

    public function presignedUrl(MediaId $id, int $ttlSeconds = 3600): PromiseInterface
    {
        return Observable::of($this->signer->presignUrl('GET', $this->urlForId($id), $ttlSeconds))
            ->toPromise();
    }

    /**
     * BONUS: aprovechando Observable, composicion expresiva de varias
     * operaciones IO con flatMap. Sube + devuelve URL firmada en UN solo
     * call. Sin Observable habria que hacer ->then(fn() => ...presigned()).
     *
     * Ademas, retry(3) gratis: si la subida falla, reintenta 3 veces.
     */
    public function putAndPresign(
        Media $media,
        StreamInterface|string $body,
        int $ttlSeconds = 3600,
    ): PromiseInterface {
        return Observable::fromPromise($this->put($media, $body))
            ->retry(3)
            ->flatMap(fn() => Observable::fromPromise($this->presignedUrl($media->id, $ttlSeconds)))
            ->toPromise();
    }

    private function urlForId(MediaId $id): string
    {
        return rtrim($this->endpoint, '/') . '/' . $this->defaultBucket . '/media/' . $id;
    }
}
