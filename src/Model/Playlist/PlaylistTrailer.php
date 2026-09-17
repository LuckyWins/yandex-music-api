<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Model\Playlist;

use LuckyWins\YandexMusic\Client;
use LuckyWins\YandexMusic\Model\Model;
use LuckyWins\YandexMusic\Model\TrailerInfo;

/**
 * A playlist's trailer: the playlist itself, the tracks the trailer plays,
 * and whether it may be shared.
 */
final class PlaylistTrailer extends Model
{
    /** @var array<string, array{0: class-string<Model>, 1: 'one'|'list'}> */
    protected const NESTED = [
        'playlist' => [Playlist::class, 'one'],
        'trailer' => [TrailerInfo::class, 'one'],
    ];

    public function __construct(
        public readonly ?Playlist $playlist = null,
        public readonly ?TrailerInfo $trailer = null,
        public readonly ?bool $shareable = null,
        public readonly ?Client $client = null,
    ) {
    }

    protected function identity(): array
    {
        return [$this->playlist, $this->trailer];
    }
}
