<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Tests\Model;

use LuckyWins\YandexMusic\Model\Model;
use LuckyWins\YandexMusic\Model\Track\TrackShort;
use LuckyWins\YandexMusic\Model\TracksList;
use LuckyWins\YandexMusic\Tests\Support\ModelTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(TracksList::class)]
final class TracksListTest extends ModelTestCase
{
    protected static function modelClass(): string
    {
        return TracksList::class;
    }

    protected static function fullPayload(): array
    {
        return [
            'uid' => 503646255,
            'revision' => 42,
            'playlistUuid' => 'a9f0c1e2-0000-4000-8000-000000000003',
            'tracks' => [
                ['id' => 31190260, 'albumId' => 4243617, 'timestamp' => '2019-06-01T12:00:00+00:00'],
                ['id' => 31190261, 'timestamp' => '2019-06-02T12:00:00+00:00'],
            ],
        ];
    }

    protected static function requiredPayload(): array
    {
        return ['uid' => 503646255];
    }

    protected function assertFullyPopulated(Model $model): void
    {
        self::assertInstanceOf(TracksList::class, $model);
        self::assertSame(503646255, $model->uid);
        self::assertSame(42, $model->revision);
        self::assertSame('a9f0c1e2-0000-4000-8000-000000000003', $model->playlistUuid);
        self::assertCount(2, $model->tracks);
        self::assertInstanceOf(TrackShort::class, $model->tracks[0]);
    }

    /**
     * The ids come out in the form tracks() wants, album included where the
     * library knows it.
     */
    public function testCompositeIdsAreReadyForTheTrackEndpoints(): void
    {
        $model = TracksList::fromApi(self::fullPayload(), self::client());

        self::assertInstanceOf(TracksList::class, $model);
        self::assertSame(['31190260:4243617', '31190261'], $model->compositeIds());
    }

    protected function equalityTriple(): array
    {
        return [
            new TracksList(503646255, 42),
            new TracksList(503646255, 42, tracks: [new TrackShort(1)]),
            new TracksList(503646255, 43),
        ];
    }
}
