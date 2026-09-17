<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Model\Concert;

use LuckyWins\YandexMusic\Client;
use LuckyWins\YandexMusic\Model\Model;

/**
 * The cheapest ticket, in whatever currency the venue sells them.
 */
final class ConcertMinPrice extends Model
{
    public function __construct(
        public readonly ?int $value = null,
        public readonly ?string $currency = null,
        public readonly ?string $currencySymbol = null,
        public readonly ?Client $client = null,
    ) {
    }

    protected function identity(): array
    {
        return [$this->value, $this->currency];
    }
}
