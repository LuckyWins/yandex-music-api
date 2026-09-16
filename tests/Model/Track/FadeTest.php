<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Tests\Model\Track;

use LuckyWins\YandexMusic\Model\Model;
use LuckyWins\YandexMusic\Model\Track\Fade;
use LuckyWins\YandexMusic\Tests\Support\ModelTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(Fade::class)]
final class FadeTest extends ModelTestCase
{
    protected static function modelClass(): string
    {
        return Fade::class;
    }

    protected static function fullPayload(): array
    {
        return ['inStart' => 0.0, 'inStop' => 1.5, 'outStart' => 270.0, 'outStop' => 273.4];
    }

    /**
     * Every field here is optional, so there is no smaller payload than one
     * arbitrary field — an empty one deserializes to null by design.
     */
    protected static function requiredPayload(): array
    {
        return ['inStart' => 0.0];
    }

    protected function assertFullyPopulated(Model $model): void
    {
        self::assertInstanceOf(Fade::class, $model);
        self::assertSame(0.0, $model->inStart);
        self::assertSame(1.5, $model->inStop);
        self::assertSame(270.0, $model->outStart);
        self::assertSame(273.4, $model->outStop);
    }

    protected function equalityTriple(): array
    {
        return [new Fade(0.0, 1.5), new Fade(0.0, 1.5), new Fade(0.0, 2.0)];
    }
}
