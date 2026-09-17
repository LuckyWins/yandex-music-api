<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Tests\Model\Playlist;

use LuckyWins\YandexMusic\Model\Model;
use LuckyWins\YandexMusic\Model\Playlist\GeneratedPlaylist;
use LuckyWins\YandexMusic\Model\Playlist\Playlist;
use LuckyWins\YandexMusic\Tests\Support\ModelTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(GeneratedPlaylist::class)]
final class GeneratedPlaylistTest extends ModelTestCase
{
    protected static function modelClass(): string
    {
        return GeneratedPlaylist::class;
    }

    protected static function fullPayload(): array
    {
        return [
            'type' => 'playlistOfTheDay',
            'ready' => true,
            'notify' => false,
            'previewDescription' => 'Каждый день новый',
            'description' => [['type' => 'text', 'value' => 'Обновляется каждый день']],
            'data' => ['uid' => 503646255, 'kind' => 1042, 'title' => 'Плейлист дня'],
        ];
    }

    protected static function requiredPayload(): array
    {
        return ['type' => 'playlistOfTheDay'];
    }

    protected function assertFullyPopulated(Model $model): void
    {
        self::assertInstanceOf(GeneratedPlaylist::class, $model);
        self::assertSame('playlistOfTheDay', $model->type);
        self::assertTrue($model->ready);
        self::assertFalse($model->notify);
        self::assertSame('Каждый день новый', $model->previewDescription);
        self::assertSame([['type' => 'text', 'value' => 'Обновляется каждый день']], $model->description);
        self::assertInstanceOf(Playlist::class, $model->data);
        self::assertSame('Плейлист дня', $model->data->title);
    }

    protected function equalityTriple(): array
    {
        return [
            new GeneratedPlaylist('playlistOfTheDay', data: new Playlist(kind: 1042)),
            new GeneratedPlaylist('playlistOfTheDay', true, false, new Playlist(kind: 1042)),
            new GeneratedPlaylist('missedLikes', data: new Playlist(kind: 1042)),
        ];
    }
}
