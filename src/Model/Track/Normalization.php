<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Model\Track;

use LuckyWins\YandexMusic\Client;
use LuckyWins\YandexMusic\Model\Model;

/**
 * Replay-gain figures for a track.
 */
final class Normalization extends Model
{
    public function __construct(
        public readonly float $gain,
        public readonly int $peak,
        public readonly ?Client $client = null,
    ) {
    }

    protected function identity(): array
    {
        return [$this->gain, $this->peak];
    }
}
