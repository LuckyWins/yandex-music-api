<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Model\DeviceAuth;

use LuckyWins\YandexMusic\Client;
use LuckyWins\YandexMusic\Model\Model;

/**
 * The first step of the OAuth device flow.
 *
 * Carries the code to show the user, where to enter it, and how the caller
 * should pace its polling while waiting for confirmation.
 */
final class DeviceCode extends Model
{
    public function __construct(
        /** Opaque code identifying this authorization attempt, sent back when polling. */
        public readonly string $deviceCode,
        /** Short code the user types into the verification page. */
        public readonly string $userCode,
        /** Page the user opens to confirm. */
        public readonly string $verificationUrl,
        /** Seconds until this code stops being accepted. */
        public readonly int $expiresIn,
        /** Seconds to wait between polls. */
        public readonly int $interval,
        public readonly ?Client $client = null,
    ) {
    }

    protected function identity(): array
    {
        return [$this->deviceCode, $this->userCode];
    }
}
