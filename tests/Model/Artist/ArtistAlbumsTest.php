<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Tests\Model\Artist;

use LuckyWins\YandexMusic\Model\Artist\ArtistAlbums;
use LuckyWins\YandexMusic\Model\Model;
use LuckyWins\YandexMusic\Model\Pager;
use LuckyWins\YandexMusic\Tests\Support\ModelTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(ArtistAlbums::class)]
final class ArtistAlbumsTest extends ModelTestCase
{
    protected static function modelClass(): string
    {
        return ArtistAlbums::class;
    }

    protected static function fullPayload(): array
    {
        return ['albums' => [['id' => 1, 'title' => 'a']], 'pager' => ['total' => 28, 'page' => 0, 'perPage' => 20]];
    }

    protected static function requiredPayload(): array
    {
        return ['albums' => []];
    }

    protected function assertFullyPopulated(Model $model): void
    {
        self::assertInstanceOf(ArtistAlbums::class, $model);
        self::assertCount(1, $model->albums);
        self::assertSame(28, $model->pager?->total);
    }

    protected function equalityTriple(): array
    {
        return [new ArtistAlbums(), new ArtistAlbums(), new ArtistAlbums([], new Pager(1, 0, 1))];
    }
}
