<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Model\Account;

use LuckyWins\YandexMusic\Client;
use LuckyWins\YandexMusic\Model\Model;

/**
 * The window of a subscription that will not renew itself.
 */
final class NonAutoRenewable extends Model
{
    public function __construct(
        public readonly string $start,
        public readonly string $end,
        public readonly ?Client $client = null,
    ) {
    }

    protected function identity(): array
    {
        return [$this->start, $this->end];
    }
}
