<?php

namespace Pascualmg\Rx\ddd\Domain\Bus;

interface MessageBus
{
    public function dispatch(Message $message);

    public function subscribe(string $messageName, callable $listener);

}
