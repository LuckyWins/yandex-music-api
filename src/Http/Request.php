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
use Psr\Http\Message\ResponseInterface;
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

    /** How much of a download to hold in memory at once. */
    private const CHUNK_SIZE = 65536;

    /** Enough hops for the audio redirect chain, few enough to notice a loop. */
    private const MAX_REDIRECTS = 5;

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
     * Post a JSON body rather than a form.
     *
     * Most of the API takes form-encoded bodies, but not all of it: the radio
     * feedback endpoints answer 400 to a form and accept the same fields as
     * JSON. Checked against the live API — the reference library still sends
     * forms there, and is refused.
     *
     * @param array<string, mixed> $data
     */
    public function postJson(string $url, array $data = []): mixed
    {
        return $this->send('POST', $url, $data, json: true);
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
     * Used for files served outside the API — cover art, audio, lyrics — where
     * the Authorization header is neither wanted nor accepted.
     */
    public function retrieve(string $url): string
    {
        return (string) $this->fetchFile($url)->getBody();
    }

    /**
     * Stream a URL into a file.
     *
     * Reads in chunks rather than buffering: a library that cannot fetch an
     * album without holding it all in memory is not much use.
     *
     * @return int bytes written
     */
    public function download(string $url, string $path): int
    {
        $body = $this->fetchFile($url)->getBody();

        $handle = fopen($path, 'wb');

        if (false === $handle) {
            throw new YandexMusicException(sprintf('Could not open %s for writing', $path));
        }

        $written = 0;

        try {
            while (!$body->eof()) {
                $chunk = $body->read(self::CHUNK_SIZE);

                if ('' === $chunk) {
                    break;
                }

                $result = fwrite($handle, $chunk);

                if (false === $result) {
                    throw new YandexMusicException(sprintf('Writing to %s failed', $path));
                }

                $written += $result;
            }
        } finally {
            fclose($handle);
            $body->close();
        }

        return $written;
    }

    /**
     * Fetch a file, following redirects.
     *
     * PSR-18 clients do not follow redirects on their own — they hand back the
     * 3xx so the caller can decide. For the API that is what we want; for files
     * it is not, because audio is served by redirect: the URL built from a
     * download manifest points at the API host, which sends you on to whichever
     * streaming host has the file.
     */
    private function fetchFile(string $url): ResponseInterface
    {
        for ($hop = 0; $hop <= self::MAX_REDIRECTS; ++$hop) {
            $request = $this->requestFactory->createRequest('GET', $url);

            try {
                $response = $this->client->sendRequest($request);
            } catch (ClientExceptionInterface $e) {
                throw new NetworkException($e->getMessage(), null, $e);
            }

            $status = $response->getStatusCode();

            if ($status >= 200 && $status <= 299) {
                return $response;
            }

            if ($status < 300 || $status > 399 || !$response->hasHeader('Location')) {
                $this->handleErrorResponse($status, (string) $response->getBody());
            }

            $url = self::resolveLocation($url, $response->getHeaderLine('Location'));
        }

        throw new NetworkException(sprintf('Gave up after %d redirects fetching the file', self::MAX_REDIRECTS));
    }

    /**
     * Where a Location header points, given where we asked from.
     */
    private static function resolveLocation(string $from, string $location): string
    {
        if (1 === preg_match('#^https?://#i', $location)) {
            return $location;
        }

        $parts = parse_url($from);

        if (false === $parts || !isset($parts['scheme'], $parts['host'])) {
            throw new NetworkException(sprintf('Could not follow a redirect to %s', $location));
        }

        $origin = $parts['scheme'].'://'.$parts['host'].(isset($parts['port']) ? ':'.$parts['port'] : '');

        return $origin.(str_starts_with($location, '/') ? '' : '/').$location;
    }

    /**
     * @param array<string, mixed>|null $data
     */
    private function send(string $method, string $url, ?array $data = null, bool $json = false): mixed
    {
        $request = $this->requestFactory->createRequest($method, $url);

        foreach ($this->headers as $name => $value) {
            $request = $request->withHeader($name, $value);
        }

        if (null !== $data) {
            [$contentType, $body] = $json
                ? ['application/json', json_encode($data, JSON_THROW_ON_ERROR)]
                : ['application/x-www-form-urlencoded', http_build_query($data)];

            $request = $request
                ->withHeader('Content-Type', $contentType)
                ->withBody($this->streamFactory->createStream($body));
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
