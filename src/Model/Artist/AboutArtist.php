<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Model\Artist;

use LuckyWins\YandexMusic\Client;
use LuckyWins\YandexMusic\Model\Cover;
use LuckyWins\YandexMusic\Model\Model;

/**
 * The artist's own page: who they are, what is written about them, and where
 * else to find them.
 */
final class AboutArtist extends Model
{
    /** @var array<string, array{0: class-string<Model>, 1: 'one'|'list'}> */
    protected const NESTED = [
        'artist' => [Artist::class, 'one'],
        'stats' => [Stats::class, 'one'],
        'links' => [ArtistLink::class, 'list'],
        'covers' => [Cover::class, 'list'],
    ];

    public function __construct(
        public readonly ?Artist $artist = null,
        public readonly ?Stats $stats = null,
        public readonly ?string $description = null,
        /** @var list<ArtistLink> */
        public readonly array $links = [],
        /** @var list<Cover> */
        public readonly array $covers = [],
        public readonly ?string $artistType = null,
        public readonly ?Client $client = null,
    ) {
    }

    protected function identity(): array
    {
        return [$this->artist];
    }
}
