<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Tests\Model\Concert;

use LuckyWins\YandexMusic\Model\Concert\ConcertTabRange;
use LuckyWins\YandexMusic\Model\Model;
use LuckyWins\YandexMusic\Tests\Support\ModelTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(ConcertTabRange::class)]
final class ConcertTabRangeTest extends ModelTestCase
{
    protected static function modelClass(): string
    {
        return ConcertTabRange::class;
    }

    protected static function fullPayload(): array
    {
        return ['offset' => 5, 'limit' => -1];
    }

    protected static function requiredPayload(): array
    {
        return ['offset' => 5];
    }

    protected function assertFullyPopulated(Model $model): void
    {
        self::assertInstanceOf(ConcertTabRange::class, $model);
        self::assertSame(5, $model->offset);
        self::assertSame(-1, $model->limit, 'the feed asks for everything after the top');
    }

    protected function equalityTriple(): array
    {
        return [new ConcertTabRange(0, 5), new ConcertTabRange(0, 5), new ConcertTabRange(5, -1)];
    }
}
