<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Tests\Model\Track;

use LuckyWins\YandexMusic\Model\Landing\Chart;
use LuckyWins\YandexMusic\Model\Model;
use LuckyWins\YandexMusic\Model\Track\Track;
use LuckyWins\YandexMusic\Model\Track\TrackShort;
use LuckyWins\YandexMusic\Tests\Support\ModelTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(TrackShort::class)]
final class TrackShortTest extends ModelTestCase
{
    protected static function modelClass(): string
    {
        return TrackShort::class;
    }

    protected static function fullPayload(): array
    {
        return [
            'id' => 31190260,
            'timestamp' => '2019-06-01T12:00:00+00:00',
            'albumId' => 4243617,
            'playCount' => 12,
            'recent' => true,
            'originalIndex' => 3,
            'chart' => ['position' => 1, 'progress' => 'same', 'listeners' => 9000, 'shift' => 0],
            'track' => ['id' => 31190260, 'title' => 'Нирвана'],
        ];
    }

    protected static function requiredPayload(): array
    {
        return ['id' => 31190260];
    }

    protected function assertFullyPopulated(Model $model): void
    {
        self::assertInstanceOf(TrackShort::class, $model);
        self::assertSame(31190260, $model->id);
        self::assertSame('2019-06-01T12:00:00+00:00', $model->timestamp);
        self::assertSame(4243617, $model->albumId);
        self::assertSame(12, $model->playCount);
        self::assertTrue($model->recent);
        self::assertSame(3, $model->originalIndex);
        self::assertInstanceOf(Chart::class, $model->chart);
        self::assertInstanceOf(Track::class, $model->track);
        self::assertSame('Нирвана', $model->track->title);
    }

    /**
     * The usual case: a playlist lists positions, and the tracks themselves
     * are fetched separately.
     */
    public function testATrackIsNotRequired(): void
    {
        $model = TrackShort::fromApi([
            'id' => 31190260,
            'albumId' => 4243617,
            'timestamp' => '2019-06-01T12:00:00+00:00',
        ], self::client());

        self::assertInstanceOf(TrackShort::class, $model);
        self::assertNull($model->track);
        self::assertSame('31190260:4243617', $model->compositeId());
    }

    public function testCompositeIdWithoutAnAlbum(): void
    {
        $model = TrackShort::fromApi(['id' => 31190260], self::client());

        self::assertInstanceOf(TrackShort::class, $model);
        self::assertSame('31190260', $model->compositeId());
    }

    protected function equalityTriple(): array
    {
        return [
            new TrackShort(31190260, albumId: 4243617),
            new TrackShort(31190260, '2019-06-01T12:00:00+00:00', 4243617),
            new TrackShort(31190260, albumId: 9999999),
        ];
    }
}
