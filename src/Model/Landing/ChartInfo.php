<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Model\Landing;

use LuckyWins\YandexMusic\Client;
use LuckyWins\YandexMusic\Model\Model;
use LuckyWins\YandexMusic\Model\Playlist\Playlist;

/**
 * A chart, which the service models as a playlist whose tracks carry their
 * standing, plus the menu of other charts.
 */
final class ChartInfo extends Model
{
    /** @var array<string, array{0: class-string<Model>, 1: 'one'|'list'}> */
    protected const NESTED = [
        'menu' => [ChartInfoMenu::class, 'one'],
        'chart' => [Playlist::class, 'one'],
    ];

    public function __construct(
        public readonly ?string $id = null,
        public readonly ?string $type = null,
        public readonly ?string $typeForFrom = null,
        public readonly ?string $title = null,
        public readonly ?ChartInfoMenu $menu = null,
        public readonly ?Playlist $chart = null,
        public readonly ?string $chartDescription = null,
        public readonly ?Client $client = null,
    ) {
    }

    protected function identity(): array
    {
        return [$this->id, $this->type];
    }
}
