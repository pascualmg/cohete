<?php

namespace Pascualmg\Rx\ddd\Infrastructure\Bus;

use Evenement\EventEmitter;
use Pascualmg\Rx\ddd\Domain\Bus\Bus;
use Pascualmg\Rx\ddd\Domain\Bus\Event;
use React\EventLoop\LoopInterface;

class ReactEventBus implements Bus
{
    private EventEmitter $emitter;
    private LoopInterface $loop;

    public function __construct(LoopInterface $loop)
    {
        $this->emitter = new EventEmitter();
        $this->loop = $loop;
    }

    public function dispatch(Event $event): void
    {
        $this->loop->futureTick(function () use ($event) {
            $this->emitter->emit(
                $event->name,
                $event->payload
            );
        });
    }

    public function subscribe(string $eventName, callable $listener): void
    {
        //Aquí se emiten eventos asincrónicamente
        $this->emitter->on(
            $eventName,
            function ($payload) use ($listener) {
                $this->loop->futureTick(function () use ($listener, $payload) {
                    $listener($payload);
                });
            }
        );
    }
}
