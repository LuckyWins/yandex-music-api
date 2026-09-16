<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Model\Artist;

use LuckyWins\YandexMusic\Client;
use LuckyWins\YandexMusic\Model\Model;

/**
 * How many people listened to an artist lately, and whether that is rising.
 */
final class Stats extends Model
{
    public function __construct(
        public readonly int $lastMonthListeners,
        public readonly int $lastMonthListenersDelta,
        public readonly ?Client $client = null,
    ) {
    }

    protected function identity(): array
    {
        return [$this->lastMonthListeners, $this->lastMonthListenersDelta];
    }
}
