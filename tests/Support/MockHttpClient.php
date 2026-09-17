<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Tests\Support;

use Nyholm\Psr7\Response;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use RuntimeException;

/**
 * A PSR-18 client that answers from a queue and records what it was asked.
 *
 * Queued responses are returned in order; the last one repeats once the queue
 * runs dry, so a polling loop can be driven without queueing a response per
 * iteration.
 */
final class MockHttpClient implements ClientInterface
{
    /** @var list<ResponseInterface> */
    private array $queue = [];

    /** @var list<RequestInterface> */
    private array $recorded = [];

    private ?ResponseInterface $last = null;

    /**
     * @param array<string, mixed>|string $body an array is encoded as JSON
     */
    public function queue(array|string $body, int $status = 200): self
    {
        $this->queue[] = new Response(
            $status,
            ['Content-Type' => 'application/json'],
            is_array($body) ? json_encode($body, JSON_THROW_ON_ERROR) : $body,
        );

        return $this;
    }

    public function sendRequest(RequestInterface $request): ResponseInterface
    {
        $this->recorded[] = $request;

        if ([] !== $this->queue) {
            $this->last = array_shift($this->queue);
        }

        if (null === $this->last) {
            throw new RuntimeException('MockHttpClient was called with nothing queued');
        }

        return $this->last;
    }

    public function requestCount(): int
    {
        return count($this->recorded);
    }

    public function lastRequest(): RequestInterface
    {
        if ([] === $this->recorded) {
            throw new RuntimeException('No request was made');
        }

        return $this->recorded[array_key_last($this->recorded)];
    }

    public function requestAt(int $index): RequestInterface
    {
        if (!isset($this->recorded[$index])) {
            throw new RuntimeException(sprintf('No request was made at index %d', $index));
        }

        return $this->recorded[$index];
    }

    /**
     * The form-encoded body of a recorded request, decoded back into an array.
     *
     * @return array<string, string>
     */
    public function formBodyAt(int $index): array
    {
        parse_str((string) $this->requestAt($index)->getBody(), $parsed);

        /** @var array<string, string> $parsed */
        return $parsed;
    }

    /**
     * The decoded body of a JSON request, for the endpoints that refuse forms.
     *
     * @return array<string, mixed>
     */
    public function jsonBodyAt(int $index): array
    {
        $decoded = json_decode((string) $this->requestAt($index)->getBody(), true);

        if (!is_array($decoded)) {
            throw new RuntimeException(sprintf('Request %d did not carry a JSON object.', $index));
        }

        /** @var array<string, mixed> $decoded */
        return $decoded;
    }
}
