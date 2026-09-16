<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Model\Artist;

use LuckyWins\YandexMusic\Client;
use LuckyWins\YandexMusic\Model\Model;

/**
 * Who else sounds like a given artist.
 */
final class SimilarArtists extends Model
{
    /** @var array<string, array{0: class-string<Model>, 1: 'one'|'list'}> */
    protected const NESTED = [
        'artist' => [Artist::class, 'one'],
        'similarArtists' => [Artist::class, 'list'],
    ];

    public function __construct(
        public readonly ?Artist $artist = null,
        /** @var list<Artist> */
        public readonly array $similarArtists = [],
        public readonly ?Client $client = null,
    ) {
    }

    protected function identity(): array
    {
        return [$this->artist, $this->similarArtists];
    }
}
