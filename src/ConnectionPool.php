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
        if($this->connections->contains($connection)) {
            return;
        }

        $this->connections->attach($connection);

        $connection->on('data', function (string $data) use ($connection) {
            /** @var ConnectionInterface $conn */
            foreach ($this->connections as $conn) {
                if ($conn !== $connection) {
                    $conn->write($data);
                }
            }
        });

        $connection->on('close', function () use ($connection) {
            /** @var ConnectionInterface $otherConnection */
            foreach ($this->connections as $otherConnection) {
                if($otherConnection !== $connection) {
                    $otherConnection->write('la conexion con ' . $connection->getRemoteAddress() . 'se a perdud');
                }
            }
            $this->connections->detach($connection);
        });
    }


}