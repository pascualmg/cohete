<?php

namespace Passh\Rx;

use React\Socket\ConnectionInterface;

class ConnectionPool
{
    protected \SplObjectStorage $connections;

    public function __construct()
    {
        $this->connections = new \SplObjectStorage();
    }

    public function add(ConnectionInterface $connection): void
    {
        if ($this->connections->contains($connection)) {
            return;
        }

        $this->connections->attach($connection);
        $this->setConnectionName($connection, '');


        $this->sendToAll($connection, "Incoming connection from " . $connection->getRemoteAddress());

        $connection->write("Welcome to the chat maikel , Tell me your name: ");

        $connection->on('data', function (string $data) use ($connection) {
            if ('' === $this->getConnectionName($connection)) {
                $this->setConnectionName($connection, trim($data));
                $connection->write("Name setted to " . $this->getConnectionName($connection));
            } else {
                $this->sendToAll($connection, $data);
            }
        });

        $connection->on('close', function () use ($connection) {
            $this->sendToAll(
                $connection,
                "desconectado " . $connection->getRemoteAddress()
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
                    $this->getConnectionName($connectionToAvoid) . ": " .
                    $dataToSendAllExceptMe
                );
            }
        }
    }

    private function getConnectionName(ConnectionInterface $connection): string
    {
        return $this->connections[$connection];

    }

    private function setConnectionName(ConnectionInterface $connection, string $name): void
    {
        $this->connections->offsetSet($connection, $name);
    }


}