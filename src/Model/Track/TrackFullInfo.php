<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Model\Track;

use LuckyWins\YandexMusic\Client;
use LuckyWins\YandexMusic\Model\Artist\Artist;
use LuckyWins\YandexMusic\Model\Model;

/**
 * A track with everything the service knows about it gathered in one place.
 */
final class TrackFullInfo extends Model
{
    /** @var array<string, array{0: class-string<Model>, 1: 'one'|'list'}> */
    protected const NESTED = [
        'track' => [Track::class, 'one'],
        'similarTracks' => [Track::class, 'list'],
        'alsoInAlbums' => [Track::class, 'list'],
        'artists' => [Artist::class, 'list'],
    ];

    public function __construct(
        public readonly ?Track $track = null,
        /** @var list<Track> */
        public readonly array $similarTracks = [],
        /** @var list<Track> The same recording as it appears on other albums. */
        public readonly array $alsoInAlbums = [],
        /** @var list<string> */
        public readonly array $aliases = [],
        /** @var list<Artist> Fuller than the artists on the track itself. */
        public readonly array $artists = [],
        public readonly ?Client $client = null,
    ) {
    }

    protected function identity(): array
    {
        return [$this->track];
    }
}
