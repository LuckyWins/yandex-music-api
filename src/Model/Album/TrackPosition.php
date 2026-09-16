<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Model\Album;

use LuckyWins\YandexMusic\Client;
use LuckyWins\YandexMusic\Model\Model;

/**
 * Where a track sits on an album: which disc, and which slot on it.
 */
final class TrackPosition extends Model
{
    public function __construct(
        public readonly int $volume,
        public readonly int $index,
        public readonly ?Client $client = null,
    ) {
    }

    protected function identity(): array
    {
        return [$this->volume, $this->index];
    }
}
