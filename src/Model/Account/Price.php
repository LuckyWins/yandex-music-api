<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Model\Account;

use LuckyWins\YandexMusic\Client;
use LuckyWins\YandexMusic\Model\Model;

/**
 * An amount of money, in the smallest unit of its currency.
 */
final class Price extends Model
{
    public function __construct(
        public readonly int $amount,
        public readonly string $currency,
        public readonly ?Client $client = null,
    ) {
    }

    protected function identity(): array
    {
        return [$this->amount, $this->currency];
    }
}
