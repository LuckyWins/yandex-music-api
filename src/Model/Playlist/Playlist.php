<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Model\Playlist;

use LuckyWins\YandexMusic\Client;
use LuckyWins\YandexMusic\Model\Artist\Artist;
use LuckyWins\YandexMusic\Model\Cover;
use LuckyWins\YandexMusic\Model\CoverDerivedColors;
use LuckyWins\YandexMusic\Model\CustomWave;
use LuckyWins\YandexMusic\Model\Landing\TrackId;
use LuckyWins\YandexMusic\Model\Model;
use LuckyWins\YandexMusic\Model\Pager;
use LuckyWins\YandexMusic\Model\Track\TrackShort;

/**
 * A playlist.
 *
 * Every field is optional, because the same class covers everything from a
 * two-field stub in a landing block to the full answer of /playlist/{uuid}.
 * A playlist made by a person carries an owner and little else; one generated
 * by the service carries $madeFor, $generatedPlaylistType and a play counter;
 * a sponsored one carries $branding.
 *
 * $tracks holds positions rather than whole tracks — see TrackShort.
 */
final class Playlist extends Model
{
    /** @var array<string, array{0: class-string<Model>, 1: 'one'|'list'}> */
    protected const NESTED = [
        'owner' => [User::class, 'one'],
        'cover' => [Cover::class, 'one'],
        'coverWithoutText' => [Cover::class, 'one'],
        'dummyCover' => [Cover::class, 'one'],
        'dummyRolloverCover' => [Cover::class, 'one'],
        'madeFor' => [MadeFor::class, 'one'],
        'madeForUser' => [MadeForUser::class, 'one'],
        'derivedColors' => [CoverDerivedColors::class, 'one'],
        'playCounter' => [PlayCounter::class, 'one'],
        'playlistAbsence' => [PlaylistAbsence::class, 'one'],
        'contest' => [Contest::class, 'one'],
        'ogData' => [OpenGraphData::class, 'one'],
        'branding' => [Brand::class, 'one'],
        'customWave' => [CustomWave::class, 'one'],
        'pager' => [Pager::class, 'one'],
        'trailer' => [PlaylistAvailability::class, 'one'],
        'topArtist' => [Artist::class, 'list'],
        'recentTracks' => [TrackId::class, 'list'],
        'tracks' => [TrackShort::class, 'list'],
        'similarPlaylists' => [self::class, 'list'],
        'lastOwnerPlaylists' => [self::class, 'list'],
    ];

    public function __construct(
        public readonly ?int $uid = null,
        public readonly ?int $kind = null,
        public readonly ?string $title = null,
        public readonly ?User $owner = null,
        public readonly ?Cover $cover = null,
        public readonly ?int $trackCount = null,
        /** @var list<TrackShort> */
        public readonly array $tracks = [],
        public readonly ?int $revision = null,
        public readonly ?int $snapshot = null,
        public readonly ?string $visibility = null,
        public readonly ?bool $collective = null,
        public readonly ?string $urlPart = null,
        public readonly ?string $created = null,
        public readonly ?string $modified = null,
        public readonly ?bool $available = null,
        public readonly ?bool $isBanner = null,
        public readonly ?bool $isPremiere = null,
        public readonly ?int $durationMs = null,
        public readonly ?int $likesCount = null,
        public readonly ?string $description = null,
        public readonly ?string $descriptionFormatted = null,
        public readonly ?string $playlistUuid = null,
        public readonly ?string $type = null,
        public readonly ?bool $ready = null,
        public readonly ?bool $everPlayed = null,
        public readonly ?string $generatedPlaylistType = null,
        public readonly ?MadeFor $madeFor = null,
        public readonly ?MadeForUser $madeForUser = null,
        public readonly ?CoverDerivedColors $derivedColors = null,
        public readonly ?PlayCounter $playCounter = null,
        public readonly ?PlaylistAbsence $playlistAbsence = null,
        public readonly ?Contest $contest = null,
        public readonly ?Brand $branding = null,
        public readonly ?OpenGraphData $ogData = null,
        public readonly ?string $ogImage = null,
        public readonly ?string $ogTitle = null,
        public readonly ?string $ogDescription = null,
        public readonly ?string $image = null,
        public readonly ?Cover $coverWithoutText = null,
        public readonly ?string $animatedCoverUri = null,
        public readonly ?string $backgroundColor = null,
        public readonly ?string $textColor = null,
        public readonly ?string $backgroundImageUrl = null,
        public readonly ?string $backgroundVideoUrl = null,
        public readonly ?string $backgroundVideoId = null,
        public readonly ?string $idForFrom = null,
        public readonly ?string $dummyDescription = null,
        public readonly ?string $dummyPageDescription = null,
        public readonly ?Cover $dummyCover = null,
        public readonly ?Cover $dummyRolloverCover = null,
        public readonly ?int $metrikaId = null,
        /** @var list<int> */
        public readonly array $coauthors = [],
        /** @var list<Artist> */
        public readonly array $topArtist = [],
        /** @var list<TrackId> */
        public readonly array $recentTracks = [],
        /** @var list<self> */
        public readonly array $similarPlaylists = [],
        /** @var list<self> */
        public readonly array $lastOwnerPlaylists = [],
        public readonly ?CustomWave $customWave = null,
        public readonly ?Pager $pager = null,
        public readonly ?bool $hasTrailer = null,
        public readonly ?PlaylistAvailability $trailer = null,
        /** @var list<mixed> Not modelled: shapes vary and the reference leaves them raw too. */
        public readonly array $tags = [],
        /** @var list<mixed> Not modelled: advertising inserts, left raw as in the reference. */
        public readonly array $prerolls = [],
        /** @var list<mixed> Not modelled: only ever seen empty. */
        public readonly array $regions = [],
        /** Not modelled: the reference types it as Any, and live responses vary. */
        public readonly mixed $isForFrom = null,
        public readonly ?Client $client = null,
    ) {
    }

    /**
     * The identifier the mutating endpoints want: owner plus kind.
     *
     * A playlist's kind is unique per user, not globally, so neither half
     * identifies it alone.
     */
    public function ownerKind(): ?string
    {
        if (null === $this->uid || null === $this->kind) {
            return null;
        }

        return $this->uid.':'.$this->kind;
    }

    /**
     * Cover art at a given size, or null when the playlist has no cover.
     *
     * The API stores the placeholder `%%` where the size belongs.
     */
    public function coverUrl(string $size = '200x200'): ?string
    {
        $uri = $this->cover?->uri;

        return null === $uri ? null : 'https://'.str_replace('%%', $size, $uri);
    }

    protected function identity(): array
    {
        return [$this->uid, $this->kind, $this->playlistUuid];
    }
}
