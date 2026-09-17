<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Tests\Model\Feed;

use LuckyWins\YandexMusic\Model\Album\Album;
use LuckyWins\YandexMusic\Model\Feed\AlbumEvent;
use LuckyWins\YandexMusic\Model\Model;
use LuckyWins\YandexMusic\Model\Track\Track;
use LuckyWins\YandexMusic\Tests\Support\ModelTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(AlbumEvent::class)]
final class AlbumEventTest extends ModelTestCase
{
    protected static function modelClass(): string
    {
        return AlbumEvent::class;
    }

    protected static function fullPayload(): array
    {
        return [
            'album' => ['id' => 4243617, 'title' => 'Hajime'],
            'tracks' => [['id' => 31190260, 'title' => 'Нирвана']],
        ];
    }

    protected static function requiredPayload(): array
    {
        return ['album' => ['id' => 4243617, 'title' => 'Hajime']];
    }

    protected function assertFullyPopulated(Model $model): void
    {
        self::assertInstanceOf(AlbumEvent::class, $model);
        self::assertInstanceOf(Album::class, $model->album);
        self::assertSame('Hajime', $model->album->title);
        self::assertCount(1, $model->tracks);
        self::assertInstanceOf(Track::class, $model->tracks[0]);
    }

    protected function equalityTriple(): array
    {
        return [
            new AlbumEvent(new Album(4243617, 'Hajime')),
            new AlbumEvent(new Album(4243617, 'Hajime'), [new Track(1)]),
            new AlbumEvent(new Album(4243618, 'Другой')),
        ];
    }
}
