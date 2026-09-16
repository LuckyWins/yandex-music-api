<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Tests\Model\Artist;

use LuckyWins\YandexMusic\Model\Artist\Stats;
use LuckyWins\YandexMusic\Model\Model;
use LuckyWins\YandexMusic\Tests\Support\ModelTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(Stats::class)]
final class StatsTest extends ModelTestCase
{
    protected static function modelClass(): string
    {
        return Stats::class;
    }

    protected static function fullPayload(): array
    {
        return ['lastMonthListeners' => 8659896, 'lastMonthListenersDelta' => -1200];
    }

    protected static function requiredPayload(): array
    {
        return ['lastMonthListeners' => 8659896, 'lastMonthListenersDelta' => -1200];
    }

    protected function assertFullyPopulated(Model $model): void
    {
        self::assertInstanceOf(Stats::class, $model);
        self::assertSame(8659896, $model->lastMonthListeners);
        self::assertSame(-1200, $model->lastMonthListenersDelta);
    }

    protected function equalityTriple(): array
    {
        return [new Stats(1, 2), new Stats(1, 2), new Stats(3, 2)];
    }
}
