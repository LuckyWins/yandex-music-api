<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Tests\Model\Playlist;

use LuckyWins\YandexMusic\Model\Model;
use LuckyWins\YandexMusic\Model\Playlist\PlayCounter;
use LuckyWins\YandexMusic\Tests\Support\ModelTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(PlayCounter::class)]
final class PlayCounterTest extends ModelTestCase
{
    protected static function modelClass(): string
    {
        return PlayCounter::class;
    }

    protected static function fullPayload(): array
    {
        return ['value' => 7, 'description' => '7 дней подряд', 'updated' => true];
    }

    protected static function requiredPayload(): array
    {
        return self::fullPayload();
    }

    protected function assertFullyPopulated(Model $model): void
    {
        self::assertInstanceOf(PlayCounter::class, $model);
        self::assertSame(7, $model->value);
        self::assertSame('7 дней подряд', $model->description);
        self::assertTrue($model->updated);
    }

    protected function equalityTriple(): array
    {
        return [
            new PlayCounter(7, '7 дней подряд', true),
            new PlayCounter(7, '7 дней подряд', false),
            new PlayCounter(8, '8 дней подряд', true),
        ];
    }
}
