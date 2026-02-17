<?php

namespace pascualmg\cohete\ddd\Infrastructure\Service;

class InMemoryRateLimiter
{
    private array $attempts = [];

    public function __construct(
        private readonly int $maxAttempts = 5,
        private readonly int $windowSeconds = 600,
    ) {
    }

    public function allow(string $key): bool
    {
        $now = time();
        $this->cleanup($key, $now);

        if (!isset($this->attempts[$key])) {
            $this->attempts[$key] = [];
        }

        if (count($this->attempts[$key]) >= $this->maxAttempts) {
            return false;
        }

        $this->attempts[$key][] = $now;
        return true;
    }

    private function cleanup(string $key, int $now): void
    {
        if (!isset($this->attempts[$key])) {
            return;
        }

        $this->attempts[$key] = array_values(array_filter(
            $this->attempts[$key],
            fn (int $timestamp) => ($now - $timestamp) < $this->windowSeconds
        ));

        if (empty($this->attempts[$key])) {
            unset($this->attempts[$key]);
        }
    }
}
