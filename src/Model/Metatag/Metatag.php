<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Model\Metatag;

use LuckyWins\YandexMusic\Client;
use LuckyWins\YandexMusic\Model\Album\Album;
use LuckyWins\YandexMusic\Model\Artist\Artist;
use LuckyWins\YandexMusic\Model\Model;
use LuckyWins\YandexMusic\Model\Playlist\Playlist;

/**
 * A tag's page: a bit of everything filed under it, and a station to play it.
 *
 * How much of each arrives is asked for per request — see metatag().
 */
final class Metatag extends Model
{
    /** @var array<string, array{0: class-string<Model>, 1: 'one'|'list'}> */
    protected const NESTED = [
        'title' => [MetatagTitle::class, 'one'],
        'artists' => [Artist::class, 'list'],
        'albums' => [Album::class, 'list'],
        'playlists' => [Playlist::class, 'list'],
        'tracksSortByValues' => [MetatagSortByValue::class, 'list'],
        'albumsSortByValues' => [MetatagSortByValue::class, 'list'],
        'playlistsSortByValues' => [MetatagSortByValue::class, 'list'],
    ];

    public function __construct(
        public readonly ?string $id = null,
        public readonly ?MetatagTitle $title = null,
        public readonly ?string $coverUri = null,
        public readonly ?string $color = null,
        public readonly ?bool $liked = null,
        public readonly ?string $stationId = null,
        public readonly ?string $customWaveAnimationUrl = null,
        /** @var list<Artist> */
        public readonly array $artists = [],
        /** @var list<Album> */
        public readonly array $albums = [],
        /** @var list<Playlist> */
        public readonly array $playlists = [],
        /** @var list<MetatagSortByValue> */
        public readonly array $tracksSortByValues = [],
        /** @var list<MetatagSortByValue> */
        public readonly array $albumsSortByValues = [],
        /** @var list<MetatagSortByValue> */
        public readonly array $playlistsSortByValues = [],
        /**
         * The four below are sent and have never been seen holding anything.
         * The reference library says the same in its own docstring and does
         * not model them either; they are kept raw rather than typed on a
         * guess, and will be typed when a response says what belongs in them.
         *
         * @var list<mixed>
         */
        public readonly array $tracks = [],
        /** @var list<mixed> */
        public readonly array $composers = [],
        /** @var list<mixed> */
        public readonly array $promotions = [],
        /** @var list<mixed> */
        public readonly array $features = [],
        public readonly ?Client $client = null,
    ) {
    }

    protected function identity(): array
    {
        return [$this->id];
    }
}
