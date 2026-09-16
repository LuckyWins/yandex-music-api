<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Model\Account;

use LuckyWins\YandexMusic\Client;
use LuckyWins\YandexMusic\Model\Model;

/**
 * How to cancel a subscription bought through a mobile operator.
 *
 * Known `method` value: `ussd`.
 */
final class Deactivation extends Model
{
    public function __construct(
        public readonly string $method,
        public readonly ?string $instructions = null,
        public readonly ?Client $client = null,
    ) {
    }

    protected function identity(): array
    {
        return [$this->method, $this->instructions];
    }
}
