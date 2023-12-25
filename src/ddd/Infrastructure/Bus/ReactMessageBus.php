<?php

namespace Pascualmg\Rx\ddd\Infrastructure\Bus;

use Evenement\EventEmitter;
use Pascualmg\Rx\ddd\Domain\Bus\Message;
use Pascualmg\Rx\ddd\Domain\Bus\MessageBus;
use React\EventLoop\LoopInterface;

class ReactMessageBus implements MessageBus
{
    private EventEmitter $emitter;
    private LoopInterface $loop;

    public function __construct(LoopInterface $loop)
    {
        $this->emitter = new EventEmitter();
        $this->loop = $loop;
    }

    public function dispatch(Message $message): void
    {
        $this->loop->futureTick(function () use ($message) {
            $this->emitter->emit(
                $message->name,
                [$message->payload]
            );
        });
    }

    public function subscribe(string $messageName, callable $listener): void
    {
        //Aquí se emiten eventos asincrónicamente
        $this->emitter->on(
            $messageName,
            function ($payload) use ($listener) {
                $this->loop->futureTick(function () use ($listener, $payload) {
                    $listener($payload);
                });
            }
        );
    }
}
