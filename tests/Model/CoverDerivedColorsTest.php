<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Tests\Model;

use LuckyWins\YandexMusic\Model\CoverDerivedColors;
use LuckyWins\YandexMusic\Model\Model;
use LuckyWins\YandexMusic\Tests\Support\ModelTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(CoverDerivedColors::class)]
final class CoverDerivedColorsTest extends ModelTestCase
{
    protected static function modelClass(): string
    {
        return CoverDerivedColors::class;
    }

    protected static function fullPayload(): array
    {
        return ['average' => '#112233', 'waveText' => '#ffffff', 'miniPlayer' => '#000000', 'accent' => '#ff0000'];
    }

    /**
     * Every field here is optional, so there is no smaller payload than one
     * arbitrary field — an empty one deserializes to null by design.
     */
    protected static function requiredPayload(): array
    {
        return ['average' => '#112233'];
    }

    protected function assertFullyPopulated(Model $model): void
    {
        self::assertInstanceOf(CoverDerivedColors::class, $model);
        self::assertSame('#112233', $model->average);
        self::assertSame('#ff0000', $model->accent);
    }

    protected function equalityTriple(): array
    {
        return [new CoverDerivedColors('#1'), new CoverDerivedColors('#1'), new CoverDerivedColors('#2')];
    }
}
