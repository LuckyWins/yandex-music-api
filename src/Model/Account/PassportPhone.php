<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Model\Account;

use LuckyWins\YandexMusic\Client;
use LuckyWins\YandexMusic\Model\Model;

/**
 * A phone number attached to the Yandex ID behind the account.
 */
final class PassportPhone extends Model
{
    public function __construct(
        public readonly string $phone,
        public readonly ?Client $client = null,
    ) {
    }

    protected function identity(): array
    {
        return [$this->phone];
    }
}
