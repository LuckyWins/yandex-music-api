<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic;

use LuckyWins\YandexMusic\Client\Account;
use LuckyWins\YandexMusic\Client\Albums;
use LuckyWins\YandexMusic\Client\Artists;
use LuckyWins\YandexMusic\Client\Clips;
use LuckyWins\YandexMusic\Client\Concerts;
use LuckyWins\YandexMusic\Client\DeviceAuth;
use LuckyWins\YandexMusic\Client\Labels;
use LuckyWins\YandexMusic\Client\Landing;
use LuckyWins\YandexMusic\Client\Likes;
use LuckyWins\YandexMusic\Client\Metatags;
use LuckyWins\YandexMusic\Client\MusicHistory;
use LuckyWins\YandexMusic\Client\Pins;
use LuckyWins\YandexMusic\Client\Playlists;
use LuckyWins\YandexMusic\Client\Presaves;
use LuckyWins\YandexMusic\Client\Queues;
use LuckyWins\YandexMusic\Client\Radio;
use LuckyWins\YandexMusic\Client\Search;
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
 * Methods are organized into traits by domain, and every one of them returns
 * typed models. Nothing hands back raw decoded JSON any more: the Legacy trait
 * that used to hold the un-ported endpoints is gone.
 */
final class Client
{
    use Account;
    use Albums;
    use Artists;
    use Clips;
    use Concerts;
    use DeviceAuth;
    use Labels;
    use Landing;
    use Likes;
    use Metatags;
    use MusicHistory;
    use Pins;
    use Playlists;
    use Presaves;
    use Queues;
    use Radio;
    use Search;
    use Tracks;

    public const BASE_URL = 'https://api.music.yandex.net';

    /**
     * How this library describes the machine it runs on.
     *
     * The queue endpoints want a device, because a queue belongs to one. The
     * identifiers stay the literal `random` the reference library sends:
     * nothing is known to depend on them, and inventing plausible-looking ones
     * would be worse than saying nothing. Pass your own to the constructor if
     * you have something real to say.
     */
    public const DEVICE = 'os=PHP; os_version=; manufacturer=LuckyWins; '
        .'model=Yandex Music API; clid=; device_id=random; uuid=random';

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
        private readonly string $device = self::DEVICE,
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

    /**
     * How this client describes the device it runs on, for the endpoints that
     * ask — the queues.
     */
    public function getDevice(): string
    {
        return $this->device;
    }

    public function reportsUnknownFields(): bool
    {
        return $this->reportUnknownFields;
    }

    /**
     * Called by models when the API sends a field none of them declare.
     *
     * The names go into `fields` and the types of what arrived in them into
     * `types`. Never the values: a response about this account carries its
     * owner's data, and a log is the wrong place for it.
     *
     * @param class-string          $model
     * @param list<string>          $fields
     * @param array<string, string> $types  field name to type, as get_debug_type() names it
     */
    public function reportUnknownFields(string $model, array $fields, array $types = []): void
    {
        $this->logger?->warning(
            'Yandex.Music API returned fields this library does not know about. '
            .'This usually means the API changed and the model needs updating.',
            ['model' => $model, 'fields' => $fields, 'types' => $types],
        );
    }
}
