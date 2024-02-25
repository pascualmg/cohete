<?php

namespace pascualmg\reactor\ddd\Infrastructure\scripts\Drafts\websocketServer;

use pascualmg\reactor\ddd\Domain\Entity\Post\Post;
use pascualmg\reactor\ddd\Infrastructure\Repository\Post\AsyncMysqlPostRepository;
use Ratchet\ConnectionInterface;
use Ratchet\MessageComponentInterface;
use React\EventLoop\Loop;

class TestMessageComponent implements MessageComponentInterface
{
    public function onOpen(ConnectionInterface $conn): void
    {
        echo 'conexion entrante';
        var_dump($conn);
    }

    public function onClose(ConnectionInterface $conn): void
    {
        echo "close";
        var_dump('close');
    }

    public function onError(ConnectionInterface $conn, \Exception $e): void
    {
        var_dump($e);
    }

    public function onMessage(ConnectionInterface $from, $msg): void
    {
        $this->getPosts($from);
    }

    /**
     * @param ConnectionInterface $from
     * @return void
     */
    public function getPosts(ConnectionInterface $from): void
    {
        (new AsyncMysqlPostRepository())
            ->findAll()
            ->then(function (array $result) use ($from) {
                $loop = Loop::get();
                $sendPostsTimer = $loop->addPeriodicTimer(1, function () use ($from, $result) {
                    /** @var Post $post */
                    foreach ($result as $post) {
                        $from->send(json_encode($post->headline));
                    }
                });
                $loop->addTimer(10, function () use ($sendPostsTimer, $loop) {
                    $loop->cancelTimer($sendPostsTimer);
                });
            });
    }

}
