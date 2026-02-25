<?php

declare(strict_types=1);

namespace pascualmg\cohete\ddd\Infrastructure\Console;

use Psr\Container\ContainerInterface;
use ReflectionClass;

class ConsoleDispatcher
{
    /** @var array<string, array{class: class-string, description: string}> */
    private array $registry = [];

    /**
     * @param class-string[] $commandClasses
     */
    public function __construct(
        private readonly ContainerInterface $container,
        array $commandClasses,
    ) {
        foreach ($commandClasses as $class) {
            $ref = new ReflectionClass($class);
            $attrs = $ref->getAttributes(ConsoleCommand::class);
            if (empty($attrs)) {
                throw new \RuntimeException("$class missing #[ConsoleCommand] attribute");
            }
            $attr = $attrs[0]->newInstance();
            $this->registry[$attr->name] = [
                'class' => $class,
                'description' => $attr->description,
            ];
        }
    }

    public function run(array $argv): int
    {
        $commandName = $argv[1] ?? 'list';
        $args = self::parseArgs(array_slice($argv, 2));

        if ($commandName === 'list' || $commandName === 'help') {
            return $this->printList();
        }

        if (!isset($this->registry[$commandName])) {
            fwrite(STDERR, "Unknown command: $commandName\n\n");
            $this->printList();
            return 1;
        }

        $entry = $this->registry[$commandName];
        $command = $this->container->get($entry['class']);

        try {
            $exitCode = ($command)($args);
            return is_int($exitCode) ? $exitCode : 0;
        } catch (\Throwable $e) {
            fwrite(STDERR, "[ERROR] {$e->getMessage()}\n");
            if (isset($args['verbose']) || isset($args['v'])) {
                fwrite(STDERR, $e->getTraceAsString() . "\n");
            }
            return 1;
        }
    }

    public static function parseArgs(array $rawArgs): array
    {
        $args = ['_' => []];
        foreach ($rawArgs as $arg) {
            if (str_starts_with($arg, '--')) {
                $part = substr($arg, 2);
                if (str_contains($part, '=')) {
                    [$key, $value] = explode('=', $part, 2);
                    $args[$key] = $value;
                } else {
                    $args[$part] = true;
                }
            } else {
                $args['_'][] = $arg;
            }
        }
        return $args;
    }

    private function printList(): int
    {
        fwrite(STDOUT, "\n  Cohete Console\n\n");
        fwrite(STDOUT, "  Available commands:\n\n");

        $maxLen = empty($this->registry) ? 0 : max(array_map('strlen', array_keys($this->registry)));

        foreach ($this->registry as $name => $entry) {
            fprintf(STDOUT, "    %-{$maxLen}s  %s\n", $name, $entry['description']);
        }

        fwrite(STDOUT, "\n  Usage: php src/console.php <command> [--option=value ...]\n\n");
        return 0;
    }
}
