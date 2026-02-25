<?php

declare(strict_types=1);

namespace pascualmg\cohete\ddd\Infrastructure\Console;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
class ConsoleCommand
{
    public function __construct(
        public readonly string $name,
        public readonly string $description = '',
    ) {
    }
}
