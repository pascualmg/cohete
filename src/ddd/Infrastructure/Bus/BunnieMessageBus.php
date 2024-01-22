<?php

namespace pascualmg\reactor\ddd\Infrastructure\Bus;

use Bunny\Async\Client;
use Bunny\Channel;
use Bunny\Message;
use Bunny\Protocol\MethodQueueDeclareOkFrame;
use pascualmg\reactor\ddd\Domain\Bus\Message as BusMessage;
use pascualmg\reactor\ddd\Domain\Bus\MessageBus;
use React\Promise\PromiseInterface;
use Rx\Observable;

class BunnieMessageBus implements MessageBus
{
    private const QUEUE_NAME = 'queue_name';
    private const EXCHANGE_NAME = 'exchange_name';
    private Observable $clientObservable;
    private Observable\RefCountObservable $channelObservable;


    public function __construct(
        private readonly Client $client
    ) {
        //alias, se declara aquí para garantizar que esta como global para cualquier function de la clase
        function fp(PromiseInterface $promise): Observable
        {
            return Observable::fromPromise($promise);
        }

        //esto hace , que el canal se comparta y no se produzca el error de que el canal ya
        //esta abierto.
        $this->channelObservable = //todo: channelObservableWithDeclaredQueue...
            fp($this->client->connect())
                ->flatMap(fn (Client $client) => fp($client->channel()))
                ->share();
    }

    /**
     * @throws \JsonException
     */
    public function dispatch(BusMessage $message): void
    {
        $payload = json_encode($message, JSON_THROW_ON_ERROR);


        $senderObservable = $this->channelObservable
            ->flatMap(fn (Channel $channel) => fp($channel->queueDeclare(self::QUEUE_NAME)) //declaramos la cola
            ->filter(fn ($okOrError) => $okOrError instanceof MethodQueueDeclareOkFrame) //solo si esta ok
            ->flatMap(fn () => fp(
                $channel->publish( //publicamos , esto devuelve un bool :)
                    $payload,
                    [],
                    self::EXCHANGE_NAME,
                    'routing_key'
                )
            )));

        $senderObservable->subscribe(
            function (bool $isPublished) {
                var_dump($isPublished);
            },
            function ($error) {
                var_dump($error);
            },
            function () {
                echo 'complete sender';
            }
        );
    }

    public function listen(string $messageName, callable $listener): void
    {
        $this->channelObservable
            ->flatMap(
                fn (Channel $channel) => fp($channel->queueDeclare(self::QUEUE_NAME)) //declaramos la cola
            ->filter(fn ($okOrError) => $okOrError instanceof MethodQueueDeclareOkFrame) //solo si esta ok
            ->flatMap(
                fn () => fp(
                    $channel->consume(
                        function (Message $message) use ($listener, $channel) {
                            $listener(json_decode($message->content, true, 512, JSON_THROW_ON_ERROR));
                            $channel->ack($message);
                        },
                        self::QUEUE_NAME
                    )
                )
            )
            )->subscribe(
                function ($next) {
                    echo $next::class . PHP_EOL;
                },
                function ($error) {
                    var_dump($error);
                },
                function () {
                    echo 'complete';
                }
            );
    }

    public function __destruct()
    {
        $this->client->disconnect();
    }
}
