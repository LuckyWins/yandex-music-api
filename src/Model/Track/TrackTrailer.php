<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Model\Track;

use LuckyWins\YandexMusic\Client;
use LuckyWins\YandexMusic\Model\Model;

/**
 * A trailer introducing a track or episode.
 */
final class TrackTrailer extends Model
{
    /** @var array<string, array{0: class-string<Model>, 1: 'one'|'list'}> */
    protected const NESTED = [
        'track' => [Track::class, 'one'],
    ];

    public function __construct(
        public readonly ?string $title = null,
        public readonly ?Track $track = null,
        public readonly ?Client $client = null,
    ) {
    }

    protected function identity(): array
    {
        return [$this->title, $this->track];
    }
}
