<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Model\DeviceAuth;

use LuckyWins\YandexMusic\Client;
use LuckyWins\YandexMusic\Model\Model;

/**
 * The token returned once the user has confirmed the device code.
 *
 * Storing and refreshing it is the caller's responsibility: this library does
 * neither, and `refreshToken` is carried only so that callers who want to can.
 */
final class OAuthToken extends Model
{
    public function __construct(
        public readonly string $accessToken,
        public readonly ?string $refreshToken = null,
        /** Seconds until the access token expires — typically about a year. */
        public readonly ?int $expiresIn = null,
        /** Usually `bearer`, though Yandex expects the `OAuth` scheme in requests. */
        public readonly ?string $tokenType = null,
        public readonly ?Client $client = null,
    ) {
    }

    protected function identity(): array
    {
        return [$this->accessToken];
    }
}
