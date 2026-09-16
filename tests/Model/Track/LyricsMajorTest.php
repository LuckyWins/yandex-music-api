<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Tests\Model\Track;

use LuckyWins\YandexMusic\Model\Model;
use LuckyWins\YandexMusic\Model\Track\LyricsMajor;
use LuckyWins\YandexMusic\Tests\Support\ModelTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(LyricsMajor::class)]
final class LyricsMajorTest extends ModelTestCase
{
    protected static function modelClass(): string
    {
        return LyricsMajor::class;
    }

    protected static function fullPayload(): array
    {
        return ['id' => 7, 'name' => 'lyricfind', 'prettyName' => 'LyricFind'];
    }

    protected static function requiredPayload(): array
    {
        return ['id' => 7, 'name' => 'lyricfind', 'prettyName' => 'LyricFind'];
    }

    protected function assertFullyPopulated(Model $model): void
    {
        self::assertInstanceOf(LyricsMajor::class, $model);
        self::assertSame(7, $model->id);
        self::assertSame('LyricFind', $model->prettyName);
    }

    protected function equalityTriple(): array
    {
        return [new LyricsMajor(7, 'a', 'A'), new LyricsMajor(7, 'b', 'B'), new LyricsMajor(8, 'a', 'A')];
    }
}
