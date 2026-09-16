<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Tests\Model\Album;

use LuckyWins\YandexMusic\Model\Album\Deprecation;
use LuckyWins\YandexMusic\Model\Model;
use LuckyWins\YandexMusic\Tests\Support\ModelTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(Deprecation::class)]
final class DeprecationTest extends ModelTestCase
{
    protected static function modelClass(): string
    {
        return Deprecation::class;
    }

    protected static function fullPayload(): array
    {
        return ['targetAlbumId' => 999, 'status' => 'replaced', 'done' => true];
    }

    /**
     * Every field here is optional, so there is no smaller payload than one
     * arbitrary field — an empty one deserializes to null by design.
     */
    protected static function requiredPayload(): array
    {
        return ['targetAlbumId' => 999];
    }

    protected function assertFullyPopulated(Model $model): void
    {
        self::assertInstanceOf(Deprecation::class, $model);
        self::assertSame(999, $model->targetAlbumId);
        self::assertTrue($model->done);
    }

    protected function equalityTriple(): array
    {
        return [new Deprecation(1, 's', true), new Deprecation(1, 's', true), new Deprecation(2, 's', true)];
    }
}
