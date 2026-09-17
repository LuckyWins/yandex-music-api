<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Model\Feed;

use LuckyWins\YandexMusic\Client;
use LuckyWins\YandexMusic\Model\Album\Album;
use LuckyWins\YandexMusic\Model\Model;
use LuckyWins\YandexMusic\Model\Track\Track;

/**
 * An album the feed has something to say about, with the tracks it suggests
 * from it.
 */
final class AlbumEvent extends Model
{
    /** @var array<string, array{0: class-string<Model>, 1: 'one'|'list'}> */
    protected const NESTED = [
        'album' => [Album::class, 'one'],
        'tracks' => [Track::class, 'list'],
    ];

    public function __construct(
        public readonly ?Album $album = null,
        /** @var list<Track> */
        public readonly array $tracks = [],
        public readonly ?Client $client = null,
    ) {
    }

    protected function identity(): array
    {
        return [$this->album];
    }
}
