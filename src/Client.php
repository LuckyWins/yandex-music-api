<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic;

use LuckyWins\YandexMusic\Client\Account;
use LuckyWins\YandexMusic\Client\DeviceAuth;
use LuckyWins\YandexMusic\Client\Legacy;
use LuckyWins\YandexMusic\Client\Tracks;
use LuckyWins\YandexMusic\Http\Request;
use Psr\Log\LoggerInterface;

/**
 * The entry point. Everything starts here.
 *
 * Without a token the API still answers, but only with what an anonymous
 * visitor may see — thirty-second previews rather than whole tracks. To get a
 * token, run the device flow: see deviceAuth().
 *
 * Methods are organized into traits by domain. DeviceAuth is ported to typed
 * models; Legacy holds everything still returning raw decoded data, and shrinks
 * as domains are converted.
 */
final class Client
{
    use Account;
    use DeviceAuth;
    use Legacy;
    use Tracks;

    public const BASE_URL = 'https://api.music.yandex.net';

    public readonly Request $request;

    private readonly Clock $clock;

    private ?string $token;

    /**
     * @param string|null  $token              an OAuth token, if you already have one
     * @param Request|null $request            supply one to control the HTTP client or its timeout
     * @param string       $language           one of en, uz, uk, us, ru, kk, hy
     * @param bool         $reportUnknownFields log fields the API sends that no model declares —
     *                                          how a change on Yandex's side gets noticed
     */
    public function __construct(
        ?string $token = null,
        ?Request $request = null,
        private readonly string $language = 'ru',
        private readonly bool $reportUnknownFields = false,
        private readonly ?LoggerInterface $logger = null,
        ?Clock $clock = null,
        private readonly string $baseUrl = self::BASE_URL,
    ) {
        $this->request = $request ?? new Request();
        $this->clock = $clock ?? new SystemClock();
        $this->token = $token;

        $this->request->setLanguage($this->language);

        if (null !== $token) {
            $this->request->setAuthorization($token);
        }
    }

    public function getToken(): ?string
    {
        return $this->token;
    }

    /**
     * Apply a token to this client. Called for you when deviceAuth() succeeds.
     */
    public function setToken(string $token): void
    {
        $this->token = $token;
        $this->request->setAuthorization($token);
    }

    /**
     * Drop the token, leaving this client unauthorized.
     *
     * Called for you when you revoke this client's own token: holding on to a
     * credential that no longer works only produces confusing failures later.
     */
    public function forgetToken(): void
    {
        $this->token = null;
        $this->request->clearAuthorization();
    }

    /**
     * The language the API is asked to answer in, ISO 639-1.
     */
    public function getLanguage(): string
    {
        return $this->language;
    }

    public function getBaseUrl(): string
    {
        return $this->baseUrl;
    }

    public function reportsUnknownFields(): bool
    {
        return $this->reportUnknownFields;
    }

    /**
     * Called by models when the API sends a field none of them declare.
     *
     * @param class-string $model
     * @param list<string> $fields
     */
    public function reportUnknownFields(string $model, array $fields): void
    {
        $this->logger?->warning(
            'Yandex.Music API returned fields this library does not know about. '
            .'This usually means the API changed and the model needs updating.',
            ['model' => $model, 'fields' => $fields],
        );
    }
}
