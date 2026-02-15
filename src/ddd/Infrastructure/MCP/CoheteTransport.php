<?php

declare(strict_types=1);

namespace pascualmg\cohete\ddd\Infrastructure\MCP;

use Evenement\EventEmitterTrait;
use PhpMcp\Server\Contracts\ServerTransportInterface;
use PhpMcp\Server\Exception\TransportException;
use React\Promise\Deferred;
use React\Promise\PromiseInterface;
use React\Stream\ThroughStream;
use React\Stream\WritableStreamInterface;

use function React\Promise\reject;
use function React\Promise\resolve;

/**
 * Custom MCP transport integrated into Cohete's existing HTTP server.
 *
 * No crea su propio servidor HTTP. Los controllers de Cohete
 * llaman a registerClient() y handleIncomingMessage() directamente.
 * Las respuestas MCP se envian via SSE streams.
 */
class CoheteTransport implements ServerTransportInterface
{
    use EventEmitterTrait;

    /** @var array<string, ThroughStream> */
    private array $sseStreams = [];

    public function listen(): void
    {
        // No-op: Cohete's ReactHttpServer ya esta escuchando
        $this->emit('ready');
    }

    public function registerClient(string $clientId, ThroughStream $stream): void
    {
        $this->sseStreams[$clientId] = $stream;

        $stream->on('close', function () use ($clientId) {
            unset($this->sseStreams[$clientId]);
            $this->emit('client_disconnected', [$clientId, 'SSE stream closed']);
        });

        $stream->on('error', function (\Throwable $error) use ($clientId) {
            unset($this->sseStreams[$clientId]);
            $this->emit('error', [new TransportException("SSE Error: {$error->getMessage()}", 0, $error), $clientId]);
            $this->emit('client_disconnected', [$clientId, 'SSE stream error']);
        });

        $this->emit('client_connected', [$clientId]);
    }

    public function isClientConnected(string $clientId): bool
    {
        return isset($this->sseStreams[$clientId]);
    }

    public function handleIncomingMessage(string $jsonRpc, string $clientId): void
    {
        $this->emit('message', [$jsonRpc, $clientId]);
    }

    public function sendToClientAsync(string $clientId, string $rawFramedMessage): PromiseInterface
    {
        if (!isset($this->sseStreams[$clientId])) {
            return reject(new TransportException("Client '{$clientId}' not connected."));
        }

        $stream = $this->sseStreams[$clientId];
        if (!$stream->isWritable()) {
            return reject(new TransportException("SSE stream for '{$clientId}' not writable."));
        }

        $jsonData = trim($rawFramedMessage);
        if ($jsonData === '') {
            return resolve(null);
        }

        $written = $this->sendSseEvent($stream, 'message', $jsonData);

        if ($written) {
            return resolve(null);
        }

        $deferred = new Deferred();
        $stream->once('drain', fn () => $deferred->resolve(null));
        return $deferred->promise();
    }

    private function sendSseEvent(WritableStreamInterface $stream, string $event, string $data, ?string $id = null): bool
    {
        if (!$stream->isWritable()) {
            return false;
        }

        $frame = "event: {$event}\n";
        if ($id !== null) {
            $frame .= "id: {$id}\n";
        }
        foreach (explode("\n", $data) as $line) {
            $frame .= "data: {$line}\n";
        }
        $frame .= "\n";

        return $stream->write($frame);
    }

    public function close(): void
    {
        $streams = $this->sseStreams;
        $this->sseStreams = [];
        foreach ($streams as $stream) {
            $stream->close();
        }
        $this->emit('close', ['CoheteTransport closed.']);
        $this->removeAllListeners();
    }
}
