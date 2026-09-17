<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Model\Feed;

use LuckyWins\YandexMusic\Client;
use LuckyWins\YandexMusic\Model\Artist\Artist;
use LuckyWins\YandexMusic\Model\Model;
use LuckyWins\YandexMusic\Model\Track\Track;

/**
 * An artist the feed is recommending, why, and what to play.
 */
final class ArtistEvent extends Model
{
    /** @var array<string, array{0: class-string<Model>, 1: 'one'|'list'}> */
    protected const NESTED = [
        'artist' => [Artist::class, 'one'],
        'tracks' => [Track::class, 'list'],
        'similarToArtistsFromHistory' => [Artist::class, 'list'],
    ];

    public function __construct(
        public readonly ?Artist $artist = null,
        /** @var list<Track> */
        public readonly array $tracks = [],
        /** @var list<Artist> the artists in your history this one resembles */
        public readonly array $similarToArtistsFromHistory = [],
        public readonly ?bool $subscribed = null,
        public readonly ?Client $client = null,
    ) {
    }

    protected function identity(): array
    {
        return [$this->artist];
    }
}
