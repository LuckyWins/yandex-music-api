<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Model\Artist;

use LuckyWins\YandexMusic\Client;
use LuckyWins\YandexMusic\Model\Model;

/**
 * How much of an artist there is to listen to.
 */
final class Counts extends Model
{
    public function __construct(
        public readonly int $tracks,
        public readonly int $directAlbums,
        public readonly int $alsoAlbums,
        public readonly int $alsoTracks,
        public readonly ?Client $client = null,
    ) {
    }

    protected function identity(): array
    {
        return [$this->tracks, $this->directAlbums, $this->alsoAlbums, $this->alsoTracks];
    }
}
