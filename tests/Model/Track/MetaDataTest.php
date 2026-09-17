<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Tests\Model\Track;

use LuckyWins\YandexMusic\Model\Model;
use LuckyWins\YandexMusic\Model\Track\MetaData;
use LuckyWins\YandexMusic\Tests\Support\ModelTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(MetaData::class)]
final class MetaDataTest extends ModelTestCase
{
    protected static function modelClass(): string
    {
        return MetaData::class;
    }

    protected static function fullPayload(): array
    {
        return ['album' => 'Nevermind', 'volume' => 1, 'year' => 1991, 'number' => 2, 'genre' => 'rock', 'lyricist' => 'K. Cobain', 'version' => 'remaster', 'composer' => 'Nirvana'];
    }

    /**
     * Every field here is optional, so there is no smaller payload than one
     * arbitrary field — an empty one deserializes to null by design.
     */
    protected static function requiredPayload(): array
    {
        return ['album' => 'Nevermind'];
    }

    protected function assertFullyPopulated(Model $model): void
    {
        self::assertInstanceOf(MetaData::class, $model);
        self::assertSame('Nevermind', $model->album);
        self::assertSame(1991, $model->year);
        self::assertSame('K. Cobain', $model->lyricist);
        self::assertSame('Nirvana', $model->composer);
    }

    protected function equalityTriple(): array
    {
        return [new MetaData('a', 1), new MetaData('a', 1), new MetaData('b', 1)];
    }
}
