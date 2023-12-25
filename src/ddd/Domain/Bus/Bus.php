<?php

namespace Pascualmg\Rx\ddd\Domain\Bus;

interface Bus
{
    public function dispatch(Message $message);

    public function subscribe(string $messageName, callable $listener);

}
