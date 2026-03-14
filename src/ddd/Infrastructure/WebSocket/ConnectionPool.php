<?php

namespace pascualmg\cohete\ddd\Infrastructure\WebSocket;

use JsonException;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use Ratchet\ConnectionInterface;

class ConnectionPool
{
    private \SplObjectStorage $objectStorage;

    public function __construct()
    {
        $this->objectStorage = new \SplObjectStorage();
    }

    public function add(ConnectionInterface $conn): void
    {
        $this->objectStorage->attach(
            $conn,
            Uuid::uuid4()
        );
    }

    public function remove(ConnectionInterface $conn): void
    {
        $this->objectStorage->detach($conn);
    }

    /**
     * @param array $payload
     * @param ConnectionInterface|null $except
     * @return void
     * @throws JsonException
     */
    public function sendToAll(array $payload, ?ConnectionInterface $except = null): void
    {
        foreach ($this->objectStorage as $connection) {
            if ($connection !== $except) {
                $connection->send(json_encode($payload, JSON_THROW_ON_ERROR));
            }
        }
    }

    public function getUuid(ConnectionInterface $from): UuidInterface
    {
        return $this->objectStorage[$from];
    }
}
