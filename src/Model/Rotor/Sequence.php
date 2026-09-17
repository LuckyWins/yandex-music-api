<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Model\Rotor;

use LuckyWins\YandexMusic\Client;
use LuckyWins\YandexMusic\Model\Model;
use LuckyWins\YandexMusic\Model\Track\Track;

/**
 * One item in what a station is about to play.
 */
final class Sequence extends Model
{
    /** @var array<string, array{0: class-string<Model>, 1: 'one'|'list'}> */
    protected const NESTED = [
        'track' => [Track::class, 'one'],
        'trackParameters' => [TrackParameters::class, 'one'],
    ];

    public function __construct(
        public readonly ?string $type = null,
        public readonly ?Track $track = null,
        public readonly ?bool $liked = null,
        public readonly ?TrackParameters $trackParameters = null,
        public readonly ?Client $client = null,
    ) {
    }

    protected function identity(): array
    {
        return [$this->type, $this->track];
    }
}
