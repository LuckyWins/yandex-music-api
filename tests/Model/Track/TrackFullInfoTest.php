<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Tests\Model\Track;

use LuckyWins\YandexMusic\Model\Model;
use LuckyWins\YandexMusic\Model\Track\Track;
use LuckyWins\YandexMusic\Model\Track\TrackFullInfo;
use LuckyWins\YandexMusic\Tests\Support\ModelTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(TrackFullInfo::class)]
final class TrackFullInfoTest extends ModelTestCase
{
    protected static function modelClass(): string
    {
        return TrackFullInfo::class;
    }

    protected static function fullPayload(): array
    {
        return ['track' => ['id' => 1], 'similarTracks' => [['id' => 2]], 'alsoInAlbums' => [['id' => 3]], 'aliases' => ['alt'], 'artists' => [['id' => 10, 'name' => 'a']]];
    }

    protected static function requiredPayload(): array
    {
        return ['track' => ['id' => 1]];
    }

    protected function assertFullyPopulated(Model $model): void
    {
        self::assertInstanceOf(TrackFullInfo::class, $model);
        self::assertSame(1, $model->track?->id);
        self::assertCount(1, $model->similarTracks);
        self::assertCount(1, $model->alsoInAlbums);
        self::assertSame(['alt'], $model->aliases);
        self::assertCount(1, $model->artists);
    }

    protected function equalityTriple(): array
    {
        return [new TrackFullInfo(), new TrackFullInfo(), new TrackFullInfo(new Track(1))];
    }
}
