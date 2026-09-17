<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Tests\Model\Landing;

use LuckyWins\YandexMusic\Model\Landing\ChartInfo;
use LuckyWins\YandexMusic\Model\Landing\ChartInfoMenu;
use LuckyWins\YandexMusic\Model\Model;
use LuckyWins\YandexMusic\Model\Playlist\Playlist;
use LuckyWins\YandexMusic\Tests\Support\ModelTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(ChartInfo::class)]
final class ChartInfoTest extends ModelTestCase
{
    protected static function modelClass(): string
    {
        return ChartInfo::class;
    }

    protected static function fullPayload(): array
    {
        return [
            'id' => 'chart',
            'type' => 'chart',
            'typeForFrom' => 'chart',
            'title' => 'Чарт',
            'chartDescription' => 'Самое популярное',
            'menu' => ['items' => [
                ['title' => 'Россия', 'url' => '/chart/russia', 'selected' => true],
                ['title' => 'Мир', 'url' => '/chart/world'],
            ]],
            'chart' => [
                'uid' => 103372440,
                'kind' => 1000,
                'title' => 'Чарт',
                'tracks' => [[
                    'id' => 31190260,
                    'chart' => ['position' => 1, 'progress' => 'same', 'listeners' => 9000, 'shift' => 0],
                ]],
            ],
        ];
    }

    protected static function requiredPayload(): array
    {
        return ['id' => 'chart'];
    }

    /**
     * A chart is a playlist whose tracks carry their standing — the model the
     * playlists stage already built, reused whole.
     */
    protected function assertFullyPopulated(Model $model): void
    {
        self::assertInstanceOf(ChartInfo::class, $model);
        self::assertSame('chart', $model->id);
        self::assertSame('Чарт', $model->title);
        self::assertSame('Самое популярное', $model->chartDescription);

        self::assertInstanceOf(ChartInfoMenu::class, $model->menu);
        self::assertCount(2, $model->menu->items);
        self::assertSame('Россия', $model->menu->selected()?->title);

        self::assertInstanceOf(Playlist::class, $model->chart);
        self::assertCount(1, $model->chart->tracks);
        self::assertSame(1, $model->chart->tracks[0]->chart?->position);
    }

    protected function equalityTriple(): array
    {
        return [
            new ChartInfo('chart', 'chart'),
            new ChartInfo('chart', 'chart', title: 'иначе'),
            new ChartInfo('chart-world', 'chart'),
        ];
    }
}
