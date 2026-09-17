<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Model\Artist;

use LuckyWins\YandexMusic\Client;
use LuckyWins\YandexMusic\Model\Model;

/**
 * An artist's position in the charts.
 */
final class Ratings extends Model
{
    public function __construct(
        public readonly int $month,
        public readonly ?int $week = null,
        public readonly ?int $day = null,
        public readonly ?Client $client = null,
    ) {
    }

    protected function identity(): array
    {
        return [$this->week, $this->month];
    }
}
