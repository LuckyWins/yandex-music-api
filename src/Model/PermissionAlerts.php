<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Model;

use LuckyWins\YandexMusic\Client;

/**
 * Warnings the service wants shown to the account.
 */
final class PermissionAlerts extends Model
{
    public function __construct(
        /** @var list<string> */
        public readonly array $alerts = [],
        public readonly ?Client $client = null,
    ) {
    }

    protected function identity(): array
    {
        return [$this->alerts];
    }
}
