<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Tests\Model\Track;

use LuckyWins\YandexMusic\Model\Model;
use LuckyWins\YandexMusic\Model\Track\Normalization;
use LuckyWins\YandexMusic\Tests\Support\ModelTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(Normalization::class)]
final class NormalizationTest extends ModelTestCase
{
    protected static function modelClass(): string
    {
        return Normalization::class;
    }

    protected static function fullPayload(): array
    {
        return ['gain' => -3.5, 'peak' => 32767];
    }

    protected static function requiredPayload(): array
    {
        return ['gain' => -3.5, 'peak' => 32767];
    }

    protected function assertFullyPopulated(Model $model): void
    {
        self::assertInstanceOf(Normalization::class, $model);
        self::assertSame(-3.5, $model->gain);
        self::assertSame(32767, $model->peak);
    }

    protected function equalityTriple(): array
    {
        return [new Normalization(-3.5, 1), new Normalization(-3.5, 1), new Normalization(-3.5, 2)];
    }
}
