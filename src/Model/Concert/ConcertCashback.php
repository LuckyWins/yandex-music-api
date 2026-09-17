<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Model\Concert;

use LuckyWins\YandexMusic\Client;
use LuckyWins\YandexMusic\Model\Model;

/**
 * What part of the ticket price comes back as points.
 */
final class ConcertCashback extends Model
{
    public function __construct(
        public readonly ?string $title = null,
        public readonly ?int $valuePercent = null,
        public readonly ?Client $client = null,
    ) {
    }

    protected function identity(): array
    {
        return [$this->title, $this->valuePercent];
    }
}
