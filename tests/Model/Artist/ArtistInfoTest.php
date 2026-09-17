<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Tests\Model\Artist;

use LuckyWins\YandexMusic\Model\Artist\Artist;
use LuckyWins\YandexMusic\Model\Artist\ArtistInfo;
use LuckyWins\YandexMusic\Model\Artist\ArtistTrailerStatus;
use LuckyWins\YandexMusic\Model\Artist\Stats;
use LuckyWins\YandexMusic\Model\Cover;
use LuckyWins\YandexMusic\Model\Model;
use LuckyWins\YandexMusic\Tests\Support\ModelTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(ArtistInfo::class)]
final class ArtistInfoTest extends ModelTestCase
{
    protected static function modelClass(): string
    {
        return ArtistInfo::class;
    }

    protected static function fullPayload(): array
    {
        return [
            'artist' => ['id' => 4611844, 'name' => 'Miyagi & Эндшпиль'],
            'likesCount' => 900000,
            'stats' => ['lastMonthListeners' => 8659896, 'lastMonthListenersDelta' => 1200],
            'trailer' => ['available' => true],
            'covers' => [['type' => 'pic', 'uri' => 'avatars.invalid/%%']],
            'description' => 'Дуэт из Владикавказа',
            'artistType' => 'artist',
        ];
    }

    protected static function requiredPayload(): array
    {
        return ['likesCount' => 900000];
    }

    protected function assertFullyPopulated(Model $model): void
    {
        self::assertInstanceOf(ArtistInfo::class, $model);
        self::assertInstanceOf(Artist::class, $model->artist);
        self::assertSame(900000, $model->likesCount);
        self::assertInstanceOf(Stats::class, $model->stats);
        self::assertInstanceOf(ArtistTrailerStatus::class, $model->trailer);
        self::assertTrue($model->trailer->available);
        self::assertCount(1, $model->covers);
        self::assertInstanceOf(Cover::class, $model->covers[0]);
        self::assertSame('artist', $model->artistType);
    }

    protected function equalityTriple(): array
    {
        return [new ArtistInfo(new Artist(4611844, 'Miyagi')), new ArtistInfo(new Artist(4611844, 'Miyagi'), 1), new ArtistInfo(new Artist(4611845, 'Другой'))];
    }
}
