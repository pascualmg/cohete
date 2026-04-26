<?php

declare(strict_types=1);

namespace pascualmg\cohete\ddd\Infrastructure\Media;

use Aws\S3\S3Client;
use pascualmg\cohete\ddd\Domain\Entity\Media\Media;
use pascualmg\cohete\ddd\Domain\Entity\Media\MediaId;
use pascualmg\cohete\ddd\Domain\Entity\Media\ValueObject\Bucket;
use pascualmg\cohete\ddd\Domain\Entity\Media\ValueObject\ContentType;
use pascualmg\cohete\ddd\Domain\Entity\Media\ValueObject\MediaKey;
use Psr\Http\Message\StreamInterface;

/**
 * ⚠️  ANTIPATTERN — NO USAR EN PRODUCCION  ⚠️
 *
 * Esta clase se queda en el codigo SOLO como referencia didactica para el
 * post de blog que compara approach sync vs async sobre object storage.
 *
 * NO implementa MediaRepository (que ahora es async-first). Si la
 * llamaras, BLOQUEARIA el event loop entero de ReactPHP durante toda la
 * subida/descarga (puede ser segundos para ficheros grandes), congelando
 * el servidor HTTP, las conexiones WebSocket activas, los timers, etc.
 *
 * La version correcta es AsyncMinioMediaRepository (React\Http\Browser
 * + AWS Signature V4 manual).
 *
 * Detectado por Pascual durante review del PR #4: cohete corre
 * single-process en ReactPHP — toda IO debe ser PromiseInterface.
 */
final class MinioMediaRepository
{
    public function __construct(
        private readonly S3Client $client,
        private readonly Bucket $defaultBucket,
    ) {
    }

    /** ⚠️ BLOQUEA el event loop durante toda la subida. */
    public function put(Media $media, StreamInterface|string $body): void
    {
        $this->client->putObject([
            'Bucket'      => (string) $media->bucket,
            'Key'         => "media/{$media->id}",
            'Body'        => $body,
            'ContentType' => (string) $media->contentType,
        ]);
    }

    /** ⚠️ BLOQUEA durante el HEAD request. */
    public function find(MediaId $id): ?Media
    {
        try {
            $head = $this->client->headObject([
                'Bucket' => (string) $this->defaultBucket,
                'Key'    => "media/{$id}",
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
            key: MediaKey::from("media/{$id}"),
            contentType: ContentType::from($head['ContentType'] ?? 'application/octet-stream'),
            byteSize: (int) ($head['ContentLength'] ?? 0),
            uploadedAt: new \DateTimeImmutable($head['Metadata']['uploaded-at'] ?? 'now'),
        );
    }

    /** ⚠️ BLOQUEA durante el DELETE. */
    public function delete(MediaId $id): void
    {
        $this->client->deleteObject([
            'Bucket' => (string) $this->defaultBucket,
            'Key'    => "media/{$id}",
        ]);
    }

    /**
     * Esto NO bloquea (es solo firma local) pero el resto si.
     * Mantenemos la firma sync por consistencia con la clase.
     */
    public function presignedUrl(MediaId $id, int $ttlSeconds = 3600): string
    {
        $cmd = $this->client->getCommand('GetObject', [
            'Bucket' => (string) $this->defaultBucket,
            'Key'    => "media/{$id}",
        ]);
        return (string) $this->client
            ->createPresignedRequest($cmd, "+{$ttlSeconds} seconds")
            ->getUri();
    }
}
