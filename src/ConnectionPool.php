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
            $this->sendToAll($connection, $data);
        });

        $connection->on('close', function () use ($connection) {
            $this->sendToAll($connection, "desconectado " . $connection->getRemoteAddress());

            $this->connections->detach($connection);
        });
    }

    /**
     * @param mixed $connectionToAvoid
     * @param string $dataToSendAllExceptMe
     * @return void
     */
    function sendToAll(ConnectionInterface $connectionToAvoid, string $dataToSendAllExceptMe): void
    {
        /** @var ConnectionInterface $conn */
        foreach ($this->connections as $conn) {
            if ($conn !== $connectionToAvoid) {
                $conn->write($dataToSendAllExceptMe);
            }
        }
    }


}