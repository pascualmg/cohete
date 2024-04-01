<?php

namespace pascualmg\cohete\ddd\Infrastructure\scripts\chat;

use Colors\Color;
use React\Socket\ConnectionInterface;
use SplObjectStorage;

class ConnectionPool
{
    protected SplObjectStorage $connections;

    public function __construct()
    {
        $this->connections = new SplObjectStorage();
    }

    public function add(ConnectionInterface $connection): void
    {
        if ($this->connections->contains($connection)) {
            return;
        }

        $this->connections->attach($connection);

        $this->sendToAll($connection, "Incoming connection from " . $connection->getRemoteAddress());

        $connection->write("Welcome to the chat maikel , Tell me your name: ");

        $connection->on('data', function (string $data) use ($connection) {
            if (null === $this->getConnectionName($connection)) {
                $name = trim($data);
                $this->setConnectionName($connection, $name);
                $connection->write(sprintf("Hello %s happy chat!", $name));
                $this->sendToAll($connection, "%s is connected to the chat $name");
            } else {
                $this->sendToAll(
                    $connection,
                    sprintf(
                        "%s: %s",
                        (new Color($this->getConnectionName($connection)))->red(),
                        $data
                    )
                );
            }
        });

        $connection->on('close', function () use ($connection) {
            $this->sendToAll(
                $connection,
                sprintf(
                    "%s has left the chat from %s",
                    $this->getConnectionName($connection),
                    $connection->getRemoteAddress()
                )
            );

            $this->connections->detach($connection);
        });
    }

    /**
     * @param mixed $connectionToAvoid
     * @param string $dataToSendAllExceptMe
     * @return void
     */
    private function sendToAll(ConnectionInterface $connectionToAvoid, string $dataToSendAllExceptMe): void
    {
        /** @var ConnectionInterface $conn */
        foreach ($this->connections as $conn) {
            if ($conn !== $connectionToAvoid) {
                $conn->write(
                    $dataToSendAllExceptMe
                );
            }
        }
    }

    private function getConnectionName(ConnectionInterface $connection): ?string
    {
        return $this->connections[$connection] ?? null;
    }

    private function setConnectionName(ConnectionInterface $connection, string $name): void
    {
        $this->connections->offsetSet($connection, $name);
    }


}
