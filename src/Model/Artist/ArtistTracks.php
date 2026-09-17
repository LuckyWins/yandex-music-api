<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Model\Artist;

use LuckyWins\YandexMusic\Client;
use LuckyWins\YandexMusic\Model\Model;
use LuckyWins\YandexMusic\Model\Pager;
use LuckyWins\YandexMusic\Model\Track\Track;

/**
 * A page of an artist's tracks.
 */
final class ArtistTracks extends Model
{
    /** @var array<string, array{0: class-string<Model>, 1: 'one'|'list'}> */
    protected const NESTED = [
        'tracks' => [Track::class, 'list'],
        'pager' => [Pager::class, 'one'],
    ];

    public function __construct(
        /** @var list<Track> */
        public readonly array $tracks = [],
        public readonly ?Pager $pager = null,
        public readonly ?Client $client = null,
    ) {
    }

    protected function identity(): array
    {
        return [$this->tracks, $this->pager];
    }
}
