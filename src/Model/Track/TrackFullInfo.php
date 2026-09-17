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
        /**
         * Other cuts of the same track — remixes, live takes and the like.
         *
         * One of the few fields here left as a raw array, because nothing has
         * ever arrived in it: sixty-four tracks across the chart and four
         * searches all sent it empty, and json_decode cannot tell an empty
         * list from an empty object. Newer than the reference library, which
         * does not have the field at all. Give it a model the day a non-empty
         * one turns up; until then a guess would only be a guess with a type
         * on it.
         *
         * @var array<array-key, mixed>|null
         */
        public readonly ?array $otherVersions = null,
        public readonly ?Client $client = null,
    ) {
    }

    protected function identity(): array
    {
        return [$this->track];
    }
}
