<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Tests\Model\Album;

use LuckyWins\YandexMusic\Model\Album\Album;
use LuckyWins\YandexMusic\Model\Album\AlbumTrailer;
use LuckyWins\YandexMusic\Model\Artist\Artist;
use LuckyWins\YandexMusic\Model\Model;
use LuckyWins\YandexMusic\Model\TrailerInfo;
use LuckyWins\YandexMusic\Tests\Support\ModelTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(AlbumTrailer::class)]
final class AlbumTrailerTest extends ModelTestCase
{
    protected static function modelClass(): string
    {
        return AlbumTrailer::class;
    }

    protected static function fullPayload(): array
    {
        return [
            'album' => ['id' => 4243617, 'title' => 'Hajime'],
            'artists' => [['id' => 4611844, 'name' => 'Miyagi & Эндшпиль']],
            'trailer' => ['title' => 'Об альбоме', 'tracks' => [['id' => 31190260]]],
        ];
    }

    protected static function requiredPayload(): array
    {
        return ['album' => ['id' => 4243617, 'title' => 'Hajime']];
    }

    protected function assertFullyPopulated(Model $model): void
    {
        self::assertInstanceOf(AlbumTrailer::class, $model);
        self::assertInstanceOf(Album::class, $model->album);
        self::assertCount(1, $model->artists);
        self::assertInstanceOf(Artist::class, $model->artists[0]);
        self::assertInstanceOf(TrailerInfo::class, $model->trailer);
        self::assertSame('Об альбоме', $model->trailer->title);
    }

    protected function equalityTriple(): array
    {
        return [new AlbumTrailer(new Album(4243617, 'Hajime')), new AlbumTrailer(new Album(4243617, 'Hajime')), new AlbumTrailer(new Album(4243618, 'Другой'))];
    }
}
