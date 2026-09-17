<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Model\MusicHistory;

use LuckyWins\YandexMusic\Client;
use LuckyWins\YandexMusic\Model\Album\Album;
use LuckyWins\YandexMusic\Model\Artist\Artist;
use LuckyWins\YandexMusic\Model\Model;
use LuckyWins\YandexMusic\Model\Playlist\Playlist;
use LuckyWins\YandexMusic\Model\Wave\Wave;

/**
 * Whatever was being listened to, filled in.
 *
 * Which of the four is present depends on what the entry is: an album, an
 * artist, a playlist or a station.
 */
final class MusicHistoryContextFullModel extends Model
{
    /** @var array<string, array{0: class-string<Model>, 1: 'one'|'list'}> */
    protected const NESTED = [
        'album' => [Album::class, 'one'],
        'artist' => [Artist::class, 'one'],
        'playlist' => [Playlist::class, 'one'],
        'wave' => [Wave::class, 'one'],
        'artists' => [Artist::class, 'list'],
    ];

    public function __construct(
        public readonly ?Album $album = null,
        public readonly ?Artist $artist = null,
        public readonly ?Playlist $playlist = null,
        public readonly ?Wave $wave = null,
        /** @var list<Artist> */
        public readonly array $artists = [],
        public readonly ?bool $available = null,
        public readonly ?int $tracksCount = null,
        public readonly ?string $simpleWaveForegroundImageUrl = null,
        public readonly ?string $simpleWaveBackgroundColor = null,
        public readonly ?Client $client = null,
    ) {
    }

    /**
     * Whichever of the four this is.
     */
    public function subject(): Album|Artist|Playlist|Wave|null
    {
        return $this->album ?? $this->artist ?? $this->playlist ?? $this->wave;
    }

    protected function identity(): array
    {
        return [$this->subject()];
    }
}
