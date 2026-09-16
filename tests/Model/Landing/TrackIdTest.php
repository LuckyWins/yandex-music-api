<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Tests\Model\Landing;

use LuckyWins\YandexMusic\Model\Landing\TrackId;
use LuckyWins\YandexMusic\Model\Model;
use LuckyWins\YandexMusic\Tests\Support\ModelTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(TrackId::class)]
final class TrackIdTest extends ModelTestCase
{
    protected static function modelClass(): string
    {
        return TrackId::class;
    }

    protected static function fullPayload(): array
    {
        return ['id' => 1, 'trackId' => 2, 'albumId' => 3, 'from' => 'chart'];
    }

    protected static function requiredPayload(): array
    {
        return ['id' => 1];
    }

    protected function assertFullyPopulated(Model $model): void
    {
        self::assertInstanceOf(TrackId::class, $model);
        self::assertSame(1, $model->id);
        self::assertSame(3, $model->albumId);
        self::assertSame('chart', $model->from);
    }

    protected function equalityTriple(): array
    {
        return [new TrackId(1, 2, 3), new TrackId(1, 2, 3), new TrackId(1, 2, 4)];
    }
}
