<?php

namespace pascualmg\reactor\ddd\Infrastructure\scripts\Drafts\websocketServer;

use pascualmg\reactor\ddd\Infrastructure\HelperFunctions\ExceptionTo;
use pascualmg\reactor\ddd\Infrastructure\HttpServer\JsonResponse;
use Ratchet\ConnectionInterface;
use Ratchet\MessageComponentInterface;

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
        var_dump(ExceptionTo::arrayWithShortTrace($e));
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