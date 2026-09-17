<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Model\Landing;

use LuckyWins\YandexMusic\Client;
use LuckyWins\YandexMusic\Model\Model;

/**
 * The tracks a play-contexts block offers beyond its entities.
 */
final class PlayContextsData extends Model
{
    /** @var array<string, array{0: class-string<Model>, 1: 'one'|'list'}> */
    protected const NESTED = [
        'otherTracks' => [TrackShortOld::class, 'list'],
    ];

    public function __construct(
        /** @var list<TrackShortOld> */
        public readonly array $otherTracks = [],
        public readonly ?Client $client = null,
    ) {
    }

    protected function identity(): array
    {
        return [$this->otherTracks];
    }
}
