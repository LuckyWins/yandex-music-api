<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Tests\Model\Artist;

use LuckyWins\YandexMusic\Model\Artist\Artist;
use LuckyWins\YandexMusic\Model\Artist\ArtistTrailer;
use LuckyWins\YandexMusic\Model\Model;
use LuckyWins\YandexMusic\Model\TrailerInfo;
use LuckyWins\YandexMusic\Tests\Support\ModelTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(ArtistTrailer::class)]
final class ArtistTrailerTest extends ModelTestCase
{
    protected static function modelClass(): string
    {
        return ArtistTrailer::class;
    }

    protected static function fullPayload(): array
    {
        return [
            'artist' => ['id' => 4611844, 'name' => 'Miyagi & Эндшпиль'],
            'trailer' => ['title' => 'О дуэте', 'tracks' => [['id' => 31190260, 'title' => 'Нирвана']]],
        ];
    }

    protected static function requiredPayload(): array
    {
        return ['artist' => ['id' => 4611844, 'name' => 'Miyagi & Эндшпиль']];
    }

    protected function assertFullyPopulated(Model $model): void
    {
        self::assertInstanceOf(ArtistTrailer::class, $model);
        self::assertInstanceOf(Artist::class, $model->artist);
        self::assertInstanceOf(TrailerInfo::class, $model->trailer);
        self::assertCount(1, $model->trailer->tracks);
    }

    protected function equalityTriple(): array
    {
        return [new ArtistTrailer(new Artist(4611844, 'Miyagi')), new ArtistTrailer(new Artist(4611844, 'Miyagi')), new ArtistTrailer(new Artist(4611845, 'Другой'))];
    }
}
