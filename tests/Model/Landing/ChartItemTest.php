<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Tests\Model\Landing;

use LuckyWins\YandexMusic\Model\Landing\Chart;
use LuckyWins\YandexMusic\Model\Landing\ChartItem;
use LuckyWins\YandexMusic\Model\Model;
use LuckyWins\YandexMusic\Model\Track\Track;
use LuckyWins\YandexMusic\Tests\Support\ModelTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(ChartItem::class)]
final class ChartItemTest extends ModelTestCase
{
    protected static function modelClass(): string
    {
        return ChartItem::class;
    }

    protected static function fullPayload(): array
    {
        return [
            'track' => ['id' => 31190260, 'title' => 'Нирвана'],
            'chart' => ['position' => 1, 'progress' => 'same', 'listeners' => 9000, 'shift' => 0],
        ];
    }

    protected static function requiredPayload(): array
    {
        return ['track' => ['id' => 31190260]];
    }

    protected function assertFullyPopulated(Model $model): void
    {
        self::assertInstanceOf(ChartItem::class, $model);
        self::assertInstanceOf(Track::class, $model->track);
        self::assertSame('Нирвана', $model->track->title);
        self::assertInstanceOf(Chart::class, $model->chart);
        self::assertSame(1, $model->chart->position);
    }

    protected function equalityTriple(): array
    {
        return [
            new ChartItem(new Track(31190260)),
            new ChartItem(new Track(31190260)),
            new ChartItem(new Track(31190261)),
        ];
    }
}
