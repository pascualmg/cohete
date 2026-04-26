<?php

declare(strict_types=1);

namespace pascualmg\cohete\ddd\Domain\Entity\Media;

use Psr\Http\Message\StreamInterface;
use React\Promise\PromiseInterface;

/**
 * Interface de dominio para object storage.
 *
 * Async-first: TODOS los metodos devuelven PromiseInterface (React/Promise).
 * Esto es bloqueante-prohibido: cohete corre single-process en ReactPHP
 * event loop. Una llamada sync bloquearia el servidor entero.
 *
 * Implementaciones:
 *   Infrastructure/Media/AsyncMinioMediaRepository.php  (prod, React\Http)
 *   Infrastructure/Media/InMemoryMediaRepository.php    (tests)
 */
interface MediaRepository
{
    /** @return PromiseInterface<void> resolve cuando los bytes estan en el bucket */
    public function put(Media $media, StreamInterface|string $body): PromiseInterface;

    /** @return PromiseInterface<?Media> resolve con la metadata o null si no existe */
    public function find(MediaId $id): PromiseInterface;

    /** @return PromiseInterface<void> resolve cuando se borro */
    public function delete(MediaId $id): PromiseInterface;

    /**
     * URL firmada con expiracion. La firma se genera localmente (no IO),
     * pero devuelve PromiseInterface por consistencia con el resto.
     *
     * @return PromiseInterface<string>
     */
    public function presignedUrl(MediaId $id, int $ttlSeconds = 3600): PromiseInterface;
}
