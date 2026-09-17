<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Tests\Model\Track;

use LuckyWins\YandexMusic\Model\Model;
use LuckyWins\YandexMusic\Model\Track\R128;
use LuckyWins\YandexMusic\Tests\Support\ModelTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(R128::class)]
final class R128Test extends ModelTestCase
{
    protected static function modelClass(): string
    {
        return R128::class;
    }

    protected static function fullPayload(): array
    {
        return ['i' => -9.5, 'tp' => 0.3];
    }

    protected static function requiredPayload(): array
    {
        return ['i' => -9.5, 'tp' => 0.3];
    }

    protected function assertFullyPopulated(Model $model): void
    {
        self::assertInstanceOf(R128::class, $model);
        self::assertSame(-9.5, $model->i);
        self::assertSame(0.3, $model->tp);
    }

    protected function equalityTriple(): array
    {
        return [new R128(-9.5, 0.3), new R128(-9.5, 0.3), new R128(-8.0, 0.3)];
    }
}
