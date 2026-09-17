<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Tests\Model\Landing;

use LuckyWins\YandexMusic\Model\Landing\Chart;
use LuckyWins\YandexMusic\Model\Model;
use LuckyWins\YandexMusic\Tests\Support\ModelTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(Chart::class)]
final class ChartTest extends ModelTestCase
{
    protected static function modelClass(): string
    {
        return Chart::class;
    }

    protected static function fullPayload(): array
    {
        return ['position' => 100, 'progress' => 'same', 'listeners' => 5000, 'shift' => 0, 'bgColor' => '#fff', 'trackId' => ['id' => 1]];
    }

    protected static function requiredPayload(): array
    {
        return ['position' => 100, 'progress' => 'same', 'listeners' => 5000, 'shift' => 0];
    }

    protected function assertFullyPopulated(Model $model): void
    {
        self::assertInstanceOf(Chart::class, $model);
        self::assertSame(100, $model->position);
        self::assertSame('same', $model->progress);
        self::assertSame(1, $model->trackId?->id);
    }

    protected function equalityTriple(): array
    {
        return [new Chart(100, 'same', 5000, 0), new Chart(100, 'same', 5000, 0), new Chart(99, 'same', 5000, 0)];
    }
}
