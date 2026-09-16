<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Tests\Model\Album;

use LuckyWins\YandexMusic\Model\Album\TrackPosition;
use LuckyWins\YandexMusic\Model\Model;
use LuckyWins\YandexMusic\Tests\Support\ModelTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(TrackPosition::class)]
final class TrackPositionTest extends ModelTestCase
{
    protected static function modelClass(): string
    {
        return TrackPosition::class;
    }

    protected static function fullPayload(): array
    {
        return ['volume' => 1, 'index' => 7];
    }

    protected static function requiredPayload(): array
    {
        return ['volume' => 1, 'index' => 7];
    }

    protected function assertFullyPopulated(Model $model): void
    {
        self::assertInstanceOf(TrackPosition::class, $model);
        self::assertSame(1, $model->volume);
        self::assertSame(7, $model->index);
    }

    protected function equalityTriple(): array
    {
        return [new TrackPosition(1, 7), new TrackPosition(1, 7), new TrackPosition(2, 7)];
    }
}
