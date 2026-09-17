<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Tests\Model\Playlist;

use LuckyWins\YandexMusic\Model\Model;
use LuckyWins\YandexMusic\Model\Playlist\Playlist;
use LuckyWins\YandexMusic\Model\Playlist\PlaylistsList;
use LuckyWins\YandexMusic\Tests\Support\ModelTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(PlaylistsList::class)]
final class PlaylistsListTest extends ModelTestCase
{
    protected static function modelClass(): string
    {
        return PlaylistsList::class;
    }

    protected static function fullPayload(): array
    {
        return [
            'playlists' => [
                ['uid' => 503646255, 'kind' => 1042, 'title' => 'Плейлист дня'],
                ['uid' => 503646255, 'kind' => 1043, 'title' => 'Премьера'],
            ],
        ];
    }

    protected static function requiredPayload(): array
    {
        return ['playlists' => [['kind' => 1042]]];
    }

    protected function assertFullyPopulated(Model $model): void
    {
        self::assertInstanceOf(PlaylistsList::class, $model);
        self::assertCount(2, $model->playlists);
        self::assertInstanceOf(Playlist::class, $model->playlists[0]);
        self::assertSame('Премьера', $model->playlists[1]->title);
    }

    protected function equalityTriple(): array
    {
        return [
            new PlaylistsList([new Playlist(kind: 1042)]),
            new PlaylistsList([new Playlist(kind: 1042)]),
            new PlaylistsList([new Playlist(kind: 1043)]),
        ];
    }
}
