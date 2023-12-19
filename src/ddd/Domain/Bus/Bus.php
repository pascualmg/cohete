<?php

namespace Pascualmg\Rx\ddd\Domain\Bus;

interface Bus
{
    public function dispatch(Event $event);
    public function subscribe(string $eventName, callable $listener);

}
