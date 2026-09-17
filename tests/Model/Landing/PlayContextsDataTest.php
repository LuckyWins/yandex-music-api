<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Tests\Model\Landing;

use LuckyWins\YandexMusic\Model\Landing\PlayContextsData;
use LuckyWins\YandexMusic\Model\Landing\TrackId;
use LuckyWins\YandexMusic\Model\Landing\TrackShortOld;
use LuckyWins\YandexMusic\Model\Model;
use LuckyWins\YandexMusic\Tests\Support\ModelTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(PlayContextsData::class)]
final class PlayContextsDataTest extends ModelTestCase
{
    protected static function modelClass(): string
    {
        return PlayContextsData::class;
    }

    protected static function fullPayload(): array
    {
        return [
            'otherTracks' => [
                ['trackId' => ['id' => 31190260, 'albumId' => 4243617], 'timestamp' => '2026-09-17T12:00:00+00:00'],
                ['trackId' => ['id' => 31190261]],
            ],
        ];
    }

    protected static function requiredPayload(): array
    {
        return ['otherTracks' => [['trackId' => ['id' => 31190260]]]];
    }

    protected function assertFullyPopulated(Model $model): void
    {
        self::assertInstanceOf(PlayContextsData::class, $model);
        self::assertCount(2, $model->otherTracks);
        self::assertInstanceOf(TrackShortOld::class, $model->otherTracks[0]);
        self::assertSame('2026-09-17T12:00:00+00:00', $model->otherTracks[0]->timestamp);
    }

    protected function equalityTriple(): array
    {
        return [
            new PlayContextsData([new TrackShortOld(new TrackId(1))]),
            new PlayContextsData([new TrackShortOld(new TrackId(1))]),
            new PlayContextsData([new TrackShortOld(new TrackId(2))]),
        ];
    }
}
