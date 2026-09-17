<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Model\Landing;

use LuckyWins\YandexMusic\Client;
use LuckyWins\YandexMusic\Model\Model;

/**
 * A track's standing in a chart, and which way it is moving.
 */
final class Chart extends Model
{
    /** @var array<string, array{0: class-string<Model>, 1: 'one'|'list'}> */
    protected const NESTED = [
        'trackId' => [TrackId::class, 'one'],
    ];

    public function __construct(
        public readonly int $position,
        public readonly string $progress,
        public readonly int $listeners,
        /** Places gained or lost since the last reckoning. */
        public readonly int $shift,
        public readonly ?string $bgColor = null,
        public readonly ?TrackId $trackId = null,
        public readonly ?Client $client = null,
    ) {
    }

    protected function identity(): array
    {
        return [$this->position, $this->listeners, $this->shift];
    }
}
