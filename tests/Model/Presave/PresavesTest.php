<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Tests\Model\Presave;

use LuckyWins\YandexMusic\Model\Album\Album;
use LuckyWins\YandexMusic\Model\Model;
use LuckyWins\YandexMusic\Model\Presave\Presaves;
use LuckyWins\YandexMusic\Tests\Support\ModelTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(Presaves::class)]
final class PresavesTest extends ModelTestCase
{
    protected static function modelClass(): string
    {
        return Presaves::class;
    }

    protected static function fullPayload(): array
    {
        return [
            'upcomingAlbums' => [['id' => 4243618, 'title' => 'Ещё не вышел']],
            'releasedAlbums' => [['id' => 4243617, 'title' => 'Hajime']],
        ];
    }

    protected static function requiredPayload(): array
    {
        return ['upcomingAlbums' => [['id' => 4243618]]];
    }

    protected function assertFullyPopulated(Model $model): void
    {
        self::assertInstanceOf(Presaves::class, $model);
        self::assertCount(1, $model->upcomingAlbums);
        self::assertInstanceOf(Album::class, $model->upcomingAlbums[0]);
        self::assertSame('Ещё не вышел', $model->upcomingAlbums[0]->title);
        self::assertCount(1, $model->releasedAlbums);
        self::assertSame('Hajime', $model->releasedAlbums[0]->title);
    }

    protected function equalityTriple(): array
    {
        return [
            new Presaves([new Album(4243618)]),
            new Presaves([new Album(4243618)]),
            new Presaves([new Album(4243619)]),
        ];
    }
}
