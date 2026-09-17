<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Model\Landing;

use LuckyWins\YandexMusic\Client;
use LuckyWins\YandexMusic\Model\Model;
use LuckyWins\YandexMusic\Model\Track\Track;

/**
 * A track in a chart, with its standing.
 */
final class ChartItem extends Model
{
    /** @var array<string, array{0: class-string<Model>, 1: 'one'|'list'}> */
    protected const NESTED = [
        'track' => [Track::class, 'one'],
        'chart' => [Chart::class, 'one'],
    ];

    public function __construct(
        public readonly ?Track $track = null,
        public readonly ?Chart $chart = null,
        public readonly ?Client $client = null,
    ) {
    }

    protected function identity(): array
    {
        return [$this->track, $this->chart];
    }
}
