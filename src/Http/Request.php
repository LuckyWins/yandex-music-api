<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Http;

use Http\Discovery\Psr17FactoryDiscovery;
use Http\Discovery\Psr18ClientDiscovery;
use JsonException;
use LuckyWins\YandexMusic\Exception\BadRequestException;
use LuckyWins\YandexMusic\Exception\NetworkException;
use LuckyWins\YandexMusic\Exception\NotFoundException;
use LuckyWins\YandexMusic\Exception\UnauthorizedException;
use LuckyWins\YandexMusic\Exception\YandexMusicException;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;

/**
 * Everything that speaks HTTP.
 *
 * Wraps a PSR-18 client, adds the headers Yandex expects, unwraps the response
 * envelope and turns unsuccessful statuses into the library's exceptions.
 *
 * Timeouts are a caveat worth knowing: PSR-18 has no notion of one, so a client
 * you inject keeps whatever timeout you configured on it. The default timeout
 * below applies only to the client this class builds for itself.
 */
final class Request
{
    public const DEFAULT_TIMEOUT = 5.0;

    private const DEFAULT_HEADERS = [
        'X-Yandex-Music-Client' => 'YandexMusicAndroid/24023621',
        'User-Agent' => 'Yandex-Music-API',
    ];

    /** @var array<string, string> */
    private array $headers = self::DEFAULT_HEADERS;

    private readonly ClientInterface $client;

    private readonly RequestFactoryInterface $requestFactory;

    private readonly StreamFactoryInterface $streamFactory;

    public function __construct(
        ?ClientInterface $client = null,
        ?RequestFactoryInterface $requestFactory = null,
        ?StreamFactoryInterface $streamFactory = null,
        float $timeout = self::DEFAULT_TIMEOUT,
    ) {
        $this->client = $client ?? self::discoverClient($timeout);
        $this->requestFactory = $requestFactory ?? Psr17FactoryDiscovery::findRequestFactory();
        $this->streamFactory = $streamFactory ?? Psr17FactoryDiscovery::findStreamFactory();
    }

    public function setAuthorization(string $token): void
    {
        // Yandex uses the OAuth scheme, not Bearer.
        $this->headers['Authorization'] = 'OAuth '.$token;
    }

    public function clearAuthorization(): void
    {
        unset($this->headers['Authorization']);
    }

    /**
     * @param string $language one of en, uz, uk, us, ru, kk, hy
     */
    public function setLanguage(string $language): void
    {
        $this->headers['Accept-Language'] = $language;
    }

    public function setHeader(string $name, string $value): void
    {
        $this->headers[$name] = $value;
    }

    /** @return array<string, string> */
    public function getHeaders(): array
    {
        return $this->headers;
    }

    /**
     * @param array<string, scalar|null> $params query parameters
     */
    public function get(string $url, array $params = []): mixed
    {
        if ([] !== $params) {
            $url .= (str_contains($url, '?') ? '&' : '?').http_build_query($params);
        }

        return $this->send('GET', $url);
    }

    /**
     * @param array<string, mixed> $data form-encoded body
     */
    public function post(string $url, array $data = []): mixed
    {
        return $this->send('POST', $url, $data);
    }

    /**
     * @param array<string, mixed> $data form-encoded body
     */
    public function put(string $url, array $data = []): mixed
    {
        return $this->send('PUT', $url, $data);
    }

    /**
     * @param array<string, mixed> $data form-encoded body
     */
    public function delete(string $url, array $data = []): mixed
    {
        return $this->send('DELETE', $url, $data);
    }

    /**
     * Fetch a URL as raw bytes, without the API headers.
     *
     * Used for files served outside the API — cover art, audio — where the
     * Authorization header is neither wanted nor accepted.
     */
    public function retrieve(string $url): string
    {
        $request = $this->requestFactory->createRequest('GET', $url);

        try {
            $response = $this->client->sendRequest($request);
        } catch (ClientExceptionInterface $e) {
            throw new NetworkException($e->getMessage(), null, $e);
        }

        $status = $response->getStatusCode();
        $body = (string) $response->getBody();

        if ($status < 200 || $status > 299) {
            $this->handleErrorResponse($status, $body);
        }

        return $body;
    }

    /**
     * @param array<string, mixed>|null $data
     */
    private function send(string $method, string $url, ?array $data = null): mixed
    {
        $request = $this->requestFactory->createRequest($method, $url);

        foreach ($this->headers as $name => $value) {
            $request = $request->withHeader($name, $value);
        }

        if (null !== $data) {
            $request = $request
                ->withHeader('Content-Type', 'application/x-www-form-urlencoded')
                ->withBody($this->streamFactory->createStream(http_build_query($data)));
        }

        try {
            $response = $this->client->sendRequest($request);
        } catch (ClientExceptionInterface $e) {
            throw new NetworkException($e->getMessage(), null, $e);
        }

        $status = $response->getStatusCode();
        $body = (string) $response->getBody();

        if ($status < 200 || $status > 299) {
            $this->handleErrorResponse($status, $body);
        }

        return $this->parse($body)->result;
    }

    /**
     * Decode a response body into the envelope.
     *
     * @throws YandexMusicException when the body is not decodable JSON
     */
    public function parse(string $body): Response
    {
        try {
            $decoded = json_decode($body, true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            throw new YandexMusicException('Invalid server response', null, $e);
        }

        return Response::fromDecoded($decoded);
    }

    /**
     * Map an unsuccessful status onto an exception.
     *
     * The mapping mirrors the reference library so that behaviour stays
     * predictable across the two ports.
     *
     * @return never
     */
    public function handleErrorResponse(int $status, string $body): void
    {
        $code = null;
        $message = 'Unknown error';

        try {
            $response = $this->parse($body);
            if ($response->hasError()) {
                $code = $response->errorCode;
                $message = $response->errorMessage();
            }
        } catch (YandexMusicException) {
            $message = 'Unknown HTTP error';
        }

        throw match (true) {
            401 === $status, 403 === $status => new UnauthorizedException($message, $code),
            400 === $status => new BadRequestException($message, $code),
            404 === $status => new NotFoundException($message, $code),
            409 === $status, 413 === $status => new NetworkException($message, $code),
            502 === $status => new NetworkException('Bad Gateway', $code),
            default => new NetworkException(sprintf('%s (%d): %s', $message, $status, $body), $code),
        };
    }

    /**
     * Build a client when the caller did not supply one.
     *
     * Guzzle is preferred because it can be given the default timeout; anything
     * else discovery turns up is used as configured, timeout included.
     */
    private static function discoverClient(float $timeout): ClientInterface
    {
        if (class_exists(\GuzzleHttp\Client::class)) {
            return new \GuzzleHttp\Client([
                'timeout' => $timeout,
                'connect_timeout' => $timeout,
                'http_errors' => false,
            ]);
        }

        return Psr18ClientDiscovery::find();
    }
}
