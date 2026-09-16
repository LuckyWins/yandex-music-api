<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Model\Track;

use LuckyWins\YandexMusic\Client;
use LuckyWins\YandexMusic\Model\Model;

/**
 * What else sounds like a given track.
 */
final class SimilarTracks extends Model
{
    /** @var array<string, array{0: class-string<Model>, 1: 'one'|'list'}> */
    protected const NESTED = [
        'track' => [Track::class, 'one'],
        'similarTracks' => [Track::class, 'list'],
    ];

    public function __construct(
        public readonly ?Track $track = null,
        /** @var list<Track> */
        public readonly array $similarTracks = [],
        public readonly ?Client $client = null,
    ) {
    }

    protected function identity(): array
    {
        return [$this->track, $this->similarTracks];
    }
}
