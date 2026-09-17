<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Tests\Model\Label;

use LuckyWins\YandexMusic\Model\Album\Album;
use LuckyWins\YandexMusic\Model\Label\LabelAlbums;
use LuckyWins\YandexMusic\Model\Model;
use LuckyWins\YandexMusic\Model\Pager;
use LuckyWins\YandexMusic\Tests\Support\ModelTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(LabelAlbums::class)]
final class LabelAlbumsTest extends ModelTestCase
{
    protected static function modelClass(): string
    {
        return LabelAlbums::class;
    }

    protected static function fullPayload(): array
    {
        return [
            'albums' => [['id' => 4243617, 'title' => 'Hajime']],
            'pager' => ['total' => 42, 'page' => 0, 'perPage' => 20],
        ];
    }

    protected static function requiredPayload(): array
    {
        return ['albums' => [['id' => 4243617]]];
    }

    protected function assertFullyPopulated(Model $model): void
    {
        self::assertInstanceOf(LabelAlbums::class, $model);
        self::assertCount(1, $model->albums);
        self::assertInstanceOf(Album::class, $model->albums[0]);
        self::assertInstanceOf(Pager::class, $model->pager);
        self::assertSame(42, $model->pager->total);
    }

    protected function equalityTriple(): array
    {
        return [new LabelAlbums([new Album(1)]), new LabelAlbums([new Album(1)]), new LabelAlbums([new Album(2)])];
    }
}
