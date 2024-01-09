<?php

namespace pascualmg\reactor\ddd\Infrastructure\scripts\Drafts\websocketServer;

use Ratchet\ConnectionInterface;

class ConnectionPool
{

    private \SplObjectStorage $connectionsPool;
    public function __construct()
    {
        $this->connectionsPool = new \SplObjectStorage();
    }

    /**
     * @param ConnectionInterface $conn
     * @return void
     */
    public function attach(ConnectionInterface $conn) :void
    {
        $this->connectionsPool->attach($conn);

    }

    public function dettach(ConnectionInterface $conn) :void
    {
        $this->connectionsPool->detach($conn);
    }
}