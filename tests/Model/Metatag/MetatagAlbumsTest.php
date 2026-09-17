<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Tests\Model\Metatag;

use LuckyWins\YandexMusic\Model\Album\Album;
use LuckyWins\YandexMusic\Model\Metatag\MetatagAlbums;
use LuckyWins\YandexMusic\Model\Metatag\MetatagSortByValue;
use LuckyWins\YandexMusic\Model\Metatag\MetatagTitle;
use LuckyWins\YandexMusic\Model\Model;
use LuckyWins\YandexMusic\Model\Pager;
use LuckyWins\YandexMusic\Tests\Support\ModelTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(MetatagAlbums::class)]
final class MetatagAlbumsTest extends ModelTestCase
{
    protected static function modelClass(): string
    {
        return MetatagAlbums::class;
    }

    protected static function fullPayload(): array
    {
        return [
            'id' => 'run',
            'title' => ['title' => 'Для бега'],
            'coverUri' => 'avatars.invalid/tag/%%',
            'color' => '#ff0000',
            'stationId' => 'tag:run',
            'pager' => ['total' => 120, 'page' => 0, 'perPage' => 20],
            'albums' => [['id' => 4243617, 'title' => 'Hajime']],
            'sortByValues' => [['value' => 'popular', 'title' => 'Популярные', 'active' => true]],
        ];
    }

    protected static function requiredPayload(): array
    {
        return ['id' => 'run'];
    }

    protected function assertFullyPopulated(Model $model): void
    {
        self::assertInstanceOf(MetatagAlbums::class, $model);
        self::assertSame('run', $model->id);
        self::assertSame('Для бега', $model->title?->title);
        self::assertSame('tag:run', $model->stationId);
        self::assertInstanceOf(Pager::class, $model->pager);
        self::assertSame(120, $model->pager->total);
        self::assertCount(1, $model->albums);
        self::assertInstanceOf(Album::class, $model->albums[0]);
        self::assertSame('Hajime', $model->albums[0]->title);
        self::assertCount(1, $model->sortByValues);
        self::assertInstanceOf(MetatagSortByValue::class, $model->sortByValues[0]);
    }

    protected function equalityTriple(): array
    {
        return [new MetatagAlbums('run'), new MetatagAlbums('run', new MetatagTitle('Для бега')), new MetatagAlbums('sleep')];
    }
}
