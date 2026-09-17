<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Model\Artist;

use LuckyWins\YandexMusic\Client;
use LuckyWins\YandexMusic\Model\Model;
use LuckyWins\YandexMusic\Model\TrailerInfo;

/**
 * An artist's trailer and the tracks it plays.
 */
final class ArtistTrailer extends Model
{
    /** @var array<string, array{0: class-string<Model>, 1: 'one'|'list'}> */
    protected const NESTED = [
        'artist' => [Artist::class, 'one'],
        'trailer' => [TrailerInfo::class, 'one'],
    ];

    public function __construct(
        public readonly ?Artist $artist = null,
        public readonly ?TrailerInfo $trailer = null,
        public readonly ?Client $client = null,
    ) {
    }

    protected function identity(): array
    {
        return [$this->artist, $this->trailer];
    }
}
