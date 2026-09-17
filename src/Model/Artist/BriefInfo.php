<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Model\Artist;

use LuckyWins\YandexMusic\Model\Album\Album;
use LuckyWins\YandexMusic\Model\Clip\Clip;
use LuckyWins\YandexMusic\Model\Cover;
use LuckyWins\YandexMusic\Model\CustomWave;
use LuckyWins\YandexMusic\Model\Landing\Chart;
use LuckyWins\YandexMusic\Model\Model;
use LuckyWins\YandexMusic\Model\Playlist\Playlist;
use LuckyWins\YandexMusic\Model\Playlist\PlaylistId;
use LuckyWins\YandexMusic\Model\Supplement\VideoSupplement;
use LuckyWins\YandexMusic\Model\Track\Track;

/**
 * Everything the service will say about an artist in one response.
 *
 * Partly typed on purpose. The fields left as raw arrays — playlists,
 * concerts, clips, vinyls — belong to domains this library has not ported yet,
 * and modelling them here would drag those domains in ahead of their turn. They
 * are kept rather than dropped, so nothing is lost while waiting.
 */
final class BriefInfo extends Model
{
    /** @var array<string, array{0: class-string<Model>, 1: 'one'|'list'}> */
    protected const NESTED = [
        'artist' => [Artist::class, 'one'],
        'albums' => [Album::class, 'list'],
        'alsoAlbums' => [Album::class, 'list'],
        'lastReleases' => [Album::class, 'list'],
        'popularTracks' => [Track::class, 'list'],
        'similarArtists' => [Artist::class, 'list'],
        'allCovers' => [Cover::class, 'list'],
        'videos' => [VideoSupplement::class, 'list'],
        'tracksInChart' => [Chart::class, 'list'],
        'stats' => [Stats::class, 'one'],
        'customWave' => [CustomWave::class, 'one'],
        'playlists' => [Playlist::class, 'list'],
        'playlistIds' => [PlaylistId::class, 'list'],
        'clips' => [Clip::class, 'list'],
        'vinyls' => [Vinyl::class, 'list'],
    ];

    public function __construct(
        public readonly ?Artist $artist = null,
        /** @var list<Album> */
        public readonly array $albums = [],
        /** @var list<Album> Albums the artist appears on without being its author. */
        public readonly array $alsoAlbums = [],
        /** @var list<Album> */
        public readonly array $lastReleases = [],
        /** @var list<Track> */
        public readonly array $popularTracks = [],
        /** @var list<Artist> */
        public readonly array $similarArtists = [],
        /** @var list<Cover> */
        public readonly array $allCovers = [],
        /** @var list<VideoSupplement> */
        public readonly array $videos = [],
        /** @var list<Chart> Where the artist's tracks currently sit in the charts. */
        public readonly array $tracksInChart = [],
        public readonly ?Stats $stats = null,
        public readonly ?CustomWave $customWave = null,
        public readonly ?bool $hasPromotions = null,
        public readonly ?bool $hasTrailer = null,
        /** @var list<int> */
        public readonly array $lastReleaseIds = [],
        /** @var list<Playlist> */
        public readonly array $playlists = [],
        /** @var list<PlaylistId> */
        public readonly array $playlistIds = [],
        /** @var list<mixed> Not modelled: the concerts domain is not ported. */
        public readonly array $concerts = [],
        /** @var list<Clip> */
        public readonly array $clips = [],
        /** @var list<Vinyl> */
        public readonly array $vinyls = [],
        /**
         * Promotional links — a different shape from the artist's own `links`,
         * despite the name: these carry a subtitle and an image.
         *
         * @var list<mixed>
         */
        public readonly array $links = [],
        /** @var array<string, mixed>|null */
        public readonly ?array $bandlinkScannerLink = null,
        /** @var list<mixed> */
        public readonly array $extraActions = [],
    ) {
    }

    protected function identity(): array
    {
        return [$this->artist];
    }
}
