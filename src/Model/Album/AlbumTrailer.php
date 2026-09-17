<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Model\Album;

use LuckyWins\YandexMusic\Client;
use LuckyWins\YandexMusic\Model\Artist\Artist;
use LuckyWins\YandexMusic\Model\Model;
use LuckyWins\YandexMusic\Model\TrailerInfo;

/**
 * An album's trailer: the album, who made it, and what the trailer plays.
 */
final class AlbumTrailer extends Model
{
    /** @var array<string, array{0: class-string<Model>, 1: 'one'|'list'}> */
    protected const NESTED = [
        'album' => [Album::class, 'one'],
        'artists' => [Artist::class, 'list'],
        'trailer' => [TrailerInfo::class, 'one'],
    ];

    public function __construct(
        public readonly ?Album $album = null,
        /** @var list<Artist> */
        public readonly array $artists = [],
        public readonly ?TrailerInfo $trailer = null,
        public readonly ?Client $client = null,
    ) {
    }

    protected function identity(): array
    {
        return [$this->album, $this->trailer];
    }
}
