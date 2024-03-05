<?php

namespace pascualmg\reactor\ddd\Domain\Bus;

class DomainEventBus
{
    public function __construct(
        private readonly MessageBus $messageBus
    ) {
    }

    public function dispatch(): void
    {

    }

    public function foo()
    {

    }
}
