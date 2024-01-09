<?php

require dirname(__DIR__, 5) . '/vendor/autoload.php';

use pascualmg\reactor\ddd\Infrastructure\scripts\Drafts\ObservableSocket;
use Rx\ObserverInterface;

$shell = new class implements ObserverInterface {

    public function onCompleted() : void
    {
    }

    public function onError (Throwable $error): void
    {
        if ($error::class === 'RuntimeException') {
            echo "el host y puerto estan ya en uso";
        }

        if ($error::class === 'InvalidArgumentException') {
            echo "algun argumento es invalido";
        }
    }


    public function onNext( $value): void
    {
        [$data, $connection] = $value;

        exec($data, $output, $result_code);

        $connection->write(
            sprintf("comando %s ejecutado , result code %d", $data, $result_code)
        );

        foreach ($output as $line) {
            $connection->write($line . PHP_EOL);
        }
    }

};

ObservableSocket::of("11334")
    ->subscribe(
        $shell
    );


