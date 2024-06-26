<?php

namespace pascualmg\cohete\ddd\Infrastructure\scripts\Drafts\websocketServer;

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

    /**
     * @param ConnectionInterface $conn
     * @return void
     */
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
     * Sends the provided payload to all connections except the specified one.
     *
     * @param array $payload The data to be sent to all connections.
     * @param ConnectionInterface|null $except The connection to exclude from sending.
     * @return void
     * @throws JsonException If an error occurs while encoding the payload to JSON.
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
