<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Model\Account;

use LuckyWins\YandexMusic\Client;
use LuckyWins\YandexMusic\Model\Model;

/**
 * How much of a subscription is left.
 *
 * Present only when auto-renewal is switched off.
 */
final class RenewableRemainder extends Model
{
    public function __construct(
        public readonly int $days,
        public readonly ?Client $client = null,
    ) {
    }

    protected function identity(): array
    {
        return [$this->days];
    }
}
