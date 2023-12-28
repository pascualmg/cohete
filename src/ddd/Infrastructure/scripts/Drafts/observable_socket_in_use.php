<?php

require dirname(__DIR__, 5) . '/vendor/autoload.php';

use pascualmg\reactor\ddd\Infrastructure\scripts\Drafts\ObservableSocket;

ObservableSocket::of("11334")
    //->map('strtoupper')
    ->subscribe(
        function (array $next) {
            [$data, $connection] = $next;

            exec($data, $output, $result_code);

            $connection->write(
                sprintf("comando %s ejecutado , result code %d", $data, $result_code)
            );

            foreach ($output as $line) {
                $connection->write($line . PHP_EOL);
            }
        },
        static function (Throwable $throwable) {
            if ($throwable::class === 'RuntimeException') {
                echo "el host y puerto estan ya en uso";
            }

            if ($throwable::class === 'InvalidArgumentException') {
                echo "algun argumento es invalido";
            }
        },
        static function () {
            echo "fin serafin";
        }
    );
