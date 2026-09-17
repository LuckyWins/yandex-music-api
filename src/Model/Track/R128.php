<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Model\Track;

use LuckyWins\YandexMusic\Client;
use LuckyWins\YandexMusic\Model\Model;

/**
 * Loudness measured to the EBU R 128 standard, for playing tracks at an even
 * volume: `i` is integrated loudness, `tp` the true peak.
 */
final class R128 extends Model
{
    public function __construct(
        public readonly float $i,
        public readonly float $tp,
        public readonly ?Client $client = null,
    ) {
    }

    protected function identity(): array
    {
        return [$this->i, $this->tp];
    }
}
