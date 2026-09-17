<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Tests\Model\Metatag;

use LuckyWins\YandexMusic\Model\Metatag\MetatagPlaylists;
use LuckyWins\YandexMusic\Model\Metatag\MetatagSortByValue;
use LuckyWins\YandexMusic\Model\Metatag\MetatagTitle;
use LuckyWins\YandexMusic\Model\Model;
use LuckyWins\YandexMusic\Model\Pager;
use LuckyWins\YandexMusic\Model\Playlist\Playlist;
use LuckyWins\YandexMusic\Tests\Support\ModelTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(MetatagPlaylists::class)]
final class MetatagPlaylistsTest extends ModelTestCase
{
    protected static function modelClass(): string
    {
        return MetatagPlaylists::class;
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
            'playlists' => [['uid' => 1, 'kind' => 1042, 'title' => 'Бег']],
            'sortByValues' => [['value' => 'popular', 'title' => 'Популярные', 'active' => true]],
        ];
    }

    protected static function requiredPayload(): array
    {
        return ['id' => 'run'];
    }

    protected function assertFullyPopulated(Model $model): void
    {
        self::assertInstanceOf(MetatagPlaylists::class, $model);
        self::assertSame('run', $model->id);
        self::assertSame('Для бега', $model->title?->title);
        self::assertSame('tag:run', $model->stationId);
        self::assertInstanceOf(Pager::class, $model->pager);
        self::assertSame(120, $model->pager->total);
        self::assertCount(1, $model->playlists);
        self::assertInstanceOf(Playlist::class, $model->playlists[0]);
        self::assertSame(1042, $model->playlists[0]->kind);
        self::assertCount(1, $model->sortByValues);
        self::assertInstanceOf(MetatagSortByValue::class, $model->sortByValues[0]);
    }

    protected function equalityTriple(): array
    {
        return [new MetatagPlaylists('run'), new MetatagPlaylists('run', new MetatagTitle('Для бега')), new MetatagPlaylists('sleep')];
    }
}
