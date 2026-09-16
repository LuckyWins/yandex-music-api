<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Tests\Model\Track;

use LuckyWins\YandexMusic\Model\Model;
use LuckyWins\YandexMusic\Model\Track\PoetryLoverMatch;
use LuckyWins\YandexMusic\Tests\Support\ModelTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(PoetryLoverMatch::class)]
final class PoetryLoverMatchTest extends ModelTestCase
{
    protected static function modelClass(): string
    {
        return PoetryLoverMatch::class;
    }

    protected static function fullPayload(): array
    {
        return ['begin' => 0, 'end' => 12, 'line' => 3];
    }

    protected static function requiredPayload(): array
    {
        return ['begin' => 0, 'end' => 12, 'line' => 3];
    }

    protected function assertFullyPopulated(Model $model): void
    {
        self::assertInstanceOf(PoetryLoverMatch::class, $model);
        self::assertSame(0, $model->begin);
        self::assertSame(12, $model->end);
        self::assertSame(3, $model->line);
    }

    protected function equalityTriple(): array
    {
        return [new PoetryLoverMatch(0, 12, 3), new PoetryLoverMatch(0, 12, 3), new PoetryLoverMatch(0, 12, 4)];
    }
}
