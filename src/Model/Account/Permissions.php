<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Model\Account;

use LuckyWins\YandexMusic\Client;
use LuckyWins\YandexMusic\Model\Model;

/**
 * What the account is allowed to do, and until when.
 *
 * `values` is what currently applies; `default` is what it falls back to when
 * the subscription lapses.
 */
final class Permissions extends Model
{
    public function __construct(
        public readonly string $until,
        /** @var list<string> */
        public readonly array $values,
        /** @var list<string> */
        public readonly array $default,
        public readonly ?Client $client = null,
    ) {
    }

    protected function identity(): array
    {
        return [$this->until, $this->values, $this->default];
    }
}
