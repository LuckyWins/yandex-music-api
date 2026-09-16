<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Tests\Model\Artist;

use LuckyWins\YandexMusic\Model\Artist\Counts;
use LuckyWins\YandexMusic\Model\Model;
use LuckyWins\YandexMusic\Tests\Support\ModelTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(Counts::class)]
final class CountsTest extends ModelTestCase
{
    protected static function modelClass(): string
    {
        return Counts::class;
    }

    protected static function fullPayload(): array
    {
        return ['tracks' => 120, 'directAlbums' => 8, 'alsoAlbums' => 30, 'alsoTracks' => 15];
    }

    protected static function requiredPayload(): array
    {
        return ['tracks' => 120, 'directAlbums' => 8, 'alsoAlbums' => 30, 'alsoTracks' => 15];
    }

    protected function assertFullyPopulated(Model $model): void
    {
        self::assertInstanceOf(Counts::class, $model);
        self::assertSame(120, $model->tracks);
        self::assertSame(8, $model->directAlbums);
    }

    protected function equalityTriple(): array
    {
        return [new Counts(1, 2, 3, 4), new Counts(1, 2, 3, 4), new Counts(9, 2, 3, 4)];
    }
}
