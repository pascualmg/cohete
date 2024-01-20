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


    public function __construct(
        private readonly Client $client
    ) {
    }

    /**
     * @throws \JsonException
     */
    public function dispatch(BusMessage $message): void
    {

        function fp(PromiseInterface $promise): Observable
        {
            return Observable::fromPromise($promise);
        }
        $payload = json_encode($message, JSON_THROW_ON_ERROR);

        $rabbitMqMessageSenderObservable = fp($this->client->connect())
            ->flatMap(fn(Client $client): Observable => fp($client->channel())
                ->map(fn(Channel $channel) => [$channel, $payload]))
            ->flatMap(function (array $args): Observable {
                [$channel,$payload] = $args;
                return fp($channel->queueDeclare(self::QUEUE_NAME))
                    ->filter( fn ($okOrError) => $okOrError instanceof MethodQueueDeclareOkFrame)
                    ->map(function () use ($channel, $payload) {
                        $channel->publish(
                            $payload,
                            [],
                        );
                        return 'enviado';
                    });
            })
        ->repeat(1000);

        $rabbitMqMessageSenderObservable->subscribe(
            function ($msg) {
                var_dump($msg);
            },
            function ($error) {
                var_dump($error);
            },
            function () {
                echo 'complete';
            }


        );
    }

    public function subscribe(string $messageName, callable $listener): void
    {
        $connectObs = Observable::fromPromise($this->client->connect());

        $queueName = self::QUEUE_NAME;

        $connectObs->flatMap(function (Client $client) use ($queueName, $listener) {
            return Observable::fromPromise($client->channel())
                ->map(function (Channel $channel) use ($listener, $client, $queueName) {
                    return [$client, $channel, $queueName, $listener];
                });
        })
            ->flatMap(function (array $args) {
                [$client, $channel, $queue, $listener] = $args;
                return Observable::fromPromise($channel->queueDeclare($queue))
                    ->map(function () use ($channel, $queue, $listener, $client) {
                        $channel->consume(
                            function (Message $message) use ($listener) {
                                $listener(json_decode($message->content, true, 512, JSON_THROW_ON_ERROR));
                            },
                            $queue
                        );
                        return 'Subscribed';
                    });
            })
            ->subscribe(function ($msg) {
                echo $msg, PHP_EOL;
            });
    }

    public function __destruct()
    {
        $this->client->disconnect();
    }
}