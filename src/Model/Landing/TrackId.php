<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Model\Landing;

use LuckyWins\YandexMusic\Client;
use LuckyWins\YandexMusic\Model\Model;

/**
 * A reference to a track rather than the track itself.
 */
final class TrackId extends Model
{
    public function __construct(
        public readonly ?int $id = null,
        public readonly ?int $trackId = null,
        public readonly ?int $albumId = null,
        public readonly ?string $from = null,
        public readonly ?Client $client = null,
    ) {
    }

    protected function identity(): array
    {
        return [$this->id, $this->trackId, $this->albumId];
    }
}
