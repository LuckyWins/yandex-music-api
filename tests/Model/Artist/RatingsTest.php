<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Tests\Model\Artist;

use LuckyWins\YandexMusic\Model\Artist\Ratings;
use LuckyWins\YandexMusic\Model\Model;
use LuckyWins\YandexMusic\Tests\Support\ModelTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(Ratings::class)]
final class RatingsTest extends ModelTestCase
{
    protected static function modelClass(): string
    {
        return Ratings::class;
    }

    protected static function fullPayload(): array
    {
        return ['month' => 42, 'week' => 17, 'day' => 5];
    }

    protected static function requiredPayload(): array
    {
        return ['month' => 42];
    }

    protected function assertFullyPopulated(Model $model): void
    {
        self::assertInstanceOf(Ratings::class, $model);
        self::assertSame(42, $model->month);
        self::assertSame(17, $model->week);
        self::assertSame(5, $model->day);
    }

    protected function equalityTriple(): array
    {
        return [new Ratings(42, 17), new Ratings(42, 17), new Ratings(42, 18)];
    }
}
