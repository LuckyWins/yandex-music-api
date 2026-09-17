<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Model\Metatag;

use LuckyWins\YandexMusic\Client;
use LuckyWins\YandexMusic\Model\Artist\Artist;
use LuckyWins\YandexMusic\Model\Model;
use LuckyWins\YandexMusic\Model\Track\Track;

/**
 * An artist under a tag, with a few of their tracks to hear why.
 */
final class MetatagArtistEntry extends Model
{
    /** @var array<string, array{0: class-string<Model>, 1: 'one'|'list'}> */
    protected const NESTED = [
        'artist' => [Artist::class, 'one'],
        'popularTracks' => [Track::class, 'list'],
    ];

    public function __construct(
        public readonly ?Artist $artist = null,
        /** @var list<Track> */
        public readonly array $popularTracks = [],
        public readonly ?Client $client = null,
    ) {
    }

    protected function identity(): array
    {
        return [$this->artist];
    }
}
