<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Model\Artist;

use LuckyWins\YandexMusic\Client;
use LuckyWins\YandexMusic\Model\Cover;
use LuckyWins\YandexMusic\Model\Model;

/**
 * An artist with the numbers around them, without the albums and tracks that
 * make brief-info heavy.
 */
final class ArtistInfo extends Model
{
    /** @var array<string, array{0: class-string<Model>, 1: 'one'|'list'}> */
    protected const NESTED = [
        'artist' => [Artist::class, 'one'],
        'stats' => [Stats::class, 'one'],
        'trailer' => [ArtistTrailerStatus::class, 'one'],
        'covers' => [Cover::class, 'list'],
    ];

    public function __construct(
        public readonly ?Artist $artist = null,
        public readonly ?int $likesCount = null,
        public readonly ?Stats $stats = null,
        public readonly ?ArtistTrailerStatus $trailer = null,
        /** @var list<Cover> */
        public readonly array $covers = [],
        public readonly ?string $description = null,
        public readonly ?string $artistType = null,
        public readonly ?Client $client = null,
    ) {
    }

    protected function identity(): array
    {
        return [$this->artist];
    }
}
