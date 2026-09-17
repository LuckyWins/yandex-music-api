<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Model;

use LuckyWins\YandexMusic\Client;
use LuckyWins\YandexMusic\Model\Track\TrackShort;

/**
 * A user's library of liked or disliked tracks.
 *
 * The tracks are positions rather than whole tracks — fetch what you want with
 * tracks(). $revision is what makes the library cheap to poll: pass it back as
 * `ifModifiedSinceRevision` and nothing comes over the wire unless it changed.
 */
final class TracksList extends Model
{
    /** @var array<string, array{0: class-string<Model>, 1: 'one'|'list'}> */
    protected const NESTED = [
        'tracks' => [TrackShort::class, 'list'],
    ];

    public function __construct(
        public readonly ?int $uid = null,
        public readonly ?int $revision = null,
        /** The library is itself a playlist; this is its uuid. */
        public readonly ?string $playlistUuid = null,
        /** @var list<TrackShort> */
        public readonly array $tracks = [],
        public readonly ?Client $client = null,
    ) {
    }

    /**
     * The track ids in the form the track endpoints want.
     *
     * @return list<string>
     */
    public function compositeIds(): array
    {
        return array_map(static fn (TrackShort $track): string => $track->compositeId(), $this->tracks);
    }

    protected function identity(): array
    {
        return [$this->uid, $this->revision];
    }
}
