<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Tests\Model\Shot;

use LuckyWins\YandexMusic\Model\Model;
use LuckyWins\YandexMusic\Model\Shot\Shot;
use LuckyWins\YandexMusic\Tests\Support\ModelTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(Shot::class)]
final class ShotTest extends ModelTestCase
{
    protected static function modelClass(): string
    {
        return Shot::class;
    }

    protected static function fullPayload(): array
    {
        return ['order' => 0, 'played' => false, 'shotId' => 'abc', 'status' => 'ready', 'shotData' => ['coverUri' => 'c', 'mdsUrl' => 'm', 'shotText' => 't']];
    }

    protected static function requiredPayload(): array
    {
        return ['order' => 0, 'played' => false, 'shotId' => 'abc', 'status' => 'ready'];
    }

    protected function assertFullyPopulated(Model $model): void
    {
        self::assertInstanceOf(Shot::class, $model);
        self::assertSame('abc', $model->shotId);
        self::assertFalse($model->played);
        self::assertSame('t', $model->shotData?->shotText);
    }

    protected function equalityTriple(): array
    {
        return [new Shot(0, false, 'abc', 'ready'), new Shot(1, true, 'abc', 'ready'), new Shot(0, false, 'xyz', 'ready')];
    }
}
