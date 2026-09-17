<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Tests\Model\Playlist;

use LuckyWins\YandexMusic\Model\Model;
use LuckyWins\YandexMusic\Model\Playlist\Playlist;
use LuckyWins\YandexMusic\Model\Playlist\PlaylistTrailer;
use LuckyWins\YandexMusic\Model\TrailerInfo;
use LuckyWins\YandexMusic\Tests\Support\ModelTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(PlaylistTrailer::class)]
final class PlaylistTrailerTest extends ModelTestCase
{
    protected static function modelClass(): string
    {
        return PlaylistTrailer::class;
    }

    protected static function fullPayload(): array
    {
        return [
            'playlist' => ['uid' => 503646255, 'kind' => 1042, 'title' => 'Плейлист дня'],
            'trailer' => [
                'title' => 'О чём этот плейлист',
                'tracks' => [['id' => 31190260, 'title' => 'Нирвана']],
            ],
            'shareable' => true,
        ];
    }

    protected static function requiredPayload(): array
    {
        return ['shareable' => true];
    }

    protected function assertFullyPopulated(Model $model): void
    {
        self::assertInstanceOf(PlaylistTrailer::class, $model);
        self::assertInstanceOf(Playlist::class, $model->playlist);
        self::assertSame(1042, $model->playlist->kind);
        self::assertInstanceOf(TrailerInfo::class, $model->trailer);
        self::assertCount(1, $model->trailer->tracks);
        self::assertTrue($model->shareable);
    }

    protected function equalityTriple(): array
    {
        return [
            new PlaylistTrailer(new Playlist(kind: 1042)),
            new PlaylistTrailer(new Playlist(kind: 1042), shareable: false),
            new PlaylistTrailer(new Playlist(kind: 1043)),
        ];
    }
}
