<?php

namespace pascualmg\reactor\ddd\Domain\Bus;

interface MessageBus
{
    public function dispatch(Message $message);

    public function listen(string $messageName, callable $listener);

}
