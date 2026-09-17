<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Model\Track;

use LuckyWins\YandexMusic\Client;
use LuckyWins\YandexMusic\Model\Landing\Chart;
use LuckyWins\YandexMusic\Model\Model;

/**
 * A track's place in a playlist rather than the track itself.
 *
 * Which is the point: a playlist of two hundred tracks arrives as two hundred
 * of these, each carrying when it was added and where it sits, and the whole
 * tracks are fetched separately with tracks(). When the response does include
 * the track, it lands in $track.
 */
final class TrackShort extends Model
{
    /** @var array<string, array{0: class-string<Model>, 1: 'one'|'list'}> */
    protected const NESTED = [
        'track' => [Track::class, 'one'],
        'chart' => [Chart::class, 'one'],
    ];

    public function __construct(
        public readonly string|int $id,
        public readonly ?string $timestamp = null,
        public readonly string|int|null $albumId = null,
        public readonly ?int $playCount = null,
        public readonly ?bool $recent = null,
        public readonly ?Chart $chart = null,
        public readonly ?Track $track = null,
        public readonly ?int $originalIndex = null,
        /** Where the track sits once the playlist is shuffled. */
        public readonly ?int $originalShuffleIndex = null,
        public readonly ?Client $client = null,
    ) {
    }

    /**
     * The id in the form the track endpoints want: `{id}:{albumId}` when the
     * album is known, the bare id otherwise.
     */
    public function compositeId(): string
    {
        return null === $this->albumId ? (string) $this->id : $this->id.':'.$this->albumId;
    }

    protected function identity(): array
    {
        return [$this->id, $this->albumId];
    }
}
