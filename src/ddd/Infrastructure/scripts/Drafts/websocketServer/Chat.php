<?php

namespace pascualmg\reactor\ddd\Infrastructure\scripts\Drafts\websocketServer;

use pascualmg\reactor\ddd\Domain\Entity\Post;
use pascualmg\reactor\ddd\Infrastructure\Repository\Post\AsyncMysqlPostRepository;
use Ratchet\ConnectionInterface;
use Ratchet\MessageComponentInterface;
use React\EventLoop\Loop;

class Chat implements MessageComponentInterface
{

    private ConnectionPool $connectionPool;

    public function __construct()
    {
        $this->connectionPool = new ConnectionPool();
    }


    public function onOpen(ConnectionInterface $conn): void
    {
        $this->connectionPool->add($conn);
    }

    public function onClose(ConnectionInterface $conn): void
    {
        $this->connectionPool->remove($conn);
    }

    public function onError(ConnectionInterface $conn, \Exception $e): void
    {
        var_dump($e);
    }

    public function onMessage(ConnectionInterface $from, $msg): void
    {
        $uuid = $this->connectionPool->getUuid($from);

        $this->connectionPool->sendToAll(
            [
                'msg' => $msg,
                'uuid' => $uuid
            ],
            $from
        );
    }
}